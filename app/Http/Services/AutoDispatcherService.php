<?php
namespace App\Http\Services;

use App\Http\Resources\Api\OrderResource;
use App\Models\Operator;
use App\Models\OperatorDetail;
use App\Models\OrderLog;
use App\Repositories\FirebaseRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoDispatcherService
{

    public function __construct(NotificationService $notificationService)
    {
        $this->firebaseRepository  = App::make(FirebaseRepository::class);
        $this->notificationService = $notificationService;
    }

    public function autoDispatch($order): void
    {
        //find branch
        $branchLatitude  = @$order->branch->lat;
        $branchLongitude = @$order->branch->lng;
        $CityId          = @$order->branch->city_id;
        if (! $branchLatitude || ! $branchLongitude) {
            Log::info('branch latitude or longitude not found order id: ' . $order->id);
            goto End;
        }
        $operators = OperatorDetail::with(['operator', 'operator.cities'])
            ->where(function (Builder $query) use ($branchLatitude, $branchLongitude, $CityId, $order) {
                $query->where(function (Builder $query2) use ($branchLatitude, $branchLongitude, $CityId, $order) {
                    $query2->where('status', 1)
                        ->whereNotNull('lat')
                        ->whereNotNull('lng')
                        ->whereHas('operator.cities', function ($q) use ($CityId) {
                            $q->where('city_id', $CityId);
                        })
                        ->whereRaw(
                            "(6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) <= 3",
                            [$branchLatitude, $branchLongitude, $branchLatitude]
                        )
                        ->whereDoesntHave('OrdersDateWithStatus')
                        ->whereDoesntHave('DriverOrders', function ($query) use ($order) {
                            $query->where('order_id', $order->id);
                        });
                })
                    ->orWhere(function (Builder $query2) use ($branchLatitude, $branchLongitude, $CityId, $order) {
                        $query2->whereIn('status', [1, 2])
                            ->whereNotNull('lat')
                            ->whereNotNull('lng')
                            ->whereHas('operator.cities', function ($q) use ($CityId) {
                                $q->where('city_id', $CityId);
                            })
                            ->whereHas('OrdersDateWithStatusSameBranch', function ($query) use ($order) {
                                $query->where('ingr_branch_id', $order->branch->id);
                            })
                            ->whereDoesntHave('DriverOrders', function ($query) use ($order) {
                                $query->where('order_id', $order->id);
                            });
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
                ),
                DB::raw("(SELECT COUNT(*) FROM orders WHERE orders.driver_id = operators.operator_id AND orders.status IN (16, 2, 17)) AS total_order_with_driver_already_count"),
                DB::raw("(SELECT COUNT(*) FROM orders WHERE orders.driver_id = operators.operator_id AND orders.ingr_branch_id = " . $order->ingr_branch_id . " AND orders.status IN (16, 2, 17)) AS same_branch_orders_count")
            )
            ->havingRaw('NOT (total_order_with_driver_already_count = 1 AND same_branch_orders_count != 1)')
            ->havingRaw('total_order_with_driver_already_count < 2')
            ->orderBy('distance', 'asc')
            ->first();

        if ($order->driver_id == null) {
            if ($operators) {
                //change order status
                // $order->status             = 2; //Pending driver acceptance
                $order->driver_id          = $operators->operator_id;
                $order->driver_assigned_at = Carbon::now('Asia/Riyadh');

                // Update or create the OrderDriver record
                // OrderDriver::create(
                //     [
                //         'order_id'         => $order->id,
                //         'driver_id'        => $operator->operator_id,
                //         'distance'         => $operator->distinct,
                //         'operator_details' => $operators->toArray(),
                //     ]
                // );
                //try save firebase
                try {
                    $orderResource = new OrderResource($order);
                    $orderData     = $orderResource->toArray(request());
                    // Attempt to save to Firebase
                    $this->firebaseRepository->save_driver_order($operators->operator_id, $orderData);
                } catch (\Exception $e) {
                    // Handle the exception (log it, show a message, etc.)
                    Log::info($e);
                }
                //save log
                $driver = Operator::findOrFail($operators->operator_id);
                OrderLog::create([
                    'order_id'    => $order->id,
                    'status'      => 2,
                    'action'      => 'Assgin Order',
                    'driver_id'   => $operators->operator_id,
                    'description' => ' assign order to driver from system automatic to ' . $driver->first_name,
                ]);
                $this->notificationService->send_for_specific('طلب جديد ', 'طلب جديد جاهز للتوصيل', $operators->operator_id);
            }
            $order->assign_try_count = $order->assign_try_count + 1;

            if ($order->assign_try_count == 3 && $order->driver_id == null) {
                $order->status = 13;
            }
            $order->save();
        }

        End:
    }
}
