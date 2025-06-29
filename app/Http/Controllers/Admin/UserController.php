<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Validator;
use App\Models\Area;
use App\Models\City;


use App\Models\User;
use App\Models\Group;

use App\Enum\UserRole;
use App\Models\UserCitys;
use App\Models\ClientUser;
use App\Models\Permission;
use App\Models\UserDetail;
use App\Models\UserGroups;
use App\Traits\FileHandler;
use App\Exports\UsersExport;
use Illuminate\Http\Request;
use App\Http\Requests\AreaRequest;
use App\Http\Requests\CityRequest;
use App\Http\Requests\UserRequest;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\TemplateRequest;

class UserController  extends Controller
{
    use FileHandler;

    public function usertList(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_users'), 403, 'You do not have permission to view this page.');

        $columns = ['id', 'first_name', 'last_name', 'email'];

        $countryId = auth()->user()->country_id;

        $query = User::whereNotIn('id', [5606, 5798, 5799])
            ->whereIn('user_role', [UserRole::ADMIN, UserRole::DISPATCHER, UserRole::REPORTS]);

        // ✅ Apply country filter
        if ($countryId) {
            $query->where(function ($q) use ($countryId) {
                $q->whereHas('getUserCitys.city', function ($sub) use ($countryId) {
                    $sub->where('country_id', $countryId);
                })
                    ->orWhereHas('cities.city', function ($sub) use ($countryId) {
                        $sub->where('country_id', $countryId);
                    })
                    ->orWhereHas('country', function ($sub) use ($countryId) {
                        $sub->where('id', $countryId);
                    });
            });
        }

        $totalData = $query->count();
        $totalFiltered = $totalData;

        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'asc');

        $order = $columns[$orderColumn] ?? $columns[0];

