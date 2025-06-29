<?php
namespace App\Http\Controllers\Admin;

use App\Enum\OrderStatus;
use App\Enum\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Branch;
use App\Models\City;
use App\Models\Client;
use App\Models\ClientBranches;
use App\Models\ClientsGroup;
use App\Models\ClientUser;
use App\Models\Country;
use App\Models\Group;
use App\Models\IntegrationCompany;
use App\Models\Operator;
use App\Models\Order;
use App\Models\Reason;
use App\Models\ReportTemplat;
use App\Models\Shift;
use App\Models\User;
use App\Models\UserCitys;
use App\Models\Vehicle;
use App\Models\Zone;
use App\Settings\GeneralSettings;
use App\Traits\TimeTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Spatie\Permission\Models\Role;

class HomeController extends Controller
{
    use TimeTrait;
    public function track_order($id)
    {

        return "Under Construction";

    }
    public function index()
    {
        //$new =SendSms::toSms("ghj","test");
        // return MapsController::geoapify();

        // dd(User::find(18)->image);
        // dd(auth()->user()->hasPermissionTo('can_make_cancel_request'));
//      $permissions = Permission::all();
//
//
//      $role = Role::find(4);
//
//      if ($role) {
//// dd(9);
//         $role->syncPermissions($permissions);
//
//
//      }
        //       \request()->session()->put('current_step', 1);
        // dd(auth()->user()->unReaNotificationsCustom()->get());

        abort_unless(auth()->user()->hasPermissionTo('basic_dispatcher_view'), 403, 'You do not have permission to view this page.');

        $branches = [];
        $fees     = 0;
        $clients  = Client::all();
        if (auth()->user()->user_role == UserRole::CLIENT) {
            //return MapsController::geoapify();
            $fees     = Client::findOrFail(auth()->user()->id)->client?->clienGroup?->default_delivery_fee;
            $branches = ClientBranches::where('client_id', auth()->user()->id)->get();

        }
        if (auth()->user()->user_role == UserRole::BRANCH) {
//        return MapsController::geoapify();
            $fees = Client::findOrFail(auth()->user()->client_id)->client?->clienGroup?->default_delivery_fee;
        }
        $order_count = Order::where(function ($q) {
            $q->whereDate('created_at', Carbon::yesterday())
                ->orWhereDate('created_at', Carbon::today());
        })->whereNotIn('status', [OrderStatus::CANCELED, OrderStatus::DELIVERED]);
        if (auth()->user()->user_role == UserRole::CLIENT) {
            $order_count = $order_count->where('ingr_shop_id', auth()->id());
        }
        if (auth()->user()->user_role == UserRole::BRANCH) {
            $order_count = $order_count->where('ingr_branch_id', auth()->user()->branch_id);
        }
        DispatcherController::ActionRoleQueryWhere($order_count, null, null);
        $order_count = $order_count->count();
        $auth_name   = auth()->user()->full_name;
        if (auth()->user()->user_role == UserRole::BRANCH) {
            $auth_name = auth()->user()->branch?->client?->full_name . ' - ' . auth()->user()->branch?->name;
        }
        // dd($auth_name);
        $InitializingMap = $this->InitializingMap();
        //  dd($InitializingMap);
        $allow_city = UserCitys::where('user_id', auth()->id())->pluck('city_id')->toArray();
//       dd($allow_city);

        $cancel_reasons = Reason::all();

        return view('admin.pages.dispatchers.index', compact(['order_count', 'cancel_reasons', 'fees', 'branches', 'clients', 'auth_name', 'InitializingMap', 'allow_city']));
    }

