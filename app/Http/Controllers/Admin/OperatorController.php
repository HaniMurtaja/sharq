<?php

namespace App\Http\Controllers\Admin;

use App\Enum\DriverStatus;
use App\Enum\UserRole;
use App\Enum\VerificationStatuses;
use App\Http\Resources\Api\OperatorResource;
use App\Repositories\FirebaseRepository;
use App\Traits\FileHandler;
use Exception;
use Carbon\Carbon;
use App\Models\City;
use App\Models\Group;
use App\Models\Shift;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Nnjeim\World\World;
use App\Models\Operator;
use Illuminate\Http\Request;
use App\Models\GroupCondition;
use App\Models\OperatorDetail;
use App\Http\Requests\GroupRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ShiftRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\OperatorRequest;
use Illuminate\Support\Facades\Redirect;
use App\Models\DriverLocationLog;
use App\Models\DriverVehicle;
use App\Models\OperatorCity;
use App\Models\OperatorStatus;
use App\Models\Vehicle;
use App\Models\Wallet;

class OperatorController extends Controller
{
    use FileHandler;
    public function __construct(FirebaseRepository $firebaseRepository)
    {
        $this->firebaseRepository = $firebaseRepository;
    }

    public function shiftsList(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_drivers_shifts'), 403, 'You do not have permission to view this page.');

        $columns = ['id', 'name', 'from', 'to'];

        $totalData = Shift::count();
        $totalFiltered = $totalData;

        $limit = $request->input('length', 10); // Default limit
        $start = $request->input('start', 0);   // Default start
        $orderColumn = $request->input('order.0.column', 0); // Default order column
        $orderDir = $request->input('order.0.dir', 'asc');   // Default order direction

        $order = $columns[$orderColumn] ?? $columns[0];

        if (empty($request->input('search.value'))) {
            $shifts = Shift::offset($start)
                ->limit($limit)
                ->orderBy($order, $orderDir)
                ->get();
        } else {
            $search = $request->input('search.value');

            $shifts = Shift::where('name', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $orderDir)
                ->get();

            $totalFiltered = Shift::where('name', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%")
                ->count();
        }

        $data = [];
        if (!empty($shifts)) {
            foreach ($shifts as $shift) {
                $nestedData['id'] = $shift->id;
                $nestedData['name'] = $shift->name;
                $nestedData['from'] = $shift->from . $shift->shift_from_type;
                $nestedData['to'] = $shift->to . $shift->shift_to_type;
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

    public function updateShift($id = null)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_drivers_shifts'), 403, 'You do not have permission to view this page.');

        $shifts = Shift::paginate(10);
        $shift = Shift::findOrFail($id);
        return response()->json($shift);
    }

    public function editShift(ShiftRequest $request, $id)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_drivers_shifts'), 403, 'You do not have permission to view this page.');

        $shift = Shift::findOrFail($id);
        $shift->update([
            'name' => $request['shift_name'],
            'from' => $request['shift_from'],
            'to' => $request['shift_to'],
            'shift_from_type' => $request['shift_from_type'],
            'shift_to_type' => $request['shift_to_type'],

        ]);
        return redirect()->route('operators');
    }


    public function saveShift(ShiftRequest $request, $id = null)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_drivers_shifts'), 403, 'You do not have permission to view this page.');
        try {
            if ($request['shift_id']) {
                $shift = Shift::findOrFail($request['shift_id']);
                $shift->update([
                    'name' => $request['shift_name'],
                    'from' => $request['shift_from'],
                    'to' => $request['shift_to'],
                    'shift_from_type' => $request['shift_from_type'],
                    'shift_to_type' => $request['shift_to_type'],

                ]);
            } else {
                Shift::create([
                    'name' => $request['shift_name'],
                    'from' => $request['shift_from'],
                    'to' => $request['shift_to'],
                    'shift_from_type' => $request['shift_from_type'],
                    'shift_to_type' => $request['shift_to_type'],
                ]);
            }

            return response()->json(['message' => 'Shift saved successfully']);
        } catch (Exception $e) {

            return response()->json(['error' => 'Failed to save shift'], 500);
        }
    }

    public function deleteShift($id)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_drivers_shifts'), 403, 'You do not have permission to view this page.');

        $shift = Shift::findOrFail($id);
        $shift->delete();
        return response()->json(['message' => 'Shift deleted successfully']);
    }

