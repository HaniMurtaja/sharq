<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Kreait\Firebase\Factory;
use App\Http\Controllers\Controller;



class OnlineOrdersController  extends Controller
{

    public function online_orders()
    {
        //    abort_unless(auth()->user()->hasPermissionTo('view_integration'), 403, 'You do not have permission to view this page.');
        return view('admin.pages.online_orders');
    }

    public function getDriversWithOrders(Request $request)
    {
        $columns = ['id', 'name', 'new_orders_count'];
        $limit = $request->input('length');
        $start = $request->input('start');
        $search = $request->input('search.value'); // Search input from DataTable

        $factory = (new Factory)->withServiceAccount(app_path('Http/Controllers/Api/firebase.json'));
        $database = $factory->withDatabaseUri('https://alshrouqexpress-97ebd-default-rtdb.firebaseio.com/')->createDatabase();

        $driversRef = $database->getReference('drivers');
        $driversData = $driversRef->getValue();

        $filteredDrivers = [];

        foreach ($driversData as $driverId => $driver) {
            $profile = $driver['profile'] ?? null;
            $newOrders = $driver['orders']['new-order'] ?? [];

            if (!empty($newOrders)) {
                $fullName = $profile ? ($profile['first_name'] . ' ' . $profile['last_name']) : 'N/A';

                if (
                    empty($search) ||
                    stripos($driverId, $search) !== false ||
                    ($profile && (stripos($profile['first_name'], $search) !== false || stripos($profile['last_name'], $search) !== false || stripos($fullName, $search) !== false))
                ) {

                    $filteredDrivers[] = [
                        'id' => $driverId,
                        'name' => $fullName,
                        'new_orders_count' => count($newOrders),
                        'new_orders' => $newOrders,
                    ];
                }
            }
        }

        $totalData = count($filteredDrivers);
        $totalFiltered = $totalData;
        $drivers = array_slice($filteredDrivers, $start, $limit);

        $data = [];
        foreach ($drivers as $driver) {
            $data[] = [
                'id' => $driver['id'],
                'name' => $driver['name'],
                'new_orders_count' => $driver['new_orders_count'],
                'details' => $driver['new_orders'],
            ];
        }

        return response()->json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data" => $data,
        ]);
    }



    public function deleteDriverOrder(Request $request)
    {
        $driverId = $request->input('driver_id');
        $orderId = $request->input('order_id');

        if (!$driverId || !$orderId) {
            return response()->json(['message' => 'Invalid request'], 400);
        }

        $factory = (new Factory)->withServiceAccount(app_path('Http/Controllers/Api/firebase.json'));
        $database = $factory->withDatabaseUri('https://alshrouqexpress-97ebd-default-rtdb.firebaseio.com/')->createDatabase();

        $orderRef = $database->getReference("drivers/{$driverId}/orders/new-order/{$orderId}");

        if ($orderRef->getValue()) {
            $orderRef->remove();
            return response()->json(['message' => 'Order deleted successfully']);
        } else {
            return response()->json(['message' => 'Order not found'], 404);
        }
    }
}
