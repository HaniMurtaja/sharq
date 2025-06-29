<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\City;
use App\Models\User;
use App\Models\Order;
use App\Enum\UserRole;
use App\Models\Client;
use App\Models\Operator;
use App\Models\OrderLog;
use App\Enum\OrderStatus;
use App\Exports\BigDataExport;
use App\Exports\OrderDashboardExport;
use App\Models\OrderDriver;
use App\Models\ClientDetail;
use Illuminate\Http\Request;
use App\Models\ClientBranches;
use App\Models\OperatorDetail;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\OrderRequest;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Repositories\FirebaseRepository;
use App\Http\Resources\Api\OrderResource;
use Illuminate\Database\Eloquent\Builder;
use  App\Http\Services\NotificationService;
use App\Traits\OrderCreationDateValidation;
use App\Http\Services\AutoDispatcherService;
use App\Models\ExportLog;
use App\Models\SmsLog;
use App\Traits\FileHandler;
use Illuminate\Support\Facades\Storage;

class OrderDashboardController extends Controller
{
    use FileHandler;
    public function dashboard(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('show_dashboard'), 403, 'You do not have permission to view this page.');

        $clients = Client::all();

        if (auth()->user()->user_role == UserRole::CLIENT) {

            $clients = Client::where('id', auth()->user()->id)->get();
        }