    public function groupsList(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_drivers_groups'), 403, 'You do not have permission to view this page.');
        $columns = ['id', 'name', 'min_free'];

        $totalData = Group::count();
        $totalFiltered = $totalData;

        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'asc');

        $order = $columns[$orderColumn] ?? $columns[0];

        if (empty($request->input('search.value'))) {
            $shifts = Group::offset($start)
                ->limit($limit)
                ->orderBy($order, $orderDir)
                ->get();
        } else {
            $search = $request->input('search.value');

            $shifts = Group::where('name', 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $orderDir)
                ->get();

            $totalFiltered = Group::where('name', 'LIKE', "%{$search}%")
                ->count();
        }

        $data = [];
        if (!empty($shifts)) {
            foreach ($shifts as $shift) {
                $nestedData['id'] = $shift->id;
                $nestedData['name'] = $shift->name;
                $nestedData['min_free'] = $shift->min_feed_order;
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


    public function saveGroup(GroupRequest $request)
    {
        // // dd($request->all());
        // try {
        abort_unless(auth()->user()->hasPermissionTo('control_drivers_groups'), 403, 'You do not have permission to view this page.');

        if ($request->group_id) {
            $this->editGroup($request, $request->group_id);
        } else {
            $group = Group::create([
                'name' => $request['group_name'],
                'min_feed_order' => $request['min_feed_order'],
                'type_feed_order' => $request['type_feed_order'],
                'additional_feed_order' => $request['additional_type_feed'],
            ]);

            // Save main group conditions
            if ($request->has('from') && $request->has('to') && $request->has('percentage') && $request->has('type')) {
                $fromArray = $request->input('from');
                $toArray = $request->input('to');
                $percentageArray = $request->input('percentage');
                $typeArray = $request->input('type');

                $count = count($fromArray);

                for ($i = 0; $i < $count; $i++) {
                    $groupCondition = new GroupCondition();

                    $groupCondition->data = [
                        'from' => $fromArray[$i] ?? '',
                        'to' => $toArray[$i] ?? '',
                        'percentage' => $percentageArray[$i] ?? '',
                        'type' => $typeArray[$i] ?? '',
                    ];
                    $groupCondition->group_id = $group->id;
                    $groupCondition->feed_type = 'main';

                    $groupCondition->save();
                }
            }

            // Save additional group conditions
            if ($request->has('additional_from') && $request->has('additional_to') && $request->has('additional_percentage') && $request->has('additional_type')) {
                $additionalFromArray = $request->input('additional_from');
                $additionalToArray = $request->input('additional_to');
                $additionalPercentageArray = $request->input('additional_percentage');
                $additionalTypeArray = $request->input('additional_type');

                $count = count($additionalFromArray);

                for ($i = 0; $i < $count; $i++) {
                    $groupCondition = new GroupCondition();

                    $groupCondition->data = [
                        'from' => $additionalFromArray[$i] ?? '',
                        'to' => $additionalToArray[$i] ?? '',
                        'percentage' => $additionalPercentageArray[$i] ?? '',
                        'type' => $additionalTypeArray[$i] ?? '',
                    ];
                    $groupCondition->group_id = $group->id;
                    $groupCondition->feed_type = 'additional';

                    $groupCondition->save();
                }
            }

            return response()->json(['message' => 'Group saved successfully']);
        }


        // } catch (Exception $e) {
        //    return response()->json(['error' => 'Failed to save group'], 500);
        // }
    }

    public function updateGroup($id = null)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_drivers_groups'), 403, 'You do not have permission to view this page.');

        $groups = Group::paginate(10);
        $group = Group::findOrFail($id);
        $main_conditions = $group->conditions()->where('feed_type', 'main')->get();
        $additional_condditions = $group->conditions()->where('feed_type', 'additional')->get();
        return response()->json(['group' => $group, 'main_conditions' => $main_conditions, 'additional_condditions' => $additional_condditions]);
    }

    public function editGroup(GroupRequest $request, $id)
    {
        // dd($request->condition_id);
        // try {
        abort_unless(auth()->user()->hasPermissionTo('control_drivers_groups'), 403, 'You do not have permission to view this page.');

        $group = Group::findOrFail($id);

        $group->update([
            'name' => $request['group_name'],
            'min_feed_order' => $request['min_feed_order'],
            'type_feed_order' => $request['type_feed_order'],
            'additional_feed_order' => $request['additional_type_feed'],
        ]);


        if ($request->has('from') && $request->has('to') && $request->has('percentage') && $request->has('type')) {
            $conditions = $group->conditions()->where('feed_type', 'main')->get();
            if (!($conditions->isEmpty())) {
                foreach ($conditions as $condition) {
                    $condition->delete();
                }
            }
            $fromArray = $request->input('from');
            $toArray = $request->input('to');
            $percentageArray = $request->input('percentage');
            $typeArray = $request->input('type');

            $count = count($fromArray);

            for ($i = 0; $i < $count; $i++) {
                $groupCondition = new GroupCondition();

                $groupCondition->data = [
                    'from' => $fromArray[$i] ?? '',
                    'to' => $toArray[$i] ?? '',
                    'percentage' => $percentageArray[$i] ?? '',
                    'type' => $typeArray[$i] ?? '',
                ];
                $groupCondition->group_id = $group->id;
                $groupCondition->feed_type = 'main';

                $groupCondition->save();
            }
        }


        if ($request->has('additional_from') && $request->has('additional_to') && $request->has('additional_percentage') && $request->has('additional_type')) {
            $conditions = $group->conditions()->where('feed_type', 'additional')->get();
            if (!($conditions->isEmpty())) {
                foreach ($conditions as $condition) {
                    $condition->delete();
                }
            }

            $additionalFromArray = $request->input('additional_from');
            $additionalToArray = $request->input('additional_to');
            $additionalPercentageArray = $request->input('additional_percentage');
            $additionalTypeArray = $request->input('additional_type');

            $count = count($additionalFromArray);

            for ($i = 0; $i < $count; $i++) {
                $groupCondition = new GroupCondition();

                $groupCondition->data = [
                    'from' => $additionalFromArray[$i] ?? '',
                    'to' => $additionalToArray[$i] ?? '',
                    'percentage' => $additionalPercentageArray[$i] ?? '',
                    'type' => $additionalTypeArray[$i] ?? '',
                ];
                $groupCondition->group_id = $group->id;
                $groupCondition->feed_type = 'additional';

                $groupCondition->save();
            }
        }

        return response()->json('group saved successfully');
        // } catch (Exception $e) {
        //    return response()->json(['error' => 'Failed to update group'], 500);
        // }
    }

    public function deleteGroup($id)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_drivers_groups'), 403, 'You do not have permission to view this page.');

        $group = Group::findOrFail($id);
        $group->delete();
        return response()->json(['error' => 'group deleted'], 200);
    }

