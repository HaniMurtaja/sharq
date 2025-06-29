<?php

namespace App\Http\Controllers\Admin;

use App\Enum\OrderStatus;
use App\Enum\UserRole;
use App\Exports\DispatchAssignReportExport;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Client;
use App\Models\Order;
use App\Models\User;
use App\Traits\FileHandler;
use App\Traits\TimeTrait;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ReportNewController extends Controller
{

    use TimeTrait;
    use FileHandler;
    public function dispatcherAssignReport(Request $request)
    {

        abort_unless(auth()->user()->hasPermissionTo('dispatcher_assign_reports'), 403, 'You do not have permission to view this page.');

        $user = auth()->user();
        $isAdminWithCountry = $user->user_role === UserRole::ADMIN && $user->country_id;

        $reportDataQuery = DB::table('orders as o')
            ->join('users as u', 'o.assigned_by', '=', 'u.id')
            ->join('cities as c', 'o.city', '=', 'c.id')
            ->select(
                'u.id as user_id',
                DB::raw("CONCAT(u.first_name, ' ', u.last_name) as full_name"),
                DB::raw('COUNT(o.assigned_by) as total_orders'),
                DB::raw('SEC_TO_TIME(ROUND(AVG(TIMESTAMPDIFF(SECOND, o.created_at, o.driver_assigned_at)) / COUNT(o.assigned_by))) as avg_assign_time'),
            );

        if ($isAdminWithCountry) {
            $reportDataQuery->whereIn('u.id', function ($query) use ($user) {
                $query->select('user_id')
                    ->from('user_citys')
                    ->join('cities', 'user_citys.city_id', '=', 'cities.id')
                    ->where('cities.country_id', $user->country_id);
            });
        }

        $this->SearchdispatcherAssignReport($request, $reportDataQuery);

        $reportDataQuery->groupBy('u.id', 'full_name');

        $reportDataQuery->orderBy('total_orders', 'desc');

        $reportData      = $reportDataQuery->paginate(1000);
        $dispatcherCount = $reportData->count();

        $citys         = City::pluck('name', 'id');
        $getDispatcher = User::where('user_role', UserRole::DISPATCHER->value)->get();
        $clientsQuery  = Client::select('id', 'first_name', 'last_name');
        // $getClientBranchesQuery = ClientBranches::orderBy('created_at', 'desc');
        if (auth()->user()->user_role == UserRole::CLIENT) {
            // $getClientBranchesQuery->where('client_id', auth()->user()->id);
            $clientsQuery = [];
        }

        if (auth()->user()->user_role == UserRole::BRANCH) {
            // $getClientBranchesQuery->where('client_id', 0);
            $clientsQuery = [];
        }
        $clients = is_array($clientsQuery) ? [] : $clientsQuery->get();
        //$getClientBranches = $getClientBranchesQuery->pluck('name','id');

        return view('admin.pages.reports.dispatcherAssignReport.index', compact('citys', 'clients', 'getDispatcher', 'reportData', 'dispatcherCount'));
    }
    public function dispatcherAssignReportShowByOLd(Request $request)
    {

        $reportDataQuery = Order::join('cities as c', 'orders.city', '=', 'c.id')
            ->whereNotNull('orders.city')
            ->where('orders.assigned_by', $request->assigned_by)
            ->select(
                'c.name as city_name',                      // Fetch city name
                DB::raw('COUNT(orders.id) as total_orders') // Count orders per city
            )
            ->groupBy('c.id', 'c.name')                 // Group by city ID & name
            ->orderByDesc(DB::raw('COUNT(orders.id)')); // Order by highest total orders
        if ($request->from_date) {
            $fromDate = date("Y-m-d H:i:s", strtotime($request->from_date));
            $reportDataQuery->where("o.created_at", ">=", $fromDate);
        }

        if ($request->totime) {
            $toDate = date("Y-m-d H:i:s", strtotime($request->totime));
            $reportDataQuery->where("o.created_at", "<=", $toDate);
        }
        // **Dump results for debugging**
        // dd($reportDataQuery->get(), $request->assigned_by);

        $reportData = $reportDataQuery->get();

        return response()->json([
            'data'          => $reportData,
            'counts_orders' => $reportData->sum('total_orders'), // Overall total orders across cities
        ]);
    }

    public function dispatcherAssignReportShowBy(Request $request)
    {
        //        abort_unless($this->restrictedTime(), 403, 'The reports are running between 4:15 AM and 11 am.');
        $user = auth()->user();
        $isAdminWithCountry = $user->user_role === UserRole::ADMIN && $user->country_id;

        $reportDataQuery = DB::table('orders as o')
            ->join('users as u', 'o.assigned_by', '=', 'u.id')
            ->join('cities as c', 'o.city', '=', 'c.id')
            ->select(
                'u.id as user_id',
                'u.first_name as user_name',
                'c.name as city_name',
                'c.id as city_id',
                DB::raw('COUNT(o.assigned_by) as total_orders'),
                DB::raw('SEC_TO_TIME(ROUND(AVG(TIMESTAMPDIFF(SECOND, o.created_at, o.driver_assigned_at)) / COUNT(o.assigned_by))) as avg_assign_time')
            )
            ->groupBy('u.id', 'u.first_name', 'c.id', 'c.name');

        if ($isAdminWithCountry) {
            $reportDataQuery->where('c.country_id', $user->country_id);
        }


        $this->SearchdispatcherAssignReport($request, $reportDataQuery);
        // $reportData = $reportDataQuery->groupBy('u.id', 'u.first_name', 'c.name','c.id')
        // ->orderBy('c.name')
        // ->get();
        $reportDataQuery->groupBy('u.id', 'u.first_name', 'c.name', 'c.id');
        $reportDataQuery->orderBy('total_orders', 'desc');
        $reportData = $reportDataQuery->get();
        return response()->json([
            'data'          => $reportData,
            'counts_orders' => $reportData->sum('total_orders'),
        ]);
    }
    public function SearchdispatcherAssignReport($request, $reportDataQuery)
    {
        //  abort_unless($this->restrictedTime(), 403, 'The reports are running between 4:15 AM and 11 am.');

        if ($request->fromtime) {
            $fromDate = date("Y-m-d H:i:s", strtotime($request->fromtime));
            $reportDataQuery->where("o.driver_assigned_at", ">=", $fromDate);
        }

        if ($request->totime) {
            $toDate = date("Y-m-d H:i:s", strtotime($request->totime));
            $reportDataQuery->where("o.driver_assigned_at", "<=", $toDate);
        }

        if ($request->from_date) {
            $fromDate = date("Y-m-d H:i:s", strtotime($request->from_date));
            $reportDataQuery->where("o.driver_assigned_at", ">=", $fromDate);
        }

        if ($request->to_date) {
            $toDate = date("Y-m-d H:i:s", strtotime($request->to_date));
            $reportDataQuery->where("o.driver_assigned_at", "<=", $toDate);
        }
        if (request()->assigned_by != '') {
            $reportDataQuery->where("o.assigned_by", request()->assigned_by);
        }
        if (request()->city_id != '') {
            $reportDataQuery->where("o.city", request()->city_id);
        }
        if ($request->client_id != '') {
            $reportDataQuery->where('o.ingr_shop_id', $request->client_id);
        }
        if ($request->ingr_branch_id != '') {
            $reportDataQuery->where('o.ingr_branch_id', $request->ingr_branch_id);
        }
        if (auth()->user()->user_role == UserRole::CLIENT) {
            $reportDataQuery->where('o.ingr_shop_id', auth()->id());
        }

        if (auth()->user()->user_role == UserRole::BRANCH) {
            $reportDataQuery->where('o.ingr_branch_id', auth()->user()->branch_id);
        }
    }
    public function clientsSalesreport(Request $request)
    {

        return view('admin.pages.reports.clientsSalesreport.index');
    }
    public function getClientsSalesReportData(Request $request)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', -1);
        // abort_unless(auth()->user()->hasPermissionTo('operators_acceptance_time_reports'), 403, 'You do not have permission to view this page.');
        //        abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');

        $query = Order::with(['shop'])
            ->where('status', OrderStatus::DELIVERED);

        if (request()->fromtime) {
            $query->where('created_at', '>=', Carbon::parse(request()->fromtime)->format('Y-m-d H:i:s'));
        }
        if (request()->totime) {
            $query->where('created_at', '<=', Carbon::parse(request()->totime)->format('Y-m-d H:i:s'));
        }

        $results = $query->select([
            'id',
            'ingr_shop_id',
            'ingr_branch_id',
            'driver_id',
        ])
            ->get()
            ->groupBy('ingr_shop_id')
            ->map(function ($orders) {

                $shop         = $orders->first();
                $price_order  = ($shop->shop->client->price_order) ?? 0;
                $driver_count = $orders->pluck('driver_id')->filter()->unique()->count();
                $branch_count = optional($shop->shop)?->branches?->count() ?? 0;

                return [
                    'client_id'       => $shop->ingr_shop_id,
                    'fullname'        => $shop->shop?->FullName,
                    'price_order'     => $price_order,
                    'total_orders'    => $orders->count(),
                    'total_amount'    => $orders->count() * $price_order,
                    'total_branches'  => $orders->pluck('ingr_branch_id')->unique()->count(),
                    'total_clients'   => $orders->pluck('ingr_shop_id')->unique()->count(),
                    'client_branches' => $branch_count,
                    'drivers_count'   => $driver_count,
                ];
            })
            ->values();

        $totalOrdersSum    = $results->sum('total_orders');
        $totalAmountSum    = $results->sum('total_amount');
        $totalBranchsSum   = $results->sum('total_branches');
        $total_clients_sum = $results->sum('total_clients');

        return DataTables::of($results)
            ->addColumn('client_id', fn($row) => $row['client_id'])
            ->addColumn('fullname', fn($row) => $row['fullname'])
            ->addColumn('client_branches', fn($row) => $row['client_branches']) // NEW COLUMN
            ->addColumn('drivers_count', fn($row) => $row['drivers_count'])     // NEW COLUMN
            ->addColumn('total_orders', fn($row) => $row['total_orders'])
            ->addColumn('price_order', fn($row) => round($row['price_order'], 2))
            ->addColumn('total_amount', fn($row) => round($row['total_amount'], 2))
            ->addColumn('action', function ($row) {
                return '<div class="d-flex justify-content-center">
                <a href="#" data-id="' . $row['client_id'] . '"
                    data-bs-toggle="modal"
                    data-bs-target="#operatorReportDetail"
                    class="flex items-center order-driver-btn justify-center w-8 h-8 text-white border rounded-lg border-gray10">
                    <img src="' . asset('new/src/assets/icons/view.svg') . '" alt="View" />
                </a>
            </div>';
            })
            // ->orderColumn('total_amount', 'total_amount $1')
            ->with('total_amount_sum', round($totalAmountSum, 2))
            ->with('total_orders_sum', round($totalOrdersSum, 2))
            ->with('total_branchs_sum', round($totalBranchsSum, 2))
            ->with('total_clients_sum', round($total_clients_sum, 2))

            ->make(true);
    }
    public function getClientsSalesReportDataPerCity(Request $request)
    {
        // abort_unless(auth()->user()->hasPermissionTo('operators_acceptance_time_reports'), 403, 'You do not have permission to view this page.');
        // abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');

        $client_id = $request->client_id;

        $reportQuery = Order::where('ingr_shop_id', $client_id)
            ->where('status', OrderStatus::DELIVERED)
            ->select(
                'cities.name as city',
                'cities.id as cityId',
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('clients.price_order'),
                DB::raw('COUNT(orders.id) * clients.price_order as total_amount'),
                DB::raw('COUNT(DISTINCT orders.ingr_branch_id) AS total_branches'),
                DB::raw('COUNT(DISTINCT CASE WHEN orders.driver_id IS NOT NULL THEN orders.driver_id END) AS total_drivers')
            )
            ->join('cities', 'orders.city', '=', 'cities.id')
            ->join('clients', 'orders.ingr_shop_id', '=', 'clients.user_id');

        // Apply filters based on request
        if ($request->fromtime) {
            $fromDate = date("Y-m-d H:i:s", strtotime($request->fromtime));
            $reportQuery->where("orders.created_at", ">=", $fromDate);
        }
        if ($request->totime) {
            $toDate = date("Y-m-d H:i:s", strtotime($request->totime));
            $reportQuery->where("orders.created_at", "<=", $toDate);
        }

        // Group by city
        $report = $reportQuery->groupBy('cities.id', 'cities.name', 'clients.price_order')->get();

        // Totals
        $totalOrdersSum  = $report->sum('total_orders');
        $totalAmountSum  = $report->sum('total_amount');
        $totalBranchsSum = $report->sum('total_branches');
        $totalDriversSum = $report->sum('total_drivers');

        // Return DataTable
        return DataTables::of($report)
            ->addColumn('cityId', fn($row) => $row->cityId)
            ->addColumn('city', fn($row) => $row->city)
            ->addColumn('total_orders', fn($row) => $row->total_orders)
            ->addColumn('price_order', fn($row) => $row->price_order)
            ->addColumn('total_amount', fn($row) => $row->total_amount)
            ->addColumn('total_branches', fn($row) => $row->total_branches)
            ->addColumn('total_drivers', fn($row) => $row->total_drivers)
            ->addColumn('action', function ($row) {
                return '<div></div>';
            })
            ->with('total_amount_sum', $totalAmountSum)
            ->with('total_orders_sum', $totalOrdersSum)
            ->with('total_branchs_sum', $totalBranchsSum)
            ->with('total_drivers_sum', $totalDriversSum)
            ->make(true);
    }

    public function getPerCityBranchesDetailes(Request $request)
    {
        $client_id = $request->client_id;
        $city_id   = $request->city_id;
        // dd($request->all());
        $query = Order::where('orders.ingr_shop_id', $client_id)
            ->where('orders.city', $city_id)
            ->where('orders.status', OrderStatus::DELIVERED)
            ->select(
                'orders.ingr_branch_id as branch_id',
                'client_branches.name as branch_name',
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('COUNT(DISTINCT CASE WHEN orders.driver_id IS NOT NULL THEN orders.driver_id END) as total_drivers'),
                DB::raw('clients.price_order'),
                DB::raw('COUNT(orders.id) * clients.price_order as total_amount')
            )
            ->join('client_branches', 'orders.ingr_branch_id', '=', 'client_branches.id')
            ->join('clients', 'orders.ingr_shop_id', '=', 'clients.user_id');

        if ($request->fromTime) {
            // dd(99);
            $query->where('orders.created_at', '>=', Carbon::parse($request->fromTime)->format('Y-m-d H:i:s'));
        }

        if ($request->toTime) {
            // dd($request->all());
            $query->where('orders.created_at', '<=', Carbon::parse($request->toTime)->format('Y-m-d H:i:s'));
        }

        $report = $query
            ->groupBy('orders.ingr_branch_id', 'client_branches.name', 'clients.price_order')
            // ->get()
        ;

        return DataTables::of($report)
            ->addColumn('branch_id', fn($row) => $row->branch_id)
            ->addColumn('branch_name', fn($row) => $row->branch_name)
            ->addColumn('total_orders', fn($row) => $row->total_orders)
            ->addColumn('total_drivers', fn($row) => $row->total_drivers)
            ->addColumn('price_order', fn($row) => $row->price_order)
            ->addColumn('total_amount', fn($row) => $row->total_amount)
            ->make(true);
    }

    public function exportDispatchersAssignReportData(Request $request)
    {
        // dd($request->all());
        abort_unless(auth()->user()->hasPermissionTo('dispatcher_assign_reports'), 403, 'You do not have permission to view this page.');
        $user = auth()->user();
        $isAdminWithCountry = $user->user_role === UserRole::ADMIN && $user->country_id;

        $reportDataQuery = DB::table('orders as o')
            ->join('users as u', 'o.assigned_by', '=', 'u.id')
            ->join('cities as c', 'o.city', '=', 'c.id')
            ->select(
                'u.id as user_id',
                DB::raw("CONCAT(u.first_name, ' ', u.last_name) as full_name"),
                DB::raw('COUNT(o.assigned_by) as total_orders'),
                DB::raw('SEC_TO_TIME(ROUND(AVG(TIMESTAMPDIFF(SECOND, o.created_at, o.driver_assigned_at)) / COUNT(o.assigned_by))) as avg_assign_time')
            )
            ->groupBy('u.id');

        if ($isAdminWithCountry) {
            $reportDataQuery->whereIn('u.id', function ($query) use ($user) {
                $query->select('user_id')
                    ->from('user_citys')
                    ->join('cities', 'user_citys.city_id', '=', 'cities.id')
                    ->where('cities.country_id', $user->country_id);
            });
        }

        $this->SearchdispatcherAssignReport($request, $reportDataQuery);

        $reportDataQuery->groupBy('u.id', 'full_name');

        $reportDataQuery->orderBy('total_orders', 'desc');

        $data = [];

        $reportDataQuery->chunk(50, function ($dispatcherChunk) use (&$data) {
            $chunkData = [];

            foreach ($dispatcherChunk as $dispatcher) {

                $nestedData['id']   = $dispatcher->user_id;
                $nestedData['name'] = $dispatcher->full_name;

                $nestedData['orders_count'] = $dispatcher->total_orders;
                $nestedData['test']         = $dispatcher->avg_assign_time;

                $chunkData[] = $nestedData;
            }

            $data = array_merge($data, $chunkData);
        });

        return $this->exportDispatcherAssignReportExcel($data);
    }

    // public function exportDispatcherAssignReportExcel($data)
    // {
    //     // abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');

    //     $filename = uniqid() . '.xlsx';
    //     Excel::store(new DispatchAssignReportExport($data), 'public/' . $filename);
    //     echo "<a id='download' href=" . asset('storage/' . $filename) . " style=' background-color: green;color: white;padding: 14px 25px;text-align: center;text-decoration: none;display: inline-block;'> Download </a> <script> document.getElementById('download').click(); </script><br></br>
    //     <a href=" . url()->previous() . "  style=' background-color: #f44336;color: white;padding: 14px 25px;text-align: center;text-decoration: none;display: inline-block;'> Back </a>
    //     ";
    // }

    public function exportDispatcherAssignReportExcel($data)
    {
        $filename = uniqid() . '.xlsx';

        Excel::store(new DispatchAssignReportExport($data), 'temp/' . $filename);

        $file = new \Illuminate\Http\File(storage_path('app/temp/' . $filename));

        $cdnUrl = $this->upload_excel_file($file, 'reports');

        return response()->json([
            'download_url' => $cdnUrl,
        ]);
    }

    public function citiesSalesreport()
    {
        // abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');

        set_time_limit(-1);

        // abort_unless(auth()->user()->hasPermissionTo('cities_sales_reports'), 403, 'You do not have permission to view this page.');

        return view('admin.pages.reports.citiesSalesreport.index');
    }

    public function getCitiesSalesReportData(Request $request)
    {
        set_time_limit(-1);
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', -1);

        $query = Order::with(['cityData', 'shop.client'])
            ->where('status', OrderStatus::DELIVERED)
            ->where('city', '!=', '0')
            ->whereNotNull('city');

        if ($request->fromtime) {
            $query->where('created_at', '>=', Carbon::parse($request->fromtime)->format('Y-m-d H:i:s'));
        }

        if ($request->totime) {
            $query->where('created_at', '<=', Carbon::parse($request->totime)->format('Y-m-d H:i:s'));
        }

        $orders = $query->select(['id', 'ingr_shop_id', 'ingr_branch_id', 'city', 'driver_id'])->get();

        $results = $orders->groupBy('city')->map(function ($ordersInCity) {
            $city = $ordersInCity->first();

            $clientTotals = [];
            $clientIds    = [];
            $driverIds    = [];

            foreach ($ordersInCity as $order) {
                $client     = optional(optional($order->shop)->client);
                $clientId   = $client?->id;
                $priceOrder = $client?->price_order ?? 0;

                if ($clientId) {
                    $clientTotals[$clientId] = ($clientTotals[$clientId] ?? 0) + $priceOrder;
                    $clientIds[$clientId]    = true;
                }

                if ($order->driver_id) {
                    $driverIds[$order->driver_id] = true;
                }
            }

            return [
                'city_id'       => $city->city,
                'city_name'     => optional($city->cityData)->name,
                'total_orders'  => $ordersInCity->count(),
                'total_amount'  => array_sum($clientTotals),
                'total_clients' => count($clientIds),
                'total_drivers' => count($driverIds),
            ];
        })->values();

        $totalOrdersSum  = $results->sum('total_orders');
        $totalAmountSum  = $results->sum('total_amount');
        $totalCitiesSum  = $results->count();
        $totalDriversSum = $results->sum('total_drivers');
        $totalClientsSum = $results->sum('total_clients');
        $total_UTRSum    = ($totalOrdersSum / $totalDriversSum);

        return DataTables::of($results)
            ->addColumn('city_id', fn($row) => $row['city_id'])
            ->addColumn('city_name', fn($row) => $row['city_name'])
            ->addColumn('total_orders', fn($row) => $row['total_orders'])
            ->addColumn('total_clients', fn($row) => $row['total_clients'])
            ->addColumn('total_drivers', fn($row) => $row['total_drivers'])
            ->addColumn('total_amount', fn($row) => round($row['total_amount'], 2))
            ->addColumn('total_UTR', fn($row) => $row['total_drivers'] > 0 ? round(($row['total_orders'] / $row['total_drivers']), 2) : 0)
            ->addColumn('action', function ($row) {
                return '<div class="d-flex justify-content-center">
                    <a href="#" data-id="' . $row['city_id'] . '"
                        data-bs-toggle="modal"
                        data-bs-target="#operatorReportDetail"
                        class="flex items-center order-driver-btn justify-center w-8 h-8 text-white border rounded-lg border-gray10">
                        <img src="' . asset('new/src/assets/icons/view.svg') . '" alt="View" />
                    </a>
                </div>';
            })
            ->with('total_amount_sum', round($totalAmountSum, 2))
            ->with('total_orders_sum', round($totalOrdersSum, 2))
            ->with('total_cities_sum', $totalCitiesSum)
            ->with('total_clients_sum', $totalClientsSum)
            ->with('total_drivers_sum', $totalDriversSum)
            ->with('total_UTR_sum', round($total_UTRSum, 2))
            ->make(true);
    }

    public function getCitiesSalesReportDataPerclient(Request $request)
    {
        $city_id = $request->city_id;

        $reportQuery = Order::where('orders.city', $city_id)
            ->where('orders.status', OrderStatus::DELIVERED)
            ->where('orders.city', '!=', '0')
            ->whereNotNull('orders.city')
            ->join('cities', 'orders.city', '=', 'cities.id')
            ->join('clients', 'orders.ingr_shop_id', '=', 'clients.user_id')
            ->join('users', 'orders.ingr_shop_id', '=', 'users.id')
            ->select(
                'clients.user_id',
                'users.first_name',
                'users.last_name',
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('orders.city as city_id'),
                DB::raw('COALESCE(clients.price_order, 0) as price_order'),
                DB::raw('COUNT(orders.id) * COALESCE(clients.price_order, 0) as total_amount'),
                DB::raw('COUNT(DISTINCT orders.ingr_shop_id) AS total_shops'),
                DB::raw('COUNT(DISTINCT orders.ingr_branch_id) AS total_branches'),
                DB::raw('COUNT(DISTINCT orders.driver_id) AS total_drivers')
            );

        if (! empty($request->fromtime)) {
            $fromDate = Carbon::parse($request->fromtime)->format('Y-m-d H:i:s');
            $reportQuery->where("orders.created_at", ">=", $fromDate);
        }

        if (! empty($request->totime)) {
            $toDate = Carbon::parse($request->totime)->format('Y-m-d H:i:s');
            $reportQuery->where("orders.created_at", "<=", $toDate);
        }

        $report = $reportQuery
            ->groupBy('clients.user_id', 'orders.city', 'users.first_name', 'users.last_name', 'clients.price_order')
            ->get();

        // Totals
        $totalOrdersSum   = $report->sum('total_orders');
        $totalAmountSum   = $report->sum('total_amount');
        $totalShopsSum    = $report->sum('total_shops');
        $totalBranchesSum = $report->sum('total_branches');
        $totalDriversSum  = $report->sum('total_drivers');

        // Return data
        return DataTables::of($report)
            ->addColumn('clientId', fn($row) => $row->user_id)
            ->addColumn('client', fn($row) => $row->first_name . ' ' . $row->last_name)
            ->addColumn('total_orders', fn($row) => $row->total_orders)
            ->addColumn('price_order', fn($row) => round($row->price_order, 2))
            ->addColumn('total_amount', fn($row) => round($row->total_amount, 2))
            ->addColumn('total_shops', fn($row) => $row->total_shops)
            ->addColumn('total_branches', fn($row) => $row->total_branches)
            ->addColumn('total_drivers', fn($row) => $row->total_drivers)
            ->with('total_amount_sum', round($totalAmountSum, 2))
            ->with('total_orders_sum', round($totalOrdersSum, 2))
            ->with('total_shops_sum', $totalShopsSum)
            ->with('total_branches_sum', $totalBranchesSum)
            ->with('total_drivers_sum', $totalDriversSum)
            ->make(true);
    }
}
