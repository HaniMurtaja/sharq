<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Area;
use App\Models\City;
use App\Models\User;
use App\Models\Zone;
use App\Models\Group;
use App\Models\Order;
use App\Enum\UserRole;
use App\Models\Branch;
use App\Models\Client;
use App\Models\Wallet;
use League\Csv\Reader;
use App\Models\Country;
use App\Models\ClientUser;
use App\Models\UserDetail;
use App\Models\ClientUsers;
use App\Traits\FileHandler;
use Illuminate\Support\Str;
use App\Models\ClientDetail;
use App\Models\ClientsGroup;

use Illuminate\Http\Request;

use App\Models\ClientBranches;
use App\Models\IntegrationCompany;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\Clients\ClientRequest;
use App\Http\Resources\Admin\ClientResource;
use App\Http\Requests\Clients\ClientBranchesRequest;
use App\Http\Requests\ExistUserRequest;
use App\Http\Requests\UserRequest;
use Illuminate\Pipeline\Pipeline;
use App\Filters\UserFiltersPipeline;
use App\Services\ClientService;
use Spatie\Activitylog\Models\Activity;

class ClientsUpdatedController extends Controller
{
    use FileHandler;

    public function index(Request $request)
    {
        $query = app(Pipeline::class)
            ->send(User::query()->where('user_role', 2))
            ->through([
                UserFiltersPipeline::class
            ])
            ->thenReturn();

        $user = auth()->user();

        $countryId = $user->country_id;



        if ($countryId) {
            $query->whereHas('client.city', function ($q) use ($countryId) {
                $q->where('country_id', $countryId);
            });
        }


        // تخصيص المستخدم حسب نوعه
        if (auth()->user()->user_role == UserRole::CLIENT) {
            $query->where('id', auth()->id());
        }

        if (auth()->user()->user_role == UserRole::BRANCH) {
            $query->where('branch_id', auth()->user()->branch_id);
        }

        $items = $query->orderByDesc('created_at')->paginate(50);

        return view('admin.pages.clientsUpdated.index', compact('items'));
    }


    public function getClientLogData(Request $request)
    {
        $clientId = $request->client_id;

        $logs = Activity::where('subject_type', \App\Models\User::class)
            ->where('subject_id', $clientId)
            ->latest()
            ->get()
            ->map(function ($log) {
                return [
                    'description' => $log->description,
                    'user_name' => $log->causer?->full_name ?? 'System',
                    'user_email' => $log->causer?->email ?? 'N/A',
                    'date' => $log->created_at->format('Y-m-d H:i:s'),
                ];
            });

        // dd($logs);

        return response()->json(['logs' => $logs]);
    }




    public function create()
    {

        $client_groups = ClientsGroup::all();
        $driver_groups = Group::all();
        $countries = Country::all();
        $integrations = IntegrationCompany::all();
        $all_branches = Branch::all();

        $user = auth()->user();
        $countryId = $user->country_id;

        $query = City::query();


        if ($countryId) {
            $query->where('country_id', $countryId);
        }

        $all_cities = $query->get();
        return view('admin.pages.clientsUpdated.add',  compact(['all_branches', 'countries',   'client_groups', 'driver_groups',   'all_cities',  'integrations']));
    }

    public function store(ClientRequest $request)
    {

        abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');

        // dd($request->all());
        $client = Client::create([
            'first_name' => $request->name,
            'phone' => $request->phone,
            'email'     => $request->email,
            'password' => Hash::make($request->password),
            'integration_token' => $this->GenerateToken(),
            'user_role' => 2,
            'is_active' => 1,

        ]);


        Wallet::create([
            'operator_id' => $client->id,
            'currency' => 'SAR'
        ]);

        if ($request['profile_photo']) {

            $filePath = $request->file('profile_photo')->store('images', 'public');


            $client->image = $filePath;

            $client->image =  $this->upload_files($request->file('profile_photo'), 'images/' . $client->id);

            $client->save();
        }
        ClientDetail::create([
            'user_id' => $client->id,
            'country_id' => $request->country_id,
            'city_id' => $request->city_id,
            'currency' => $request->currency,
            'auto_dispatch' => $request->auto_dispatch ?? 0,
            'is_integration' => $request->is_integration ?? 0,
            'integration_id' => $request->integration_id,
            'default_prepartion_time' => $request->default_prepartion_time,
            'min_prepartion_time' => $request->min_prepartion_time,
            'partial_pay' => $request->partial_pay,
            'note' => $request->note,
            'client_group_id' => $request->client_group_id,
            'driver_group_id' => $request->driver_group_id,
            'account_number' => @$request->account_number,
            'price_order'     => @$request->price_order,
        ]);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($client)
            ->withProperties([
                'client_id' => $client->id,
                'client_name' => $client->first_name,
                'created_by_name' => auth()->user()->full_name,
                'created_by_email' => auth()->user()->email,
                'created_at' => now()->toDateTimeString(),
            ])
            ->log(auth()->user()->full_name . ' created a new client: ' . $client->first_name);


        return redirect()->route('clientupdated');  //route
    }