    public function operatorsList(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_drivers'), 403, 'You do not have permission to view this page.');

        $columns = ['id', 'name', 'phone', 'total_orders', 'group_name', 'status'];

        $user = auth()->user();
        $isAdminWithCountry = $user->user_role === UserRole::ADMIN && $user->country_id;


        $operators = Operator::where('id', 4595)->with('cities')->get();





        $query = Operator::query();

        if ($isAdminWithCountry) {
            $countryId = $user->country_id;
            // dd($countryId);
            $query->whereHas('cities.city', function ($q) use ($countryId) {
                $q->where('country_id', $countryId);
            });
        }

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                    ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        $totalData = Operator::count();
        $totalFiltered = $query->count();

        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'asc');
        $order = $columns[$orderColumn] ?? $columns[0];

        $operators = $query
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $orderDir)
            ->get();

        $data = [];
        foreach ($operators as $operator) {
            $total_orders = 0;
            $nestedData['id'] = $operator->id;
            $nestedData['name'] = $operator->full_name;
            $nestedData['phone'] = $operator->phone;
            $nestedData['national_no'] = $operator->operator?->social_id_no ?? '---';
            $nestedData['group_name'] = $operator->operator?->group?->name;
            $nestedData['total_orders'] = $total_orders;
            $nestedData['verification_status'] = VerificationStatuses::tryFrom($operator->operator?->is_verified)?->getLabel();
            $nestedData['status'] = DriverStatus::tryFrom($operator->operator?->status)?->value;
            $data[] = $nestedData;
        }

        $json_data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        ];

        return response()->json($json_data);
    }


    public function saveOperator(OperatorRequest $request)
    {
        // dd($request->all());
        abort_unless(auth()->user()->hasPermissionTo('control_drivers'), 403, 'You do not have permission to view this page.');
        if ($request->operator_id) {
            $this->editOperator($request, $request->operator_id);
        } else {
            $operator = Operator::create([
                'first_name' => $request['first_name'],
                'last_name' => $request['last_name'],
                'phone' => $request['phone'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
                'user_role' => 3,
            ]);
            // dd(99999);
            $operator_detial = new OperatorDetail();
            //   dd(99);
            $operator_detial->operator_id = $operator->id;
            $operator_detial->status = 1;
            $operator_detial->birth_date = date('Y-m-d', strtotime($request['birth_date']));
            $operator_detial->emergency_contact_name = $request['emergency_contact_name'];
            $operator_detial->emergency_contact_phone = $request['emergency_contact_phone'];
            $operator_detial->social_id_no = $request['social_id_no'];
            $operator_detial->city_id = $request->city[0];
            $operator_detial->iban = $request['iban'];
            $operator_detial->group_id = $request['group_id'];
            $operator_detial->branch_group_id = $request['branch_group_id'];
            $operator_detial->shift_id = $request['shift_id'];
            $operator_detial->jop_type = $request['jop_type'];
            $operator_detial->days_off = json_encode($request['days_off']);

            $operator_detial->lat = isset($request['lat']) ? $request['lat'] : null;
            $operator_detial->lng = isset($request['lng']) ? $request['lng'] : null;
            $operator_detial->location =  \DB::raw("POINT(0, 0)");
            $operator_detial->order_value = isset($request['order_value']) ? $request['order_value'] : 0;



            if ($request->hasFile('profile_image')) {

                //                $filePath = $request->file('profile_image')->store('images', 'public');


                $operator->image = $this->upload_files($request->file('profile_image'), 'images/' . $operator->id);
                $operator->save();
            }

            $operator_detial->save();

            foreach ($request->city as $city_id) {
                DB::table('operator_cities')->insert([
                    'operator_id' => $operator_detial->operator_id,
                    'city_id' => $city_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if ($request['id_card_image']) {
                $operator->addMedia($request['id_card_image'])->toMediaCollection('card_images');
            }


            // if ($request['license_front_image']) {
            //     $operator->license_front_image = $this->upload_files($request->file('license_front_image'), 'images/' . $operator->id);
            //     // $operator->addMedia($request['license_front_image'])->toMediaCollection('license_front_image');
            // }
            // if ($request['license_back_image']) {
            //     $operator->license_back_image = $this->upload_files($request->file('license_back_image'), 'images/' . $operator->id);
            //     // $operator->addMedia($request['license_back_image'])->toMediaCollection('license_back_image');
            // }

            Wallet::create([
                'operator_id' => $operator->id,
                'currency' => 'SAR'
            ]);
            // add firebase object
            if ($request->input('car_type') && $request->input('car_type') === 'driver') {
                $this->saveVehicle($request, $operator->id);
            }

            if ($request->company_vehicle_id) {
                DriverVehicle::create([
                    'driver_id' => $operator->id,
                    'vehicle_id' => $request->company_vehicle_id
                ]);
            }


            $operatorResource = new OperatorResource($operator);
            $operatorData = $operatorResource->toArray(request());
            //try save firebase
            try {
                // Attempt to save to Firebase
                $this->firebaseRepository->save_driver($operator->id, $operatorData);
            } catch (\Exception $e) {
                // Handle the exception (log it, show a message, etc.)
                Log::info($e);
            }
        }


        return response()->json(['message' => 'Operator saved successfully']);
    }


    public function changeOperatorVerificationStatus(Request $request)
    {
        // dd($request->id);
        $operator = OperatorDetail::where('operator_id', $request->id)->first();

        if ($operator) {
            $operator->is_verified = $request->is_verified;
            $operator->save();
        }

        return response()->json([
            'verification_status' => VerificationStatuses::tryFrom($operator->is_verified)?->getLabel(),
            'verification_status_value' => VerificationStatuses::tryFrom($operator->is_verified)?->value,
        ]);
    }

    public function getVerificationData($id) {
        abort_unless(auth()->user()->hasPermissionTo('control_drivers'), 403, 'You do not have permission to view this page.');
        // dd(99);
        $operator = Operator::with([
            'operator' => function ($query) {
                $query->select(
                    'operator_id',  
                    'social_id_no',
                    'is_verified',
                    'id_card_image_front',
                    'id_card_image_back',
                    'license_front_image',
                    'license_back_image'
                );
            }
        ])->findOrFail($id);
       
      
      
        
        return response()->json([
            
            'operator' => $operator,
 
            'url_license_front_image' => $operator->operator?->license_front_image ,
            'url_license_back_image' => $operator->operator?->license_back_image ,
            
            'verification_status' => VerificationStatuses::tryFrom($operator->operator?->is_verified)?->getLabel(),
            'verification_status_value' => VerificationStatuses::tryFrom($operator->operator?->is_verified)?->value,

            'id_card_image_front' => $operator->operator?->id_card_image_front,
            'id_card_image_back' => $operator->operator?->id_card_image_back

        ]);
    }


    public function updateOperator($id)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_drivers'), 403, 'You do not have permission to view this page.');

        $operator = Operator::with([
            'operator' => function ($query) {
                $query->select(
                    'operator_id',
                    'birth_date',
                    'emergency_contact_name',
                    'emergency_contact_phone',
                    'social_id_no',
                    'city_id',
                    'iban',
                    'status',
                    'group_id',
                    'branch_group_id',
                    'shift_id',
                    'days_off',
                    'jop_type',
                    'lat',
                    'lng',
                    'order_value',
                    'is_verified',
                    'id_card_image_front',
                    'id_card_image_back',
                    'license_front_image',
                    'license_back_image'
                );
            }
        ])->findOrFail($id);
        // $operator_detail
        // dd($operator->driverOrders);
        $profile_url = $operator->image;
        // dd($profile_url);
        $url_card_image = $operator->getFirstMediaUrl('card_images');
      
        $daysOff = [];
        if ($operator->operator?->days_off) {
            $daysOff = json_decode($operator->operator->days_off);
        }

        $vehicle_type = '';
        $vehicle = DriverVehicle::where('driver_id', $id)->orderBy('created_at', 'desc')->first();
        if ($vehicle?->vehicle?->owner == 'company') {
            $vehicle_type = 'company';
        }

        if ($vehicle?->vehicle?->owner == 'driver') {
            $vehicle_type = 'driver';
        }
        // dd($vehicle_type);
        // dd([   'id_card_image_front' => $operator->operator?->id_card_image_front,
        //             'id_card_image_back' => $operator->operator?->id_card_image_back,
        //             'profile_url' => $profile_url,
        //     ]);
        $vehicle_image_url = $vehicle?->vehicle?->getFirstMediaUrl('vehicle_image');
        $id_card_image_url = $vehicle?->vehicle?->getFirstMediaUrl('id_card_image');
        // dd($operator->operator?->is_verified, VerificationStatuses::tryFrom($operator->operator?->is_verified)?->getLabel(),);
        //    dd($operator->cities->pluck('city_id')->toArray());


        // dd([  'url_license_front_image' => $operator->operator?->license_front_image ,
        //     'url_license_back_image' => $operator->operator?->license_back_image ,]);


        return response()->json([
            'operator' => $operator,
            'profile_url' => $profile_url,
            'city_ids' => $operator->cities->pluck('city_id')->toArray(),
            'url_card_image' => $url_card_image,
 
            'url_license_front_image' => $operator->operator?->license_front_image ,
            'url_license_back_image' => $operator->operator?->license_back_image ,
            'daysOff' => $daysOff,
            'vehicle_type' => $vehicle_type,
            'vehicle' =>  $vehicle?->vehicle,
            'vehicle_image_url' => $vehicle_image_url,
            'id_card_image_url' => $id_card_image_url,
            'verification_status' => VerificationStatuses::tryFrom($operator->operator?->is_verified)?->getLabel(),
            'verification_status_value' => VerificationStatuses::tryFrom($operator->operator?->is_verified)?->value,

            'id_card_image_front' => $operator->operator?->id_card_image_front,
            'id_card_image_back' => $operator->operator?->id_card_image_back

        ]);
    }

    public function  editOperator(OperatorRequest $request, $id)
    {
        // dd($request->all());
        abort_unless(auth()->user()->hasPermissionTo('control_drivers'), 403, 'You do not have permission to view this page.');

        $operator = Operator::findOrFail($id);
        // dd(4444);
        $operator->first_name = $request['first_name'];
        $operator->last_name = $request['last_name'];
        $operator->email = $request['email'];
        $operator->user_role = 3;
        $operator->phone = $request['phone'];
        if ($request['password'] != null) {
            $operator->password = Hash::make($request['password']);
        }

        if ($request->hasFile('profile_image')) {

            $filePath = $this->upload_files($request->file('profile_image'), 'images/' . $operator->id);;


            $operator->image = $filePath;
        }

        $operator->save();

        $existingCityIds = $operator->cities->pluck('city_id')->toArray();
        $newCityIds = $request->city;


        $toAdd = array_diff($newCityIds, $existingCityIds);
        foreach ($toAdd as $city_id) {
            OperatorCity::create([
                'operator_id' => $operator->id,
                'city_id'     => $city_id,
            ]);
        }


        $toDelete = array_diff($existingCityIds, $newCityIds);
        // dd($toDelete);
        OperatorCity::where('operator_id', $operator->id)
            ->whereIn('city_id', $toDelete)
            ->delete();



        $operator_detail = OperatorDetail::updateOrCreate(
            ['operator_id' => $id],
            [
                'birth_date' => isset($request['birth_date']) ? Carbon::parse($request['birth_date'])->toDateString() : null,
                'emergency_contact_name' => $request['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $request['emergency_contact_phone'] ?? null,
                'social_id_no' => $request['social_id_no'] ?? null,
                'city_id' => $request->city[0] ?? null,
                'iban' => $request['iban'] ?? null,
                'group_id' => $request['group_id'] ?? null,
                'branch_group_id' => $request['branch_group_id'] ?? null,
                'shift_id' => $request['shift_id'] ?? null,
                'jop_type' => $request['jop_type'] ?? null,
                'days_off' => isset($request['days_off']) ? json_encode($request['days_off']) : null,
                'lat' => $request['lat'] ?? null,
                'lng' => $request['lng'] ?? null,
                'order_value' => $request['order_value'] ?? \DB::raw('order_value'),
                'location' => DB::raw("POINT(0, 0)"),

            ]
        );

        // dd(99922);


        if ($request['id_card_image']) {
            $operator->clearMediaCollection('card_images');

            $operator->addMedia($request['id_card_image'])->toMediaCollection('card_images');
        }
        // if ($request['license_front_image']) {
        //     $operator->license_front_image = $this->upload_files($request->file('license_front_image'), 'images/' . $operator->id);
        //     // $operator->addMedia($request['license_front_image'])->toMediaCollection('license_front_image');
        // }
        // if ($request['license_back_image']) {
        //     $operator->license_back_image = $this->upload_files($request->file('license_back_image'), 'images/' . $operator->id);
        //     // $operator->addMedia($request['license_back_image'])->toMediaCollection('license_back_image');
        // }
        if (! $operator->wallet) {
            Wallet::create([
                'operator_id' => $operator->id,
                'currency' => 'SAR'
            ]);
        }

        if ($request->input('car_type') && $request->input('car_type') === 'driver') {
            $this->saveVehicle($request, $operator->id);
        }

        if ($request->company_vehicle_id) {
            DriverVehicle::create([
                'driver_id' => $operator->id,
                'vehicle_id' => $request->company_vehicle_id
            ]);
        }


        // add firebase object
        $operatorResource = new OperatorResource($operator);
        $operatorData = $operatorResource->toArray(request());
        //try save firebase
        try {
            // Attempt to save to Firebase
            $this->firebaseRepository->save_driver($operator->id, $operatorData);
        } catch (\Exception $e) {
            // Handle the exception (log it, show a message, etc.)
            Log::info($e);
        }


        // return redirect()->route('operators');
    }

    public function saveVehicle($request, $operator_id)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_drivers'), 403, 'You do not have permission to view this page.');

        $vehicle = Vehicle::updateOrCreate([
            'name' => $request->name,
            'type' =>  $request->type,
            'plate_number' =>  $request->plate_number,
            'vin_number' =>  $request->vin_number,
            'make' =>  $request->make,
            'model' =>  $request->model,
            'year' =>  $request->year,
            'color' =>  $request->color,
            'vehicle_milage' =>  $request->vehicle_milage,
            'last_service_milage' =>  $request->last_service_milage,
            'due_service_milage' =>  $request->due_service_milage,
            'service_milage_limit' =>  $request->service_milage_limit,
            'operator_id' =>  $operator_id,
            'owner' => 'driver'
        ]);

        // Check if the file exists before processing vehicle image
        if ($request->hasFile('vehicle_image')) {
            // Check if the vehicle already has media
            if ($vehicle->hasMedia('vehicle_image')) {
                $vehicle->clearMediaCollection('vehicle_image');
            }

            // Add the new media to the vehicle collection
            $vehicle->addMedia($request->file('vehicle_image'))->toMediaCollection('vehicle_image');
        }

        // Check if the file exists before processing id card image
        if ($request->hasFile('id_card_image_vehicle')) {
            // Check if the vehicle already has media
            if ($vehicle->hasMedia('id_card_image')) {
                $vehicle->clearMediaCollection('id_card_image');
            }

            // Add the new media to the id card collection
            $vehicle->addMedia($request->file('id_card_image_vehicle'))->toMediaCollection('id_card_image');
        }

        // Link the driver to the vehicle
        DriverVehicle::create([
            'driver_id' => $operator_id,
            'vehicle_id' => $vehicle->id
        ]);
    }


    public function deleteOperator($id)
    {
        // dd($id);
        abort_unless(auth()->user()->hasPermissionTo('control_drivers'), 403, 'You do not have permission to view this page.');

        $operator = Operator::findOrFail($id);
        $operator->delete();
        return response()->json('operator deleted successfully');
    }

    public function changeStatus(Request $request)
    {
        // dd($request->all());
        abort_unless(auth()->user()->hasPermissionTo('control_drivers'), 403, 'You do not have permission to view this page.');

        $operator = OperatorDetail::where('operator_id', $request->id)->first();
        if ($request->status == 1) {
            $operator->status = 1;
        } else {
            $operator->status = 4;
        }

        $operator->save();
        $user = Operator::findOrFail($request->id);

        //        OperatorStatus::create(['operator_id' => $request->id, 'status' => $operator->status]);
        $operatorResource = new OperatorResource($user);
        $operatorData = $operatorResource->toArray(request());
        //try save firebase
        try {
            // Attempt to save to Firebase
            $this->firebaseRepository->save_driver($request->id, $operatorData);
        } catch (\Exception $e) {
            // Handle the exception (log it, show a message, etc.)
            Log::info($e);
        }
    }

    public function getDriverLocationsLog(Request $request)
    {
        $driver = Operator::findOrFail($request->id);
        $logs =  DriverLocationLog::where('driver_id', $request->id)->whereDate('created_at', '<=', Carbon::today())->orderBy('created_at', 'desc')->take(50)->get()->map(function ($log) {
            $log->tracking_url = "https://www.google.com/maps?q={$log->lat},{$log->lng}";
            $log->created = Carbon::parse($log->created_at)->format('Y-m-d H:i:s');

            return $log;
        });
        return response()->json(['logs' => $logs, 'driver' => $driver]);
    }
}