    private function InitializingMap()
    {
        if (auth()->user()->user_role == UserRole::CLIENT) {
            // dd('clieent');
            $branch = ClientBranches::where('client_id', auth()->user()->id)->orderBy('created_at', 'asc')->first();
            $result = [
                'lat'  => $branch->lat,
                'lng'  => $branch->lng,
                'zoom' => 8,
            ];
        } elseif (auth()->user()->user_role == UserRole::BRANCH) {
            $branch = ClientBranches::where('id', auth()->user()->branch_id)->orderBy('created_at', 'asc')->first();
            $result = [
                'lat'  => $branch->lat,
                'lng'  => $branch->lng,
                'zoom' => 12,
            ];
        } else {
            $result = [
                'lat'  => 23.8859,
                'lng'  => 45.0792,
                'zoom' => 6,
            ];
        }

        return $result;
    }
    public function select_user()
    {}
    public function search_order(Request $request)
    {
        $order = Order::where(function ($q) {
            $q->whereDate('created_at', Carbon::yesterday())
                ->orWhereDate('created_at', Carbon::today());
        });
        if ($request->search) {

            // ->count();

            if (auth()->user()->hasRole('Client')) {
                $order->where('ingr_shop_id', auth()->id());
            }
            $order_count = $order->count();
            return view('admin.pages.index', compact('order_count'));
        }
        return "";
    }

    public function orders()
    {
        abort_unless(auth()->user()->hasPermissionTo('orders_basic_view'), 403, 'You do not have permission to view this page.');

        $clients = Client::all();
        $drivers = Operator::all();
        return view('admin.pages.orders', compact(['clients', 'drivers']));
    }

    public function operators()
    {
        abort_unless(auth()->user()->hasPermissionTo('control_drivers'), 403, 'You do not have permission to view this page.');

        $shifts    = Shift::paginate(10);
        $groups    = Group::paginate(10);
        $operators = Operator::paginate(10);
       

        $all_groups = Group::all();
        $all_shifts = Shift::all();

        $user = auth()->user();
        $countryId = $user->country_id;

        $query = City::query();


        if ($countryId) {
            $query->where('country_id', $countryId);
        }

         $cities = $query->get();

        // dd($cities);
        $vehicles = Vehicle::where('owner', 'company')->get();
        $settings = new GeneralSettings();
        return view('admin.pages.operators', compact(['shifts', 'settings', 'groups', 'operators', 'vehicles', 'cities', 'all_groups', 'all_shifts']));
    }

    public function clients()
    {
        abort_unless(auth()->user()->hasPermissionTo('control_clients'), 403, 'You do not have permission to view this page.');

        $branches   = Branch::count();
        $drivers    = Operator::all();
        $groups     = ClientsGroup::count();
        $all_cities = City::all();
        $all_areas  = Area::all();
        $zones      = Zone::count();

        $clients       = Client::count();
        $client_groups = ClientsGroup::all();
        $driver_groups = Group::all();
        $countries     = Country::all();
        $integrations  = IntegrationCompany::all();

        $templates = Role::all();

        $areas        = Area::all();
        $all_branches = Branch::all();
        $users        = User::all();

        return view('admin.pages.clients', compact(['users', 'templates', 'areas', 'all_branches', 'countries', 'branches', 'clients', 'client_groups', 'driver_groups', 'drivers', 'groups', 'all_cities', 'all_areas', 'zones', 'integrations']));
    }

    public function users()
    {
        abort_unless(auth()->user()->hasPermissionTo('control_users'), 403, 'You do not have permission to view this page.');

        $users  = ClientUser::paginate(10);
        $groups = Group::all();

        $roles = Role::all();
        $citys = City::pluck('id', 'name');
        $countries = Country::all();

        return view('admin.pages.users', compact(['users', 'countries' ,'groups', 'roles', 'citys']));
    }
    public function vehicles()
    {
        abort_unless(auth()->user()->hasPermissionTo('view_vehicles'), 403, 'You do not have permission to view this page.');

        $settings  = new GeneralSettings();
        $operators = Operator::all();
        return view('admin.pages.vehicles', compact(['operators', 'settings']));
    }