        if (empty($request->input('search.value'))) {
            $users = $query->offset($start)
                ->limit($limit)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $search = $request->input('search.value');

            $searchQuery = clone $query;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%");
            });

            $totalFiltered = $searchQuery->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%");
            })->count();

            $users = $query->offset($start)
                ->limit($limit)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $data = [];
        foreach ($users as $user) {
            $nestedData['id'] = $user->id;
            $nestedData['first_name'] = $user->first_name;
            $nestedData['last_name'] = $user->last_name;
            $nestedData['email'] = $user->email;
            $data[] = $nestedData;
        }

        return response()->json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        ]);
    }


    public function editUser($id = null)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_users'), 403, 'You do not have permission to view this page.');

        $user = User::where('id', '!=', 5606)->findOrFail($id);
        //  dd($user->user);
        $group_ids = ($user->groups->pluck('group_id')->toArray());
        $city_ids =  (@$user->getUserCitys->pluck('city_id')->toArray());
        $profile_url = $user->getFirstMediaUrl('profile');
        $user_detail = $user->user;
        $last_role = $user->roles->last();
        $user_role = $user->user_role?->value;
        return response()->json([
            'user' => $user,
            'user_role' => $user_role,
            'user_detail' => $user_detail,
            'group_ids' => $group_ids,
            'profile_url' => $profile_url,
            'last_role' => $last_role ? $last_role->name : null,
            'city_ids' => $city_ids
        ]);
    }

    public function updateUser(UserRequest $request, $id)
    {

        abort_unless(auth()->user()->hasPermissionTo('control_users'), 403, 'You do not have permission to view this page.');
        $user = User::where('id', '!=', 5606)->findOrFail($id);

        $userDetails = UserDetail::where('user_id', $user->id)->first();


        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email'     => $request->email,
            'user_role' => $request->user_role,
            'country_id' => $request->country_id
        ]);

        $user->syncRoles([$request->role]);


        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        if ($request->groups) {
            $user_groups = $user->groups;
            if ($user_groups) {
                foreach ($user_groups as $group) {
                    $group->delete();
                }
            }
            foreach ($request->groups as $group) {
                UserGroups::create([
                    'user_id' => $user->id,
                    'group_id' => $group
                ]);
            }
        }
        if ($request->city_ids != null) {
            UserCitys::where('user_id', $user->id)->delete();
            foreach ($request->city_ids as $city_id) {
                UserCitys::create([
                    'user_id' => $user->id,
                    'city_id' => $city_id
                ]);
            }
        }
        if ($userDetails) {
            $userDetails->update([
                'locked' => $request->locked ?? 0,
                // 'group_id' => $request->group_id ?? null,
                'marketplace_access' => $request->marketplace_access ?? 0,
                'mac_address' => $request->mac_address ?? null,
                'sim_card' => $request->sim_card ?? null,
                'sn' => $request->sn ?? null,
                'request_per_second' => $request->request_per_second ?? null,
            ]);
        } else {
            $client_user = UserDetail::create([
                'user_id' => $user->id,
                'locked' => $request->locked ?? 0,

                'marketplace_access' =>  $request->marketplace_access ?? 0,
                'mac_address' =>  $request->mac_address ?? null,
                'sim_card' =>  $request->sim_card ?? null,
                'sn' =>  $request->sn ?? null,
                'request_per_second' =>  $request->request_per_second ?? null,

            ]);
        }





        if ($request->hasFile('profile_photo')) {

            $user->image = $this->upload_files($request->file('profile_photo'), 'client_images');;
            //            $user->addMedia($request->file('profile_photo'))->toMediaCollection('profile');
        }
    }



    public function saveUser(UserRequest $request)
    {
        // dd($request->all());
        abort_unless(auth()->user()->hasPermissionTo('control_users'), 403, 'You do not have permission to view this page.');
        // try {

        if ($request->user_id) {
            $this->updateUser($request, $request->user_id);
        } else {
            $user = ClientUser::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email'     => $request->email,
                'user_role' => $request->user_role,
                'password' => Hash::make($request->password),
                'country_id' => $request->country_id

            ]);
            $user->syncRoles([$request->role]);
            if ($request->groups) {
                foreach ($request->groups as $group) {
                    UserGroups::create([
                        'user_id' => $user->id,
                        'group_id' => $group
                    ]);
                }
            }
            if ($request->city_ids != null) {
                foreach ($request->city_ids as $city_id) {
                    UserCitys::create([
                        'user_id' => $user->id,
                        'city_id' => $city_id
                    ]);
                }
            }

            $client_user = UserDetail::create([
                'user_id' => $user->id,
                'locked' => $request->locked ?? 0,

                'marketplace_access' =>  $request->marketplace_access ?? 0,
                'mac_address' =>  $request->mac_address ?? null,
                'sim_card' =>  $request->sim_card ?? null,
                'sn' =>  $request->sn ?? null,
                'request_per_second' =>  $request->request_per_second ?? null,

            ]);
            if ($request['profile_photo']) {
                $user->addMedia($request['profile_photo'])->toMediaCollection('profile');
            }
        }



        return response()->json(['message' => 'User saved successfully']);
        // } catch (Exception $e) {

        //     return response()->json(['error' => 'Failed to save user'], 500);
        // }
    }




    public function deleteUser($id)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_users'), 403, 'You do not have permission to view this page.');

        $user = ClientUser::findOrFail($id);
        $user->delete();
        return response()->json('user deleted successfully');
    }



    public function saveTemplate(TemplateRequest $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_users_templates'), 403, 'You do not have permission to view this page.');
        if ($request->template_id) {
            $role = Role::findOrFail($request->template_id);

            $role->permissions()->detach();

            $role->update([
                'name' => $request->name
            ]);
        } else {
            $role = Role::create(['name' => $request->name]);
        }

        $permissions = $request->except(['_token', 'name', 'template_id']);
        $permissions = array_keys($permissions);

        $role->givePermissionTo($permissions);
    }


    public function templateList(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_users_templates'), 403, 'You do not have permission to view this page.');

        $columns = ['id', 'name'];

        $totalData = Role::count();
        $totalFiltered = $totalData;

        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'asc');

        $order = $columns[$orderColumn] ?? $columns[0];

        if (empty($request->input('search.value'))) {
            $roles = Role::offset($start)
                ->limit($limit)
                ->orderBy($order, $orderDir)
                ->get();
        } else {
            $search = $request->input('search.value');

            $roles = Role::where('name', 'LIKE', "%{$search}%")

                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $orderDir)
                ->get();

            $totalFiltered = ClientUser::where('first_name', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%")
                ->count();
        }

        $data = [];
        if (!empty($roles)) {
            foreach ($roles as $role) {
                $nestedData['id'] = $role->id;
                $nestedData['name'] = $role->name;
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

    public function editTemplate($id)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_users_templates'), 403, 'You do not have permission to view this page.');

        $role = Role::findOrFail($id);
        $all_permission = @$role->permissions()->pluck('name')->toarray();

        return response()->json(['role' => $role, 'permissions' => $all_permission]);
    }


    public function deleteTemplate($id)
    {
        abort_unless(auth()->user()->hasPermissionTo('control_users_templates'), 403, 'You do not have permission to view this page.');

        $role = Role::findOrFail($id);
        $role->delete();
        return response()->json('role deleted successfully');
    }



    public function exportUserTemplate(Request $request)
    {
        // dd(request()->all());
        $reportQuery = User::where('id', '!=', 5606)
            ->where('id', '!=', 5798)
            ->where('id', '!=', 5799)
            ->wherein('user_role', [UserRole::ADMIN, UserRole::DISPATCHER, UserRole::REPORTS]);

        $data = $reportQuery
            ->groupBy('users.id', 'users.first_name')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'user_name' => $user->full_name,

                    'email' => $user->email,
                    'role' => $user->user_role?->getLabel(),
                ];
            })
            ->toArray();

        return $this->exportUsersExcel($data);
    }


    public  function exportUsersExcel($data)
    {
        // abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');



        $filename = uniqid() . '.xlsx';


        Excel::store(new UsersExport($data), 'temp/' . $filename);

        $file = new \Illuminate\Http\File(storage_path('app/temp/' . $filename));


        $cdnUrl = $this->upload_excel_file($file, 'reports');

        return response()->json([
            'download_url' => $cdnUrl
        ]);
    }
}
