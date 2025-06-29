<?php

namespace App\Http\Services;

use App\Http\Resources\Api\OrderResource;
use App\Models\ClientBranches;
use App\Models\Operator;
use App\Models\OperatorDetail;
use App\Models\OrderDriver;
use App\Models\OrderLog;
use App\Repositories\FirebaseRepository;
use App\Settings\GeneralSettings;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoDispatcherService_2
{

    public function __construct(NotificationService $notificationService)
    {
        $this->firebaseRepository = App::make(FirebaseRepository::class);
        $this->notificationService = $notificationService;
    }

    public function autoDispatch($order): void
    {
         {
            //find branch
            $branch = ClientBranches::find($order->ingr_branch_id);
            $branchLatitude = @$branch->lat;
            $branchLongitude = @$branch->lng;
            if (!$branchLatitude || !$branchLongitude) {
                Log::info('branch latitude or longitude not found order id: ' . $order->id);
                goto End;
            }
            $settings = new GeneralSettings();
            $currentTime = Carbon::now('Asia/Riyadh');
            //find nearest operator
            // $operator = OperatorDetail::where('status', 1)->whereNotNull('lat')->whereNotNull('lng')->whereDoesntHave('operator.orders', function ($query) {
            //     $query->whereIn('status', [2, 13]);
            // })->select('id', 'operator_id', 'status', 'lat', 'lng',
            //     DB::raw("6371 * acos(cos(radians(" . $branchLatitude . "))
            //                     * cos(radians(lat)) * cos(radians(lng) - radians(" . $branchLongitude . "))
            //                     + sin(radians(" . $branchLatitude . ")) * sin(radians(lat)))  AS distance"
            //     )
            // )
            //     ->orderBy('distance', 'asc')
            //     ->first();
            $operator = OperatorDetail::
            where('status', 1)
                ->whereNotNull('lat')
                ->whereNotNull('lng')
                ->where('city_id', $branch->city_id)
                ->whereRaw(
                    "(6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) <= 3",
                    [$branchLatitude, $branchLongitude, $branchLatitude]
                )
                ->whereDoesntHave('OrdersDateWithStatus')
                ->whereDoesntHave('DriverOrders',function ($query)use($order) {
                    $query->where('order_id', $order->id);
                })


                ->orWhere(function ($query) use ($branch, $settings, $currentTime, $branchLatitude, $branchLongitude) {
                    $query
                        ->where('city_id', $branch->city_id)

                        ->Where(function ($query) use ( $branch, $settings, $currentTime, $branchLatitude, $branchLongitude) {
                            $query->whereHas('OrdersDate', function ($query) {
                                $query->where('status', 8);
                            })
                                ->orWhereHas('OrdersDate', function ($subQuery) use ($branch, $settings, $currentTime) {
                                    $subQuery->where('ingr_branch_id', $branch->id)

                                        ->whereHas('DriverData', function ($operatorQuery) {
                                            $operatorQuery->wherein('status', [1,2])
                                                ->whereNotNull('lat')
                                                ->whereNotNull('lng');
                                        })
//                                    ->whereRaw('DATE_ADD(driver_assigned_at, INTERVAL ? MINUTE) >= ?', [$settings->time_multi_order_assign, $currentTime]);
                                        ->havingRaw('(SELECT COUNT(*) FROM operators od
                                                    INNER JOIN orders o ON od.operator_id = o.driver_id
                                                    AND o.status  IN (16,17)
                                                    AND DATE(o.created_at) = CURDATE()) < ?', [$settings->max_driver_orders])

                                        ->orderBy('created_at', 'desc')
                                        ->limit(1);
                                });
//
//                            ->orWhereHas('OrdersDate', function ($subQuery) use ($settings, $branchLatitude, $branchLongitude) {
//                                    $subQuery->wherein('status', [1,2])
//                                        ->whereNotNull('lat')
//                                        ->whereNotNull('lng')
//                                        ->whereRaw(
//                                            "(6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) <= 0.3",
//                                            [$branchLatitude, $branchLongitude, $branchLatitude]
//                                        )
//                                    ;
//                                })
//                                ->havingRaw('(SELECT COUNT(*) FROM order_drivers od
//                                                INNER JOIN orders o ON od.order_id = o.id
//                                                WHERE od.driver_id = order_drivers.driver_id
//                                                AND o.status NOT IN (9, 20, 10, 13)
//                                                AND DATE(o.created_at) = CURDATE()) < ?', [$settings->max_driver_orders])
//
//                                ->orderBy('created_at', 'desc')
//                                ->limit(1);
                        });

                })
                ->select(
                    'operators.id',
                    'operators.operator_id',
                    'operators.status',
                    'operators.lat',
                    'operators.lng',
                    DB::raw(
                        "6371 * acos(cos(radians(" . $branchLatitude . "))
                            * cos(radians(lat)) * cos(radians(lng) - radians(" . $branchLongitude . "))
                            + sin(radians(" . $branchLatitude . ")) * sin(radians(lat))) AS distance"
                    )
                )
                ->orderBy('distance', 'asc')
                ->first();
//    if ($operator){
//        dd($operator);;
//    }
            // dd($operator);
             if ($order->driver_id == null) {

            if ($operator ) {
                //change order status
                $order->status = 2; //Pending driver acceptance
                $order->driver_id = $operator->operator_id;
                $order->driver_assigned_at = Carbon::now('Asia/Riyadh');

                // Update or create the OrderDriver record
                OrderDriver::create(
                    ['order_id' => $order->id,'driver_id' => $operator->operator_id]);
                //try save firebase
                try {
                    $orderResource = new OrderResource($order);
                    $orderData = $orderResource->toArray(request());
                    // Attempt to save to Firebase
                    $this->firebaseRepository->save_driver_order($operator->operator_id, $orderData);
                } catch (\Exception $e) {
                    // Handle the exception (log it, show a message, etc.)
                    Log::info($e);
                }
                //save log
                $driver = Operator::findOrFail($operator->operator_id);
                OrderLog::create([
                    'order_id' => $order->id,
                    'status' => 2,
                    'action' => 'Assgin Order',
                    'driver_id' => $operator->operator_id,
                    'description' => ' assign order to driver from system automatic to ' . $driver->first_name,
                ]);
                $this->notificationService->send_for_specific('طلب جديد ', 'طلب جديد جاهز للتوصيل', $operator->operator_id);

            }
             $order->assign_try_count = $order->assign_try_count + 1;

            if ($order->assign_try_count == 3 && $order->driver_id == null) {
                $order->status = 13;
            }
             $order->save();
             }
        }
//        Log::info('order assigned to new driver');
        End:
    }
}