    public function reports()
    {

        abort_unless(auth()->user()->hasPermissionTo('orders_report'), 403, 'You do not have permission to view this page.');
        abort_unless($this->restrictedTime(), 403, 'The reports are running between 4:15 AM and 11 am.');

        $report_types = ReportTemplat::all();

        $branches = [];
        if (auth()->user()->user_role == UserRole::CLIENT) {
            $branches = ClientBranches::select('id', 'name')->where('client_id', auth()->id())->get();
        }

        // dd($branches);
        return view('admin.pages.reports', compact('report_types', 'branches'));
    }
    public function messages()
    {
        return view('admin.pages.messages');
    }
    public function settings()
    {
        abort_unless(auth()->user()->hasPermissionTo('control_consolidated_orders_settings'), 403, 'You do not have permission to view this page.');
        $settings = new GeneralSettings();
        // dd($settings->vehicle_types);
        // foreach ($settings->vehicle_types['vehicle_types'] as $test) {
        //    dd($test);
        // }
        $clients         = Client::all();
        $special_clients = Client::whereHas('client', function ($q) {
            $q->where('has_special_business_hours', 1);
        })->pluck('id')->toArray();

        $cities = City::all();
        return view('admin.pages.settings', compact(['cities', 'settings', 'clients', 'special_clients']));
    }

    public function logout()
    {
        $user = Auth::guard('web')->user();
        if ($user) {
            Auth::guard('web')->logout();
            return redirect('admin/login');
        } else {

            dd('User not authenticated', Auth::guard('web')->check(), Auth::guard('web'));
        }
        return redirect('admin/login');
    }

    public function locations()
    {
        abort_unless(auth()->user()->hasPermissionTo('view_location'), 403, 'You do not have permission to view this page.');
        $countries     = Country::paginate(10);
        $cities        = City::paginate(10);
        $areas         = Area::paginate(10);
        $all_cities    = City::all();
        $all_countries = Country::all();
        $all_areas     = Area::all();
        $drivers       = Operator::all();
        return view('admin.pages.locations', compact(['cities', 'drivers', 'all_areas', 'areas', 'all_countries', 'all_cities', 'countries']));
    }

    public function fetchNotifications(Request $request)
    {
        $userId        = auth()->id();
        $notifications = auth()->user()->notifications()->orderBy('created_at', 'desc')->get();

        $html = '';
        foreach ($notifications as $notification) {
            $html .= '<a href="#" class="dropdown-item " data-id="' . $notification->id . '">';
            $html .= '<i class="fas fa-envelope mr-2"></i> ' . e($notification->body);
            $html .= '<span style="display: block;font-size: 10px;color: #ababab !important;position: relative;left: 24px;">' . $notification->created_at->diffForHumans();
            $html .= ' <i class="fas fa-eye ml-2 mark-as-read" style="cursor: pointer;" data-id="' . $notification->id . '"></i>';
            $html .= '</span>';
            $html .= '</a>';
        }

        return response()->json(['html' => $html]);
    }

    public function markAsRead(Request $request)
    {

        $notifications = auth()->user()->unReaNotificationsCustom()->get();

        if ($notifications) {
            foreach ($notifications as $notification) {
                $notification->is_read = 1;
                $notification->save();
            }

            $count = auth()->user()->unReaNotificationsCustom()->count();
            return response()->json(['success' => true, 'count' => $count]);
        }

        return response()->json(['success' => false], 404);
    }

    public function driver_reports()
    {
        abort_unless(auth()->user()->hasPermissionTo('view_export_reports'), 403, 'You do not have permission to view this page.');
        abort_unless($this->restrictedTime(), 403, 'The reports are running between 4:15 AM and 11 am.');

        return view('admin.pages.reports.driver-reports');
    }

    public function systemClosed()
    {
        return response()->view('admin.pages.system-close', [], 403);
    }

    public function integrations()
    {
        abort_unless(auth()->user()->hasPermissionTo('view_integration'), 403, 'You do not have permission to view this page.');

        $companies = IntegrationCompany::all();
        return view('admin.pages.integrations', compact('companies'));
    }

}
