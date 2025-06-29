<?php

namespace App\Http\Controllers\Admin;

use DB;
use Carbon\Carbon;

use App\Models\Order;
use App\Enum\UserRole;
use App\Models\Client;

use App\Models\Vehicle;
use App\Models\Operator;
use App\Enum\OrderStatus;
use App\Enum\DriverStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;

class DashboardController extends Controller
{
    public function dashboard()
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
      
        return view('admin.pages.charts.dashboard', compact([
            'totalServiceFees',
            'clients',
            'orders_count',
            'operators_count',
            'clients_count'
        ]));
    }

    public function getCharts(Request $request)
    {
        // dd(3);
        return response()->json([

            'totalServiceFees' => 0,
            'orders_count' => 0,
            'ordersPerHoursTotal' =>0,
            'ordersPerHoursaverageOrdersPerHour' => 0,
            'totalAverageWaitingTime2' =>0,
            'totalAverageWaitingTime' => 0,
            'total_average_picked_time' =>0,
            'total_average_time' => 0,
            'inactiveCounts' => 0,
            'activeCounts' => 0,
            'hours' => 0,
            'averageOfflineDurations' =>0,
            'driverOfflineAvgDates' => 0,
            'colors' =>0,
            'cities' => 0,
            'ordersHours' => 0,
            'deliveredCounts' => 0,
            'cancelledCounts' => 0,
            'orderCounts' =>0,
            'rejectionRate' => 0,
            'acceptanceRate' => 0,

            'ordersPerHours' => 0,
            'dates' => 0,
            'arriveInValues' => 0,
            'averageWaitingTimes' => 0,
            'waiting_pickup_dates' =>0,
            'avg_delivery_dates' =>0,
            'avg_deliveryTimes' => 0,
        ]);
        // dd($request->all());
        abort_unless(auth()->user()->hasPermissionTo('show_dashboard'), 403, 'You do not have permission to view this page.');
        $startDate = '';
        $endDate = '';

        if ($request->filled('date')) {
            $dateInput = $request->input('date');

            if (str_contains($dateInput, ' to ')) {

                $dates = explode(' to ', $dateInput);


                $startDate = Carbon::createFromFormat('Y-m-d', $dates[0])->startOfDay();
                $endDate = Carbon::createFromFormat('Y-m-d', $dates[1])->endOfDay();
            } else {

                $startDate = Carbon::createFromFormat('Y-m-d', $dateInput)->startOfDay();
            }
        }

        // dd($startDate, $endDate);


        $client = $request->client;
        if ($request->client == -1) {
            $client = null;
        }
        // dd($client);
        if (auth()->user()->user_role == UserRole::CLIENT) {
            $client = auth()->id();
        }

        $order_query  = Order::query();


        if ($startDate && $endDate) {
            $order_query->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate);
        } elseif ($startDate) {
            $order_query->where('created_at', '>=', $startDate);
        } else {
            $order_query->whereDate('created_at', Carbon::yesterday())
                ->orWhereDate('created_at', Carbon::today());
        }

        if ($client) {
            $order_query->where('ingr_shop_id', $client);
        }

        if (auth()->user()->user_role == UserRole::CLIENT) {
            $order_query->where('ingr_shop_id', auth()->id());
        }

        if (auth()->user()->user_role == UserRole::BRANCH) {
            $order_query->where('ingr_branch_id', auth()->user()->branch_id);
        }

        $orders_count = $order_query->count();

        $totalServiceFees  = $order_query->sum('service_fees');







        $ordersHours = [];
        $deliveredCounts = [];
        $cancelledCounts = [];


        $chartData = $this->orderStatusChart($startDate, $endDate, $client);

        if ($chartData['groupBy'] === 'hours') {
            $ordersHours = $chartData['hours'];
            $deliveredCounts = $chartData['deliveredCounts'];
            $cancelledCounts = $chartData['cancelledCounts'];
        } elseif ($chartData['groupBy'] === 'days') {
            $ordersHours = $chartData['days'];
            $deliveredCounts = $chartData['deliveredCounts'];
            $cancelledCounts = $chartData['cancelledCounts'];
        }



        $ordersPerHours = $this->ordersPerHour($startDate, $endDate, $client)['hours'];
        $ordersPerHoursTotal = $this->ordersPerHour($startDate, $endDate, $client)['totals'];
        $ordersPerHoursaverageOrdersPerHour = $this->ordersPerHour($startDate, $endDate, $client)['averageOrdersPerHour'];

        $dates = $this->driverArriveTimeAvrg($startDate, $endDate, $client)['dates'];
        $arriveInValues = $this->driverArriveTimeAvrg($startDate, $endDate, $client)['arriveInValues'];
        $total_average_picked_time = $this->driverArriveTimeAvrg($startDate, $endDate, $client)['total_average_picked_time'];

        $chartDataWitingPickup = $this->waitingPickupTimeChart($startDate, $endDate, $client);
        $waiting_pickup_dates = $chartDataWitingPickup['dates'];
        $averageWaitingTimes = $chartDataWitingPickup['averageWaitingTimes'];
        $totalAverageWaitingTime = $chartDataWitingPickup['totalAverageWaitingTime'];


        $chartDataAvrgDeliveryTime = $this->AvgDeliveryTime($startDate, $endDate, $client);
        $avg_delivery_dates = $chartDataAvrgDeliveryTime['dates'];
        $avg_deliveryTimes = $chartDataAvrgDeliveryTime['averageDeliveryTimes'];
        $total_average_time = $chartDataAvrgDeliveryTime['total_average_time'];

        $orers_acceptance_rate = $this->showAcceptanceRateChart($startDate, $endDate, $client);
        $acceptanceRate = $orers_acceptance_rate['acceptanceRate'];
        $rejectionRate = $orers_acceptance_rate['rejectionRate'];


        $orders_per_city = $this->showOrdersPerCityChart($startDate, $endDate, $client);
        $cities = $orders_per_city['cities'];
        $orderCounts = $orders_per_city['orderCounts'];
        $colors = $orders_per_city['colors'];


        $driverOfflineAvg = $this->driverOfflineTimesChart($startDate, $endDate, $client);
        $driverOfflineAvgDates = $driverOfflineAvg['dates'];
        $averageOfflineDurations = $driverOfflineAvg['averageDurations'];
        $totalAverageWaitingTime2 = $driverOfflineAvg['totalAverageWaitingTime'];

        $ordersAssignedPerHour = $this->ordersAssignedPerHourChart($startDate, $endDate);
        $hours = $ordersAssignedPerHour['hours'];
        $activeCounts = $ordersAssignedPerHour['activeCounts'];
        $inactiveCounts = $ordersAssignedPerHour['inactiveCounts'];




        return response()->json([

            'totalServiceFees' => $totalServiceFees,
            'orders_count' => $orders_count,
            'ordersPerHoursTotal' => $ordersPerHoursTotal,
            'ordersPerHoursaverageOrdersPerHour' => $ordersPerHoursaverageOrdersPerHour,
            'totalAverageWaitingTime2' => $totalAverageWaitingTime2,
            'totalAverageWaitingTime' => $totalAverageWaitingTime,
            'total_average_picked_time' => $total_average_picked_time,
            'total_average_time' => $total_average_time,
            'inactiveCounts' => $inactiveCounts,
            'activeCounts' => $activeCounts,
            'hours' => $hours,
            'averageOfflineDurations' => $averageOfflineDurations,
            'driverOfflineAvgDates' => $driverOfflineAvgDates,
            'colors' => $colors,
            'cities' => $cities,


            'ordersHours' => $ordersHours,
            'deliveredCounts' => $deliveredCounts,
            'cancelledCounts' => $cancelledCounts,




            'orderCounts' => $orderCounts,
            'rejectionRate' => $rejectionRate,
            'acceptanceRate' => $acceptanceRate,

            'ordersPerHours' => $ordersPerHours,
            'dates' => $dates,
            'arriveInValues' => $arriveInValues,
            'averageWaitingTimes' => $averageWaitingTimes,
            'waiting_pickup_dates' => $waiting_pickup_dates,
            'avg_delivery_dates' => $avg_delivery_dates,
            'avg_deliveryTimes' => $avg_deliveryTimes,
        ]);
    }


    public function orderStatusChart($startDate = null, $endDate = null, $client = null)
    {
        return [
            'groupBy' => 'hours',
            'hours' =>0,
            'deliveredCounts' =>0,
            'cancelledCounts' => 0,
        ];
        $query = DB::table('orders');

        // Apply date filters
        if ($startDate && !$endDate) {
            $query->whereDate('updated_at', $startDate);
        } elseif ($startDate && $endDate) {
            $query->whereDate('updated_at', '>=', $startDate)
                ->whereDate('updated_at', '<=', $endDate);
        } else {
            $query->whereDate('updated_at', Carbon::yesterday())
                ->orWhereDate('updated_at', Carbon::today());
        }

        // Apply client or branch filters
        if ($client) {
            $query->where('ingr_shop_id', $client);
        }

        if (auth()->user()->user_role == UserRole::CLIENT) {
            $query->where('ingr_shop_id', auth()->id());
        }

        if (auth()->user()->user_role == UserRole::BRANCH) {
            $query->where('ingr_branch_id', auth()->user()->branch_id);
        }

        // Adjust grouping and selection based on date range
        if ($startDate && $endDate) {
            $query->selectRaw("
                DATE(updated_at) as day,
                COUNT(CASE WHEN status = 9 THEN 1 END) as delivered_orders,
                COUNT(CASE WHEN status = 10 THEN 1 END) as cancelled_orders
            ")
                ->groupByRaw("DATE(updated_at)")
                ->orderByRaw("DATE(updated_at)");
        } else {
            $query->selectRaw("
                HOUR(updated_at) as hour,
                COUNT(CASE WHEN status = 9 THEN 1 END) as delivered_orders,
                COUNT(CASE WHEN status = 10 THEN 1 END) as cancelled_orders
            ")
                ->groupByRaw("HOUR(updated_at)")
                ->orderByRaw("HOUR(updated_at)");
        }

        $result = $query->get();

        // Process the result based on grouping
        if ($startDate && $endDate) {
            // Prepare data grouped by days
            $operatorData = $result->keyBy('day')->toArray();
            $days = array_keys($operatorData);
            $deliveredCounts = array_column($operatorData, 'delivered_orders');
            $cancelledCounts = array_column($operatorData, 'cancelled_orders');

            return [
                'groupBy' => 'days',
                'days' => $days,
                'deliveredCounts' => $deliveredCounts,
                'cancelledCounts' => $cancelledCounts,
            ];
        } else {
            // Prepare data grouped by hours
            $operatorData = $result->keyBy('hour')->toArray();
            $hours = array_keys($operatorData);
            $deliveredCounts = array_column($operatorData, 'delivered_orders');
            $cancelledCounts = array_column($operatorData, 'cancelled_orders');

            if (!in_array(0, $hours)) {
                array_unshift($hours, 0);
                array_unshift($deliveredCounts, 0);
                array_unshift($cancelledCounts, 0);
            }

            array_multisort($hours, $deliveredCounts, $cancelledCounts);

            return [
                'groupBy' => 'hours',
                'hours' => $hours,
                'deliveredCounts' => $deliveredCounts,
                'cancelledCounts' => $cancelledCounts,
            ];
        }
    }




    public function ordersPerHour($startDate = null, $endDate = null, $client = null) //done
    {
        return [
            'hours' => 0,
            'totals' => 0,
            'averageOrdersPerHour' => 0,
        ];
        // Select orders per hour with the conditions applied.
        $query = Order::selectRaw('HOUR(created_at) as hour, COUNT(*) as total')
            ->groupBy('hour')
            ->orderBy('hour');



        if ($startDate && $endDate) {
            $query->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate);
        } elseif ($startDate) {
            $query->where('created_at', '>=', $startDate);
        } else {
            $query->whereDate('created_at', Carbon::yesterday())
                ->orWhereDate('created_at', Carbon::today());
        }


        if ($client) {
            $query->where('ingr_shop_id', $client);
        }

        if (auth()->user()->user_role == UserRole::CLIENT) {
            $query->where('ingr_shop_id', auth()->id());
        }
        if (auth()->user()->user_role == UserRole::BRANCH) {
            $query->where('ingr_branch_id', auth()->user()->branch_id);
        }

        $ordersPerHour = $query->pluck('total', 'hour')->all();


        if (!isset($ordersPerHour[0])) {
            $ordersPerHour[0] = 0;
        }


        ksort($ordersPerHour);


        $hours = array_keys($ordersPerHour);
        $totals = array_values($ordersPerHour);


        if (count($totals) > 1) {
            $averageOrdersPerHour = array_sum($totals) / (count($totals) - 1);
        } else {
            $averageOrdersPerHour = 0;
        }


        // dd([
        //     'hours' => $hours,
        //     'totals' => $totals,
        //     'averageOrdersPerHour' => $averageOrdersPerHour,
        // ]);
        return [
            'hours' => $hours,
            'totals' => $totals,
            'averageOrdersPerHour' => (int) $averageOrdersPerHour,
        ];
    }


    public function driverArriveTimeAvrg($startDate = null, $endDate = null, $client = null)
    {
        return [
            'dates' => 0,
            'arriveInValues' => 0,
            'total_average_picked_time' => 0,
        ];
        $query = Order::with(['orderLogs' => function ($logQuery) {
            $logQuery->selectRaw(
                'order_id,
             TIMESTAMPDIFF(MINUTE, MIN(CASE WHEN status = 17 THEN created_at END), MAX(CASE WHEN status = 16 THEN created_at END)) as picked_time,
             TIMESTAMPDIFF(MINUTE, MIN(CASE WHEN status = 16 THEN created_at END), MAX(CASE WHEN status = 6 THEN created_at END)) as waiting_time'
            )
                ->groupBy('order_id');
        }]);


        if ($startDate && $endDate) {
            $query->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate);
        } elseif ($startDate) {
            $query->where('created_at', '>=', $startDate);
        } else {
            $query->whereDate('created_at', Carbon::yesterday())
                ->orWhereDate('created_at', Carbon::today());
        }


        if ($client) {
            $query->where('ingr_shop_id', $client);
        }
        if (auth()->user()->user_role == UserRole::CLIENT) {
            $query->where('ingr_shop_id', auth()->id());
        }
        if (auth()->user()->user_role == UserRole::BRANCH) {
            $query->where('ingr_branch_id', auth()->user()->branch_id);
        }
        // dd($query->get());
        $orders = $query->get();
        $averageDeliveryTimes = [];
        $averageWaitingTimes = [];
        $allPickedTimes = [];

        foreach ($orders as $order) {
            $logTime = $order->orderLogs->first()->picked_time ?? 0;
            $waitingLogTime = $order->orderLogs->first()->waiting_time ?? 0;

            $hour = $order->created_at->format('H');

            if (!isset($averageDeliveryTimes[$hour])) {
                $averageDeliveryTimes[$hour] = [];
                $averageWaitingTimes[$hour] = [];
            }

            $averageDeliveryTimes[$hour][] = $logTime;
            $averageWaitingTimes[$hour][] = $waitingLogTime;
            $allPickedTimes[] = $logTime;
        }


        $finalHours = array_keys($averageDeliveryTimes);


        array_unshift($finalHours, 0);


        sort($finalHours);


        $finalAvgDeliveryTimes = [];
        $finalAvgWaitingTimes = [];

        foreach ($finalHours as $hour) {

            if (isset($averageDeliveryTimes[$hour])) {
                $finalAvgDeliveryTimes[] = collect($averageDeliveryTimes[$hour])->avg();
            } else {
                $finalAvgDeliveryTimes[] = 0; // Default value if not set
            }

            if (isset($averageWaitingTimes[$hour])) {
                $finalAvgWaitingTimes[] = collect($averageWaitingTimes[$hour])->avg();
            } else {
                $finalAvgWaitingTimes[] = 0; // Default value if not set
            }
        }

        // Calculate the total average picked time
        $totalAvgPickedTimeMinutes = collect($allPickedTimes)->avg();

        // Convert to hours, minutes, and seconds
        $hours = floor($totalAvgPickedTimeMinutes / 60);
        $minutes = floor($totalAvgPickedTimeMinutes % 60);
        $seconds = round(($totalAvgPickedTimeMinutes * 60) % 60);

        $totalAveragePickedTime = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

        return [
            'dates' => $finalHours,
            'arriveInValues' => $finalAvgDeliveryTimes,
            'total_average_picked_time' => $totalAveragePickedTime,
        ];
    }


    // public function waitingPickupTimeChart($startDate = null, $endDate = null, $client = null) //done
    // {
    //     $query = DB::table('orders')
    //         ->select(DB::raw('driver_arrive_time - preparation_time as waiting_time'), DB::raw('DATE(created_at) as date'));

    //     if ($startDate && $endDate) {
    //         $query->whereBetween('created_at', [$startDate, $endDate]);
    //     }

    //     if ($client) {
    //         $query->where('ingr_shop_id', $client);
    //     }
    //     if (  auth()->user()->user_role == UserRole::CLIENT) {
    //         $query->where('ingr_shop_id', auth()->id());
    //     }

    //     $waitingTimes = $query->get();
    //     $grouped = $waitingTimes->groupBy('date');
    //     $dates = [];
    //     $averageWaitingTimes = [];

    //     foreach ($grouped as $date => $times) {
    //         $dates[] = $date;
    //         $averageWaitingTimes[] = $times->avg('waiting_time');
    //     }

    //     return [
    //         'dates' => $dates,
    //         'averageWaitingTimes' => $averageWaitingTimes
    //     ];
    // }


    public function waitingPickupTimeChart($startDate = null, $endDate = null, $client = null)
    {
        return [
            'dates' => 0,
            'averageWaitingTimes' =>0,
            'totalAverageWaitingTime' =>0,
        ];
        $query = Order::with(['orderLogs' => function ($logQuery) {
            $logQuery->selectRaw(
                'order_id,
                 TIMESTAMPDIFF(MINUTE, MIN(CASE WHEN status = 16 THEN created_at END), MAX(CASE WHEN status = 6 THEN created_at END)) as picked_time,
                 TIMESTAMPDIFF(MINUTE, MIN(CASE WHEN status = 16 THEN created_at END), MAX(CASE WHEN status = 6 THEN created_at END)) as waiting_time'
            )
                ->groupBy('order_id');
        }]);

        // Apply date range filter if provided
        if ($startDate && $endDate) {
            $query->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate);
        } elseif ($startDate) {
            $query->where('created_at', '>=', $startDate);
        } else {
            $query->whereDate('created_at', Carbon::yesterday())
                ->orWhereDate('created_at', Carbon::today());
        }

        // Filter by client if provided or the authenticated user's ID if they are a Client role
        if ($client) {
            $query->where('ingr_shop_id', $client);
        }
        if (auth()->user()->user_role == UserRole::CLIENT) {
            $query->where('ingr_shop_id', auth()->id());
        }
        if (auth()->user()->user_role == UserRole::BRANCH) {
            $query->where('ingr_branch_id', auth()->user()->branch_id);
        }

        $orders = $query->get();
        $averageWaitingTimes = [];

        foreach ($orders as $order) {
            $waitingLogTime = $order->orderLogs->first()->waiting_time ?? 0;
            $hour = $order->created_at->format('H'); // Get the hour part of the date

            // Group times by hour
            if (!isset($averageWaitingTimes[$hour])) {
                $averageWaitingTimes[$hour] = [];
            }

            $averageWaitingTimes[$hour][] = $waitingLogTime;
        }

        $finalHours = array_keys($averageWaitingTimes);
        array_unshift($finalHours, 0);
        sort($finalHours);
        $finalAvgWaitingTimes = [];

        foreach ($finalHours as $hour) {
            $finalAvgWaitingTimes[] = isset($averageWaitingTimes[$hour]) ? collect($averageWaitingTimes[$hour])->avg() : 0;
        }

        $totalAvgWaitingTime = collect($finalAvgWaitingTimes)->avg();

        $hours = floor($totalAvgWaitingTime / 60);
        $minutes = $totalAvgWaitingTime % 60;
        $seconds = 0;
        $totalAvgWaitingTimeFormatted = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

        return [
            'dates' => $finalHours,
            'averageWaitingTimes' => $finalAvgWaitingTimes,
            'totalAverageWaitingTime' => $totalAvgWaitingTimeFormatted,
        ];
    }







    // public function AvgDeliveryTime($startDate = null, $endDate = null, $client = null) //done
    // {
    //     $query = DB::table('orders')
    //         ->select(DB::raw('delivered_in as delivery_time'), DB::raw('DATE(created_at) as date'));
    //     if ($startDate && $endDate) {
    //         $query->whereBetween('created_at', [$startDate, $endDate]);
    //     }

    //     if ($client) {
    //         $query->where('ingr_shop_id', $client);
    //     }
    //     if (  auth()->user()->user_role == UserRole::CLIENT) {
    //         $query->where('ingr_shop_id', auth()->id());
    //     }

    //     $deliveryTimes = $query->get();
    //     $grouped = $deliveryTimes->groupBy('date');
    //     $dates = [];
    //     $averageDeliveryTimes = [];

    //     foreach ($grouped as $date => $times) {
    //         $dates[] = $date;
    //         $averageDeliveryTimes[] = $times->avg('delivery_time');
    //     }

    //     return [
    //         'dates' => $dates,
    //         'averageDeliveryTimes' => $averageDeliveryTimes
    //     ];
    // }


    public function AvgDeliveryTime($startDate = null, $endDate = null, $client = null)
    {
        return [
            'dates' => 0,
            'averageDeliveryTimes' =>0,
            'averageWaitingTimes' => 0,
            'total_average_time' => 0,
        ];
        $query = Order::with(['orderLogs' => function ($logQuery) {
            $logQuery->selectRaw(
                'order_id,
             TIMESTAMPDIFF(MINUTE, MIN(CASE WHEN status = 17 THEN created_at END), MAX(CASE WHEN status = 9 THEN created_at END)) as delivery_time,
             TIMESTAMPDIFF(MINUTE, MIN(CASE WHEN status = 16 THEN created_at END), MAX(CASE WHEN status = 6 THEN created_at END)) as waiting_time'
            )
                ->groupBy('order_id');
        }]);

        if ($startDate && $endDate) {
            $query->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate);
        } elseif ($startDate) {
            $query->where('created_at', '>=', $startDate);
        } else {
            $query->whereDate('created_at', Carbon::yesterday())
                ->orWhereDate('created_at', Carbon::today());
        }

        if ($client) {
            $query->where('ingr_shop_id', $client);
        }
        if (auth()->user()->user_role == UserRole::CLIENT) {
            $query->where('ingr_shop_id', auth()->id());
        }
        if (auth()->user()->user_role == UserRole::BRANCH) {
            $query->where('ingr_branch_id', auth()->user()->branch_id);
        }

        $orders = $query->get();
        $averageDeliveryTimes = [];
        $averageWaitingTimes = [];
        $allDeliveryTimes = [];

        foreach ($orders as $order) {
            $logTime = $order->orderLogs->first()->delivery_time ?? 0;
            $waitingLogTime = $order->orderLogs->first()->waiting_time ?? 0;

            // Extract hour from created_at
            $hour = $order->created_at->format('H'); // Get the hour in 24-hour format

            // Group times by hour
            if (!isset($averageDeliveryTimes[$hour])) {
                $averageDeliveryTimes[$hour] = [];
                $averageWaitingTimes[$hour] = [];
            }

            $averageDeliveryTimes[$hour][] = $logTime;
            $averageWaitingTimes[$hour][] = $waitingLogTime;
            $allDeliveryTimes[] = $logTime; // Store all delivery times for total average calculation
        }

        // Calculate the average for each hour
        $finalHours = array_keys($averageDeliveryTimes);

        // Add 0 at the beginning of the array
        array_unshift($finalHours, 0);

        // Sort $finalHours in descending order by values
        sort($finalHours);
        // $finalHours = array_reverse($finalHours);

        $finalAvgDeliveryTimes = [];
        $finalAvgWaitingTimes = [];

        foreach ($finalHours as $hour) {
            // Check if $hour exists as a key in $averageDeliveryTimes and $averageWaitingTimes
            if (isset($averageDeliveryTimes[$hour])) {
                $finalAvgDeliveryTimes[] = collect($averageDeliveryTimes[$hour])->avg();
            } else {
                $finalAvgDeliveryTimes[] = 0; // Default value if not set
            }

            if (isset($averageWaitingTimes[$hour])) {
                $finalAvgWaitingTimes[] = collect($averageWaitingTimes[$hour])->avg();
            } else {
                $finalAvgWaitingTimes[] = 0; // Default value if not set
            }
        }

        // Calculate the total average delivery time across all orders
        $totalAvgDeliveryTimeMinutes = collect($allDeliveryTimes)->avg();

        // Convert the total average delivery time from minutes to hours, minutes, and seconds
        $hours = floor($totalAvgDeliveryTimeMinutes / 60);
        $minutes = floor($totalAvgDeliveryTimeMinutes % 60);
        $seconds = round(($totalAvgDeliveryTimeMinutes * 60) % 60);

        $totalAverageTime = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

        return [
            'dates' => $finalHours,
            'averageDeliveryTimes' => $finalAvgDeliveryTimes,
            'averageWaitingTimes' => $finalAvgWaitingTimes,
            'total_average_time' => $totalAverageTime,
        ];
    }






    public function showAcceptanceRateChart($startDate = null, $endDate = null, $client = null) //done
    {
        return [
            'acceptanceRate' => 0,
            'rejectionRate' => 0
        ];

        $query = DB::table('orders');

        if ($startDate && $endDate) {
            $query->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate);
        } elseif ($startDate) {
            $query->where('created_at', '>=', $startDate);
        } else {
            $query->whereDate('created_at', Carbon::yesterday())
                ->orWhereDate('created_at', Carbon::today());
        }

        if ($client) {
            $query->where('ingr_shop_id', $client);
        }

        if (auth()->user()->user_role == UserRole::CLIENT) {
            $query->where('ingr_shop_id', auth()->id());
        }

        if (auth()->user()->user_role == UserRole::BRANCH) {
            $query->where('ingr_branch_id', auth()->user()->branch_id);
        }


        $totalOrders = (clone $query)->count();

        $acceptedOrders = (clone $query)
            ->where('status', '!=', OrderStatus::DRIVER_REJECTED)
            ->count();

        $rejectedOrders = (clone $query)
            ->where('status', OrderStatus::DRIVER_REJECTED)
            ->count();

        // Debugging the results
        // dd(compact('totalOrders', 'acceptedOrders', 'rejectedOrders'));


        if ($totalOrders > 0) {
            $acceptanceRate = number_format(($acceptedOrders / $totalOrders) * 100, 1);
            $rejectionRate = number_format(($rejectedOrders / $totalOrders) * 100, 1);
        } else {

            $acceptanceRate = 0;
            $rejectionRate = 0;
        }
        // dd($acceptanceRate, $rejectionRate);
        return [
            'acceptanceRate' => $acceptanceRate,
            'rejectionRate' => $rejectionRate
        ];
    }


    public function showOrdersPerCityChart($startDate = null, $endDate = null, $client = null) //done
    {
        return 0;
        $query = DB::table('orders')
            ->join('client_branches', 'orders.ingr_branch_id', '=', 'client_branches.id')
            ->join('cities', 'client_branches.city_id', '=', 'cities.id')
            ->select(DB::raw('cities.name as city_name, COUNT(orders.id) as order_count'))
            ->groupBy('cities.name');


        if ($startDate && $endDate) {
            $query->where('orders.created_at', '>=', $startDate)
                ->where('orders.created_at', '<=', $endDate);
        }


        if ($startDate && $endDate) {
            $query->where('orders.created_at', '>=', $startDate)
                ->where('orders.created_at', '<=', $endDate);
        } elseif ($startDate) {
            $query->where('orders.created_at', '>=', $startDate);
        } else {
            $query->whereDate('orders.created_at', Carbon::yesterday())
                ->orWhereDate('orders.created_at', Carbon::today());
        }

        if ($client) {
            $query->where('orders.ingr_shop_id', $client);
        }

        if (auth()->user()->user_role == UserRole::CLIENT) {
            $query->where('orders.ingr_shop_id', auth()->id());
        }
        if (auth()->user()->user_role == UserRole::BRANCH) {
            $query->where('ingr_branch_id', auth()->user()->branch_id);
        }

        $ordersPerCity = $query->get();
        $cities = $ordersPerCity->pluck('city_name');
        $orderCounts = $ordersPerCity->pluck('order_count');
        $colors = $orderCounts->map(function ($item, $key) {

            $colorPalette = [
                [255, 0, 0],    // Bold Red
                [0, 255, 0],    // Bold Green
                [0, 0, 255],    // Bold Blue
                [128, 0, 128],  // Purple
                [0, 0, 128],    // Navy
                [255, 255, 0],  // Yellow
                [255, 165, 0],  // Orange
                [255, 20, 147], // Deep Pink
                [0, 128, 128],  // Teal
                [0, 255, 255],  // Cyan
                [255, 69, 0],   // Orange Red
                [75, 0, 130],   // Indigo
                [173, 216, 230] // Light Blue
            ];


            static $shuffledColors;
            if (!$shuffledColors) {
                $shuffledColors = $colorPalette;
                shuffle($shuffledColors);
            }


            $index = $key % count($shuffledColors);
            $baseColor = $shuffledColors[$index];


            return sprintf('rgba(%d, %d, %d, 1)', $baseColor[0], $baseColor[1], $baseColor[2]);
        });



        // dd( [
        //     'cities' => $cities,
        //     'orderCounts' => $orderCounts,
        //     'colors' => $colors
        // ]);
        return [
            'cities' => $cities,
            'orderCounts' => $orderCounts,
            'colors' => $colors
        ];
    }


    public function driverOfflineTimesChart($startDate = null, $endDate = null, $client = null)
    {
        return [
            'dates' => 0,
            'averageDurations' => 0,

            'totalAverageWaitingTime' => 0,
        ];
        $query = Order::with(['orderLogs' => function ($logQuery) {
            $logQuery->selectRaw(
                'order_id,
                 TIMESTAMPDIFF(MINUTE, MIN(CASE WHEN status = 8 THEN created_at END), MAX(CASE WHEN status = 9 THEN created_at END)) as delivery_time,
                 TIMESTAMPDIFF(MINUTE, MIN(CASE WHEN status = 16 THEN created_at END), MAX(CASE WHEN status = 6 THEN created_at END)) as waiting_time'
            )
                ->groupBy('order_id');
        }]);


        if ($startDate && $endDate) {
            $query->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate);
        } elseif ($startDate) {
            $query->where('created_at', '>=', $startDate);
        } else {
            $query->whereDate('created_at', Carbon::yesterday())
                ->orWhereDate('created_at', Carbon::today());
        }


        if ($client) {
            $query->where('ingr_shop_id', $client);
        }
        if (auth()->user()->user_role == UserRole::CLIENT) {
            $query->where('ingr_shop_id', auth()->id());
        }
        if (auth()->user()->user_role == UserRole::BRANCH) {
            $query->where('ingr_branch_id', auth()->user()->branch_id);
        }

        $orders = $query->get();
        $averageDeliveryTimes = [];
        $averageWaitingTimes = [];

        // Grouping delivery times and waiting times by hour
        foreach ($orders as $order) {
            $deliveryTime = $order->orderLogs->first()->delivery_time ?? 0;
            $waitingLogTime = $order->orderLogs->first()->waiting_time ?? 0;
            $hour = $order->created_at->format('H'); // Get the hour part of the date

            // Group times by hour
            if (!isset($averageDeliveryTimes[$hour])) {
                $averageDeliveryTimes[$hour] = [];
                $averageWaitingTimes[$hour] = [];
            }

            $averageDeliveryTimes[$hour][] = $deliveryTime;
            $averageWaitingTimes[$hour][] = $waitingLogTime;
        }

        $finalHours = array_keys($averageDeliveryTimes);
        array_unshift($finalHours, 0);
        sort($finalHours);
        $finalAvgDeliveryTimes = [];
        $finalAvgWaitingTimes = [];

        foreach ($finalHours as $hour) {
            $finalAvgDeliveryTimes[] = isset($averageDeliveryTimes[$hour]) ? collect($averageDeliveryTimes[$hour])->avg() : 0;
            $finalAvgWaitingTimes[] = isset($averageWaitingTimes[$hour]) ? collect($averageWaitingTimes[$hour])->avg() : 0;
        }

        $totalAvgDeliveryTime = collect($finalAvgDeliveryTimes)->avg();

        $hours = floor($totalAvgDeliveryTime / 60);
        $minutes = $totalAvgDeliveryTime % 60;
        $seconds = 0;

        $totalAvgDeliveryTimeFormatted = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

        return [
            'dates' => $finalHours,
            'averageDurations' => $finalAvgDeliveryTimes,

            'totalAverageWaitingTime' => $totalAvgDeliveryTimeFormatted,
        ];
    }





    // public function ordersAssignedPerHourChart()
    // {

    //     $query = DB::table('order_drivers')
    //         ->select(
    //             DB::raw('HOUR(order_drivers.created_at) as hour'),
    //             DB::raw('MINUTE(order_drivers.created_at) as minute'),
    //             DB::raw('SECOND(order_drivers.created_at) as second'),
    //             DB::raw('COUNT(order_drivers.order_id) as order_count')
    //         )
    //         ->groupBy('hour', 'minute', 'second')
    //         ->orderBy('hour')
    //         ->orderBy('minute')
    //         ->orderBy('second');


    //     if (auth()->user()->user_role == UserRole::CLIENT) {
    //         $query->whereExists(function ($subQuery) {
    //             $subQuery->select(DB::raw(1))
    //                 ->from('orders')
    //                 ->whereColumn('orders.id', 'order_drivers.order_id')
    //                 ->where('orders.ingr_shop_id', auth()->id());
    //         });
    //     }

    //     if (auth()->user()->user_role == UserRole::BRANCH) {
    //         $query->whereExists(function ($subQuery) {
    //             $subQuery->select(DB::raw(1))
    //                 ->from('orders')
    //                 ->whereColumn('orders.id', 'order_drivers.order_id')
    //                 ->where('orders.ingr_branch_id', auth()->user()->branch_id);
    //         });
    //     }



    //     $assignmentsPerHour = $query->get();

    //     $hours = $assignmentsPerHour->map(function ($item) {
    //         return sprintf('%02d:%02d:%02d', $item->hour, $item->minute, $item->second);
    //     });

    //     $orderCounts = $assignmentsPerHour->pluck('order_count')->toArray();
    //     $orderCounts[] = 0;

    //     return ['hours' => $hours, 'orderCounts' => $orderCounts];
    // }



    public function ordersAssignedPerHourChart($startDate = null, $endDate = null)
    {

        $query = DB::table('operators')
            ->selectRaw("
            HOUR(updated_at) as hour,
            COUNT(CASE WHEN status != 4 THEN 1 END) as active_count,
            COUNT(CASE WHEN status = 4 THEN 1 END) as inactive_count
        ");

        if ($startDate && $endDate) {
            $query->where('updated_at', '>=', $startDate)
                ->where('updated_at', '<=', $endDate);
        } elseif ($startDate) {
            $query->where('updated_at', '>=', $startDate);
        } else {
            // dd(888);
            $query->whereDate('updated_at', Carbon::yesterday())
                ->orWhereDate('updated_at', Carbon::today());
        }


        $query->groupByRaw("HOUR(updated_at)")
            ->orderByRaw("HOUR(updated_at)");

        $result = $query->get();

        $operatorData = $result->keyBy('hour')->toArray();
        $hours = array_keys($operatorData);
        $activeCounts = array_column($operatorData, 'active_count');
        $inactiveCounts = array_column($operatorData, 'inactive_count');

        if (!in_array(0, $hours)) {
            array_unshift($hours, 0);
            array_unshift($activeCounts, 0);
            array_unshift($inactiveCounts, 0);
        }

        array_multisort($hours, $activeCounts, $inactiveCounts);

        return [
            'hours' => $hours,
            'activeCounts' => $activeCounts,
            'inactiveCounts' => $inactiveCounts,
        ];
    }


    public function getClientsOrdersData(Request $request)
    {
        $pendingStatuses = [
            OrderStatus::CREATED,
            OrderStatus::PENDINE_DRIVER_ACCEPTANCE,
            OrderStatus::DRIVER_ACCEPTANCE_TIMEOUT,
        ];

        $activeStatuses = [
            OrderStatus::DRIVER_ACCEPTED,
            OrderStatus::ARRIVED_TO_DROPOFF,
            OrderStatus::ARRIVED_PICK_UP,
            OrderStatus::PICKED_UP,
        ];

        $failedStatuses = [OrderStatus::FAILED];
        $canceledStatuses = [OrderStatus::CANCELED];
        $deliveredStatuses = [OrderStatus::DELIVERED];

        $clients = Client::with(['orders' => function ($query) use (
            $pendingStatuses,
            $activeStatuses,
            $failedStatuses,
            $canceledStatuses,
            $deliveredStatuses
        ) {
            $query->with(['orderLogs' => function ($logQuery) {
                $logQuery->selectRaw(
                    'order_id,
                TIMESTAMPDIFF(MINUTE,
                    MIN(CASE WHEN status = ? THEN created_at END),
                    MAX(CASE WHEN status = ? THEN created_at END)) as waiting_time,
                TIMESTAMPDIFF(MINUTE,
                    MIN(CASE WHEN status = ? THEN created_at END),
                    MAX(CASE WHEN status = ? THEN created_at END)) as delivery_time',
                    [
                        OrderStatus::ARRIVED_PICK_UP,
                        OrderStatus::PICKED_UP,
                        OrderStatus::DRIVER_ACCEPTED,
                        OrderStatus::DELIVERED,
                    ]
                )->groupBy('order_id');
            }]);
        }]);

        if ($request->client && $request->client != -1) {
            $clients->where('id', $request->client);
        }
        if (auth()->user()->user_role == UserRole::CLIENT) {
            // dd(99);
            $clients->where('id', auth()->id());
        }
        if (auth()->user()->user_role == UserRole::BRANCH) {
            $clients->where('id', auth()->user()->client_id);
        }


        $result = [];
        // dd(auth()->id(),$clients->where('id', auth()->id())->get());
        foreach ($clients->get() as $client) {
            $orders = $client->orders;

            $pendingCount = $orders->whereIn('status', $pendingStatuses)->count();
            $activeCount = $orders->whereIn('status', $activeStatuses)->count();
            $failedCount = $orders->whereIn('status', $failedStatuses)->count();
            $canceledCount = $orders->whereIn('status', $canceledStatuses)->count();
            $deliveredCount = $orders->whereIn('status', $deliveredStatuses)->count();

            $averageWaitingTime = $orders->pluck('orderLogs')
                ->flatten()
                ->pluck('waiting_time')
                ->avg();

            $averageDeliveryTime = $orders->pluck('orderLogs')
                ->flatten()
                ->pluck('delivery_time')
                ->avg();

            $formattedWaitingTime = $this->formatTime($averageWaitingTime);
            $formattedDeliveryTime = $this->formatTime($averageDeliveryTime);

            $result[] = [
                'client_id' => $client->id,
                'full_name' => $client->full_name,
                'image' => $client->image,
                'pending_orders' => $pendingCount,
                'active_orders' => $activeCount,
                'failed_orders' => $failedCount,
                'canceled_orders' => $canceledCount,
                'delivered_orders' => $deliveredCount,
                'average_waiting_time' => $formattedWaitingTime,
                'average_delivery_time' => $formattedDeliveryTime,
            ];
        }
        // dd($result);

        return $result;
    }


    private function formatTime($timeInMinutes)
    {
        $hours = floor($timeInMinutes / 60);
        $minutes = $timeInMinutes % 60;
        $seconds = 0;
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }



    public function getDashboardDriversData(Request $request)
    {
        $search = $request->search;
        $operators = Operator::with(['statuses', 'orders']);
        if ($search) {
            $operators->where(function (Builder $query) use ($search) {
                $query->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }
        $result = [];

        foreach ($operators->get() as $operator) {
            $statuses = $operator->statuses()
                ->where('status', '!=', 4)
                ->orderBy('created_at')
                ->get();

            $totalTime = 0;
            $activeTime = 0;
            $statusCount = 0;

            foreach ($statuses as $index => $status) {
                $nextStatus = $statuses[$index + 1] ?? null;

                if ($nextStatus) {
                    $timeSpent = $nextStatus->created_at->diffInMinutes($status->created_at);
                } else {
                    $timeSpent = $status->updated_at->diffInMinutes($status->created_at);
                }

                $totalTime += $timeSpent;

                if ($status->status != 4) {
                    $activeTime += $timeSpent;
                }

                $statusCount++;
            }

            $averageAttendanceTime = $statusCount > 0 ? $totalTime / $statusCount : 0;

            $totalOrders = $operator->orders()->count();
            $acceptedOrders = $operator->orders()
                ->whereNotIn('status', [2, 18, 13])
                ->count();

            $acceptanceRate = $totalOrders > 0 ? ($acceptedOrders / $totalOrders) * 100 : 0;

            $attendanceRate = $totalTime > 0 ? ($activeTime / $totalTime) * 100 : 0;

            $result[] = [
                'operator_id' => $operator->id,
                'full_name' => $operator->full_name,
                'phone' => $operator->phone,
                'image' => $operator->image,
                'lat' => $operator->operator?->lat,
                'lng' => $operator->operator?->lng,
                'id' => $operator->id,
                'vehicle' => Vehicle::where('operator_id', $operator->id)->first()?->name ?? '-',
                'status' => DriverStatus::tryFrom($operator->operator?->status)?->getLabel() ?? '-',
                'status_value' => DriverStatus::tryFrom($operator->operator?->status)?->value,
                'average_attendance_time' => round($averageAttendanceTime, 2),
                'attendance_rate' => round($attendanceRate, 2) . '%',
                'acceptance_rate' => round($acceptanceRate, 2) . '%',
            ];
        }

        // dd($result);

        return response()->json($result);
    }
    public function getChartsNew(Request $request)
    {
        return "Stop";
        // dd($request->all());
        abort_unless(auth()->user()->hasPermissionTo('show_dashboard'), 403, 'You do not have permission to view this page.');
        $startDate = '';
        $endDate = '';

        if ($request->filled('date')) {
            $dateInput = $request->input('date');

            if (str_contains($dateInput, ' to ')) {

                $dates = explode(' to ', $dateInput);


                $startDate = $dates[0];
                $endDate = $dates[1];
            } else {

                if($dateInput=='to'){
                        $dateInput = Carbon::now()->toDateString();
                }
                $startDate = Carbon::createFromFormat('Y-m-d', $dateInput)->startOfDay();
            }
        }

        // dd($startDate, $endDate);


        $client = $request->client;
        if ($request->client == -1) {
            $client = null;
        }
        // dd($client);
        if (auth()->user()->user_role == UserRole::CLIENT) {
            $client = auth()->id();
        }

        $order_query  = Order::query();


        if ($startDate && $endDate) {
            $order_query->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate);
        } elseif ($startDate) {
            $order_query->where('created_at', '>=', $startDate);
        } else {
            $order_query->whereDate('created_at', Carbon::yesterday())
                ->orWhereDate('created_at', Carbon::today());
        }

        if ($client) {
            $order_query->where('ingr_shop_id', $client);
        }

        if (auth()->user()->user_role == UserRole::CLIENT) {
            $order_query->where('ingr_shop_id', auth()->id());
        }

        if (auth()->user()->user_role == UserRole::BRANCH) {
            $order_query->where('ingr_branch_id', auth()->user()->branch_id);
        }

        $orders_count = $order_query->count();

        $totalServiceFees  = $order_query->sum('service_fees');







        $ordersHours = [];
        $deliveredCounts = [];
        $cancelledCounts = [];


        $chartData = $this->orderStatusChart($startDate, $endDate, $client);

        if ($chartData['groupBy'] === 'hours') {
            $ordersHours = $chartData['hours'];
            $deliveredCounts = $chartData['deliveredCounts'];
            $cancelledCounts = $chartData['cancelledCounts'];
        } elseif ($chartData['groupBy'] === 'days') {
            $ordersHours = $chartData['days'];
            $deliveredCounts = $chartData['deliveredCounts'];
            $cancelledCounts = $chartData['cancelledCounts'];
        }



        $ordersPerHours = $this->ordersPerHour($startDate, $endDate, $client)['hours'];
        $ordersPerHoursTotal = $this->ordersPerHour($startDate, $endDate, $client)['totals'];
        $ordersPerHoursaverageOrdersPerHour = $this->ordersPerHour($startDate, $endDate, $client)['averageOrdersPerHour'];

        $dates = $this->driverArriveTimeAvrg($startDate, $endDate, $client)['dates'];
        $arriveInValues = $this->driverArriveTimeAvrg($startDate, $endDate, $client)['arriveInValues'];
        $total_average_picked_time = $this->driverArriveTimeAvrg($startDate, $endDate, $client)['total_average_picked_time'];

        $chartDataWitingPickup = $this->waitingPickupTimeChart($startDate, $endDate, $client);
        $waiting_pickup_dates = $chartDataWitingPickup['dates'];
        $averageWaitingTimes = $chartDataWitingPickup['averageWaitingTimes'];
        $totalAverageWaitingTime = $chartDataWitingPickup['totalAverageWaitingTime'];


        $chartDataAvrgDeliveryTime = $this->AvgDeliveryTime($startDate, $endDate, $client);
        $avg_delivery_dates = $chartDataAvrgDeliveryTime['dates'];
        $avg_deliveryTimes = $chartDataAvrgDeliveryTime['averageDeliveryTimes'];
        $total_average_time = $chartDataAvrgDeliveryTime['total_average_time'];

        $orers_acceptance_rate = $this->showAcceptanceRateChart($startDate, $endDate, $client);
        $acceptanceRate = $orers_acceptance_rate['acceptanceRate'];
        $rejectionRate = $orers_acceptance_rate['rejectionRate'];


        $orders_per_city = $this->showOrdersPerCityChart($startDate, $endDate, $client);
        $cities = $orders_per_city['cities'];
        $orderCounts = $orders_per_city['orderCounts'];
        $colors = $orders_per_city['colors'];


        $driverOfflineAvg = $this->driverOfflineTimesChart($startDate, $endDate, $client);
        $driverOfflineAvgDates = $driverOfflineAvg['dates'];
        $averageOfflineDurations = $driverOfflineAvg['averageDurations'];
        $totalAverageWaitingTime2 = $driverOfflineAvg['totalAverageWaitingTime'];

        $ordersAssignedPerHour = $this->ordersAssignedPerHourChart($startDate, $endDate);
        $hours = $ordersAssignedPerHour['hours'];
        $activeCounts = $ordersAssignedPerHour['activeCounts'];
        $inactiveCounts = $ordersAssignedPerHour['inactiveCounts'];




        return response()->json([

            'totalServiceFees' => $totalServiceFees,
            'orders_count' => $orders_count,
            'ordersPerHoursTotal' => $ordersPerHoursTotal,
            'ordersPerHoursaverageOrdersPerHour' => $ordersPerHoursaverageOrdersPerHour,
            'totalAverageWaitingTime2' => $totalAverageWaitingTime2,
            'totalAverageWaitingTime' => $totalAverageWaitingTime,
            'total_average_picked_time' => $total_average_picked_time,
            'total_average_time' => $total_average_time,
            'inactiveCounts' => $inactiveCounts,
            'activeCounts' => $activeCounts,
            'hours' => $hours,
            'averageOfflineDurations' => $averageOfflineDurations,
            'driverOfflineAvgDates' => $driverOfflineAvgDates,
            'colors' => $colors,
            'cities' => $cities,


            'ordersHours' => $ordersHours,
            'deliveredCounts' => $deliveredCounts,
            'cancelledCounts' => $cancelledCounts,




            'orderCounts' => $orderCounts,
            'rejectionRate' => $rejectionRate,
            'acceptanceRate' => $acceptanceRate,

            'ordersPerHours' => $ordersPerHours,
            'dates' => $dates,
            'arriveInValues' => $arriveInValues,
            'averageWaitingTimes' => $averageWaitingTimes,
            'waiting_pickup_dates' => $waiting_pickup_dates,
            'avg_delivery_dates' => $avg_delivery_dates,
            'avg_deliveryTimes' => $avg_deliveryTimes,
        ]);
    }

}