        $order_query  = Order::whereDate('created_at', Carbon::yesterday())
            ->orWhereDate('created_at', Carbon::today());
        if (auth()->user()->user_role == UserRole::CLIENT) {
            $order_query->where('ingr_shop_id', auth()->id());
        }
        if (auth()->user()->user_role == UserRole::BRANCH) {
            // dd(99);
            $order_query->where('ingr_branch_id', auth()->user()->branch_id);
        }
        $orders_count = $order_query->count();
        $operators_count = Operator::count();
        // dd($orders_count);
        $clients_count = Client::count();
        $totalServiceFees = $order_query->sum('service_fees');
        $citys = City::pluck('name', 'id');
        return view('admin.pages.dashboard.index', compact([
            'totalServiceFees',
            'clients',
            'orders_count',
            'operators_count',
            'citys',
            'clients_count'
        ]));
    }
    public function index(Request $request)
    {
        // dd($request->all());
        //abort_unless(auth()->user()->hasPermissionTo('previous_orders_basic_view'), 403, 'You do not have permission to view this page.');
        $clientsQuery = Client::select('id', 'first_name', 'last_name');
        $driversQuery = Operator::select('id', 'first_name', 'last_name');
        $getClientBranchesQuery = ClientBranches::orderBy('created_at', 'desc');
        $itemsQuery = Order::orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');

        if (auth()->user()->user_role == UserRole::CLIENT) {
            $itemsQuery->where('ingr_shop_id', auth()->id());
            $getClientBranchesQuery->where('client_id', auth()->user()->id);
            $clientsQuery = [];
            $driversQuery = [];
        }

        if (auth()->user()->user_role == UserRole::BRANCH) {
            $itemsQuery->where('ingr_branch_id', auth()->user()->branch_id);
            $getClientBranchesQuery->where('client_id', 0);
            $clientsQuery = [];
            $driversQuery = [];
        }
        $clients = is_array($clientsQuery) ? [] : $clientsQuery->get();
        $drivers = is_array($driversQuery) ? [] : $driversQuery->get();
        $getClientBranches = $getClientBranchesQuery->pluck('name', 'id');
        DispatcherController::ActionRoleQueryWhere($itemsQuery, null, null);
        // dd($itemsQuery->count());
        $this->search($itemsQuery, $request);

        $sumvalue = $itemsQuery->sum('value');
        $sumservice_fees =  $itemsQuery->sum('service_fees');

        $items = $itemsQuery->paginate(10);
        $citys = City::pluck('name', 'id');
        return view('admin.pages.orderdashboard.index', compact(['items', 'clients', 'drivers', 'getClientBranches', 'sumservice_fees',  'citys', 'sumvalue']));
    }


    public function search($query, $request)
    {
        //dd($request->all());
        if ($request->id != '') {
            $query->where('id', $request->id);
        }
        if ($request->client_order_id_string != '') {
            $query->where('client_order_id_string', $request->client_order_id_string);
        }
        if ($request->client_order_id != '') {
            $query->where('client_order_id', $request->client_order_id);
        }
        if ($request->status_ids != '') {
            $query->whereIn('status', $request->status_ids);
        }
        if ($request->city_id != '') {
            $query->where('city', $request->city_id);
        }
        if ($request->driver_id != '') {
            $query->where('driver_id', $request->driver_id);
        }
        if ($request->client_id != '') {
            $query->where('ingr_shop_id', $request->client_id);
        }
        if ($request->ingr_branch_id != '') {
            $query->where('ingr_branch_id', $request->ingr_branch_id);
        }
        if ($request->assigned_by != '') {
            $query->where('assigned_by', $request->assigned_by);
        }
        if ($request->customer_phone != '') {
            $query->Where('customer_phone', 'like', '%' . $request->customer_phone . '%');
        }
        if ($request->customer_name != '') {
            $query->Where('customer_name', 'like', '%' . $request->customer_name . '%');
        }

        if (auth()->user()->user_role == UserRole::CLIENT) {
            $query->where('ingr_shop_id', auth()->id());
        }

        if (auth()->user()->user_role == UserRole::BRANCH) {
            $query->where('ingr_branch_id', auth()->user()->branch_id);
        }
        //    dd(request()->all());
        if (!empty(request()->fromtime)) {
            $fromtime = date("Y-m-d H:i:s", strtotime(request()->fromtime));
            $column = request()->datesearch ?? 'created_at';
            $query->where($column, ">=", $fromtime);
        }

        if (!empty(request()->totime)) {
            $totimetime = date("Y-m-d H:i:s", strtotime(request()->totime));
            $column = request()->datesearch ?? 'created_at';
            $query->where($column, "<=", $totimetime);
        }
    }




    public function ordersChartData(Request $request)
    {
        $request->validate([
            'from_date' => 'required',
            'to_date'   => 'required',
        ]);
        $orderQuery = Order::query();
        $orderQuery->where('status', OrderStatus::DELIVERED);

        DispatcherController::ActionRoleQueryWhere($orderQuery, null, null);

        // dd($orderQuery->count());

        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date);
        if ($request->clientFilter != null && $request->clientFilter != '-1') {
            $orderQuery->where('ingr_shop_id', $request->clientFilter);
        }


        if (auth()->user()->user_role == UserRole::CLIENT) {
            $orderQuery->where('ingr_shop_id', auth()->id());
        }

        if (auth()->user()->user_role == UserRole::BRANCH) {
            $orderQuery->where('ingr_branch_id', auth()->user()->branch_id);
        }



        if ($request->city_id != "") {
            $orderQuery->where('city', $request->city_id);
        }
        if (request()->from_date != "") {
            $from_date = Carbon::parse(request()->from_date);
            $orderQuery->where("created_at", ">=", $from_date);
        }

        if (request()->to_date != "") {
            $to_date = Carbon::parse(request()->to_date);
            $orderQuery->where("created_at", "<=", $to_date);
        }

        $hoursDifference = $fromDate->diffInHours($toDate);

        if ($hoursDifference <= 20) {

            $hours = [];
            $counts = [];


            for ($hour = $fromDate->copy(); $hour <= $toDate; $hour->addHour()) {
                $hourLabel = $hour->format('H:00 - H:59');
                $hours[$hourLabel] = 0;
            }


            $orders =  $orderQuery->whereBetween('created_at', [$fromDate, $toDate])
                ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
                ->groupBy('hour')
                ->orderBy('hour')
                ->get();

            foreach ($orders as $order) {
                $hourLabel = sprintf('%02d:00 - %02d:59', $order->hour, $order->hour);
                if (isset($hours[$hourLabel])) {
                    $hours[$hourLabel] = $order->count;
                }
            }

            return response()->json([
                'dates' => array_keys($hours),
                'counts' => array_values($hours),
                'counts_orders' => array_sum(array_values($hours))
            ]);
        } else {

            $orders = $orderQuery
                //->whereBetween('created_at', [$fromDate, $toDate])
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            //  dd($orders);


            return response()->json([
                'dates' => $orders->pluck('date')->toArray(),
                'counts' => $orders->pluck('count')->toArray(),
                'counts_orders' => $orders->pluck('count')->sum()
            ]);
        }
    }
    public function OrdersPerCityChartData(Request $request)
    {
        // dd(9);
        $request->validate([
            'from_date' => 'required',
            'to_date'   => 'required',
        ]);
        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date);
        $reportQuery = Order::query()
            ->select(
                'cities.name as city',
                DB::raw('COUNT(orders.id) as total_orders'),

            )
            ->join('cities', 'orders.city', '=', 'cities.id');
        $reportQuery->where('orders.status', OrderStatus::DELIVERED);

        DispatcherController::ActionRoleQueryWhere($reportQuery, null, null);

        // dd($reportQuery->count());



        if ($request->clientFilter != null && $request->clientFilter != '-1') {
            $reportQuery->where('orders.ingr_shop_id', $request->clientFilter);
        }

        if (auth()->user()->user_role == UserRole::CLIENT) {
            $reportQuery->where('orders.ingr_shop_id', auth()->id());
        }

        if (auth()->user()->user_role == UserRole::BRANCH) {
            $reportQuery->where('orders.ingr_branch_id', auth()->user()->branch_id);
        }

        if ($request->city_id != null && $request->city_id != '-1') {
            $reportQuery->where('orders.city', $request->city_id);
        }
        // Apply filters based on request
        if (request()->from_date != '') {
            $from_date = date("Y-m-d H:i:s", strtotime(request()->from_date));
            //$reportQuery->where("orders.created_at", ">=", $from_date);
        }
        if (request()->to_date != '') {
            $to_date = date("Y-m-d H:i:s", strtotime(request()->to_date));
            // $reportQuery->where("orders.created_at", "<=", $to_date);
        }
        $reportQuery->whereBetween('orders.created_at', [$fromDate, $toDate]);
        $counts_orders = $reportQuery->count();
        $citys = $reportQuery->groupBy('cities.name')->get();
        return response()->json([
            'data' =>  $citys,
            'counts_orders' => $counts_orders
        ]);
        return $citys;
    }


    public function OrdersPerClientChartData(Request $request)
    {
        $request->validate([
            'from_date' => 'required',
            'to_date'   => 'required',
        ]);

        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date);

        $query = Order::query()
            ->select(
                DB::raw("CONCAT_WS(' ', users.first_name, users.last_name) as client_name"),
                'orders.ingr_shop_id as client_id',
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('COUNT(DISTINCT orders.ingr_branch_id) as total_branches'),
                DB::raw("COUNT(DISTINCT CASE WHEN orders.driver_id IS NOT NULL AND orders.driver_id != 0 THEN orders.driver_id END) as total_drivers")
            )
            ->join('users', 'users.id', '=', 'orders.ingr_shop_id')
            ->where('orders.status', OrderStatus::DELIVERED)
            ->whereBetween('orders.created_at', [$fromDate, $toDate])
            ->groupBy('orders.ingr_shop_id', 'users.id', 'users.first_name', 'users.last_name')
            ->orderByDesc('total_orders');



        DispatcherController::ActionRoleQueryWhere($query, null, null);



        if (auth()->user()->user_role == UserRole::CLIENT) {
            $query->where('orders.ingr_shop_id', auth()->id());
        }

        if (auth()->user()->user_role == UserRole::BRANCH) {
            $query->where('orders.ingr_branch_id', auth()->user()->branch_id);
        }

        if ($request->city_id && $request->city_id != '-1') {
            $query->where('orders.city', $request->city_id);
        }

        $clients = $query->get();
        // dd([
        //     'data' => $clients
        // ]);
        return response()->json([
            'data' => $clients
        ]);
    }




    public function OrdersPerClientstData(Request $request)
    {
        $reportQuery = Order::query()
            ->select(
                'users.id as user_id',
                'users.first_name as user_name',

                DB::raw("Count(*) as total_orders "),
                DB::raw("SUM(CASE WHEN orders.status = 1 THEN 1 ELSE 0 END) as pending_orders"),

                // Count orders In Progress (status IN (17,16,6,8))
                DB::raw("SUM(CASE WHEN orders.status IN (17,16,6,8) THEN 1 ELSE 0 END) as in_progress_orders"),
                // Count orders Cancel (status = 10)
                DB::raw("SUM(CASE WHEN orders.status = 10 THEN 1 ELSE 0 END) as cancel_orders"),
                // Count orders Delivered (status = 9)
                DB::raw("SUM(CASE WHEN orders.status = 9 THEN 1 ELSE 0 END) as delivered_orders"),
                // Average operator waiting time (assumes operator_start_time and operator_end_time are available)
                DB::raw("TIME_FORMAT(SEC_TO_TIME(AVG(TIMESTAMPDIFF(SECOND, orders.arrived_to_pickup_time, orders.picked_up_time))), '%H:%i:%s') as avg_operator_waiting"),
                // Average delivered time (difference between order created and delivered)
                DB::raw("TIME_FORMAT(SEC_TO_TIME(AVG(TIMESTAMPDIFF(SECOND, orders.created_at, orders.delivered_at))), '%H:%i:%s') as avg_delivered")
            )
            ->join('users', 'orders.ingr_shop_id', '=', 'users.id');



        DispatcherController::ActionRoleQueryWhere($reportQuery, null, null);

        if (auth()->user()->user_role == UserRole::CLIENT) {
            $reportQuery->where('orders.ingr_shop_id', auth()->id());
        }

        if (auth()->user()->user_role == UserRole::BRANCH) {
            $reportQuery->where('orders.ingr_branch_id', auth()->user()->branch_id);
        }


        if ($request->clientFilter != null && $request->clientFilter != '-1') {
            $reportQuery->where('orders.ingr_shop_id', $request->clientFilter);
        }
        if ($request->city_id != null && $request->city_id != '-1') {
            $reportQuery->where('orders.city', $request->city_id);
        }
        if (request()->from_date != '') {
            $from_date = date("Y-m-d H:i:s", strtotime(request()->from_date));
            $reportQuery->where("orders.created_at", ">=", $from_date);
        }
        if (request()->to_date != '') {
            $to_date = date("Y-m-d H:i:s", strtotime(request()->to_date));
            $reportQuery->where("orders.created_at", "<=", $to_date);
        }

        // Get overall orders count (you may count the orders before grouping, if needed)
        $counts_orders = $reportQuery->count();

        // Group the results by user id and user name
        $users = $reportQuery->groupBy('users.id', 'users.first_name')->get();
        // dd(9);
        return response()->json([
            'data' => $users,
            'counts_orders' => $counts_orders,
            'counts_delivered_orders' =>  $users->sum('delivered_orders'),
            'counts_cancel_orders' =>  $users->sum('cancel_orders'),
        ]);
    }

    public function OrdersPerClientstDataExport(Request $request)
    {
        // dd(request()->all());
        $reportQuery = Order::query()
            ->select(
                'users.id as user_id',
                'users.first_name as user_name',
                DB::raw("COUNT(*) as total_orders"),
                DB::raw("SUM(CASE WHEN orders.status = 1 THEN 1 ELSE 0 END) as pending_orders"),
                DB::raw("SUM(CASE WHEN orders.status IN (17,16,6,8) THEN 1 ELSE 0 END) as in_progress_orders"),
                DB::raw("SUM(CASE WHEN orders.status = 10 THEN 1 ELSE 0 END) as cancel_orders"),
                DB::raw("SUM(CASE WHEN orders.status = 9 THEN 1 ELSE 0 END) as delivered_orders"),
                DB::raw("TIME_FORMAT(SEC_TO_TIME(AVG(TIMESTAMPDIFF(SECOND, orders.arrived_to_pickup_time, orders.picked_up_time))), '%H:%i:%s') as avg_operator_waiting"),
                DB::raw("TIME_FORMAT(SEC_TO_TIME(AVG(TIMESTAMPDIFF(SECOND, orders.created_at, orders.delivered_at))), '%H:%i:%s') as avg_delivered")
            )
            ->join('users', 'orders.ingr_shop_id', '=', 'users.id');


        DispatcherController::ActionRoleQueryWhere($reportQuery, null, null);

        if (auth()->user()->user_role == UserRole::CLIENT) {
            $reportQuery->where('orders.ingr_shop_id', auth()->id());
        }

        if (auth()->user()->user_role == UserRole::BRANCH) {
            $reportQuery->where('orders.ingr_branch_id', auth()->user()->branch_id);
        }

        if ($request->client_id && $request->client_id != '-1') {
            $reportQuery->where('orders.ingr_shop_id', $request->client_id);
        }

        if ($request->city_id && $request->city_id != '-1') {
            $reportQuery->where('orders.city', $request->city_id);
        }

        if ($request->fromtime) {
            $from_date = date("Y-m-d H:i:s", strtotime($request->fromtime));
            $reportQuery->where("orders.created_at", ">=", $from_date);
        }

        if ($request->totime) {
            $to_date = date("Y-m-d H:i:s", strtotime($request->totime));
            $reportQuery->where("orders.created_at", "<=", $to_date);
        }

        $data = $reportQuery
            ->groupBy('users.id', 'users.first_name')
            ->get()
            ->map(function ($user) {
                return [
                    'user_name' => $user->user_name,
                    'total_orders' => $user->total_orders,
                    'pending_orders' => $user->pending_orders,
                    'in_progress_orders' => $user->in_progress_orders,
                    'cancel_orders' => $user->cancel_orders,
                    'delivered_orders' => $user->delivered_orders,
                    'avg_operator_waiting' => $user->avg_operator_waiting,
                    'avg_delivered' => $user->avg_delivered,
                ];
            })
            ->toArray();

        return $this->exportOrdersClientExcel($data);
    }


    public  function exportOrdersClientExcel($data)
    {
        // abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');



        $filename = uniqid() . '.xlsx';


        Excel::store(new OrderDashboardExport($data), 'temp/' . $filename);

        $file = new \Illuminate\Http\File(storage_path('app/temp/' . $filename));


        $cdnUrl = $this->upload_excel_file($file, 'reports');

        return response()->json([
            'download_url' => $cdnUrl
        ]);
    }




    public function OrdersPerCityData(Request $request)
    {

        $request->validate([
            'from_date' => 'required',
            'to_date'   => 'required',
        ]);

        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date);

        $reportQuery = Order::query()
            // ->where('orders.status', OrderStatus::DELIVERED)
            ->select(
                'cities.name as city',
                DB::raw('COUNT(*) as total_orders'),
                DB::raw("SUM(CASE WHEN orders.status = 1 THEN 1 ELSE 0 END) as pending_orders"),
                DB::raw("SUM(CASE WHEN orders.status IN (17,16,6,8) THEN 1 ELSE 0 END) as in_progress_orders"),
                DB::raw("SUM(CASE WHEN orders.status = 10 THEN 1 ELSE 0 END) as cancel_orders"),
                DB::raw("SUM(CASE WHEN orders.status = 9 THEN 1 ELSE 0 END) as delivered_orders"),
                DB::raw("TIME_FORMAT(SEC_TO_TIME(AVG(TIMESTAMPDIFF(SECOND, orders.arrived_to_pickup_time, orders.picked_up_time))), '%H:%i:%s') as avg_operator_waiting"),
                DB::raw("TIME_FORMAT(SEC_TO_TIME(AVG(TIMESTAMPDIFF(SECOND, orders.created_at, orders.delivered_at))), '%H:%i:%s') as avg_delivered"),
                DB::raw("COUNT(DISTINCT orders.driver_id) as total_drivers"),
                DB::raw("COUNT(DISTINCT orders.assigned_by) as total_dispatchers")
            )
            ->join('cities', 'orders.city', '=', 'cities.id');


        DispatcherController::ActionRoleQueryWhere($reportQuery, null, null);

        if (auth()->user()->user_role == UserRole::CLIENT) {
            $reportQuery->where('orders.ingr_shop_id', auth()->id());
        }

        if (auth()->user()->user_role == UserRole::BRANCH) {
            $reportQuery->where('orders.ingr_branch_id', auth()->user()->branch_id);
        }


        if ($request->clientFilter != null && $request->clientFilter != '-1') {
            $reportQuery->where('orders.ingr_shop_id', $request->clientFilter);
        }
        if ($request->city_id != null && $request->city_id != '-1') {
            $reportQuery->where('orders.city', $request->city_id);
        }
        if ($request->from_date != '') {
            $from_date = date("Y-m-d H:i:s", strtotime($request->from_date));
            $reportQuery->where("orders.created_at", ">=", $from_date);
        }
        if ($request->to_date != '') {
            $to_date = date("Y-m-d H:i:s", strtotime($request->to_date));
            $reportQuery->where("orders.created_at", "<=", $to_date);
        }

        $counts_orders = $reportQuery->count();


        $cities = $reportQuery->groupBy('cities.id', 'cities.name')->get();

        return response()->json([
            'data' => $cities,
            'counts_orders' => $counts_orders,
            'counts_delivered_orders' => $cities->sum('delivered_orders'),
            'counts_cancel_orders' => $cities->sum('cancel_orders'),
            'total_drivers' => $cities->sum('total_drivers'),
            'total_dispatchers' => $cities->sum('total_dispatchers'),
        ]);
    }



    public function OrdersPerClientData(Request $request)
    {
        $request->validate([
            'from_date' => 'required',
            'to_date'   => 'required',
        ]);

        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date);

        $reportQuery = Order::query()
            // ->where('status', OrderStatus::DELIVERED)
            ->select(
                DB::raw("CONCAT_WS(' ', users.first_name, users.last_name) as client_name"),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw("SUM(CASE WHEN orders.status = 1 THEN 1 ELSE 0 END) as pending_orders"),
                DB::raw("SUM(CASE WHEN orders.status IN (17,16,6,8) THEN 1 ELSE 0 END) as in_progress_orders"),
                DB::raw("SUM(CASE WHEN orders.status = 10 THEN 1 ELSE 0 END) as cancel_orders"),
                DB::raw("SUM(CASE WHEN orders.status = 9 THEN 1 ELSE 0 END) as delivered_orders"),
                DB::raw("TIME_FORMAT(SEC_TO_TIME(AVG(TIMESTAMPDIFF(SECOND, orders.arrived_to_pickup_time, orders.picked_up_time))), '%H:%i:%s') as avg_operator_waiting"),
                DB::raw("TIME_FORMAT(SEC_TO_TIME(AVG(TIMESTAMPDIFF(SECOND, orders.created_at, orders.delivered_at))), '%H:%i:%s') as avg_delivered"),
                DB::raw("COUNT(DISTINCT orders.driver_id) as total_drivers"),
                DB::raw("COUNT(DISTINCT orders.assigned_by) as total_dispatchers")
            )
            ->join('users', 'orders.ingr_shop_id', '=', 'users.id');


        DispatcherController::ActionRoleQueryWhere($reportQuery, null, null);

        if (auth()->user()->user_role == UserRole::CLIENT) {
            $reportQuery->where('orders.ingr_shop_id', auth()->id());
        }

        if (auth()->user()->user_role == UserRole::BRANCH) {
            $reportQuery->where('orders.ingr_branch_id', auth()->user()->branch_id);
        }


        if ($request->city_id && $request->city_id != '-1') {
            $reportQuery->where('orders.city', $request->city_id);
        }

        if ($request->clientFilter && $request->clientFilter != '-1') {
            $reportQuery->where('orders.ingr_shop_id', $request->clientFilter);
        }

        if ($request->from_date != '') {
            $from_date = date("Y-m-d H:i:s", strtotime($request->from_date));
            $reportQuery->where("orders.created_at", ">=", $from_date);
        }

        if ($request->to_date != '') {
            $to_date = date("Y-m-d H:i:s", strtotime($request->to_date));
            $reportQuery->where("orders.created_at", "<=", $to_date);
        }

        $counts_orders = $reportQuery->count();

        $clients = $reportQuery->groupBy('users.id', 'users.first_name', 'users.last_name')->get();

        return response()->json([
            'data' => $clients,
            'counts_orders' => $counts_orders,
            'counts_delivered_orders' => $clients->sum('delivered_orders'),
            'counts_cancel_orders' => $clients->sum('cancel_orders'),
            'total_drivers' => $clients->sum('total_drivers'),
            'total_dispatchers' => $clients->sum('total_dispatchers'),
        ]);
    }


    public function UnifonicResponse(Request $request)
    {
        $phone = Order::findOrFail($request->order_id)->customer_phone;

        $logs = SmsLog::where('number', (int) $phone)
            ->orderBy('id', 'desc')
            ->take(100)
            ->get()
            ->map(function ($log) {

                if (is_array($log->response_body)) {
                    $log->response_body = json_encode($log->response_body);
                } else {

                    $decoded = json_decode($log->getRawOriginal('response_body'), true);

                    if (json_last_error() === JSON_ERROR_NONE) {
                        $log->response_body = json_encode($decoded);
                    } else {

                        $log->response_body = (string) $log->getRawOriginal('response_body');
                    }
                }

                return $log;
            });
        // dd($logs);
        return response()->json(['logs' => $logs]);
    }
}
