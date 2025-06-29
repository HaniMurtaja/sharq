<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Group;
use App\Models\Shift;
use App\Models\Client;
use App\Models\Vehicle;
use Nnjeim\World\World;
use App\Models\Operator;
use App\Models\ClientDetail;
use Illuminate\Http\Request;
use App\Settings\GeneralSettings;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Models\SpecialBusinessHours;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\Settings\EtaRequest;
use App\Http\Requests\Settings\TaxesRequest;
use Google\Service\PeopleService\ClientData;
use App\Http\Requests\Settings\AccountRequest;
use App\Http\Requests\Settings\PaymentRequest;
use App\Http\Requests\Settings\PrivacyRequest;
use App\Http\Requests\Settings\CustomerRequest;
use App\Http\Requests\Settings\ServicesRequest;
use App\Http\Requests\Settings\OperatorsRequest;
use App\Http\Requests\Settings\ApiSettingsRequest;
use App\Http\Requests\Settings\DispatchingRequest;
use App\Http\Requests\Settings\AutoDispatchRequest;
use App\Http\Requests\Settings\VehicleTypesRequest;
use App\Http\Requests\Settings\AnnouncementsRequest;
use App\Http\Requests\Settings\DashboardPageRequest;
use App\Http\Requests\Settings\DispatcherPageRequest;
use App\Http\Requests\Settings\FoodicsConnectionRequest;
use Google\Service\MyBusinessBusinessInformation\SpecialHours;

class SettingsController  extends Controller
{
   public function saveAccount(AccountRequest $request)
   {
      // dd(99);
      // dd($request->validated());

      abort_unless(auth()->user()->hasPermissionTo('control_consolidated_orders_settings'), 403, 'You do not have permission to view this page.');

      $settings = new GeneralSettings();
      $settings->account = $request->validated();
      $settings->save();
      return redirect(route('settings') . '#account');
   }
   public function saveVehicleTypes(VehicleTypesRequest $request)
   {
      abort_unless(auth()->user()->hasPermissionTo('control_consolidated_orders_settings'), 403, 'You do not have permission to view this page.');

      $settings = new GeneralSettings();
      // dd($request->validated());
      $settings->vehicle_types = $request->validated()['vehicle_types'];
      $settings->save();
      return redirect(route('settings') . '#vehicles');
   }
   public function savePrivacy(PrivacyRequest $request)
   {
      abort_unless(auth()->user()->hasPermissionTo('control_consolidated_orders_settings'), 403, 'You do not have permission to view this page.');
      if ($request->new_password) {
         $user = User::findOrFail(auth()->id());
         $user->password = Hash::make($request->new_password);
         $user->save();
      }

      return redirect(route('settings') . '#privacy');
   }

   public function saveApi(ApiSettingsRequest $request)
   {
      abort_unless(auth()->user()->hasPermissionTo('control_consolidated_orders_settings'), 403, 'You do not have permission to view this page.');
      $settings = new GeneralSettings();
      // dd($request->validated());
      $settings->api_settings = ['api_key' => $request->validated()['api_key'], 'max_distance_accept' => (string) $request->max_distance_accept . " KM"];
      $settings->save();
      return redirect(route('settings') . '#api');
   }

   public function saveOperators(OperatorsRequest $request)
   {
      abort_unless(auth()->user()->hasPermissionTo('control_consolidated_orders_settings'), 403, 'You do not have permission to view this page.');
      $settings = new GeneralSettings();
      // dd($request->validated());
      $settings->operators = $request->validated();
      $settings->save();
      return redirect(route('settings') . '#operators');
   }

   public function saveAutoDispatch(AutoDispatchRequest $request)
   {
      abort_unless(auth()->user()->hasPermissionTo('control_consolidated_orders_settings'), 403, 'You do not have permission to view this page.');
      $settings = new GeneralSettings();
      // dd($request->validated());
      $settings->auto_dispatch = $request->validated();
      $settings->max_driver_orders = $request->max_driver_orders;
      $settings->auto_dispatch_per_city = $request->auto_dispatch_per_city;
      $settings->max_distance_per_city = $request->max_distance_per_city;
      $settings->auto_dispatch_per_city = $request->auto_dispatch_per_city;
      $settings->save();
      return redirect(route('settings') . '#auto_dispatch');
   }