    public function edit($id = NULL)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');

        $client = Client::findOrFail($id);
        $client_detail = $client->client;
        $profile_url = $client->image;

        $client_groups = ClientsGroup::all();
        $driver_groups = Group::all();
        $countries = Country::all();
        $integrations = IntegrationCompany::all();
        $all_branches = Branch::all();

        $user = auth()->user();
        $countryId = $user->country_id;

        $query = City::query();


        if ($countryId) {
            $query->where('country_id', $countryId);
        }

        $all_cities = $query->get();

        return view('admin.pages.clientsUpdated.edit', compact(['all_branches', 'countries',   'client_groups', 'driver_groups',   'all_cities',   'integrations', 'client', 'client_detail', 'profile_url']));
    }



    public function update(ClientRequest $request, $id)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');

        $client = Client::findOrFail($id);

        $client->update([
            'first_name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'user_role' => 2,
        ]);



        if ($request->hasFile('profile_photo')) {
            $client->image = $this->upload_files($request->file('profile_photo'), 'images/' . $client->id);
        }

        $client->save();

        if ($request->filled('password')) {
            $client->update(['password' => Hash::make($request->password)]);
        }


        if (!$client->wallet) {
            Wallet::create([
                'operator_id' => $client->id,
                'currency' => 'SAR',
            ]);
        }


        $this->createOrUpdateClientDetail($client->id, $request);

        return redirect()->route('clientupdated');
    }


    protected function createOrUpdateClientDetail($userId, $request)
    {
        ClientDetail::updateOrCreate(
            ['user_id' => $userId],
            [
                'country_id' => $request->country_id,
                'city_id' => $request->city_id,
                'currency' => $request->currency,
                'default_prepartion_time' => $request->default_prepartion_time,
                'min_prepartion_time' => $request->min_prepartion_time,
                'partial_pay' => $request->partial_pay,
                'note' => $request->note,
                'client_group_id' => $request->client_group_id,
                'driver_group_id' => $request->driver_group_id,
                'auto_dispatch' => $request->auto_dispatch ?? 0,
                'is_integration' => $request->is_integration ?? 0,
                'integration_id' => $request->integration_id,
                'price_order' => $request->price_order,
                'account_number' => $request->account_number,
            ]
        );
    }






    public function destroy($id, ClientService $clientService)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');
        $clientService->deleteClientById($id);
        return redirect()->route('clientupdated');
    }



    public function view($id)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');

        $client = Client::withCount('orders')->findOrFail($id);


        $client = new ClientResource($client);
        $client = $client->toArray(request());


        // dd($client['is_active']);

        $all_cities = City::all();

        $driver_groups = Group::all();
        $all_branches = Branch::all();
        $areas = Area::all();
        $templates = Role::all();

        return view('admin.pages.clientsUpdated.view', compact(['templates', 'client', 'driver_groups', 'all_cities', 'all_branches', 'areas']));
    }

    public function getUsersSearch(Request $request)
    {
        $search = $request->q;

        $users = User::select('id', 'first_name', 'last_name')
            ->where('first_name', 'LIKE', "%{$search}%")
            ->orWhere('last_name', 'LIKE', "%{$search}%")
            ->limit(20)
            ->select(['id', 'first_name', 'last_name'])
            ->get();

        return response()->json($users);
    }



    public function saveClientBranch(ClientBranchesRequest $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');

        $validatedData = $request->validated();
        $businessHours = $request->input('business_hours', []);

        $branchData = [
            'client_id' => $validatedData['branch_client_id'],
            'name' => $validatedData['branch_name'],
            'phone' => $validatedData['branch_phone'],
            'pickup_id' => $validatedData['pickup_id'],
            'custom_id' => $validatedData['custom_id'] ?? null,
            'client_group' => $validatedData['client_group_id'] ?? null,
            'driver_group' => $validatedData['driver_group_id'] ?? null,
            'lat' => $validatedData['lat'] ?? null,
            'lng' => $validatedData['lng'] ?? null,
            'country' => $validatedData['country'] ?? null,
            'city_id' => $validatedData['city_id'] ?? null,
            'area_id' => $validatedData['area_id'] ?? null,
            'street' => $validatedData['street'],
            'landmark' => $validatedData['landmark'] ?? null,
            'building' => $validatedData['building'] ?? null,
            'floor' => $validatedData['floor'] ?? null,
            'apartment' => $validatedData['apartment'] ?? null,
            'description' => $validatedData['description'] ?? null,
            'business_hours' => $businessHours,
        ];

        $branch = ClientBranches::updateOrCreate(
            ['id' => $request->input('branch_id')],
            $branchData
        );

        $userData = [
            'first_name' => $validatedData['branch_name'],
            'phone' => $validatedData['branch_phone'],
            'email' => $validatedData['branch_email'],
            'client_id' => $validatedData['branch_client_id'],
            'branch_id' => $branch->id,
            'user_role' => UserRole::BRANCH,
            'password' => Hash::make($validatedData['branch_password']) ?? Hash::make(123456),
        ];


        // dd($userData['password']);

        $user_branch = User::updateOrCreate(
            ['branch_id' => $branch->id],
            $userData
        );

        $user_branch->syncRoles([6]);

        // try {
        //     $firebase = new \App\Repositories\FirebaseRepositoryTest();
        //     $firebase->saveBranches(collect([$branch]));
        // } catch (\Throwable $e) {

        //     return response()->json(['message' => 'Failed to sync branch to Firebase: ' . $e->getMessage()]);
        // }


        return response()->json(['message' => 'Branch saved successfully']);
    }




    public function editClientBrnch(Request $request)
    {
        $branch = ClientBranches::findOrFail($request->id);
        $user = User::where('branch_id', $branch->id)->first();
        return response()->json(['branch' => $branch, 'user' => $user]);
    }








    public function getClientBranches(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');

        $query = ClientBranches::with(['city'])
            ->where('client_id', $request->id)
            ->select('id', 'client_id', 'auto_dispatch', 'is_active', 'name', 'phone', 'city_id', 'created_at');

        return DataTables::of($query)
            ->addColumn('city', fn($row) => $row->city?->name ?? '-')
            ->addColumn('user_branch', function ($row) {
                return User::where('branch_id', $row->id)->first();
            })


            ->addColumn('created_at', fn($row) => $row->created_at->format('Y-m-d'))

            ->rawColumns(['action'])
            ->make(true);
    }


    public function changeClientBranchStatus(Request $request)
    {
        // dd($request->all());
        abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');

        $branch = ClientBranches::findOrFail($request->id);
        $branch->is_active = $request->status;
        $branch->save();
        return response()->json('change successfully');
    }

    public function changeClientBranchAutoDispatch(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');

        $branch = ClientBranches::findOrFail($request->id);
        $branch->auto_dispatch = $request->auto_dispatch;
        $branch->save();
        return response()->json('change successfully');
    }

    public function changeClientStatus(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');

        $client = Client::findOrFail($request->client_id);

        $client->is_active = $request->is_active;
        $client->save();


        $statusText = $request->is_active ? 'activated' : 'deactivated';


        activity()
            ->causedBy(auth()->user())
            ->performedOn($client)
            ->withProperties([
                'client_id' => $client->id,
                'client_name' => $client->first_name,
                'new_status' => $request->is_active ? 'active' : 'inactive',
                'changed_by_name' => auth()->user()->full_name,
                'changed_by_email' => auth()->user()->email,
                'changed_at' => now()->toDateTimeString(),
            ])
            ->log(auth()->user()->full_name . " {$statusText} client: " . $client->first_name);

        return response()->json('change successfully');
    }





    public function getOrders(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');

        $orders = Order::with(['branch'])
            ->where('ingr_shop_id', $request->id);

        return DataTables::of($orders)
            ->addColumn('created_at', fn($order) => $order->created_at->format('Y-m-d h:i a'))
            ->addColumn('branch', fn($order) => $order->branch?->name ?? '-')
            ->addColumn('customer_name', fn($order) => $order->customer_name)
            ->addColumn('customer_area', fn($order) => $order->city)
            ->addColumn('status', fn($order) => $order->status->getLabel())

            ->filterColumn('branch', function ($query, $keyword) {
                $query->whereHas('branch', function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', "%{$keyword}%");
                });
            })
            ->filterColumn('customer_name', function ($query, $keyword) {
                $query->where('customer_name', 'LIKE', "%{$keyword}%");
            })

            ->make(true);
    }




    public function uploadBranches(Request $request)
    {
        $request->validate([
            'branches_file' => 'required|mimes:csv,txt,xlsx',
        ]);

        $client_id = $request->client_id_for_uplaod_branches;
        $file = $request->file('branches_file');

        try {
            $records = [];


            $extension = $file->getClientOriginalExtension();

            if (in_array($extension, ['csv', 'txt'])) {

                $csv = Reader::createFromPath($file->getPathname(), 'r');
                $csv->setHeaderOffset(0);
                $records = iterator_to_array($csv->getRecords());
            } elseif ($extension === 'xlsx') {

                $spreadsheet = IOFactory::load($file->getPathname());
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();


                $headers = array_map('trim', $rows[0]);
                unset($rows[0]);

                foreach ($rows as $row) {
                    $records[] = array_combine($headers, $row);
                }
            } else {
                throw new \Exception('Unsupported file format.');
            }

            $existingBranchIds = [];
            $branches = [];

            foreach ($records as $key => $record) {
                if ($request->upload_type == 2) {
                    if (!empty($record['Branch Name'])) {
                        $branchId = $record['Pickup ID'] ?? null;
                        $branchName = $record['Branch Name'];
                        $customID = $record['pickupId'] ?? null;
                        $phone = $record['Phone'] ?? null;
                        $lat = $record['lat'] ?? null;
                        $lng = $record['lng'] ?? null;

                        if (ClientBranches::where('id', $branchId)->exists()) {
                            $existingBranchIds[] = $branchId;
                        } else {
                            $branchData = [
                                'id' => $branchId,
                                'client_id' => $client_id,
                                'name' => $branchName,
                                'country' => $request->country ?? null,
                                'city_id' => $request->city_id ?? null,
                                'area_id' => $request->area_id ?? null,
                                'lat' => $lat,
                                'lng' => $lng,
                                'pickup_id' => $customID ? 2 : 1,
                                'custom_id' => $customID,
                                'phone' => $phone,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];

                            if ($branchId !== null) {
                                $branchData['id'] = $branchId;
                            }

                            $branches[] = $branchData;
                        }
                    }
                } else {
                    if (!empty($record['Branch Name'])) {
                        $branchId = $record['Branch ID'] ?? null;
                        $branchName = $record['Branch Name'];
                        $lat = $record['lat'] ?? null;
                        $lng = $record['lng'] ?? null;

                        if (ClientBranches::where('id', $branchId)->exists()) {
                            $existingBranchIds[] = $branchId;
                        } else {
                            $branchData = [
                                'id' => $branchId,
                                'client_id' => $client_id,
                                'name' => $branchName,
                                'country' => $request->country ?? null,
                                'city_id' => $request->city_id ?? null,
                                'area_id' => $request->area_id ?? null,
                                'lat' => $lat,
                                'lng' => $lng,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];

                            if ($branchId !== null) {
                                $branchData['id'] = $branchId;
                            }

                            $branches[] = $branchData;
                        }
                    }
                }
            }
            // dd($branches);
            if (!empty($branches)) {
                ClientBranches::insert($branches);
            }

            return response()->json([
                'message' => 'Branches uploaded successfully.',
                'existingBranchIds' => $existingBranchIds,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error processing the file: ' . $e->getMessage(),
            ], 500);
        }
    }


    // users

    public function saveClientUser(UserRequest $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');


        try {

            $user = ClientUser::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email'     => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone

            ]);
            $user->syncRoles([$request->role]);

            $client_user = UserDetail::create([
                'user_id' => $user->id,
                'locked' => $request->locked ?? 0,

                'marketplace_access' =>  $request->marketplace_access ?? 0,
                'mac_address' =>  $request->mac_address ?? null,
                'sim_card' =>  $request->sim_card ?? null,
                'sn' =>  $request->sn ?? null,
                'request_per_second' =>  $request->request_per_second ?? null,

            ]);
            ClientUsers::create([
                'client_id' => $request->id,
                'user_id' => $user->id,
                'status' => 0
            ]);


            return response()->json(['message' => 'User saved successfully']);
        } catch (Exception $e) {

            return response()->json(['error' => 'Failed to save user'], 500);
        }
    }







    public function getUsers(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');

        $client = Client::findOrFail($request->id);
        $excludedUserIds = $client->users->pluck('user_id')->toArray();

        $query = ClientUsers::with('user.roles')
            ->where('client_id', $request->id);

        return DataTables::of($query)
            ->filter(function ($query) use ($request) {
                if ($search = $request->input('search.value')) {
                    $query->whereHas('user', function ($q) use ($search) {
                        $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                    });
                }
            })
            ->addColumn('id', fn($row) => $row->id)
            ->addColumn('name', fn($row) => $row->user?->full_name)
            ->addColumn('email', fn($row) => $row->user?->email)
            ->addColumn('status', fn($row) => $row->status)

            ->addColumn('last_role', fn($row) => $row->user?->roles->last()?->name ?? null)
            ->addColumn('profile_url', fn($row) => $row->user?->image)

            ->rawColumns(['profile_url'])
            ->with([
                'all_users' => ClientUser::whereNotIn('id', $excludedUserIds)
                    ->select(['id', 'first_name', 'last_name'])
                    ->get()
                    ->map(function ($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->full_name,
                        ];
                    })
            ])
            ->make(true);
    }








    public function changeClientUserStatus(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');


        $user = ClientUsers::findOrFail($request->id);
        $user->status = $request->status;
        $user->save();
    }

    public function editClientExistUser(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');


        $user = ClientUsers::findOrFail($request->id)->user;
        // dd($user->image);
        return response()->json(['user' => $user, 'role' => $user?->roles->last()?->name, 'mac_address' => $user->user?->mac_address]);
    }




    public function saveClientExistUser(ExistUserRequest $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');

        // dd($request->all());
        try {

            ClientUsers::create([
                'client_id' => $request->id,
                'user_id' => $request->user_id,
                'status' => 0
            ]);

            $client_user = UserDetail::create([
                'user_id' => $request->user_id,
                'locked' => $request->locked ?? 0,

                'marketplace_access' =>  $request->marketplace_access ?? 0,
                'mac_address' =>  $request->mac_address ?? null,
                'sim_card' =>  $request->sim_card ?? null,
                'sn' =>  $request->sn ?? null,
                'request_per_second' =>  $request->request_per_second ?? null,

            ]);

            return response()->json(['message' => 'User saved successfully']);
        } catch (Exception $e) {

            return response()->json(['error' => 'Failed to save user'], 500);
        }
    }




    public function updateClientExistUser(UserRequest $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');


        try {
            $user = ClientUser::findOrFail($request->edit_user_client_id);
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            if ($request->password) {
                $user->password = Hash::make($request->password);
            }

            if ($request->profile_photo) {

                $filePath = $request->file('profile_photo')->store('images', 'public');


                $user->image = $filePath;

                $user->image =  $this->upload_files($request->file('profile_photo'), 'images/' . $user->id);
            }

            $user->save();

            $user_detail =  UserDetail::where('user_id', $user->id)->first();
            if ($user_detail) {
                $user_detail?->update([
                    'mac_address' => $request->mac_address
                ]);
            }



            return response()->json(['message' => 'User saved successfully']);
        } catch (Exception $e) {

            return response()->json(['error' => 'Failed to save user'], 500);
        }
    }


    public function deleteClientExistUser(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');


        try {
            $user = ClientUser::findOrFail($request->edit_user_client_id);
            $user->delete();
            ClientUsers::where('client_id', $request->id)->where('user_id', $request->edit_user_client_id)->first()->delete();
            return response()->json(['message' => 'User deleteed successfully']);
        } catch (Exception $e) {

            return response()->json(['error' => 'Failed to delete user'], 500);
        }
    }






    public function GenerateToken()
    {

        do {
            $token = Str::random(80);
        } while (\App\Models\User::where('integration_token', $token)->exists());

        return $token;
    }
}
