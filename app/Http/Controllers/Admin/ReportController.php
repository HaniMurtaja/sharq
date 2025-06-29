<?php

namespace App\Http\Controllers\Admin;

use App\Enum\OrderStatus;
use App\Enum\ReportStatus;
use App\Enum\UserRole;
use App\Exports\ClientsReportsExport;
use App\Exports\OperatorAssignReportExport;
use App\Exports\OperatorsExport;
use App\Exports\OrdersDataTableExport;
use App\Exports\OrdersExport;
use App\Exports\OrdersReportsExport;
use App\Exports\VehiclesExport;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Client;
use App\Models\Operator;
use App\Models\OperatorReportHistory;
use App\Models\OperatorStatus;
use App\Models\Order;
use App\Models\OrderDriver;
use App\Models\OrderLog;
use App\Models\OrderNote;
use App\Models\OrderReport;
use App\Models\ReportHistory;
use App\Models\ReportTemplat;
use App\Models\User;
use App\Models\Vehicle;
use App\Settings\GeneralSettings;
use App\Traits\FileHandler;
use App\Traits\TimeTrait;
use Box\Spout\Common\Type;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ReportController extends Controller
{

    use TimeTrait, FileHandler;

    public function calculateDistance($pickUpLat, $pickUpLng, $lat, $lng)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        if ($pickUpLat == null || $pickUpLng == null) {
            return 0;
        }
        if ($lat == null || $lng == null) {
            return 0;
        }
        $latFrom = deg2rad($pickUpLat);
        $lngFrom = deg2rad($pickUpLng);
        $latTo   = deg2rad($lat);
        $lngTo   = deg2rad($lng);

        $latDelta = $latTo - $latFrom;
        $lngDelta = $lngTo - $lngFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($latFrom) * cos($latTo) *
            sin($lngDelta / 2) * sin($lngDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // Distance in kilometers
    }

    public function orderList(Request $request)
    {
        // dd(9);
        abort_unless(auth()->user()->hasPermissionTo('orders_report'), 403, 'You do not have permission to view this page.');

        $columns = ['id', 'tracking_id', 'ref_id', 'created_date', 'order_comments', 'delivery_distance_km', 'integration_channel', 'delivered_by', 'customer_name', 'customer_phone', 'shop_id', 'shop', 'shop_phone', 'branch', 'branch_area', 'branch_id', 'driver_name', 'driver_id', 'driver_group', 'area', 'city', 'order_type', 'arrived_to_pickup_time', 'pickup_time', 'arrived_to_dropoff_time', 'delivered_time', 'pickup_distance_km', 'driver_distance_km', 'cancelled_by', 'assign_by', 'cancel_request', 'driver_cancel_request', 'delivery_charge', 'order_value', 'order_status', 'payment_type', 'business_date', 'assign', 'preparation_time', 'assignment_time', 'accepted_time'];

        $limit       = $request->input('length', 10);
        $start       = $request->input('start', 0);
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir    = $request->input('order.0.dir', 'asc');

        $order = $columns[$orderColumn] ?? $columns[0];

        $query = Order::query();

        DispatcherController::ActionRoleQueryWhere($query, null, null);
        // dd($query->count());
        if (auth()->user()->user_role == UserRole::CLIENT) {
            $query->where('ingr_shop_id', auth()->id());
        }

        if (auth()->user()->user_role == UserRole::BRANCH) {
            $query->where('ingr_branch_id', auth()->user()->branch_id);
        }



        if (request()->filled('client_branch')) {
            $query->where('ingr_branch_id', request()->client_branch);
        }

        if (request()->fromtime != '') {
            $fromtime = date("Y-m-d H:i:s", strtotime(request()->fromtime));
            $query->where("created_at", ">=", $fromtime);
        }
        if (request()->totime != '') {
            $totimetime = date("Y-m-d H:i:s", strtotime(request()->totime));
            $query->where("created_at", "<=", $totimetime);
        }
        // dd($request->status);
        if ($request->filled('status') && $request->status[0] != -1) {

            $query->whereIn('status', $request->input('status'));
        }

        // if ($request->filled('date')) {
        //     // dd($request->date);
        //     $dates = explode(' - ', $request->input('date'));

        //     $startDate = Carbon::createFromFormat('m/d/Y', $dates[0])->startOfDay();
        //     $endDate = Carbon::createFromFormat('m/d/Y', $dates[1])->endOfDay();

        //     $query->whereBetween('created_at', [$startDate, $endDate]);
        // }

        if ($request->filled('date')) {
            // Dump the received date for debugging
            // dd($request->date);

            // Split the input date string
            $dates = explode(' to ', $request->input('date'));

            // Ensure the format matches the input
            $startDate = Carbon::createFromFormat('Y-m-d', $dates[0])->startOfDay();
            $endDate   = Carbon::createFromFormat('Y-m-d', $dates[1])->endOfDay();

            // Apply the date range filter to the query
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        $totalData     = $query->count();
        $totalFiltered = $totalData;

        if ($request->filled('search.value')) {
            $search = $request->input('search.value');
            $query->where(function (Builder $query) use ($search) {
                $query->where('client_order_id', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%')
                    ->orWhere('customer_phone', 'like', '%' . $search . '%')
                    ->orWhere('customer_name', 'like', '%' . $search . '%')
                    ->orWhereHas('shop', function ($query) use ($search) {
                        $query->where('users.first_name', 'like', '%' . $search . '%')
                            ->orWhere('users.phone', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('driver', function ($query) use ($search) {
                        $query->whereHas('driver', function ($query) use ($search) {
                            $query->whereRaw("CONCAT(users.first_name, ' ', users.last_name) LIKE ?", ['%' . $search . '%'])
                                ->orWhere('users.phone', 'like', '%' . $search . '%');
                        });
                    })
                    ->orWhereHas('branch', function ($query) use ($search) {
                        $query->where('client_branches.name', 'like', '%' . $search . '%')
                            ->orWhere('client_branches.phone', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('branchIntegration', function ($query) use ($search) {
                        $query->where('client_branches.name', 'like', '%' . $search . '%')
                            ->orWhere('client_branches.phone', 'like', '%' . $search . '%');
                    });
            });
        }

        $totalFiltered = $query->count();
        $orders        = $query->offset($start)
            ->limit($limit)
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [];

        foreach ($orders as $order) {
            // dd($order->order_number);
            $driver_order = $order->drivers()->orderBy('created_at', 'desc')->first();
            $driver       = $driver_order?->driver;

            $assign             = OrderLog::where('order_id', $order->id)->where('status', OrderStatus::PENDINE_DRIVER_ACCEPTANCE)->orderBy('created_at', 'desc')->first()?->created_at->format('H:i:s');
            $assign_date        = OrderLog::where('order_id', $order->id)->where('status', OrderStatus::PENDINE_DRIVER_ACCEPTANCE)->orderBy('created_at', 'desc')->first()?->created_at->format('Y-m-d h:i a');
            $accept_date        = OrderLog::where('order_id', $order->id)->where('status', OrderStatus::DRIVER_ACCEPTED)->orderBy('created_at', 'desc')->first()?->created_at->format('Y-m-d h:i a');
            $arrive_branch_date = OrderLog::where('order_id', $order->id)->where('status', OrderStatus::ARRIVED_PICK_UP)->orderBy('created_at', 'desc')->first()?->created_at->format('Y-m-d h:i a');
            $recive_date        = OrderLog::where('order_id', $order->id)->where('status', OrderStatus::PICKED_UP)->orderBy('created_at', 'desc')->first()?->created_at->format('Y-m-d h:i a');
            $arrive_client_date = OrderLog::where('order_id', $order->id)->where('status', OrderStatus::ARRIVED_TO_DROPOFF)->orderBy('created_at', 'desc')->first()?->created_at->format('Y-m-d h:i a');
            $delivery_date      = OrderLog::where('order_id', $order->id)->where('status', OrderStatus::DELIVERED)->orderBy('created_at', 'desc')->first()?->created_at->format('Y-m-d h:i a');
            $delivery_by        = OrderLog::where('order_id', $order->id)->where('status', OrderStatus::DELIVERED)->orderBy('created_at', 'desc')->first()?->driver;
            $order_comments     = OrderNote::where('order_id', $order->id)->pluck('message')->toArray();

            $branch          = $order->branch ?? $order->branchIntegration;
            $branchLatitude  = $branch->lat ?? 0;
            $branchLongitude = $branch->lng ?? 0;

            $distance = $this->calculateDistance(
                $branchLatitude,
                $branchLongitude,
                $order->lat,
                $order->lng
            );

            $delivery_distance_km = $distance;

            $driverDistance = (float) ($driver_order?->distance ?? 0);

            $orderDistance = (float) ($delivery_distance_km ?? 0);

            $totalDistance = $driverDistance + $orderDistance;

            $cancelled_by          = OrderLog::where('order_id', $order->id)->where('status', OrderStatus::CANCELED)->orderBy('created_at', 'desc')->first()?->description;
            $assign_by             = OrderLog::where('order_id', $order->id)->where('status', OrderStatus::PENDINE_DRIVER_ACCEPTANCE)->orderBy('created_at', 'desc')->first()?->description;
            $cancel_request        = OrderLog::where('order_id', $order->id)->where('status', OrderStatus::PENDING_CLIENT_ORDER_CANCELLATION)->orderBy('created_at', 'desc')->first()?->description;
            $driver_cancel_request = OrderLog::where('order_id', $order->id)->where('status', OrderStatus::PENDING_ORDER_CANCELLATION)->orderBy('created_at', 'desc')->first()?->description;
            // 'shop' => $this->shop ?? $this->branchIntegration?->client ,

            // 'branch' => $this->branch ?? $this->branchIntegration,
            $integration_channel = $order->shop?->client?->integration?->name ?? $order->branchIntegration?->client?->client?->integration?->name;

            $nestedData['tracking_id']         = $order->id;
            $nestedData['order_number']        = $order->order_number;
            $nestedData['ref_id']              = $order->order_number;
            $nestedData['integration_channel'] = $integration_channel ?? '---';

            $nestedData['cancelled_by']          = $cancelled_by;
            $nestedData['cancel_request']        = $cancel_request;
            $nestedData['driver_cancel_request'] = $driver_cancel_request;
            $nestedData['assign_by']             = $assign_by;

            $nestedData['shop_id']                 = $order->shop?->id ?? $order->branchIntegration?->client?->id;
            $nestedData['account_owner']           = $order->shop?->full_name ?? $order->branchIntegration?->client?->full_name;
            $nestedData['delivered_by']            = $delivery_by?->full_name;
            $nestedData['order_comments']          = $order_comments;
            $nestedData['customer_name']           = $order->customer_name;
            $nestedData['customer_phone']          = $order->customer_phone;
            $nestedData['client_account_number']   = $order->shop?->client?->account_number ?? $order->branchIntegration?->client?->client?->account_number;
            $nestedData['shop']                    = $order->shop?->first_name ?? $order->branchIntegration?->client?->first_name;
            $nestedData['shop_phone']              = $order->shop?->phone ?? $order->branchIntegration?->client?->phone;
            $nestedData['branch']                  = $order->branch?->name ?? $order->branchIntegration?->name;
            $nestedData['branch_id']               = $order->branch?->id ?? $order->branchIntegration?->id;
            $nestedData['branch_area']             = $order->branch?->area?->name ?? $order->branchIntegration?->area?->name;
            $nestedData['driver_name']             = $driver?->full_name;
            $nestedData['driver_id']               = $driver?->id;
            $nestedData['driver_group']            = $driver?->operator?->group?->name;
            $nestedData['area']                    = $order->city;
            $nestedData['city']                    = $order->branch?->city?->name;
            $nestedData['order_type']              = 'Delivery';
            $nestedData['fail_reason']             = '---';
            $nestedData['cancel_reason']           = '---';
            $nestedData['order_status']            = $order->status->getLabel();
            $nestedData['delivery_distance_km']    = $delivery_distance_km . 'km';
            $nestedData['assignment_time']         = $assign_date;
            $nestedData['accepted_time']           = $accept_date;
            $nestedData['arrived_to_pickup_time']  = $arrive_branch_date;
            $nestedData['pickup_time']             = $recive_date;
            $nestedData['arrived_to_dropoff_time'] = $arrive_client_date;
            $nestedData['delivered_time']          = $delivery_date;
            $nestedData['pickup_distance_km']      = $driver_order?->distance ? $driver_order?->distance . ' km' : '';
            $nestedData['driver_distance_km']      = $totalDistance . " km";
            $nestedData['order_value']             = $order->value + $order->service_fees;
            $nestedData['delivery_charge']         = $order->service_fees;
            $nestedData['payment_type']            = $order->payment_type ? $order->payment_type->getLabel() : '';
            $nestedData['preparation_time']        = $order->preparation_time;
            $nestedData['business_date']           = $order->created_at->format('Y-m-d');
            $nestedData['assign']                  = $assign;
            $nestedData['created_date']            = $order->created_at->format('Y-m-d h:i a');
            $data[]                                = $nestedData;
        }

        $json_data = [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data,
        ];

        return response()->json($json_data);
    }

    public function saveHistory(Request $request)
    {
        // dd($request->all());
        $startDate = '';
        $endDate   = '';

        // if ($request->filled('date')) {

        //     $dates = explode(' - ', $request->input('date'));

        //     $startDate = Carbon::createFromFormat('m/d/Y', $dates[0])->startOfDay();
        //     $endDate = Carbon::createFromFormat('m/d/Y', $dates[1])->endOfDay();
        // }

        if ($request->filled('date')) {
            // Dump the received date for debugging
            // dd($request->date);

            // Split the input date string
            $dates = explode(' to ', $request->input('date'));

            // Ensure the format matches the input
            $startDate = Carbon::createFromFormat('Y-m-d', $dates[0])->startOfDay();
            $endDate   = Carbon::createFromFormat('Y-m-d', $dates[1])->endOfDay();
        }

        $client_id = null;
        $branch_id = $request->client_branch;
        if (auth()->user()->user_role == UserRole::CLIENT) {
            $client_id = auth()->id();
        }

        if (auth()->user()->user_role == UserRole::BRANCH) {
            $branch_id = auth()->id();
        }

        ReportHistory::create([
            'name'           => 'order_report',
            'status'         => ReportStatus::PENDING,
            'order_statuses' => $request->status,
            'date_from'      => $startDate,
            'date_to'        => $endDate,
            'client_id'      => $client_id,
            'branch_id'      => $branch_id,
            'type'           => $request->type,
            'template_id'    => $request->reportSelect,

        ]);
        return response()->json(['success']);
    }

    public function historyList(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('orders_report'), 403, 'You do not have permission to view this page.');

        $columns = ['id', 'name', 'status', 'type', 'from', 'to', 'created_at'];

        $limit       = $request->input('length', 10);
        $start       = $request->input('start', 0);
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir    = $request->input('order.0.dir', 'asc');

        $order = $columns[$orderColumn] ?? $columns[0];

        $histories = ReportHistory::query();

        if (auth()->user()->user_role == UserRole::CLIENT) {
            $histories->where('client_id', auth()->id());
        }

        if (auth()->user()->user_role == UserRole::BRANCH) {
            $histories->where('branch_id', auth()->user()->branch_id);
        }

        $totalData     = $histories->count();
        $totalFiltered = $totalData;

        // if (request()->filled('client_branch')) {
        //     $query->where('ingr_branch_id', request()->client_branch);
        // }

        if (empty($request->input('search.value'))) {
            $histories = $histories->offset($start)
                ->limit($limit)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $search = $request->input('search.value');

            $histories = $histories->Where('name', 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy('created_at', 'desc')
                ->get();

            $totalFiltered = $histories->Where('name', 'LIKE', "%{$search}%")
                ->count();
        }

        $data = [];
        if (! empty($histories)) {
            foreach ($histories as $history) {
                $nestedData['id']         = $history->id;
                $nestedData['name']       = $history->name;
                $nestedData['status']     = $history->status->getLabel();
                $nestedData['type']       = $history->type;
                $nestedData['from']       = $history->date_from;
                $nestedData['to']         = $history->date_to;
                $nestedData['created_at'] = $history->created_at->format('Y:m:d');
                $data[]                   = $nestedData;
            }
        }

        $json_data = [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data,
        ];

        return response()->json($json_data);
    }

    public function exportOrders(Request $request, $id)
    {
        set_time_limit(-1);

        abort_unless(auth()->user()->hasPermissionTo('orders_report'), 403, 'You do not have permission to view this page.');
        // dd($request->all());
        $report = ReportHistory::findOrFail($id);
        // dd($report);
        $statuses    = $report->order_statuses;
        $date_from   = $report->date_from;
        $date_to     = $report->date_to;
        $type_report = $report->type;
        $client_id   = $report->client_id;
        $branch_id   = $report->branch_id;
        $ordersQuery = Order::query();

        // Filter by statuses

        if ($client_id) {
            $ordersQuery->where('ingr_shop_id', $client_id);
        }

        if ($branch_id) {

            $ordersQuery->where('ingr_branch_id', $branch_id);
        }

        if ($statuses && $statuses[0] != -1 && $statuses[0] != '') {
            $ordersQuery->whereIn('status', $statuses);
        }

        // Filter by date range
        if (! empty($date_from) && ! empty($date_to)) {
            $startDate = \Carbon\Carbon::parse($date_from)->startOfDay();
            $endDate   = \Carbon\Carbon::parse($date_to)->endOfDay();
            $ordersQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Check if there are matching orders

        if ($ordersQuery->count() === 0) {
            return back()->with('error', 'No orders found for the given criteria.');
        }

        $reportTemplate = ReportTemplat::find($report->template_id);
        $columns        = $reportTemplate?->columns;

        if (! $columns) {
            return back()->with('error', 'Report template columns are missing.');
        }

        if ($type_report === 'csv') {
            return $this->streamAsCsv($ordersQuery, $columns);
        } elseif ($type_report === 'excel') {

            $data = [];

            $ordersQuery->chunk(50, function ($ordersChunk) use (&$data, $columns) {
                foreach ($ordersChunk as $order) {
                    $data[] = $this->getNestedOrderData($order, $columns);
                }
            });
            return $this->streamAsCsv($ordersQuery, $columns);
            // return $this->exportAsExcel($data, $columns);
        }

        return response()->json(['error' => 'Invalid export type selected'], 400);
    }

    // public function exportOrders(Request $request, $id)
    // {
    //     // dd($id);
    //     abort_unless(auth()->user()->hasPermissionTo('view_export_reports'), 403, 'You do not have permission to view this page.');

    //     $report = ReportHistory::findOrFail($id);
    //     // dd($report);
    //     $statuses = $report->order_statuses;
    //     $date_from = $report->date_from;
    //     $date_to = $report->date_to;
    //     $type_report = $report->type;
    //     // dd($status);

    //     $orders = Order::query();

    //     if ($statuses && $statuses[0] != -1 && $statuses[0] != '') {

    //         $orders->whereIn('status', $statuses);
    //     }

    //     if ($date_from != "" && $date_to != "") {
    //         // dd(33);
    //         $date_from = \Carbon\Carbon::parse($date_from)->startOfDay();
    //         $date_to = \Carbon\Carbon::parse($date_to)->endOfDay();

    //         $orders->whereBetween('created_at', [$date_from, $date_to]);
    //     }

    //     $orders = $orders->orderBy('created_at', 'desc')->get();

    //     $reportTemplate = ReportTemplat::find($report->template_id);
    //     $columns = $reportTemplate?->columns;
    //     // dd($orders);
    //     if ($orders->isEmpty()) {
    //         return back()->with('error', 'No orders found for the given criteria.');
    //     }

    //     $data = [];
    //     foreach ($orders as $order) {
    //         $nestedData = $this->getNestedOrderData($order, $columns);
    //         $data[] = $nestedData;
    //     }
    //     // dd($data);

    //     if ($type_report === 'csv') {
    //         return $this->exportAsCsv($data, $columns);
    //     } elseif ($type_report === 'excel') {
    //         return $this->exportAsExcel($data, $columns);
    //     }

    //     return response()->json(['error' => 'Invalid export type selected'], 400);
    // }

    public function driverReports()
    {
        abort_unless(auth()->user()->hasPermissionTo('view_export_reports'), 403, 'You do not have permission to view this page.');
        abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');

        $drivers = Operator::all();
        return view('admin.pages.driver-reports', compact('drivers'));
    }

    public function getReportsList(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('view_export_reports'), 403, 'You do not have permission to view this page.');
        abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');

        $columns = ['id', 'driver', 'order_number', 'reason', 'details', 'created_at'];

        $totalData     = OrderReport::count();
        $totalFiltered = $totalData;

        $limit       = $request->input('length', 10);
        $start       = $request->input('start', 0);
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir    = $request->input('order.0.dir', 'asc');

        $order = $columns[$orderColumn] ?? $columns[0];

        $query = OrderReport::query();

        if ($request->filled('drivers')) {
            $query->whereIn('driver_id', $request->input('drivers'));
        }

        if ($request->filled('date')) {
            // dd($request->date);
            $dates = explode(' - ', $request->input('date'));

            $startDate = Carbon::createFromFormat('m/d/Y', $dates[0])->startOfDay();
            $endDate   = Carbon::createFromFormat('m/d/Y', $dates[1])->endOfDay();

            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $totalFiltered = $query->count();

        $orders = $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $orderDir)
            ->get();

        $data = [];

        foreach ($orders as $order) {
            // dd($order);
            $nestedData['id']           = $order->id;
            $nestedData['driver']       = $order->driver->full_name;
            $nestedData['order_number'] = $order->order->order_number;
            $nestedData['reason']       = $order->reason;
            $nestedData['details']      = $order->description;

            $nestedData['created_at'] = $order->created_at?->format('Y:m:d');
            $data[]                   = $nestedData;
        }

        $json_data = [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data,
        ];

        return response()->json($json_data);
    }

    public function brandReports()
    {
        abort_unless(auth()->user()->hasPermissionTo('client_reports'), 403, 'You do not have permission to view this page.');
        abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');

        $clients = Client::all();
        return view('admin.pages.reports.clients', compact('clients'));
    }

    public function getBrandsList(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('client_reports'), 403, 'You do not have permission to view this page.');
        abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11AM.');

        $columns       = ['id', 'name', 'branch_count', 'orders_count', 'average_time'];
        $totalData     = Client::count();
        $totalFiltered = $totalData;

        $limit       = $request->input('length', 10);
        $start       = $request->input('start', 0);
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir    = $request->input('order.0.dir', 'asc');
        $order       = $columns[$orderColumn] ?? $columns[0];

        // Query the Client with necessary filters
        $query = Client::query()
            ->withCount(['orders' => function ($query) use ($request) {
                if ($request->filled('date')) {
                    $dates     = explode(' - ', $request->input('date'));
                    $startDate = Carbon::createFromFormat('m/d/Y', $dates[0])->startOfDay();
                    $endDate   = Carbon::createFromFormat('m/d/Y', $dates[1])->endOfDay();
                    // Apply whereBetween on orders created_at date
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            }]);

        $user = auth()->user();

        if ($user->user_role === UserRole::ADMIN && $user->country_id) {


            $query->whereHas('client.city', function ($sub) use ($user) {
                $sub->where('country_id', $user->country_id);
            });
        }


        // Eager load orders and their related orderLogs without filtering logs by date
        $query->with(['orders' => function ($orderQuery) use ($request) {
            if ($request->filled('date')) {
                $dates     = explode(' - ', $request->input('date'));
                $startDate = Carbon::createFromFormat('m/d/Y', $dates[0])->startOfDay();
                $endDate   = Carbon::createFromFormat('m/d/Y', $dates[1])->endOfDay();
                $orderQuery->whereBetween('created_at', [$startDate, $endDate]);
            }
            $orderQuery->with(['orderLogs' => function ($logQuery) {
                $logQuery->selectRaw('order_id, TIMESTAMPDIFF(MINUTE, MIN(CASE WHEN status = 17 THEN created_at END), MAX(CASE WHEN status = 9 THEN created_at END)) as delivery_time,
                TIMESTAMPDIFF(MINUTE, MIN(CASE WHEN status = 16 THEN created_at END), MAX(CASE WHEN status = 6 THEN created_at END)) as waiting_time ')
                    ->groupBy('order_id');
            }]);
        }]);

        // Search Filter
        if ($request->filled('search.value')) {
            $searchTerm = $request->input('search.value');
            $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$searchTerm}%"])
                ->orWhere('phone', 'LIKE', "%{$searchTerm}%");
        }

        // Filter by Driver
        if ($request->filled('driver')) {
            $query->whereHas('orders', function ($q) use ($request) {
                $q->where('driver_id', $request->input('driver'));
            });
        }

        $totalFiltered = $query->count();

        // Fetch clients with pagination and sorting
        $clients = $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $orderDir)
            ->get();

        // Prepare data for response
        $data = [];
        foreach ($clients as $client) {
            $nestedData['id']           = $client->id;
            $nestedData['name']         = $client->full_name;
            $nestedData['branch_count'] = $client->branches()->count();
            $nestedData['orders_count'] = $client->orders_count;

            // Calculate average delivery time
            $totalTime        = 0;
            $orderCount       = 0;
            $waitingTotalTime = 0;
            foreach ($client->orders as $order) {
                $logTime = $order->orderLogs->first()->delivery_time ?? 0; // Get the delivery time from orderLogs
                $totalTime += $logTime;

                $waitingLogTime = $order->orderLogs->first()->waiting_time ?? 0;
                $waitingTotalTime += $waitingLogTime;
                $orderCount++;
            }
            $nestedData['average_time'] = ($orderCount > 0 ? round($totalTime / $orderCount, 2) : 0) . ' Minutes';
            $nestedData['waiting_time'] = ($orderCount > 0 ? round($waitingTotalTime / $orderCount, 2) : 0) . ' Minutes';

            $data[] = $nestedData;
        }

        // Return JSON response
        $json_data = [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data,
        ];

        return response()->json($json_data);
    }

    public function driverStatusList(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('view_export_reports'), 403, 'You do not have permission to view this page.');
        abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');

        $columns = ['id', 'driver_name', 'driver_phone', 'status', 'created_at', 'avg_online_hours'];

        $limit       = $request->input('length', 10);
        $start       = $request->input('start', 0);
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir    = $request->input('order.0.dir', 'asc');

        $order = $columns[$orderColumn] ?? $columns[0];

        $query = OperatorStatus::query();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('date')) {
            $dates     = explode(' - ', $request->input('date'));
            $startDate = Carbon::createFromFormat('m/d/Y', $dates[0])->startOfDay();
            $endDate   = Carbon::createFromFormat('m/d/Y', $dates[1])->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($request->filled('operator_name')) {
            // Filter by operator's name (first_name field)
            $query->whereHas('operator', function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->input('operator_name') . '%');
            });
        }

        $totalFiltered = $query->count();

        $orders = $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $orderDir)
            ->get();

        $operatorStatuses = $query->orderBy('created_at')->get();

        $operatorOnlineTimes = [];
        $operatorSessions    = [];

        foreach ($operatorStatuses as $status) {
            $operatorId = $status->operator_id;

            if (! isset($operatorOnlineTimes[$operatorId])) {
                $operatorOnlineTimes[$operatorId] = 0;
                $operatorSessions[$operatorId]    = 0;
            }

            if ($status->status == 1) {
                $lastOnlineTime[$operatorId] = Carbon::parse($status->created_at);
            } elseif ($status->status == 4 && isset($lastOnlineTime[$operatorId])) {
                $onlineDuration = $lastOnlineTime[$operatorId]->diffInSeconds(Carbon::parse($status->created_at));
                if ($onlineDuration > 0) {
                    $operatorOnlineTimes[$operatorId] += $onlineDuration;
                    $operatorSessions[$operatorId]++;
                }
                unset($lastOnlineTime[$operatorId]);
            }
        }

        foreach ($operatorOnlineTimes as $operatorId => $totalTime) {
            if ($operatorSessions[$operatorId] > 0) {
                $averageTimeInSeconds             = $totalTime / $operatorSessions[$operatorId];
                $operatorOnlineTimes[$operatorId] = gmdate("H:i:s", $averageTimeInSeconds); // Format to H:i:s
            } else {
                $operatorOnlineTimes[$operatorId] = '00:00:00'; // No sessions found
            }
        }
        $data = [];
        foreach ($orders as $order) {
            $nestedData['id']               = $order->operator->id;
            $nestedData['driver_name']      = $order->operator->first_name;
            $nestedData['driver_phone']     = $order->operator->phone;
            $nestedData['status']           = $order->status->getLabel();
            $nestedData['created_at']       = $order->created_at?->format('Y:m:d h:m:s');
            $nestedData['avg_online_hours'] = $operatorOnlineHours[$operatorId] ?? '00:00:00';
            $data[]                         = $nestedData;
        }

        $json_data = [
            "draw"            => intval($request->input('draw')),
            'recordsTotal'    => $query->count(),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data,
        ];

        return response()->json($json_data);
    }

    public function getDriverOrdersData(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('view_export_reports'), 403, 'You do not have permission to view this page.');
        abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');

        $columns = ['id', 'driver_name', 'driver_phone', 'total_orders'];

        $limit       = $request->input('length', 10);
        $start       = $request->input('start', 0);
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir    = $request->input('order.0.dir', 'asc');

        $order = $columns[$orderColumn] ?? $columns[0];
        $query = Operator::query();

        // Apply the conditional date filter to the count subquery
        $dateFilter = function ($query) use ($request) {
            if ($request->filled('date')) {
                $dates     = explode(' - ', $request->input('date'));
                $startDate = Carbon::createFromFormat('m/d/Y', $dates[0])->startOfDay();
                $endDate   = Carbon::createFromFormat('m/d/Y', $dates[1])->endOfDay();
                $query->whereBetween('orders.created_at', [$startDate, $endDate]);
            }
        };

        $query->withCount([
            'orders as total_orders' => function ($query) use ($dateFilter) {
                $dateFilter($query);
            },
        ]);

        if ($request->filled('operator_name')) {
            $query->where('first_name', 'like', '%' . $request->input('operator_name') . '%');
        }

        $operators = $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $orderDir)
            ->get();
        $data = [];
        foreach ($operators as $operator) {
            $nestedData['id']           = $operator->id;
            $nestedData['driver_name']  = $operator->first_name;
            $nestedData['driver_phone'] = $operator->phone;
            $nestedData['total_orders'] = $operator->total_orders;
            $data[]                     = $nestedData;
        }

        $json_data = [
            'recordsTotal'    => $query->count(), // Total records without pagination
            'recordsFiltered' => $query->count(), // Total records after filtering
            "data"            => $data,
        ];

        return response()->json($json_data);
    }

    private function getTotalOrdersForDriver($operatorId)
    {
        return Order::whereHas('drivers', function ($q) use ($operatorId) {
            $q->where('id', $operatorId);
        })->count();
    }

    public function billings()
    {
        abort_unless(auth()->user()->hasPermissionTo('view_export_reports'), 403, 'You do not have permission to view this page.');
        // abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');

        $operators = Operator::whereHas('wallet', function ($query) {
            $query->where('balance', '!=', '0.00');
        })->get();
        // dd($operators);
        $total_balance           = 0;
        $total_balance_after_tax = 0;
        $settings                = new GeneralSettings();
        foreach ($operators as $operator) {
            $total_balance += $operator->wallet->balance;
            $total_balance_after_tax += $total_balance - $settings->taxes['income_tax'];
        }
        return view('admin.pages.reports.billings', compact(['total_balance', 'total_balance_after_tax']));
    }

    public function getBillingsData(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('view_export_reports'), 403, 'You do not have permission to view this page.');
        // abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');

        $columns       = ['id', 'driver', 'order_count', 'service_fees', 'operator_fees', 'balance', 'after_tax'];
        $totalData     = Operator::count();
        $totalFiltered = $totalData;
        $limit         = $request->input('length');
        $start         = $request->input('start');
        $order         = $columns[$request->input('order.0.column')];
        $dir           = $request->input('order.0.dir');
        if (empty($request->input('search.value'))) {
            $branches = Operator::offset($start)->limit($limit)->orderBy($order, $dir)->get();
        } else {
            $search   = $request->input('search.value');
            $branches = Operator::whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                ->orWhere('phone', 'LIKE', "%{$search}%")->offset($start)->limit($limit)->orderBy($order, $dir)->get();
            $totalFiltered = Operator::whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                ->orWhere('phone', 'LIKE', "%{$search}%")->count();
        }
        $data = [];
        if (! empty($branches)) {
            foreach ($branches as $branch) {

                $order_count = OrderDriver::where('driver_id', $branch->id)
                    ->distinct('order_id')
                    ->count('order_id');

                $avgServiceFees = OrderDriver::where('order_drivers.driver_id', $branch->id)
                    ->join('orders', 'order_drivers.order_id', '=', 'orders.id')
                    ->avg('orders.service_fees');

                $settings = new GeneralSettings();

                $after_tax = $branch->wallet?->balance - $settings->taxes['income_tax'] . 'SAR';
                if ($branch->wallet?->balance == 0) {
                    $after_tax = '0.00 SAR';
                }
                $nestedData['id']            = $branch->id;
                $nestedData['driver']        = $branch->full_name;
                $nestedData['order_count']   = $order_count;
                $nestedData['service_fees']  = $avgServiceFees;
                $nestedData['operator_fees'] = $branch->operator?->order_value;
                $nestedData['balance']       = $branch->wallet?->balance ? $branch->wallet?->balance . 'SAR' : '0.00 SAR';
                $nestedData['after_tax']     = $after_tax;
                $data[]                      = $nestedData;
            }
        }
        $json_data = [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data,
        ];
        return response()->json($json_data);
    }

    public function getCodBillingsData(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('view_export_reports'), 403, 'You do not have permission to view this page.');
        // abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');

        $columns       = ['id', 'driver', 'balance'];
        $totalData     = Operator::count();
        $totalFiltered = $totalData;
        $limit         = $request->input('length');
        $start         = $request->input('start');
        $order         = $columns[$request->input('order.0.column')];
        $dir           = $request->input('order.0.dir');
        if (empty($request->input('search.value'))) {
            $branches = Operator::offset($start)->limit($limit)->orderBy($order, $dir)->get();
        } else {
            $search   = $request->input('search.value');
            $branches = Operator::whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                ->orWhere('phone', 'LIKE', "%{$search}%")->offset($start)->limit($limit)->orderBy($order, $dir)->get();
            $totalFiltered = Operator::whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                ->orWhere('phone', 'LIKE', "%{$search}%")->count();
        }
        $data = [];
        if (! empty($branches)) {
            foreach ($branches as $branch) {

                $nestedData['id']     = $branch->id;
                $nestedData['driver'] = $branch->full_name;

                $nestedData['balance'] = $branch->wallet?->balance ? $branch->wallet?->balance . 'SAR' : '0.00 SAR';

                $data[] = $nestedData;
            }
        }
        $json_data = [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data,
        ];
        return response()->json($json_data);
    }

    public function operatorReports()
    {
        abort_unless(auth()->user()->hasPermissionTo('operators_reports'), 403, 'You do not have permission to view this page.');
        abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');

        return view('admin.pages.reports.operator-reports');
    }

    public function saveOperatorReportHistory(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('operators_reports'), 403, 'You do not have permission to view this page.');
        abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');

        $startDate = '';
        $endDate   = '';
        // dd($request->date);
        if ($request->filled('date')) {
            $dates = explode(' - ', $request->input('date'));

            $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
            $endDate   = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
        }

        OperatorReportHistory::create([
            'model'       => $request->type,
            'report_name' => $request->report_name,
            'date_from'   => $startDate,
            'date_to'     => $endDate,
        ]);

        return response()->json(['success']);
    }

    public function getoperatorReports(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('operators_reports'), 403, 'You do not have permission to view this page.');
        abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');

        $columns = ['id', 'name', 'status', 'type', 'from', 'to', 'created_at'];

        $totalData     = OperatorReportHistory::count();
        $totalFiltered = $totalData;

        $limit       = $request->input('length', 10);
        $start       = $request->input('start', 0);
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir    = $request->input('order.0.dir', 'asc');

        $order = $columns[$orderColumn] ?? $columns[0];

        if (empty($request->input('search.value'))) {
            $histories = OperatorReportHistory::offset($start)
                ->limit($limit)
                ->orderBy($order, $orderDir)
                ->get();
        } else {
            $search = $request->input('search.value');

            $histories = OperatorReportHistory::Where('report_name', 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $orderDir)
                ->get();

            $totalFiltered = OperatorReportHistory::Where('report_name', 'LIKE', "%{$search}%")
                ->count();
        }

        $data = [];
        if (! empty($histories)) {
            foreach ($histories as $history) {
                $nestedData['id']         = $history->id;
                $nestedData['name']       = $history->report_name;
                $nestedData['status']     = 'Success';
                $nestedData['type']       = 'xlsx';
                $nestedData['from']       = $history->date_from;
                $nestedData['to']         = $history->date_to;
                $nestedData['model']      = $history->model;
                $nestedData['created_at'] = $history->created_at->format('Y:m:d');
                $data[]                   = $nestedData;
            }
        }

        $json_data = [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data,
        ];

        return response()->json($json_data);
    }

    public function exportVehicle(Request $request, $id)
    {
        // dd($id);
        abort_unless(auth()->user()->hasPermissionTo('view_export_reports'), 403, 'You do not have permission to view this page.');
        abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');

        $report = OperatorReportHistory::findOrFail($id);
        // dd($report);

        $date_from = $report->date_from;
        $date_to   = $report->date_to;

        $vehicles = Vehicle::query();

        if ($date_from != "" && $date_to != "") {
            // dd(33);
            $date_from = \Carbon\Carbon::parse($date_from)->startOfDay();
            $date_to   = \Carbon\Carbon::parse($date_to)->endOfDay();

            $vehicles->whereBetween('created_at', [$date_from, $date_to]);
        }

        $vehicles = $vehicles->get();

        // dd($orders);
        if ($vehicles->isEmpty()) {
            return back()->with('error', 'No vehicles found for the given criteria.');
        }

        $fileName = $report->report_name;

        return Excel::download(new VehiclesExport($vehicles), $fileName . '.xlsx');

        return back()->withErrors(['type_report' => 'Invalid report type provided.']);
    }

    public function exportOperators(Request $request, $id)
    {
        // dd($id);
        abort_unless(auth()->user()->hasPermissionTo('view_export_reports'), 403, 'You do not have permission to view this page.');
        abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');

        $report = OperatorReportHistory::findOrFail($id);
        // dd($report);

        $date_from = $report->date_from;
        $date_to   = $report->date_to;

        $operators = Operator::query();

        $user = auth()->user();
        $isAdminWithCountry = $user->user_role === UserRole::ADMIN && $user->country_id;



        if ($isAdminWithCountry) {
            $operators->whereHas('cities.city', function ($q) use ($user) {
                $q->where('country_id', $user->country_id);
            });
        }


        if ($date_from != "" && $date_to != "") {
            // dd(33);
            $date_from = \Carbon\Carbon::parse($date_from)->startOfDay();
            $date_to   = \Carbon\Carbon::parse($date_to)->endOfDay();

            $operators->whereBetween('created_at', [$date_from, $date_to]);
        }

        $operators = $operators->get();

        // dd($orders);
        if ($operators->isEmpty()) {
            return back()->with('error', 'No operators found for the given criteria.');
        }

        $fileName = $report->report_name;

        return Excel::download(new OperatorsExport($operators), $fileName . '.xlsx');

        return back()->withErrors(['type_report' => 'Invalid report type provided.']);
    }

    public function saveReportTemplate(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('view_export_reports'), 403, 'You do not have permission to view this page.');
        abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');

        $request->validate([
            'report_name_creator' => 'required',
            'type_report_creator' => 'required',
        ]);
        // dd($request->all());
        $columns = $request->except('_token', 'report_name_creator', 'id', 'type_report_creator');
        if ($request->id) {
            $template = ReportTemplat::findOrFail($request->id);
            $template->update([
                'name'          => $request->report_name_creator,
                'template_type' => $request->type_report_creator,
                'columns'       => $columns,
            ]);
        } else {
            ReportTemplat::create([
                'name'          => $request->report_name_creator,
                'template_type' => $request->type_report_creator,
                'columns'       => $columns,
            ]);
        }

        return response()->json(['message' => 'Template saved successfully!'], 200);

        // dd($columns);

    }

    public function getTemplateName()
    {
        // dd(0);
        abort_unless(auth()->user()->hasPermissionTo('view_export_reports'), 403, 'You do not have permission to view this page.');
        // abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');

        $templates = ReportTemplat::all();
        return response()->json(['templates' => $templates]);
    }

    public function deleteReportTemplate($id)
    {
        abort_unless(auth()->user()->hasPermissionTo('view_export_reports'), 403, 'You do not have permission to view this page.');
        abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');

        $template = ReportTemplat::findOrFail($id);
        $template->delete();
        return response()->json('deleted success');
    }

    public function editReportTemplate($id)
    {
        abort_unless(auth()->user()->hasPermissionTo('view_export_reports'), 403, 'You do not have permission to view this page.');
        abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');

        $template = ReportTemplat::findOrFail($id);
        return response()->json(['template' => $template]);
    }

    public function getTemplateReportData(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('view_export_reports'), 403, 'You do not have permission to view this page.');
        // abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');

        $columns = ['id', 'name'];

        $totalData     = ReportTemplat::count();
        $totalFiltered = $totalData;

        $limit       = $request->input('length', 10);
        $start       = $request->input('start', 0);
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir    = $request->input('order.0.dir', 'asc');

        $order = $columns[$orderColumn] ?? $columns[0];

        if (empty($request->input('search.value'))) {
            $histories = ReportTemplat::offset($start)
                ->limit($limit)
                ->orderBy($order, $orderDir)
                ->get();
        } else {
            $search = $request->input('search.value');

            $histories = ReportTemplat::Where('name', 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $orderDir)
                ->get();

            $totalFiltered = ReportTemplat::Where('name', 'LIKE', "%{$search}%")
                ->count();
        }

        $data = [];
        if (! empty($histories)) {
            foreach ($histories as $history) {
                $nestedData['id']   = $history->id;
                $nestedData['name'] = $history->name;

                $data[] = $nestedData;
            }
        }

        $json_data = [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data,
        ];

        return response()->json($json_data);
    }

    // public function exportOrdersDataTable(Request $request)
    // {
    //     // dd($request->all());
    //     abort_unless(auth()->user()->hasPermissionTo('view_export_reports'), 403, 'You do not have permission to view this page.');

    //     $reportTemplateId = $request->input('reportSelect-export-form');
    //     $reportTemplate = ReportTemplat::find($reportTemplateId);

    //     $statusString = $request->input('status-export-form')[0];

    //     // Split the comma-separated string into an array
    //     $statuses = explode(',', $statusString);
    //     // dd($statuses);
    //     if (!$reportTemplate) {
    //         return response()->json(['error' => 'Invalid report template selected'], 400);
    //     }

    //     $columns = $reportTemplate->columns;

    //     $query = Order::query();
    //     // dd($request->all());
    //     if ($request->filled('date-export-form')) {
    //         // dd($request->filled('date-export-form'));

    //         $dates = explode(' to ', $request->input('date-export-form'));

    //         // Ensure the format matches the input
    //         $startDate = Carbon::createFromFormat('Y-m-d', $dates[0])->startOfDay();
    //         $endDate = Carbon::createFromFormat('Y-m-d', $dates[1])->endOfDay();
    //         // dd($startDate, $endDate);
    //         $query->whereBetween('created_at', [$startDate, $endDate]);
    //     }
    //     // dd($statuses);
    //     if ($statuses && $statuses[0] != -1 && $statuses[0] != '') {

    //         $query->whereIn('status', $statuses);
    //     }

    //     $orders = $query->orderBy('created_at', 'desc')->get();
    //     // dd($orders);
    //     $data = [];
    //     foreach ($orders as $order) {
    //         $nestedData = $this->getNestedOrderData($order, $columns);
    //         $data[] = $nestedData;
    //     }
    //     // dd($data);
    //     $exportType = $request->input('type-export-form');
    //     if ($exportType === 'csv') {
    //         return $this->exportAsCsv($data, $columns);
    //     } elseif ($exportType === 'excel') {
    //         return $this->exportAsExcel($data, $columns);
    //     }

    //     return response()->json(['error' => 'Invalid export type selected'], 400);
    // }

    public function exportOrdersDataTable(Request $request)
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');

        // Check permissions
        // dd($request->all());
        abort_unless(auth()->user()->hasPermissionTo('view_export_reports'), 403, 'You do not have permission to view this page.');

        $reportTemplateId = $request->input('reportSelect-export-form');
        $reportTemplate   = ReportTemplat::find($reportTemplateId);

        if (! $reportTemplate) {
            return response()->json(['error' => 'Invalid report template selected'], 400);
        }

        $statusString = $request->input('status-export-form')[0];
        $statuses     = explode(',', $statusString); // Convert comma-separated string to array

        $columns = $reportTemplate->columns;

        $query = Order::query();

        DispatcherController::ActionRoleQueryWhere($query, null, null);
        // dd($query->count());
        if (auth()->user()->user_role == UserRole::CLIENT) {
            $query->where('ingr_shop_id', auth()->id());
        }

        if (auth()->user()->user_role == UserRole::BRANCH) {
            $query->where('ingr_branch_id', auth()->user()->branch_id);
        }

        if (request()->filled('client_branch_form')) {
            $query->where('ingr_branch_id', request()->client_branch_form);
        }

        if (request()->fromtime != '') {
            $fromtime = Carbon::parse(request()->fromtime);
            $query->where("created_at", ">=", $fromtime);
        }
        if (request()->totime != '') {
            $totimetime = Carbon::parse(request()->totime);
            $query->where("created_at", "<=", $totimetime);
        }
        // Filter by date range

        if ($request->filled('date-export-form')) {

            $dates     = explode(' to ', $request->input('date-export-form'));
            $startDate = Carbon::parse($dates[0]);
            $endDate   = Carbon::parse($dates[1]);
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($statuses && $statuses[0] != -1 && $statuses[0] != '') {
            $query->whereIn('status', $statuses);
        }
        $exportType = $request->input('type-export-form');
        if ($exportType === 'csv') {

            return $this->streamAsCsv($query, $columns);
        } elseif ($exportType === 'excel') {
            // dd(7);
            $data = [];
            $query->chunk(50, function ($ordersChunk) use (&$data, $columns) {
                foreach ($ordersChunk as $order) {
                    $data[] = $this->getNestedOrderData($order, $columns);
                }
            });
            //ini_set('memory_limit', '-1');
            return $this->exportExcel($data, $exportType, $columns);
            // return $this->streamAsCsv($query, $columns);
            // return $this->exportAsExcel($query, $columns);
        }
        return response()->json(['error' => 'Invalid export type selected'], 400);
    }

    public static function exportExcel($data, $exportType, $columns)
    {

        $filename = uniqid() . '.xlsx';
        Excel::store(new OrdersReportsExport($data, $columns), 'public/' . $filename);
        echo "<a id='download' href=" . asset('storage/' . $filename) . " style=' background-color: green;color: white;padding: 14px 25px;text-align: center;text-decoration: none;display: inline-block;'> Download </a> <script> document.getElementById('download').click(); </script><br></br>
<a href=" . url()->previous() . "  style=' background-color: #f44336;color: white;padding: 14px 25px;text-align: center;text-decoration: none;display: inline-block;'> Back </a>
";
    }

    protected function streamAsCsv($query, $columns)
    {
        $fileName = 'orders_export_' . time() . rand(20300, 999999999) . '.csv';

        return response()->streamDownload(function () use ($query, $columns) {
            $handle = fopen('php://output', 'w');

            // Add UTF-8 BOM for Arabic support
            fwrite($handle, "\xEF\xBB\xBF");

            // Add column headers
            fputcsv($handle, $columns);
            // Stream orders in chunks
            $query->chunk(50, function ($ordersChunk) use ($handle, $columns) {
                foreach ($ordersChunk as $order) {
                    $nestedData = $this->getNestedOrderData($order, $columns);

                    // Ensure data is properly encoded in UTF-8
                    $encodedData = array_map(function ($value) {
                        return mb_convert_encoding($value, 'UTF-8', 'auto');
                    }, $nestedData);

                    fputcsv($handle, $encodedData);
                }
            });

            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // protected function exportAsExcel($query, $columns)
    // {
    //     $fileName = 'orders_export_' . now()->format('Ymd_His') . '.xlsx';

    //     return response()->streamDownload(function () use ($query, $columns) {
    //         // Create an XLSX writer instance
    //         $writer = WriterFactory::create(Type::XLSX);
    //         $writer->openToFile('php://output'); // Open the file for streaming

    //         // Add UTF-8 BOM for Arabic support
    //         // In this case, UTF-8 is natively handled by Spout, so BOM is unnecessary.

    //         // Add column headers
    //         $headerRow = WriterEntityFactory::createRowFromArray($columns);
    //         $writer->addRow($headerRow);

    //         // Stream orders in chunks
    //         $query->chunk(50, function ($ordersChunk) use ($writer, $columns) {
    //             foreach ($ordersChunk as $order) {
    //                 $nestedData = $this->getNestedOrderData($order, $columns);

    //                 // Ensure data is properly encoded in UTF-8
    //                 $encodedData = array_map(function ($value) {
    //                     return mb_convert_encoding($value, 'UTF-8', 'auto');
    //                 }, $nestedData);

    //                 $row = WriterEntityFactory::createRowFromArray($encodedData);
    //                 $writer->addRow($row);
    //             }
    //         });

    //         $writer->close(); // Close the writer and send the file
    //     }, $fileName, [
    //         'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    //         'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
    //     ]);
    // }

    private function getNestedOrderData($order, $columns)
    {
        abort_unless(auth()->user()->hasPermissionTo('view_export_reports'), 403, 'You do not have permission to view this page.');
        // dd(6);
        $nestedData   = [];
        $driver_order = $order->drivers()->orderBy('created_at', 'desc')->first();
        $driver       = $driver_order?->driver;

        // dd(9);

        // $assign_date = OrderLog::where('order_id', $order->id)->where('status', OrderStatus::PENDINE_DRIVER_ACCEPTANCE)->latest()->first();
        // $accept_date = OrderLog::where('order_id', $order->id)->where('status', OrderStatus::DRIVER_ACCEPTED)->latest()->first();
        // $arrive_branch_date = OrderLog::where('order_id', $order->id)->where('status', OrderStatus::ARRIVED_PICK_UP)->latest()->first();
        // $recive_date = OrderLog::where('order_id', $order->id)->where('status', OrderStatus::PICKED_UP)->latest()->first();
        // $arrive_client_date = OrderLog::where('order_id', $order->id)->where('status', OrderStatus::ARRIVED_TO_DROPOFF)->latest()->first();
        // $delivery_date = OrderLog::where('order_id', $order->id)->where('status', OrderStatus::DELIVERED)->latest()->first();
        // $delivery_by = OrderLog::where('order_id', $order->id)->where('status', OrderStatus::DELIVERED)->latest()->first()?->driver;
        // $order_comments = OrderNote::where('order_id', $order->id)->pluck('message')->toArray();

        $orderLogs = OrderLog::where('order_id', $order->id)
            ->whereIn('status', [
                OrderStatus::PENDINE_DRIVER_ACCEPTANCE,
                OrderStatus::DRIVER_ACCEPTED,
                OrderStatus::ARRIVED_PICK_UP,
                OrderStatus::PICKED_UP,
                OrderStatus::ARRIVED_TO_DROPOFF,
                OrderStatus::DELIVERED,
                OrderStatus::CANCELED,
                OrderStatus::PENDING_CLIENT_ORDER_CANCELLATION,
                OrderStatus::PENDING_ORDER_CANCELLATION,
            ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('status');

        $assign_date        = ($orderLogs[OrderStatus::PENDINE_DRIVER_ACCEPTANCE->value] ?? collect())->first() ?? null;
        $accept_date        = ($orderLogs[OrderStatus::DRIVER_ACCEPTED->value] ?? collect())->first() ?? null;
        $arrive_branch_date = ($orderLogs[OrderStatus::ARRIVED_PICK_UP->value] ?? collect())->first() ?? null;
        $recive_date        = ($orderLogs[OrderStatus::PICKED_UP->value] ?? collect())->first() ?? null;
        $arrive_client_date = ($orderLogs[OrderStatus::ARRIVED_TO_DROPOFF->value] ?? collect())->first() ?? null;
        $delivery_date      = ($orderLogs[OrderStatus::DELIVERED->value] ?? collect())->first() ?? null;
        $delivery_by        = $delivery_date?->driver;

        $cancelled_by          = ($orderLogs[OrderStatus::CANCELED->value] ?? collect())->first()?->description;
        $assign_by             = ($orderLogs[OrderStatus::PENDINE_DRIVER_ACCEPTANCE->value] ?? collect())->first()?->description;
        $cancel_request        = ($orderLogs[OrderStatus::PENDING_CLIENT_ORDER_CANCELLATION->value] ?? collect())->first()?->description;
        $driver_cancel_request = ($orderLogs[OrderStatus::PENDING_ORDER_CANCELLATION->value] ?? collect())->first()?->description;

        $order_comments = OrderNote::where('order_id', $order->id)
            ->select('message')
            ->get()
            ->pluck('message')
            ->toArray();

        $branch          = $order->branch ?? $order->branchIntegration;
        $branchLatitude  = $branch->lat ?? 0;
        $branchLongitude = $branch->lng ?? 0;

        $distance = 0;

        $delivery_distance_km = $distance;

        $driverDistance = (float) ($driver_order?->distance ?? 0);

        $orderDistance = (float) ($delivery_distance_km ?? 0);

        $totalDistance = $driverDistance + $orderDistance;

        $integration_channel = $order->shop?->client?->integration?->name ?? $order->branchIntegration?->client?->client?->integration?->name;

        // $cancelled_by = OrderLog::where('order_id', $order->id)->where('status', OrderStatus::CANCELED)->orderBy('created_at', 'desc')->first()?->description;
        // $assign_by = OrderLog::where('order_id', $order->id)->where('status', OrderStatus::PENDINE_DRIVER_ACCEPTANCE)->orderBy('created_at', 'desc')->first()?->description;
        // $cancel_request = OrderLog::where('order_id', $order->id)->where('status', OrderStatus::PENDING_CLIENT_ORDER_CANCELLATION)->orderBy('created_at', 'desc')->first()?->description;
        // $driver_cancel_request = OrderLog::where('order_id', $order->id)->where('status', OrderStatus::PENDING_ORDER_CANCELLATION)->orderBy('created_at', 'desc')->first()?->description;

        // Loop through each column and assign data
        // 'shop' => $this->shop ?? $this->branchIntegration?->client ,

        // 'branch' => $this->branch ?? $this->branchIntegration,
        foreach ($columns ?? [] as $key => $header) {
            switch ($key) {
                case 'tracking_id':
                    $nestedData[$key] = $order->id;
                    break;
                case 'order_number':
                    $nestedData[$key] = $order->order_number;
                    break;
                case 'ref_id':
                    $nestedData[$key] = $order->client_order_id_string;
                    break;
                case 'integration_channel':
                    $nestedData[$key] = $integration_channel ?? '---';
                    break;

                case 'cancelled_by':
                    $nestedData[$key] = $cancelled_by;
                    break;

                case 'assign_by':
                    $nestedData[$key] = $assign_by;
                    break;

                case 'cancel_request':
                    $nestedData[$key] = $cancel_request;
                    break;

                case 'driver_cancel_request':
                    $nestedData[$key] = $driver_cancel_request;
                    break;

                case 'shop_id':
                    $nestedData[$key] = $order->shop?->id ?? $order->branchIntegration?->client?->id;
                    break;
                case 'account_owner':
                    $nestedData[$key] = $order->shop?->full_name ?? $order->branchIntegration?->client?->full_name;
                    break;
                case 'delivered_by':
                    $nestedData[$key] = $delivery_by?->full_name;
                    break;
                case 'order_comments':
                    $nestedData[$key] = implode(', ', $order_comments);
                    break;
                case 'customer_name':
                    $nestedData[$key] = $order->customer_name;
                    break;
                case 'customer_phone':
                    $nestedData[$key] = $order->customer_phone;
                    break;

                case 'client_account_number':
                    $nestedData[$key] = $order->shop?->client?->account_number ?? $order->branchIntegration?->client?->client?->account_number;
                    break;

                case 'shop':
                    $nestedData[$key] = $order->shop?->first_name ?? $order->branchIntegration?->client?->first_name;
                    break;
                case 'shop_phone':
                    $nestedData[$key] = $order->shop?->phone ?? $order->branchIntegration?->client?->phone;
                    break;
                case 'branch':
                    $nestedData[$key] = $order->branch?->name ?? $order->branchIntegration?->name;
                    break;
                case 'branch_id':
                    $nestedData[$key] = $order->branch?->id ?? $order->branchIntegration?->id;
                    break;

                case 'driver_distance_km':
                    $nestedData[$key] = $totalDistance . " km";
                    break;

                case 'branch_area':
                    $nestedData[$key] = $order->branch?->area?->name ?? $order->branchIntegration?->area?->name;
                    break;
                case 'driver_name':
                    $nestedData[$key] = $driver?->full_name;
                    break;
                case 'driver_id':
                    $nestedData[$key] = $driver?->id;
                    break;
                case 'driver_group':
                    $nestedData[$key] = $driver?->operator?->group?->name;
                    break;
                case 'area':
                    $nestedData[$key] = $order->city;
                    break;
                case 'city':
                    $nestedData[$key] = $order->branch?->city?->name ?? $order->branchIntegration?->city?->name;
                    break;
                case 'order_type':
                    $nestedData[$key] = 'Delivery';
                    break;
                case 'order_status':
                    $nestedData[$key] = $order->status->getLabel();
                    break;
                case 'delivery_distance_km':
                    $nestedData[$key] = $delivery_distance_km . 'km';
                    break;
                case 'assignment_time':
                    $nestedData[$key] = " " . $assign_date?->created_at->format('d/m/Y h:i:s a');
                    break;
                case 'accepted_time':
                    $nestedData[$key] = " " . $accept_date?->created_at->format('d/m/Y h:i:s a');
                    break;
                case 'arrived_to_pickup_time':
                    $nestedData[$key] = " " . $arrive_branch_date?->created_at->format('d/m/Y h:i:s a');
                    break;
                case 'pickup_time':
                    $nestedData[$key] = " " . $recive_date?->created_at->format('d/m/Y h:i:s a');
                    break;
                case 'arrived_to_dropoff_time':
                    $nestedData[$key] = " " . $arrive_client_date?->created_at->format('d/m/Y h:i:s a');
                    break;
                case 'delivered_time':
                    $nestedData[$key] = " " . $delivery_date?->created_at->format('d/m/Y h:i:s a');
                    break;
                case 'pickup_distance_km':
                    $nestedData[$key] = $driver_order?->distance ? $driver_order?->distance . ' km' : '';
                    break;

                case 'fail_reason':
                    $nestedData[$key] = '---';
                    break;

                case 'cancel_reason':
                    $nestedData[$key] = '---';
                    break;

                case 'order_value':
                    $nestedData[$key] = $order->value + $order->service_fees;
                    break;
                case 'delivery_charge':
                    $nestedData[$key] = $order->service_fees;
                    break;
                case 'payment_type':
                    $nestedData[$key] = $order->payment_type ? $order->payment_type->getLabel() : '';
                    break;
                case 'preparation_time':
                    $nestedData[$key] = $order->preparation_time;
                    break;
                case 'business_date':
                    $nestedData[$key] = $order->created_at ? " " . $order->created_at->format('Y-m-d') : '';
                    break;

                case 'assign':
                    $nestedData[$key] = " " . $assign_date?->created_at->format('d/m/Y h:i:s a');
                    break;
                case 'created_date':

                    $nestedData[$key] = $order->created_at ? " " . $order->created_at->format('d/m/Y h:i:s a') : '';
                    break;
                default:
                    $nestedData[$key] = $order->$key ?? '';
                    break;
            }
        }

        return $nestedData;
    }

    private function exportAsCsv($data, $columns)
    {
        abort_unless(auth()->user()->hasPermissionTo('view_export_reports'), 403, 'You do not have permission to view this page.');

        $filename = "orders_" . rand(99999999, 33333333333) . date('Y-m-d') . time() . ".csv";
        $handle   = fopen('php://output', 'w');

        // Set the headers for the download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Write the header row (column names)
        fputcsv($handle, array_values($columns));

        // Write the data rows
        foreach ($data as $row) {

            fputcsv($handle, $row);
        }

        fclose($handle);
        exit();
    }

    // Helper function to check if a value is a date
    private function isDate($value)
    {
        if (empty($value)) {
            return false;
        }
        // Check if the value can be interpreted as a valid date
        return (bool) strtotime($value);
    }

    // private function exportAsExcel($data, $columns)
    // {
    //     // dd($columns);
    //     abort_unless(auth()->user()->hasPermissionTo('view_export_reports'), 403, 'You do not have permission to view this page.');

    //     $filename = 'orders_' . date('Y-m-d') . '.xlsx';
    //     // $fileName = 'orders_export_' . now()->format('Ymd_His') . '.csv';

    //     return response()->streamDownload(function () use ($data, $columns) {
    //         $filename = 'orders_' . date('Y-m-d') . '.xlsx';
    //         Excel::download(new OrdersDataTableExport($data, $columns), $filename);
    //     }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);

    //     // return Excel::download(new OrdersDataTableExport($data, $columns), $filename);
    // }

    private function exportAsExcel($data, $columns)
    {
        // dd($columns);
        abort_unless(auth()->user()->hasPermissionTo('view_export_reports'), 403, 'You do not have permission to view this page.');

        $filename = 'orders_' . date('Y-m-d') . '.xlsx';

        return Excel::download(new OrdersDataTableExport($data, $columns), $filename);
    }

    public function exportAllOrdersDataTable(Request $request)
    {

        abort_unless(auth()->user()->hasPermissionTo('view_export_reports'), 403, 'You do not have permission to view this page.');
        $query = Order::query();

        if (auth()->user()->hasRole('Client')) {
            $query->where('ingr_shop_id', auth()->id());
        }

        $status = $request->input('status-export-form');

        if ($request->filled('status') && $request->status[0] != -1 && $request->status[0] != null) {
            $query->whereIn('status', $request->input('status'));
        }

        if ($request->filled('date-export-form')) {
            $dates = explode(' to ', $request->input('date-export-form'));

            // Ensure the format matches the input
            $startDate = Carbon::createFromFormat('Y-m-d', $dates[0])->startOfDay();
            $endDate   = Carbon::createFromFormat('Y-m-d', $dates[1])->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($request->filled('client-export-form') && ! auth()->user()->hasRole('Client')) {

            $query->where('ingr_shop_id', $request->input('client-export-form'));
        }

        if ($request->filled('driver-export-form')) {
            $query->whereHas('drivers', function ($q) use ($request) {
                $q->where('order_drivers.driver_id', $request->input('driver-export-form'));
            });
        }

        if ($request->filled('search-export-form')) {
            $search = $request->input('search-export-form');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                    ->orWhere('ingr_shop_id', 'LIKE', "%{$search}%");
            });
        }

        $orders = $query

            ->orderBy('created_at', 'desc')
            ->get();
        return Excel::download(new OrdersExport($orders), 'orders.xlsx');
    }
    public function reportCitysOld()
    {
        ini_set('max_execution_time', '-1');

        if (request()->fromtime != '') {
            $fromDate = date("Y-m-d H:i:s", strtotime(request()->fromtime));
            // $query->where("orders.created_at", ">=", $fromtime);
        }
        if (request()->totime != '') {
            $toDate = date("Y-m-d H:i:s", strtotime(request()->totime));
            // $query->where("orders.created_at", "<=", $totimetime);
        }
        $fromDate = Carbon::parse(request()->input('from_date')); // Example: "2025-02-10 03:00:00"
        $toDate   = Carbon::parse(request()->input('to_date'));   // Example: "2025-02-10 07:00:00"

        $reportQuery = Order::where('status', OrderStatus::DELIVERED)->select(
            'cities.name as city',
            DB::raw('COUNT(DISTINCT orders.driver_id) as number_of_drivers'),
            DB::raw('COUNT(orders.id) as number_of_orders'),
            DB::raw('TIME_FORMAT(SEC_TO_TIME(AVG(TIMESTAMPDIFF(SECOND, orders.created_at, orders.driver_assigned_at))), "%H:%i:%s") as DriverAssigned'),
            DB::raw('TIME_FORMAT(SEC_TO_TIME(AVG(TIMESTAMPDIFF(SECOND, orders.created_at, orders.driver_accepted_time))), "%H:%i:%s") as DriverAcceptance'),
            DB::raw('TIME_FORMAT(SEC_TO_TIME(AVG(TIMESTAMPDIFF(SECOND, orders.driver_accepted_time, orders.arrived_to_pickup_time))), "%H:%i:%s") as DriverArrival'),
            DB::raw('TIME_FORMAT(SEC_TO_TIME(AVG(TIMESTAMPDIFF(SECOND, orders.arrived_to_pickup_time, orders.picked_up_time))), "%H:%i:%s") as DriverWaiting'),
            DB::raw('TIME_FORMAT(SEC_TO_TIME(AVG(TIMESTAMPDIFF(SECOND, orders.created_at, orders.delivered_at))), "%H:%i:%s") as DriverDeliver')
        )
            ->join('cities', 'orders.city', '=', 'cities.id');

        if (request()->fromtime != '') {
            $fromDate = date("Y-m-d H:i:s", strtotime(request()->fromtime));
            $reportQuery->where("orders.created_at", ">=", $fromDate);
        }
        if (request()->totime != '') {
            $toDate = date("Y-m-d H:i:s", strtotime(request()->totime));
            $reportQuery->where("orders.created_at", "<=", $toDate);
        }
        $citys    = $reportQuery->groupBy('cities.name')->get();
        $fromTime = Carbon::parse(request()->fromtime);
        $toTime   = Carbon::parse(request()->totime);

        $numberOfDays = $toTime->diffInDays($fromTime);
        if ($numberOfDays == 0) {
            $numberOfDays = 1;
        }
        //dd( $citys );
        // dd( $numberOfDays );
        return view('admin.pages.reports.citys.index', compact(['citys', 'numberOfDays']));
    }
    public function reportCitysOldWorking()
    {
        ini_set('max_execution_time', '-1');

        if (request()->fromtime != '') {
            $fromDate = date("Y-m-d H:i:s", strtotime(request()->fromtime));
            // $query->where("orders.created_at", ">=", $fromtime);
        }
        if (request()->totime != '') {
            $toDate = date("Y-m-d H:i:s", strtotime(request()->totime));
            // $query->where("orders.created_at", "<=", $totimetime);
        }
        $fromDate = Carbon::parse(request()->input('from_date')); // Example: "2025-02-10 03:00:00"
        $toDate   = Carbon::parse(request()->input('to_date'));   // Example: "2025-02-10 07:00:00"

        // Build your query to get the raw average seconds and counts
        $reportQuery = Order::where('status', OrderStatus::DELIVERED)
            ->select(
                'cities.name as city',
                DB::raw('COUNT(DISTINCT orders.driver_id) as number_of_drivers'),
                DB::raw('COUNT(orders.id) as total_orders'),
                // Return average differences as seconds
                DB::raw('AVG(TIMESTAMPDIFF(SECOND, orders.created_at, orders.driver_assigned_at)) as DriverAssignedSeconds'),
                DB::raw('AVG(TIMESTAMPDIFF(SECOND, orders.created_at, orders.driver_accepted_time)) as DriverAcceptanceSeconds'),
                DB::raw('AVG(TIMESTAMPDIFF(SECOND, orders.driver_accepted_time, orders.arrived_to_pickup_time)) as DriverArrivalSeconds'),
                DB::raw('AVG(TIMESTAMPDIFF(SECOND, orders.arrived_to_pickup_time, orders.picked_up_time)) as DriverWaitingSeconds'),
                DB::raw('AVG(TIMESTAMPDIFF(SECOND, orders.created_at, orders.delivered_at)) as DriverDeliverSeconds')
            )
            ->join('cities', 'orders.city', '=', 'cities.id');

        // Apply filters based on request
        if (request()->fromtime != '') {
            $fromDate = date("Y-m-d H:i:s", strtotime(request()->fromtime));
            $reportQuery->where("orders.created_at", ">=", $fromDate);
        }
        if (request()->totime != '') {
            $toDate = date("Y-m-d H:i:s", strtotime(request()->totime));
            $reportQuery->where("orders.created_at", "<=", $toDate);
        }

        // Group by city
        $citys = $reportQuery->groupBy('cities.name')->get();

        // Calculate the number of days from the request time range
        $fromTime     = Carbon::parse(request()->fromtime);
        $toTime       = Carbon::parse(request()->totime);
        $numberOfDays = $toTime->diffInDays($fromTime);
        if ($numberOfDays == 0) {
            $numberOfDays = 1; // Prevent division by zero
        }

        // Process each city record to calculate daily averages
        foreach ($citys as $city) {

            // Daily average orders count
            $city->daily_orders = $city->total_orders / $numberOfDays;

            // Divide each raw average seconds by numberOfDays and format as H:i:s
            $city->daily_DriverAssigned   = gmdate("H:i:s", $city->DriverAssignedSeconds / $numberOfDays);
            $city->daily_DriverAcceptance = gmdate("H:i:s", $city->DriverAcceptanceSeconds / $numberOfDays);
            $city->daily_DriverArrival    = gmdate("H:i:s", $city->DriverArrivalSeconds / $numberOfDays);
            $city->daily_DriverWaiting    = gmdate("H:i:s", $city->DriverWaitingSeconds / $numberOfDays);
            $city->daily_DriverDeliver    = gmdate("H:i:s", $city->DriverDeliverSeconds / $numberOfDays);
        }
        //   dd( $citys );
        // dd( $numberOfDays );
        return view('admin.pages.reports.citys.index', compact(['citys', 'numberOfDays']));
    }
    public function reportCitys()
    {
        abort_unless(auth()->user()->hasPermissionTo('utr_reports'), 403, 'You do not have permission to view this page.');

        ini_set('max_execution_time', '-1');
        //        abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');

        // if (request()->fromtime != '') {
        //     $fromDate = date("Y-m-d H:i:s", strtotime(request()->fromtime));
        //     // $query->where("orders.created_at", ">=", $fromtime);
        // }
        // if (request()->totime != '') {
        //     $toDate = date("Y-m-d H:i:s", strtotime(request()->totime));
        //     // $query->where("orders.created_at", "<=", $totimetime);
        // }
        // $fromDate = Carbon::parse(request()->input('from_date')); // Example: "2025-02-10 03:00:00"
        // $toDate = Carbon::parse(request()->input('to_date')); // Example: "2025-02-10 07:00:00"

        // Build your query to get the raw average seconds and counts
        $reportQuery = Order::where('status', OrderStatus::DELIVERED)
            ->select(
                'cities.name as city',
                DB::raw('COUNT(DISTINCT orders.driver_id) as number_of_drivers'),
                DB::raw('COUNT(orders.id) as total_orders'),
                // Return average differences as seconds
                DB::raw('AVG(TIMESTAMPDIFF(SECOND, orders.created_at, orders.driver_assigned_at)) as DriverAssignedSeconds'),
                DB::raw('AVG(TIMESTAMPDIFF(SECOND, orders.created_at, orders.driver_accepted_time)) as DriverAcceptanceSeconds'),
                DB::raw('AVG(TIMESTAMPDIFF(SECOND, orders.driver_accepted_time, orders.arrived_to_pickup_time)) as DriverArrivalSeconds'),
                DB::raw('AVG(TIMESTAMPDIFF(SECOND, orders.arrived_to_pickup_time, orders.picked_up_time)) as DriverWaitingSeconds'),
                DB::raw('AVG(TIMESTAMPDIFF(SECOND, orders.created_at, orders.delivered_at)) as DriverDeliverSeconds')
            )
            ->join('cities', 'orders.city', '=', 'cities.id');



        $user = auth()->user();

        if ($user->user_role === UserRole::ADMIN && $user->country_id) {


            $reportQuery->whereHas('cityData', function ($sub) use ($user) {
                $sub->where('country_id', $user->country_id);
            });
        }

        // Apply filters based on request
        if (request()->fromtime != '') {
            $fromDate = date("Y-m-d H:i:s", strtotime(request()->fromtime));
            $reportQuery->where("orders.created_at", ">=", $fromDate);
        }
        if (request()->totime != '') {
            $toDate = date("Y-m-d H:i:s", strtotime(request()->totime));
            $reportQuery->where("orders.created_at", "<=", $toDate);
        }

        // Group by city
        $citys = $reportQuery->groupBy('cities.name')->get();

        // Calculate the number of days from the request time range
        $fromTime     = Carbon::parse(request()->fromtime);
        $toTime       = Carbon::parse(request()->totime);
        $numberOfDays = $toTime->diffInDays($fromTime);
        if ($numberOfDays == 0) {
            $numberOfDays = 1; // Prevent division by zero
        }

        // Process each city record to calculate daily averages
        foreach ($citys as $city) {
            // Daily average orders count
            //$city->daily_orders = $city->total_orders / $numberOfDays;
            $city->daily_orders = $city->total_orders;

            // Calculate average seconds for each metric
            $avgAssignedSeconds   = $city->DriverAssignedSeconds / $numberOfDays;
            $avgAcceptanceSeconds = $city->DriverAcceptanceSeconds / $numberOfDays;
            $avgArrivalSeconds    = $city->DriverArrivalSeconds / $numberOfDays;
            $avgWaitingSeconds    = $city->DriverWaitingSeconds / $numberOfDays;
            $avgDeliverSeconds    = $city->DriverDeliverSeconds / $numberOfDays;

            // Format the seconds into H:i:s strings for display
            $city->daily_DriverAssigned   = gmdate("H:i:s", $avgAssignedSeconds);
            $city->daily_DriverAcceptance = gmdate("H:i:s", $avgAcceptanceSeconds);
            $city->daily_DriverArrival    = gmdate("H:i:s", $avgArrivalSeconds);
            $city->daily_DriverWaiting    = gmdate("H:i:s", $avgWaitingSeconds);
            $city->daily_DriverDeliver    = gmdate("H:i:s", $avgDeliverSeconds);

            // Convert average seconds into minutes (as a float)
            $avgAssignedMinutes   = $avgAssignedSeconds / 60;
            $avgAcceptanceMinutes = $avgAcceptanceSeconds / 60;
            $avgArrivalMinutes    = $avgArrivalSeconds / 60;
            $avgWaitingMinutes    = $avgWaitingSeconds / 60;
            $avgDeliverMinutes    = $avgDeliverSeconds / 60;

            // Determine color for daily_DriverAssigned
            if ($avgAssignedMinutes <= 30) {
                $city->color_DriverAssigned = 'green';
            } elseif ($avgAssignedMinutes <= 40) {
                $city->color_DriverAssigned = 'yellow';
            } else {
                $city->color_DriverAssigned = 'red';
            }

            // Determine color for daily_DriverWaiting
            if ($avgWaitingMinutes <= 10) {
                $city->color_DriverWaiting = 'green';
            } elseif ($avgWaitingMinutes <= 20) {
                $city->color_DriverWaiting = 'yellow';
            } elseif ($avgWaitingMinutes <= 30) {
                $city->color_DriverWaiting = 'red';
            } else {
                $city->color_DriverWaiting = 'red';
            }

            // Determine color for daily_DriverArrival
            if ($avgArrivalMinutes <= 10) {
                $city->color_DriverArrival = 'green';
            } elseif ($avgArrivalMinutes <= 20) {
                $city->color_DriverArrival = 'yellow';
            } elseif ($avgArrivalMinutes <= 30) {
                $city->color_DriverArrival = 'red';
            } else {
                $city->color_DriverArrival = 'red';
            }

            // Determine color for daily_DriverAcceptance
            if ($avgAcceptanceMinutes <= 3) {
                $city->color_DriverAcceptance = 'green';
            } elseif ($avgAcceptanceMinutes <= 6) {
                $city->color_DriverAcceptance = 'yellow';
            } else {
                $city->color_DriverAcceptance = 'red';
            }

            // Determine color for daily_DriverDeliver
            if ($avgDeliverMinutes <= 3) {
                $city->color_DriverDeliver = 'green';
            } elseif ($avgDeliverMinutes <= 6) {
                $city->color_DriverDeliver = 'yellow';
            } else {
                $city->color_DriverDeliver = 'red';
            }
            if ($city->number_of_drivers == 0) {
                // Handle the case where there are no drivers
                $city->utr = 0;
            } else {
                $temp      = ($city->daily_orders / $city->number_of_drivers);
                $city->utr = round($temp, 2);
            }
            // UTR ≥ 12: green, UTR < 12 and ≥ 10: yellow, UTR < 10: red.
            if ($city->utr >= 12) {
                $city->color_utr = 'green';
            } elseif ($city->utr >= 10) {
                $city->color_utr = 'yellow';
            } else {
                $city->color_utr = 'red';
            }
        }
        //dd( $citys );
        // dd( $numberOfDays );
        return view('admin.pages.reports.citys.index', compact(['citys', 'numberOfDays']));
    }

    public function clientReports()
    {
        abort_unless(auth()->user()->hasPermissionTo('accounting_client_reports'), 403, 'You do not have permission to view this page.');
        abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');
        // dd(9);
        $clients = Client::all();
        return view('admin.pages.reports.new-clients', compact('clients'));
    }

    // public function getClientsList(Request $request)
    // {
    //     abort_unless(auth()->user()->hasPermissionTo('accounting_client_reports'), 403, 'You do not have permission to view this page.');
    //     // abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');

    //     $columns = ['id', 'name', 'phone', 'email', 'account_no', 'country', 'city', 'currency', 'parial_pay', 'group', 'note', 'integration_company', 'defualt_prepration_time', 'min_prepration_time'];
    //     $totalData = Client::count();
    //     $totalFiltered = $totalData;

    //     $limit = $request->input('length', 10);
    //     $start = $request->input('start', 0);
    //     $orderColumn = $request->input('order.0.column', 0);
    //     $orderDir = $request->input('order.0.dir', 'asc');
    //     $order = $columns[$orderColumn] ?? $columns[0];

    //     $query = Client::query();

    //     if ($request->filled('date')) {
    //         $dates = explode(' - ', $request->input('date'));
    //         $startDate = Carbon::createFromFormat('m/d/Y', $dates[0])->startOfDay();
    //         $endDate = Carbon::createFromFormat('m/d/Y', $dates[1])->endOfDay();
    //         $query->whereBetween('created_at', [$startDate, $endDate]);
    //     }

    //     if ($request->filled('clients')) {

    //         $clientIds = is_array($request->clients) ? $request->clients : explode(',', $request->clients);
    //         // dd($request->clients);
    //         $query->whereIn('id', $clientIds);
    //     }

    //     if ($request->filled('search.value')) {
    //         $searchTerm = $request->input('search.value');
    //         $query->where(function ($q) use ($searchTerm) {
    //             $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$searchTerm}%"])
    //                 ->orWhere('phone', 'LIKE', "%{$searchTerm}%")
    //                 ->orWhere('email', 'LIKE', "%{$searchTerm}%");
    //         });
    //     }

    //     $totalFiltered = $query->count();

    //     $clients = $query->offset($start)
    //         ->limit($limit)
    //         ->orderBy('created_at', 'desc')
    //         ->select('id', 'first_name', 'last_name', 'phone', 'email')
    //         ->get();

    //     // Return JSON response
    //     $json_data = [
    //         "draw" => intval($request->input('draw')),
    //         "recordsTotal" => intval($totalData),
    //         "recordsFiltered" => intval($totalFiltered),
    //         "data" => $this->getClientsTableColumnsData($clients),
    //     ];

    //     return response()->json($json_data);
    // }

    public function getClientsList(Request $request)
    {

        abort_unless(auth()->user()->hasPermissionTo('accounting_client_reports'), 403, 'You do not have permission to view this page.');
        // abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');
        // Querying the clients and joining the related tables correctly
        $query = Client::with([
            'client.country',
            'client.city',
            'client.clienGroup',
            'client.integration',
        ])
            ->join('clients', 'users.id', '=', 'clients.user_id')             // Join with the clients table
            ->leftJoin('groups', 'clients.client_group_id', '=', 'groups.id') // Join with the groups table
            ->select(
                'users.id',
                'users.email',
                'users.phone',
                'users.first_name',
                'users.last_name',
                'clients.id as client_id',         // Alias for client id
                'clients.default_prepartion_time', // Ensure this is selected explicitly
                'clients.min_prepartion_time',     // Ensure this is selected explicitly
                'clients.account_number',
                'clients.currency',
                'clients.partial_pay',
                'clients.client_group_id',
                'clients.note',
                'clients.integration_id',
                'groups.name as client_group_name'
            );
        // Date filtering

        $user = auth()->user();

        if ($user->user_role === UserRole::ADMIN && $user->country_id) {


            $query->whereHas('client.city', function ($sub) use ($user) {
                $sub->where('country_id', $user->country_id);
            });
        }

        if ($request->filled('date')) {
            $dates     = explode(' - ', $request->input('date'));
            $startDate = Carbon::createFromFormat('m/d/Y', $dates[0])->startOfDay();
            $endDate   = Carbon::createFromFormat('m/d/Y', $dates[1])->endOfDay();
            // dd($startDate, $endDate);
            $query->whereBetween('users.created_at', [$startDate, $endDate]);
        }

        // Filter by specific client IDs if needed
        if ($request->filled('clients')) {
            $clientIds = is_array($request->clients) ? $request->clients : explode(',', $request->clients);
            $query->whereIn('users.id', $clientIds);
        }

        // Search functionality
        if ($request->filled('search.value')) {
            $searchTerm = $request->input('search.value');
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$searchTerm}%"])
                    ->orWhere('phone', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('email', 'LIKE', "%{$searchTerm}%");
            });
        }
        // dd($query->first());
        return DataTables::of($query)
            // ->editColumn('user_id', fn($row) => $row->user_id)
            ->editColumn('name', fn($row) => $row->full_name)
            ->editColumn('phone', fn($row) => $row->phone)
            ->editColumn('email', fn($row) => $row->email)
            ->editColumn('account_no', fn($row) => $row->client?->account_number ?? '---')
            ->editColumn('country', fn($row) => optional($row->client?->country)->name ?? '---')
            ->editColumn('city', fn($row) => optional($row->client?->city)->name ?? '---')
            ->editColumn('currency', fn($row) => optional($row->client?->currency)->getLabel() ?? '---')
            ->editColumn('parial_pay', fn($row) => $row->client?->partial_pay ?? '---')
            ->editColumn('group', fn($row) => $row->client_group_name ?? '---') // Access the alias for the group name
            ->editColumn('note', fn($row) => $row->client?->note ?? '---')
            ->editColumn('integration_company', fn($row) => optional($row->client?->integration)->name ?? '---')
            ->editColumn('defualt_prepration_time', fn($row) => $row->client?->default_prepartion_time ?? 0)
            ->editColumn('min_prepration_time', fn($row) => $row->client?->min_prepartion_time ?? 0)
            ->rawColumns(['name'])
            ->make(true);
    }

    public function exportClientsData(Request $request)
    {
        // dd($request->all());
        abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');

        // ini_set('memory_limit', '-1');
        set_time_limit(-1);
        //        abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');

        // Check permissions
        abort_unless(auth()->user()->hasPermissionTo('view_export_reports'), 403, 'You do not have permission to view this page.');

        $clientIDs = $request->input('clients-export-form')[0];
        $clients   = explode(',', $clientIDs);

        $query = Client::query();

        $user = auth()->user();

        if ($user->user_role === UserRole::ADMIN && $user->country_id) {


            $query->whereHas('client.city', function ($sub) use ($user) {
                $sub->where('country_id', $user->country_id);
            });
        }

        if ($request->filled('date-export-form')) {
            $range = explode(' - ', $request->input('date-export-form'));

            if (count($range) === 2) {
                try {
                    $startDate = Carbon::createFromFormat('m/d/Y', trim($range[0]))->startOfDay();
                    $endDate   = Carbon::createFromFormat('m/d/Y', trim($range[1]))->endOfDay();

                    // dd($startDate, $endDate);
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                } catch (\Exception $e) {
                }
            }
        }

        // dd($query->get());

        if ($clients && $clients[0] != -1 && $clients[0] != '') {
            $query->whereIn('id', $clients);
        }

        $query->orderBy('created_at', 'desc')
            ->select('id', 'first_name', 'last_name', 'phone', 'email');

        $data = [];

        $query->chunk(50, function ($clientsChunk) use (&$data) {
            $chunkData = $this->getClientsTableColumnsData($clientsChunk);
            $data      = array_merge($data, $chunkData);
        });

        return $this->exportClientsExcel($data);
    }

    public function exportClientsExcel($data)
    {
        abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');

        $filename = uniqid() . '.xlsx';

        Excel::store(new ClientsReportsExport($data), 'temp/' . $filename);

        $file = new \Illuminate\Http\File(storage_path('app/temp/' . $filename));

        $cdnUrl = $this->upload_excel_file($file, 'reports');

        return response()->json([
            'download_url' => $cdnUrl,
        ]);
    }

    private function getClientsTableColumnsData($clients)
    {
        abort_unless(auth()->user()->hasPermissionTo('view_export_reports'), 403, 'You do not have permission to view this page.');
        abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');

        $data = [];

        foreach ($clients as $client) {
            if (! $client->client) {
                continue;
            }

            $nestedData['id']    = $client->id;
            $nestedData['name']  = $client->full_name;
            $nestedData['phone'] = $client->phone;
            $nestedData['email'] = $client->email;

            $nestedData['account_no'] = $client->client->account_number ?? '---';
            $nestedData['country']    = $client->client->country?->name ?? '---';
            $nestedData['city']       = $client->client->city?->name ?? '---';
            $nestedData['currency']   = $client->client->currency?->getLabel() ?? '---';
            $nestedData['parial_pay'] = $client->client->partial_pay ?? '---';
            $nestedData['group']      = $client->client->clienGroup?->name ?? '---';
            $nestedData['note']       = $client->client->note ?? '---';

            $nestedData['integration_company']     = $client->client->integration?->name ?? '---';
            $nestedData['defualt_prepration_time'] = $client->client->default_prepartion_time ?? 0;
            $nestedData['min_prepration_time']     = $client->client->min_prepartion_time ?? 0;

            $data[] = $nestedData;
        }

        return $data;
    }

    public function operatorAssignReport()
    {
        abort_unless($this->restrictedTime(), 403, 'The reports are running between 4:15 AM and 11 am.');

        return view('admin.pages.reports.operatorAssignReport.index');
    }

    public function getOperatorOrderSummaryData(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('view_export_reports'), 403, 'You do not have permission to view this page.');
        abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');
        // dd($request->driver_id);
        $query = Client::query()
            ->whereHas('orders', function ($query) use ($request) {
                $query->where('driver_id', $request->driver_id)
                    ->whereNotNull('driver_accepted_time');

                if ($request->filled('fromtime')) {
                    $fromtime = \Carbon\Carbon::createFromFormat('Y/m/d H:i', $request->fromtime)->format('Y-m-d H:i:s');
                    $totime   = $request->filled('totime')
                        ? \Carbon\Carbon::createFromFormat('Y/m/d H:i', $request->totime)->format('Y-m-d H:i:s')
                        : now()->format('Y-m-d H:i:s');

                    $query->whereBetween('created_at', [$fromtime, $totime]);
                }
            })
            ->with(['orders' => function ($query) use ($request) {
                $query->select('id', 'ingr_shop_id', 'driver_assigned_at', 'driver_accepted_time')
                    ->where('driver_id', $request->driver_id)
                    ->whereNotNull('driver_accepted_time')
                ;

                if ($request->filled('fromtime')) {
                    $fromtime = \Carbon\Carbon::createFromFormat('Y/m/d H:i', $request->fromtime)->format('Y-m-d H:i:s');
                    $totime   = $request->filled('totime')
                        ? \Carbon\Carbon::createFromFormat('Y/m/d H:i', $request->totime)->format('Y-m-d H:i:s')
                        : now()->format('Y-m-d H:i:s');

                    $query->whereBetween('created_at', [$fromtime, $totime]);
                }
            }]);

        $user = auth()->user();
        $isAdminWithCountry = $user->user_role === UserRole::ADMIN && $user->country_id;


        if ($isAdminWithCountry) {
            $countryId = $user->country_id;
            // dd(9);
            $query->whereHas('client.city', function ($q) use ($countryId) {
                $q->where('country_id', $countryId);
            });
        }

        return DataTables::of($query)

            ->addColumn('full_name', fn($row) => $row->first_name . ' ' . $row->last_name)
            ->addColumn('client_id', fn($row) => $row->id)
            ->addColumn('orders_count', fn($row) => $row->orders->count())

            ->addColumn('avg_accept_time', fn($row) => $row->calculateAvgTime('driver_assigned_at', 'driver_accepted_time'))

            ->make(true);
    }

    public function getOperatorAssignReportData(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('operators_acceptance_time_reports'), 403, 'You do not have permission to view this page.');
        abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');

        $today     = ($request->fromtime) ? Carbon::parse($request->fromtime) : Carbon::today();
        $yesterday = ($request->totime) ? Carbon::parse($request->totime) : Carbon::yesterday();

        $operators = User::query()
            ->leftJoin('operators', 'operators.operator_id', '=', 'users.id')
            ->leftJoin('cities', 'cities.id', '=', 'operators.city_id')
            ->leftJoin('orders', 'users.id', '=', 'orders.driver_id')
            ->where('users.user_role', 3)
            ->whereNotNull('orders.driver_accepted_time')
            ->whereBetween(DB::raw('DATE(orders.created_at)'), [$today, $yesterday])
            ->select([
                'users.id',
                'users.first_name',
                'users.last_name',
                'operators.id as operator_id',
                'cities.name as city_name',
                DB::raw('COUNT(orders.id) as orders_count'),
                DB::raw('AVG(TIMEDIFF(orders.driver_accepted_time, orders.driver_assigned_at)) as avg_accept_time'),
            ])
            ->groupBy([
                'users.id',
                'users.first_name',
                'users.last_name',
                'operators.id',
                'cities.name',
            ]);


        $user = auth()->user();
        $isAdminWithCountry = $user->user_role === UserRole::ADMIN && $user->country_id;





        if ($isAdminWithCountry) {
            $countryId = $user->country_id;

            $operators->whereHas('cities.city', function ($q) use ($countryId) {
                $q->where('country_id', $countryId);
            });
        }

        if ($request->filled('acceptance_rate')) {

            if ($request->acceptance_rate == 1) {
                $operators->havingRaw('COALESCE(AVG(TIMESTAMPDIFF(SECOND, orders.driver_assigned_at, orders.driver_accepted_time)), 0) <= ?', [120]);
            } elseif ($request->acceptance_rate == 0) {
                $operators->havingRaw('COALESCE(AVG(TIMESTAMPDIFF(SECOND, orders.driver_assigned_at, orders.driver_accepted_time)), 0) > ?', [120]);
            }
        }

        return DataTables::of($operators)
            ->editColumn('id', fn($row) => $row->id)
            ->editColumn('full_name', fn($row) => "{$row->first_name} {$row->last_name}") // Compute full_name in PHP
            ->editColumn('orders_count', fn($row) => $row->orders_count)
            ->editColumn('city_name', fn($row) => $row->city_name)
            ->editColumn('avg_accept_time', fn($row) => $row->avg_accept_time ? Carbon::parse($row->avg_accept_time)->format('H:i:s') : '00:00:00')
            ->addColumn('action', function ($row) {
                return '<div class="d-flex justify-content-center">
                <a href="#" data-id="' . $row->id . '"
                    data-bs-toggle="modal"
                    data-bs-target="#operatorReportDetail"
                    class="flex items-center order-driver-btn justify-center w-8 h-8 text-white border rounded-lg border-gray10">
                    <img src="' . asset('new/src/assets/icons/view.svg') . '" alt="View" />
                </a>
            </div>';
            })
            ->orderColumn('orders_count', 'orders_count $1')
            ->orderColumn('avg_accept_time', 'avg_accept_time $1')
            ->make(true);
    }

    public function exportOperatorsAssignReportData(Request $request)
    {
        abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');

        set_time_limit(-1);

        abort_unless(auth()->user()->hasPermissionTo('view_export_reports'), 403, 'You do not have permission to view this page.');

        $today     = ($request->date_export_form) ? Carbon::parse($request->date_export_form) : Carbon::today();
        $yesterday = ($request->date_export_to) ? Carbon::parse($request->date_export_to) : Carbon::yesterday();

        $operators = User::query()
            ->leftJoin('operators', 'operators.operator_id', '=', 'users.id')
            ->leftJoin('cities', 'cities.id', '=', 'operators.city_id')
            ->leftJoin('orders', 'users.id', '=', 'orders.driver_id')
            ->where('users.user_role', 3)
            ->whereNotNull('orders.driver_accepted_time')
            ->whereBetween(DB::raw('DATE(orders.created_at)'), [$today, $yesterday])
            ->select([
                'users.id',
                'users.first_name',
                'users.last_name',
                'operators.id as operator_id',
                'cities.name as city_name',
                DB::raw('COUNT(orders.id) as orders_count'),
                DB::raw('AVG(TIMEDIFF(orders.driver_accepted_time, orders.driver_assigned_at)) as avg_accept_time'),
            ])
            ->groupBy([
                'users.id',
                'users.first_name',
                'users.last_name',
                'operators.id',
                'cities.name',
            ]);


        $user = auth()->user();
        $isAdminWithCountry = $user->user_role === UserRole::ADMIN && $user->country_id;





        if ($isAdminWithCountry) {
            $countryId = $user->country_id;

            $operators->whereHas('cities.city', function ($q) use ($countryId) {
                $q->where('country_id', $countryId);
            });
        }

        $data = [];

        $operators->chunk(50, function ($opertorsChunk) use (&$data) {
            $chunkData = [];

            foreach ($opertorsChunk as $operator) {

                $nestedData['id']              = $operator->id;
                $nestedData['name']            = $operator->full_name;
                $nestedData['city']            = $operator->city_name;
                $nestedData['orders_count']    = $operator->orders_count;
                $nestedData['avg_accept_time'] = $operator->avg_accept_time ? Carbon::parse($operator->avg_accept_time)->format('H:i:s') : '00:00:00';

                $chunkData[] = $nestedData;
            }

            $data = array_merge($data, $chunkData);
        });

        return $this->exportOperatorsAssignReportExcel($data);
    }

    public function exportOperatorsAssignReportExcel($data)
    {
        abort_unless($this->restrictedTime(), 403, 'The reports are running between 2 AM and 11 AM.');

        $filename = uniqid() . '.xlsx';

        Excel::store(new OperatorAssignReportExport($data), 'temp/' . $filename);

        $file = new \Illuminate\Http\File(storage_path('app/temp/' . $filename));

        $cdnUrl = $this->upload_excel_file($file, 'reports');

        return response()->json([
            'download_url' => $cdnUrl,
        ]);
    }
}