   public function saveoDispatcherPage(DispatcherPageRequest $request)
   {
      abort_unless(auth()->user()->hasPermissionTo('control_consolidated_orders_settings'), 403, 'You do not have permission to view this page.');

      $settings = new GeneralSettings();
      // dd($request->validated());
      $settings->dispatcher_page = $request->validated();
      $settings->time_multi_order_assign = $request->time_allowed_accept_more_than_order ?? 0;

      $settings->save();

      return redirect(route('settings') . '#dispatcher');
   }

   public function saveDashboardPage(DashboardPageRequest $request)
   {
      abort_unless(auth()->user()->hasPermissionTo('control_consolidated_orders_settings'), 403, 'You do not have permission to view this page.');

      // dd($request->all());
      $settings = new GeneralSettings();
      // dd($request->validated());
      $settings->dashboard_page = $request->validated();
      $settings->save();
      return redirect(route('settings') . '#dashboard');
   }

   public function saveServices(ServicesRequest $request)
   {
      abort_unless(auth()->user()->hasPermissionTo('control_consolidated_orders_settings'), 403, 'You do not have permission to view this page.');
      $settings = new GeneralSettings();
      // dd($request->validated());
      $settings->services = $request->validated();
      $settings->save();
      return redirect(route('settings') . '#service');
   }

   public function saveEtaSettinga(EtaRequest $request)
   {
      // dd(9);
      abort_unless(auth()->user()->hasPermissionTo('control_consolidated_orders_settings'), 403, 'You do not have permission to view this page.');
      $settings = new GeneralSettings();
      // dd($request->validated());
      $settings->eta = $request->validated();
      $settings->save();
      return redirect(route('settings') . '#eta');
   }

   public function saveCustomerMessages(CustomerRequest $request)
   {
      abort_unless(auth()->user()->hasPermissionTo('control_consolidated_orders_settings'), 403, 'You do not have permission to view this page.');

      $settings = new GeneralSettings();
      // dd($request->validated());
      $settings->customer_messages = $request->validated();
      $settings->save();
      return redirect(route('settings') . '#customer_messages');
   }

   public function saveAnnouncements(AnnouncementsRequest $request)
   {
      abort_unless(auth()->user()->hasPermissionTo('control_consolidated_orders_settings'), 403, 'You do not have permission to view this page.');
      $settings = new GeneralSettings();
      // dd($request->validated());
      $settings->announcements = $request->validated();
      $settings->save();
      return redirect(route('settings') . '#announcements');
   }

   public function saveTaxes(TaxesRequest $request)
   {
      abort_unless(auth()->user()->hasPermissionTo('control_consolidated_orders_settings'), 403, 'You do not have permission to view this page.');

      $settings = new GeneralSettings();
      // dd($request->validated());
      $settings->taxes = $request->validated();
      $settings->save();
      return redirect(route('settings') . '#taxes');
   }

   public function saveDispatching(DispatchingRequest $request)
   {
      abort_unless(auth()->user()->hasPermissionTo('control_consolidated_orders_settings'), 403, 'You do not have permission to view this page.');

      $settings = new GeneralSettings();
      // dd($request->validated());
      $settings->dispatching = $request->validated();
      $settings->save();
      return redirect(route('settings') . '#dispatching');
   }

   public function savePayment(PaymentRequest $request)
   {
      abort_unless(auth()->user()->hasPermissionTo('control_consolidated_orders_settings'), 403, 'You do not have permission to view this page.');

      $settings = new GeneralSettings();
      // dd($request->validated());
      $settings->payment_gateway = $request->validated();
      $settings->save();
      return redirect(route('settings') . '#payment_gateway');
   }

   public function saveFoodics(FoodicsConnectionRequest $request)
   {
      abort_unless(auth()->user()->hasPermissionTo('control_consolidated_orders_settings'), 403, 'You do not have permission to view this page.');
      $settings = new GeneralSettings();
      // dd($request->validated());
      $settings->foodics_connection = $request->validated();
      $settings->save();
      return redirect(route('settings') . '#foodics_connection');
   }



