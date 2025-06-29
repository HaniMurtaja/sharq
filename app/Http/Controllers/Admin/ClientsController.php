<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Area;
use App\Models\City;
use App\Models\Zone;
use App\Models\Group;
use App\Models\Branch;
use App\Models\Client;
use App\Models\Wallet;
use App\Models\Country;
use App\Enum\DeleveryFeed;
use App\Enum\UserRole;
use App\Models\ClientUser;
use App\Models\UserDetail;
use App\Models\ZoneDetail;
use Carbon\CarbonTimeZone;
use App\Models\ClientUsers;
use App\Models\ClientDetail;
use App\Models\ClientsGroup;
use Illuminate\Http\Request;
use App\Models\ClientBranches;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\GroupCalculationMethod;
use App\Http\Requests\ExistUserRequest;
use App\Http\Requests\Clients\ZoneRequest;
use App\Http\Requests\Clients\GroupRequest;
use App\Http\Requests\Clients\BranchRequest;
use App\Http\Requests\Clients\ClientRequest;
use App\Http\Services\MapDistanceCalculator;
use App\Http\Requests\Clients\ClientBranchesRequest;
use App\Models\IntegrationCompany;
use App\Models\Order;
use App\Models\User;
use App\Models\WebHook;
use App\Traits\FileHandler;
use Illuminate\Support\Str;
use League\Csv\Reader;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ClientsController extends Controller
{
    use FileHandler;

    public function branchesList(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_branch_groups'), 403, 'You do not have permission to view this page.');


        $totalData = Branch::count();
        $totalFiltered = $totalData;

        if (empty($request->input('search'))) {
            $branches = Branch::orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            $search = $request->input('search');

            $branches = Branch::where('name', 'LIKE', "%{$search}%")

                ->orderBy('created_at', 'desc')
                ->paginate(10);

            $totalFiltered = Branch::where('name', 'LIKE', "%{$search}%")

                ->count();
        }

        $data = [];
        if (!empty($branches)) {
            foreach ($branches as $branch) {
                $nestedData['id'] = $branch->id;
                $nestedData['name'] = $branch->name;
                $data[] = $nestedData;
            }
        }


        return response()->json(['branches' => $data, 'branches_count' => $totalFiltered]);
    }


    public function editBranch($id = NULL)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_branch_groups'), 403, 'You do not have permission to view this page.');

        $branch = Branch::findOrFail($id);
        return response()->json(['branch' => $branch]);
    }

    public function updateBranch(BranchRequest $request, $id)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_branch_groups'), 403, 'You do not have permission to view this page.');

        $branch = Branch::findOrFail($id);
        $branch->update([
            'name' => $request['branch_name'],
            'driver_id' => $request['driver_id'],
        ]);
    }

    public function saveBranch(BranchRequest $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_branch_groups'), 403, 'You do not have permission to view this page.');


        try {
            if ($request->branch_id) {
                $this->updateBranch($request, $request->branch_id);
            } else {
                Branch::create([
                    'name' => $request->branch_name,
                    'driver_id' => $request->driver_id
                ]);
            }


            return response()->json(['message' => 'Branch saved successfully']);
        } catch (Exception $e) {

            return response()->json(['error' => 'Failed to save branch'], 500);
        }
    }

    public function deleteBranch($id)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_branch_groups'), 403, 'You do not have permission to view this page.');

        $branch = Branch::findOrFail($id);
        $branch->delete();
        return response()->json('Branch deleted successfully');
    }

    public function clientsGroupList(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_clients_groups'), 403, 'You do not have permission to view this page.');

        $columns = ['id', 'name', 'calculation_method'];

        $totalData = ClientsGroup::count();
        $totalFiltered = $totalData;



        if (empty($request->input('search'))) {
            $groups = ClientsGroup::orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            $search = $request->input('search');

            $groups = ClientsGroup::where('name', 'LIKE', "%{$search}%")

                ->orderBy('created_at', 'desc')
                ->paginate(10);

            $totalFiltered = ClientsGroup::where('name', 'LIKE', "%{$search}%")

                ->count();
        }

        $data = [];
        if (!empty($groups)) {
            foreach ($groups as $group) {
                $nestedData['id'] = $group->id;
                $nestedData['name'] = $group->name;

                $data[] = $nestedData;
            }
        }

        // $json_data = [
        //     "draw" => intval($request->input('draw')),
        //     "recordsTotal" => intval($totalData),
        //     "recordsFiltered" => intval($totalFiltered),
        //     "data" => $data
        // ];

        return response()->json(['groups' => $data, 'groups_count' => $totalFiltered]);
    }



    public function saveClientsGroup(GroupRequest $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_clients_groups'), 403, 'You do not have permission to view this page.');

        // dd($request->all());
        // try {
        if ($request->client_group_id) {
            $this->updateClientsGroup($request, $request->client_group_id);
        } else {
            $group = ClientsGroup::create([
                'name' => $request['group_name'],
                'calculation_method' => $request['calculation_method'],
                'default_delivery_fee' => $request['default_delivery_fee'],
                'collection_amount' => $request['collection_amount'],
                'service_type' => $request['service_type'],
            ]);

            $calculationMethodData = $request->except(['_token', 'group_name', 'client_group_id', 'calculation_method', 'default_delivery_fee', 'collection_amount', 'service_type']);


            GroupCalculationMethod::updateOrCreate(
                [
                    'group_id' => $group->id,
                    'data' => ($calculationMethodData)
                ]
            );
        }

        return response()->json(['message' => 'Group saved successfully']);
        // } catch (Exception $e) {
        //     return response()->json(['error' => 'Failed to save group'], 500);
        // }
    }


    public function changeClientActive(Request $request)
    {

        abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');
        // dd($request->id);
        $client = Client::findOrFail($request->id);
        $client->is_active = $request->is_active;
        $client->save();
        return response()->json('success');
    }

    public function editClientsGroup($id = NULL)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_clients_groups'), 403, 'You do not have permission to view this page.');

        $group = ClientsGroup::findOrFail($id);
        $group->calculation_method_label = $group->calculation_method->value;
        $feedType = $group->calculation_method->value;
        $viewContent = '';
        if ($feedType) {
            $deliveryFeed = DeleveryFeed::tryFrom($feedType);
            $calculation_method = $group->calculationMethod;
            if ($deliveryFeed) {
                $calculationMethod = $deliveryFeed->getCalculationMethod();
                $all_cities = City::all();
                $all_areas = Area::all();
                $parameters = [
                    'all_cities' => $all_cities,
                    'all_areas' => $all_areas,
                    'calculation_method' => $calculation_method

                ];
                $viewContent = view($calculationMethod, $parameters)->render();
            }
        }



        return response()->json(['group' => $group, 'viewContent' => $viewContent]);
    }

    public function updateClientsGroup(GroupRequest $request, $id)
    {

        abort_unless(auth()->user()->hasPermissionTo('control_clients_groups'), 403, 'You do not have permission to view this page.');

        $group = ClientsGroup::findOrFail($id);
        $group->update([
            'name' => $request['group_name'],
            'calculation_method' => $request['calculation_method'],
            'default_delivery_fee' => $request['default_delivery_fee'],
            'collection_amount' => $request['collection_amount'],
            'service_type' => $request['service_type'],
        ]);
        $calculation_method = $group->calculationMethod;
        if ($calculation_method) {
            $calculation_method->delete();
        }

        $calculationMethodData = $request->except(['_token', 'group_name', 'client_group_id', 'calculation_method', 'default_delivery_fee', 'collection_amount', 'service_type']);
        // dd($calculationMethodData);
        GroupCalculationMethod::updateOrCreate(
            [
                'group_id' => $group->id,
                'data' => ($calculationMethodData)
            ]
        );
    }

    public function deleteClientsGroup($id)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_clients_groups'), 403, 'You do not have permission to view this page.');

        $group = ClientsGroup::findOrFail($id);
        $group->delete();
        return response()->json('group deleted successully');
    }



    public function zoneList(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_areas_zones'), 403, 'You do not have permission to view this page.');



        $totalData = Zone::count();
        $totalFiltered = $totalData;


        if (empty($request->input('search'))) {
            $zones = Zone::orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            $search = $request->input('search');

            $zones = Zone::where('name', 'LIKE', "%{$search}%")

                ->orderBy('created_at', 'desc')
                ->paginate(10);

            $totalFiltered = Zone::where('name', 'LIKE', "%{$search}%")

                ->count();
        }

        $data = [];
        if (!empty($zones)) {
            foreach ($zones as $zone) {
                $nestedData['id'] = $zone->id;
                $nestedData['name'] = $zone->name;
                $data[] = $nestedData;
            }
        }



        return response()->json(['zones' => $data, 'zones_count' => $totalFiltered]);
    }

    public function saveZone(ZoneRequest $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_areas_zones'), 403, 'You do not have permission to view this page.');

        try {
            if ($request->zone_id) {
                $this->updateZone($request, $request->zone_id);
            } else {
                $zone = Zone::create([
                    'name' => $request['zone_name'],
                ]);
                if ($request->has('city') || $request->has('area')) {
                    $cities = $request->input('city');
                    $areas = $request->input('area');
                    for ($i = 0; $i < count($cities); $i++) {
                        if (isset($areas[$i])) {
                            ZoneDetail::create([
                                'zone_id' => $zone->id,
                                'city_id' => $cities[$i],
                                'area_id' => $areas[$i],
                            ]);
                        } else {

                            ZoneDetail::create([
                                'zone_id' => $zone->id,
                                'city_id' => $cities[$i],
                                'area_id' => NULL,
                            ]);
                        }
                    }
                }
            }

            return response()->json(['message' => 'Zone saved successfully']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to save zone'], 500);
        }
    }

    public function editZone($id = NULL)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_areas_zones'), 403, 'You do not have permission to view this page.');

        $zone = Zone::findOrFail($id);
        $locations = $zone->details()->get();
        return response()->json(['zone' => $zone, 'locations' => $locations]);
    }

    public function updateZone(ZoneRequest $request, $id)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_areas_zones'), 403, 'You do not have permission to view this page.');

        $zone = Zone::findOrFail($id);
        $zone->update([
            'name' => $request->input('zone_name'),
        ]);
        // Handle zone details
        if ($request->has('city')) {
            $cities = $request->input('city');
            $areas = $request->input('area');
            $locations = ZoneDetail::where('zone_id', $zone->id)->get();
            foreach ($locations as $location) {
                $location->delete();
            }
            for ($i = 0; $i < count($cities); $i++) {
                $zoneDetailData = [
                    'zone_id' => $zone->id,
                    'city_id' => $cities[$i],
                    'area_id' => isset($areas[$i]) ? $areas[$i] : NULL,
                ];
                ZoneDetail::create($zoneDetailData);
            }
        }
    }

    public function deleteZone($id)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_areas_zones'), 403, 'You do not have permission to view this page.');

        $zone = Zone::findOrFail($id);
        $zone->delete();
        return response()->json('zone deleted successfully');
    }




    public function clientList(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');

        $user = auth()->user();
        $countryId = $user->country_id;

        $query = Client::query();

        if ($countryId) {
            $query->whereHas('client.city', function ($q) use ($countryId) {
                $q->where('country_id', $countryId);
            });
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%")
                    ->orWhereHas('client', function ($query) use ($search) {
                        $query->where('account_number', 'LIKE', "%{$search}%");
                    });
            });
        }

        $totalData = Client::count();
        $totalFiltered = $query->count();
        $clients = $query->orderBy('created_at', 'desc')->paginate(10);

        $data = [];

        foreach ($clients as $client) {
            $nestedData['id'] = $client->id;
            $nestedData['full_name'] = $client->full_name;
            $nestedData['total_balance'] = $client->wallet?->balance ?? 0;
            $nestedData['total_orders'] = $client->orders()?->count();
            $nestedData['total_branches'] = $client->branches()?->count();
            $nestedData['country'] = $client->client?->country?->name;
            $nestedData['currency'] = $client->client?->currency?->getLabel();
            $nestedData['client_parial_pay'] = $client->client?->partial_pay;
            $nestedData['client_defualt_preperation_time'] = $client->client?->default_prepartion_time;
            $nestedData['client_min_preperation_time'] = $client->client?->min_prepartion_time;
            $nestedData['client_client_group'] = $client->client?->clienGroup?->name;
            $nestedData['client_operator_group'] = $client->client?->driverGroup?->name;
            $nestedData['city'] = $client->client?->city?->name ?? '-';
            $nestedData['shop_profile'] = $client->image;
            $nestedData['integration_token'] = $client->integration_token;
            $nestedData['account_number'] = $client?->client?->account_number;
            $nestedData['is_active'] = $client->is_active;
            $nestedData['price_order'] = $client->client?->price_order;
            $data[] = $nestedData;
        }

        return response()->json([
            'clients' => $data,
            'clients_count' => $totalFiltered
        ]);
    }

    public function GenerateToken()
    {

        do {
            $token = Str::random(80);
        } while (\App\Models\User::where('integration_token', $token)->exists());

        return $token;
    }
    public function saveClient(ClientRequest $request)
    {
        // dd($request->all());
        // try
        // {
        abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');


        // try {
        if ($request->id) {
            $this->updateClient($request, $request->id);
        } else {


            $client = Client::create([
                'first_name' => $request->name,
                'phone' => $request->phone,
                'email'     => $request->email,
                'password' => Hash::make($request->password),
                'integration_token' => $this->GenerateToken(),
                'user_role' => 2,
                'is_active' => 1,

            ]);
            $token = $client->createToken('auth-token')->plainTextToken;
            $client->firebase_token = $token;
            $client->save();
            // dd($token);
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
        }

        // activity()->log('🔧 Basic log test: saveClient() triggered');



        return response()->json(['message' => 'Client saved successfully']);
        // } catch (Exception $e) {
        //     return response()->json(['error' => 'Failed to save client'], 500);
        // }


        // }
        // catch (Exception $e)
        // {
        //     return response()->json(['error' => 'Failed to save client'], 500);
        // }
    }

    public function editClient($id = NULL)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');


        $client = Client::findOrFail($id);
        $client_detail = $client->client;
        $profile_url = $client->image;
        return response()->json(['client' => $client, 'client_detail' => $client_detail, 'profile_url' => $profile_url]);
    }

    public function updateClient(ClientRequest $request, $id)
    {
        // dd($request->all());
        // try {
        abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');

        $client = Client::findOrFail($id);
        // dd($client?->client?->auto_dispatch);
        $client->update([
            'first_name' => $request->name,
            'phone' => $request->phone,
            'email'     => $request->email,
            'user_role' => 2,
        ]);


        if (!($client->integration_token)) {
            $client->integration_token = $client->firebase_token;
            $client->save();
        }

        if ($client->tokens()->count() === 0) {

            $token = $client->createToken('auth_token')->plainTextToken;
            $client->firebase_token = $token;
            $client->save();
        }

        if (! $client->wallet) {
            Wallet::create([
                'operator_id' => $client->id,
                'currency' => 'SAR'
            ]);
        }
        if ($request->filled('password')) {
            $client->update(['password' => Hash::make($request->password)]);
        }
        if ($request['profile_photo']) {
            // dd(99);
            // $filePath = $request->file('profile_photo')->store('images', 'public');


            // $client->image = $filePath;
            // dd()
            $client->image =  $this->upload_files($request->file('profile_photo'), 'images/' . $client->id);
            $client->save();
            // dd($client->image);
        }
        if ($client->client) {
            $client->client->update([
                'user_id' => $client->id,
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
                'account_number' => @$request->account_number

            ]);
        } else {
            ClientDetail::create([
                'user_id' => $client->id,
                'country_id' => $request->country_id,
                'city_id' => $request->city_id,
                'currency' => $request->currency,
                'auto_dispatch' => $request->auto_dispatch ?? 0,
                'default_prepartion_time' => $request->default_prepartion_time,
                'min_prepartion_time' => $request->min_prepartion_time,
                'partial_pay' => $request->partial_pay,
                'note' => $request->note,
                'client_group_id' => $request->client_group_id,
                'driver_group_id' => $request->driver_group_id,
                'price_order' => $request->price_order,
                'account_number' => @$request->account_number

            ]);
        }

        return redirect()->route('clients');
        // } catch (Exception $e) {
        //    return response()->json(['error' => 'Failed to update client'], 500);
        // }
    }



    public function deleteClient($id)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');

        $client = Client::findOrFail($id);
        $client->delete();
        return response()->json('client deleted successfully');
    }

    public function saveClientBranch(ClientBranchesRequest $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');

        $validatedData = $request->validated();

        $businessHours = $request->input('business_hours', []);


        if ($request->has('branch_id') && !empty($request->input('branch_id'))) {

            $branch = ClientBranches::findOrFail($request->input('branch_id'));

            $user_branch = User::where('branch_id', $branch->id)->first();

            if (!$user_branch) {
                $user_branch =  User::create([
                    'first_name' => $validatedData['branch_name'],
                    'phone' => $validatedData['branch_phone'],
                    'email' => $validatedData['branch_email'],
                    'password' => $request->branch_password ? Hash::make($request->branch_password) : Hash::make(123456),
                    'client_id' => $validatedData['branch_client_id'],
                    'branch_id' => $branch->id,
                    'user_role' => UserRole::BRANCH,

                ]);
            }

            if ($user_branch) {
                $user_branch->email =  $validatedData['branch_email'];
                if ($request->filled('branch_password')) {
                    $user_branch->password =  Hash::make($request->branch_password);
                }
                $user_branch->save();
            }
            // $user_branch->syncRoles([6]);

            $branch->update([
                'client_id' => $validatedData['branch_client_id'],
                'name' => $validatedData['branch_name'],
                'phone' => $validatedData['branch_phone'],
                'pickup_id' => $validatedData['pickup_id'],
                'custom_id' => @$validatedData['custom_id'],
                'client_group' => $validatedData['client_group_id'] ?? NULL,
                'driver_group' => $validatedData['driver_group_id'] ?? NULL,
                'lat' => $validatedData['lat'] ?? NULL,
                'lng' => $validatedData['lng'] ?? NULL,
                'country' => $validatedData['country'] ?? NULL,
                'city_id' => $validatedData['city_id'] ?? NULL,
                'area_id' => $validatedData['area_id'] ?? NULL,
                'street' => $validatedData['street'],
                'landmark' => $validatedData['landmark'] ?? NULL,
                'building' => $validatedData['building'] ?? NULL,
                'floor' => $validatedData['floor'] ?? NULL,
                'apartment' => $validatedData['apartment'] ?? NULL,
                'description' => $validatedData['description'] ?? NULL,
                'business_hours' => $businessHours,
            ]);
            $user_branch->syncRoles([6]);
        } else {

            $branch = ClientBranches::create([
                'client_id' => $validatedData['branch_client_id'],
                'name' => $validatedData['branch_name'],
                'phone' => $validatedData['branch_phone'],
                'client_group' => $validatedData['client_group_id'] ?? NULL,
                'driver_group' => $validatedData['driver_group_id'] ?? NULL,
                'lat' => $validatedData['lat'] ?? NULL,
                'lng' => $validatedData['lng'] ?? NULL,
                'pickup_id' => $validatedData['pickup_id'],
                'custom_id' => @$validatedData['custom_id'],
                'country' => $validatedData['country'] ?? NULL,
                'city_id' => $validatedData['city_id'] ?? NULL,
                'area_id' => $validatedData['area_id'] ?? NULL,
                'street' => $validatedData['street'],
                'landmark' => $validatedData['landmark'] ?? NULL,
                'building' => $validatedData['building'] ?? NULL,
                'floor' => $validatedData['floor'] ?? NULL,
                'apartment' => $validatedData['apartment'] ?? NULL,
                'description' => $validatedData['description'] ?? NULL,
                'business_hours' => $businessHours,
            ]);


            $user_branch =  User::create([
                'first_name' => $validatedData['branch_name'],
                'phone' => $validatedData['branch_phone'],
                'email' => $validatedData['branch_email'],
                'password' => Hash::make($request->branch_password),
                'client_id' => $validatedData['branch_client_id'],
                'branch_id' => $branch->id,
                'user_role' => UserRole::BRANCH,

            ]);

            $user_branch->syncRoles([6]);
        }
        return response()->json(['message' => 'Branch saved successfully']);
        // } catch (Exception $e) {
        //     return response()->json(['error' => 'Failed to save branch'], 500);
        // }
    }



    public function editClientBrnch(Request $request)
    {
        $branch = ClientBranches::findOrFail($request->id);
        $user = User::where('branch_id', $branch->id)->first();
        return response()->json(['branch' => $branch, 'user' => $user]);
    }




    public function getOrders(Request $request)
    {
        // dd($request->all());
        abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');

        $columns = ['id', 'created_at', 'branch', 'customer_name', 'customer_area', 'status'];
        $totalData = Order::where('ingr_shop_id', $request->id)->count();
        // dd($totalData);
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $query =  Order::offset($start)->where('ingr_shop_id', $request->id);
        if (empty($request->input('search.value'))) {
            $branches = $query->limit($limit)->orderBy($order, $dir)->get();
        } else {
            $search = $request->input('search.value');
            $branches = $query->where('id', 'LIKE', "%{$search}%")->orWhere('customer_name', 'LIKE', "%{$search}%")->offset($start)->limit($limit)->orderBy($order, $dir)->get();
            $totalFiltered = Order::where('id', 'LIKE', "%{$search}%")->orWhere('customer_name', 'LIKE', "%{$search}%")->where('ingr_shop_id', $request->id)->count();
        }
        $data = [];
        if (!empty($branches)) {

            foreach ($branches as $branch) {
                $nestedData['id'] = $branch->id;
                $nestedData['created_at'] = $branch->created_at->format('Y-m-d h:i a');
                $nestedData['branch'] = $branch->branch?->name;
                $nestedData['customer_name'] = $branch->customer_name;
                $nestedData['customer_area'] = $branch->city;
                $nestedData['status'] = $branch->status->getLabel();
                $nestedData['model'] = $branch;
                $data[] = $nestedData;
            }
        }
        $json_data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        ];
        return response()->json($json_data);
    }




    public function getBranches(Request $request)
    {
        // dd($request->all());PtempP_
        abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');

        $columns = ['id', 'name', 'phone', 'created_at'];
        $totalData = ClientBranches::where('client_id', $request->id)->count();
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        // $order = $columns[$request->input('order.0.column')];
        // $dir = $request->input('order.0.dir');
        $query = ClientBranches::offset($start)->where('client_id', $request->id);
        if (empty($request->input('search.value'))) {
            $branches = $query->limit($limit)->orderBy('created_at', 'desc')->get();
        } else {
            $search = $request->input('search.value');
            $branches = $query->where('name', 'LIKE', "%{$search}%")->orWhere('phone', 'LIKE', "%{$search}%")->offset($start)->limit($limit)->orderBy('created_at', 'desc')->get();
            $totalFiltered = ClientBranches::where('name', 'LIKE', "%{$search}%")->orWhere('phone', 'LIKE', "%{$search}%")->where('client_id', $request->id)->count();
        }
        $data = [];
        if (!empty($branches)) {
            foreach ($branches as $branch) {
                // dd($branch);
                $nestedData['id'] = $branch->id;
                $nestedData['name'] = $branch->name;
                $nestedData['city'] = $branch->city?->name;
                $nestedData['phone'] = $branch->phone;
                $nestedData['created_at'] = $branch->created_at->format('Y-m-d');
                $nestedData['model'] = $branch->toArray();
                $nestedData['user_branch'] = User::where('branch_id', $branch->id)->first();
                $data[] = $nestedData;
            }
        }
        $json_data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        ];
        // dd($json_data);
        return response()->json($json_data);
    }


    public function getBranch($branch_id)
    {
        // abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');

        $branch = ClientBranches::findOrFail($branch_id);

        return response()->json($branch);
    }
    public function getBranchOfMaster()
    {
        // abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');

        $branch = ClientBranches::get();

        return response()->json($branch);
    }

    public function distanceMatrix(MapDistanceCalculator $mapDistanceCalculator,  Request $request)
    {

        return $mapDistanceCalculator->distanceMatrix($request);
    }

    public function changeClientBranchStatus(Request $request)
    {
        // dd($request->all());
        abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');

        $branch = ClientBranches::findOrFail($request->id);
        $branch->is_active = $request->status;
        $branch->save();
    }

    public function changeClientBranchAutoDispatch(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');

        $branch = ClientBranches::findOrFail($request->id);
        $branch->auto_dispatch = $request->auto_dispatch;
        $branch->save();
    }

    public function getUsers(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');

        $columns = ['id', 'name'];
        $client = Client::findOrFail($request->id);
        $excludedUserIds = $client->users->pluck('user_id')->toArray();
        $all_users = ClientUser::whereNotIn('id', $excludedUserIds)->get();
        $totalData = ClientUsers::where('client_id', $request->id)->count();
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')] ?? 'id';
        $dir = $request->input('order.0.dir') ?? 'asc';
        $query = ClientUsers::where('client_id', $request->id);
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $query = $query->whereHas('user', function ($q) use ($search) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
            });
            $totalFiltered = $query->count();
        }
        $users = $query->offset($start)->limit($limit)->orderBy('created_at', 'desc')->get();
        $data = [];
        // dd($users);
        if ($users) {
            foreach ($users as $user) {
                // dd($user);
                $nestedData['id'] = $user->user?->id;
                $nestedData['name'] = $user->user?->full_name;
                $nestedData['model'] = $user;
                $nestedData['email'] = $user->user?->email;
                $nestedData['user'] = $user->user?->user;
                $nestedData['last_role'] =  $user->user?->roles->last()?->name ?? null;
                $nestedData['profile_url'] = $user->user?->image ?? NULL;
                $data[] = $nestedData;
            }
        }
        $users = [];
        if ($all_users) {
            foreach ($all_users as $user) {
                $nestedData['id'] = $user->id;
                $nestedData['name'] = $user->full_name;
                $users[] = $nestedData;
            }
        }
        $json_data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
            'all_users' => $users
        ];
        return response()->json($json_data);
    }

    public function saveClientUser(UserRequest $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');

        // dd($request->all());
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
            $user->save();
            // dd($user->user);

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

    public function changeClientUserStatus(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');

        // dd($request->all());
        $user = ClientUsers::findOrFail($request->id);
        $user->status = $request->status;
        $user->save();
    }


    public function getCalculationMethod(Request $request)
    {
        $feedType = $request->input('feed_type');

        if ($feedType) {
            $deliveryFeed = DeleveryFeed::tryFrom($feedType);
            if ($deliveryFeed) {
                $calculationMethod = $deliveryFeed->getCalculationMethod();
                $all_cities = City::all();
                $all_areas = Area::all();
                $parameters = [
                    'all_cities' => $all_cities,
                    'all_areas' => $all_areas,

                ];
                $viewContent = view($calculationMethod, $parameters)->render();

                return response()->json(['viewContent' => $viewContent]);
            }
        }

        return response()->json(['error' => 'Invalid feed type'], 400);
    }


    public function chargeWallet(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_clients_wallet_option'), 403, 'You do not have permission to view this page.');

        // dd($request->all());
        $validatedData = $request->validate([
            'amount' => 'required|integer|min:1',
        ]);

        $client = Client::findOrFail($request->client_id);
        if (!$client->wallet) {
            Wallet::create([
                'operator_id' => $client->id,
                'currency' => 'SAR'
            ]);
        }


        $wallet = Wallet::where('operator_id', $request->client_id)->first();
        $wallet->balance += $validatedData['amount'];
        $wallet->save();

        return response()->json(['success' => true, 'amount' => $wallet->balance]);
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
}