   public function saveBusinessHours(Request $request)
   {
      abort_unless(auth()->user()->hasPermissionTo('control_consolidated_orders_settings'), 403, 'You do not have permission to view this page.');

      $settings = new GeneralSettings();

      $settings->business_hours = ['start_time' => $request->start_time, 'end_time' => $request->end_time];
      $settings->shift_end_tomorrow = $request->shift_end_tomorrow ?? 0;
      $settings->save();

      return redirect(route('settings') . '#business_hours');
   }

   public function saveSpecialBusinessHours(Request $request)
   {
      // dd($request->all());
      abort_unless(auth()->user()->hasPermissionTo('control_consolidated_orders_settings'), 403, 'You do not have permission to view this page.');

      $request->merge([
         'special_start_time' => trim($request->input('special_start_time')),
         'special_end_time' => trim($request->input('special_end_time')),
      ]);
      //   dd($request->all())/;
      $request->validate([
         'special_start_time' => 'required|date',
         'special_end_time' => 'required|date|after:special_start_time',
         'clients' => 'nullable|array',
         'clients.*' => 'exists:users,id',
      ]);


      $settings = new GeneralSettings();
      // dd($request->special_start_time,  $request->special_end_time);
      $start_time = Carbon::createFromFormat('Y-m-d\TH:i', $request->special_start_time)->format('Y-m-d H:i:s');
      $end_time = Carbon::createFromFormat('Y-m-d\TH:i', $request->special_end_time)->format('Y-m-d H:i:s');

      // dd($start_time, $end_time);
      $settings->special_business_hours = ['start_time' => $start_time, 'end_time' => $end_time];
      // $settings->business_hours['end_time'] = $request->end_time;
      $settings->save();
      if ($request->has('clients') && is_array($request->clients) && !empty($request->clients)) {
         foreach ($request->clients as $client) {
            $client = ClientDetail::where('user_id', $client)->first();
            $client->has_special_business_hours = 1;
            $client->save();

            SpecialBusinessHours::create([
               'start' => $start_time,
               'end' =>  $end_time,
               'client_id' => $client->user_id
            ]);
         }

         ClientDetail::whereNotIn('user_id', $request->clients)
            ->update(['has_special_business_hours' => 0]);
      } else {

         ClientDetail::query()->update(['has_special_business_hours' => 0]);
      }

      return response()->json('success');

      // return redirect(route('settings') . '#special_business_hours');
   }

   public function getSopecialHours(Request $request)
   {
      abort_unless(auth()->user()->hasPermissionTo('control_consolidated_orders_settings'), 403, 'You do not have permission to view this page.');

      // dd($request->all());PtempP_
      $columns = ['id', 'start_date', 'end_date', 'start_time', 'end_time',  'client'];
      $totalData = SpecialBusinessHours::count();
      $totalFiltered = $totalData;
      $limit = $request->input('length');
      $start = $request->input('start');
      $order = $columns[$request->input('order.0.column')];
      $dir = $request->input('order.0.dir');
      if (empty($request->input('search.value'))) {
         $branches = SpecialBusinessHours::offset($start)->limit($limit)->orderBy($order, $dir)->get();
      } else {
         $search = $request->input('search.value');
         $branches = SpecialBusinessHours::where('client_id', 'LIKE', "%{$search}%")->offset($start)->limit($limit)->orderBy($order, $dir)->get();
         $totalFiltered = SpecialBusinessHours::where('client_id', 'LIKE', "%{$search}%")->count();
      }
      $data = [];
      if (!empty($branches)) {
         foreach ($branches as $branch) {
            $nestedData['id'] = $branch->id;


            $nestedData['start_date'] = $branch->start ? $branch->start->format('Y-m-d') : null;
            $nestedData['start_time'] = $branch->start ? $branch->start->format('H:i:s') : null;


            $nestedData['end_date'] = $branch->end ? $branch->end->format('Y-m-d') : null;
            $nestedData['end_time'] = $branch->end ? $branch->end->format('H:i:s') : null;

            $nestedData['client'] = $branch->client->full_name;
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
}
