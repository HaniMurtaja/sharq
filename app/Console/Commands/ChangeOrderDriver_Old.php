<?php

namespace App\Console\Commands;

use App\Http\Resources\Api\OrderResource;
use App\Http\Services\AutoDispatcherService;
use App\Http\Services\NotificationService;
use App\Models\ClientBranches;
use App\Models\Operator;
use App\Models\OperatorDetail;
use App\Models\Order;
use App\Models\OrderDriver;
use App\Models\OrderLog;
use App\Repositories\FirebaseRepository;
use App\Settings\GeneralSettings;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChangeOrderDriver_Old extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'changeOrderDriverOld';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'change order  driver after acceptance timeout';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->firebaseRepository = App::make(FirebaseRepository::class);
        $this->notificationService = $notificationService;

    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(AutoDispatcherService $autoDispatcherService)
    {
        $orders = Order::with('shop', 'branch', 'driver')->whereIn('status', [
            '13',
        ])->
        where(function ($q){
            $q->whereDate('created_at', Carbon::yesterday())
                ->orWhereDate('created_at', Carbon::today());
        })
        ->get();

        // dd($orders);

        foreach ($orders as $order) {
            //find branch
            $branch = ClientBranches::find($order->ingr_branch_id);
            $branchLatitude = $branch->lat;
            $branchLongitude = $branch->lng;
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

            $operator = OperatorDetail::where('status', 1)
                ->whereNotNull('lat')
                ->whereNotNull('lng')
                ->where('city_id', $branch->city_id)
                ->whereRaw(
                    "(6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) <= 7",
                    [$branchLatitude, $branchLongitude, $branchLatitude]
                )
                ->whereDoesntHave('OrdersDateWithStatus')

                ->orWhere(function ($query) use ($branch, $settings, $currentTime, $branchLatitude, $branchLongitude) {
                    $query
                        ->where('city_id', $branch->city_id)

                        ->Where(function ($query) use ( $branch, $settings, $currentTime, $branchLatitude, $branchLongitude) {
                            $query->whereHas('OrdersDate', function ($query) {
                                $query->where('status', 8);
                            })
                                ->orWhereHas('OrdersDate', function ($subQuery) use ($branch, $settings, $currentTime) {
                                    $subQuery->where('ingr_branch_id', $branch->id)

                                    ->whereHas('driver.operator', function ($operatorQuery) {
                                            $operatorQuery->where('status', '!=', 4)
                                                ->whereNotNull('lat')
                                                ->whereNotNull('lng');
                                        })
                                        ->whereRaw('DATE_ADD(created_at, INTERVAL ? MINUTE) >= ?', [$settings->time_multi_order_assign, $currentTime])
                                        ->havingRaw('(SELECT COUNT(*) FROM order_drivers od
                                                INNER JOIN orders o ON od.order_id = o.id
                                                WHERE od.driver_id = order_drivers.driver_id
                                                AND o.status NOT IN (9, 20, 10, 13)
                                                AND DATE(o.created_at) = CURDATE()) < ?', [$settings->max_driver_orders])
                                        ->orderBy('created_at', 'desc')
                                        ->limit(1);
                                })->orWhereHas('OrdersDate', function ($subQuery) use ($settings, $branchLatitude, $branchLongitude) {
                                    $subQuery->where('status', '!=', 4)
                                                ->whereNotNull('lat')
                                                ->whereNotNull('lng')
                                                ->whereRaw(
                                                    "(6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) <= 0.3",
                                                    [$branchLatitude, $branchLongitude, $branchLatitude]
                                                )
                                            ;
                                        })
                                        ->havingRaw('(SELECT COUNT(*) FROM order_drivers od
                                                INNER JOIN orders o ON od.order_id = o.id
                                                WHERE od.driver_id = order_drivers.driver_id
                                                AND o.status NOT IN (9, 20, 10, 13)
                                                AND DATE(o.created_at) = CURDATE()) < ?', [$settings->max_driver_orders])

                                        ->orderBy('created_at', 'desc')
                                        ->limit(1);
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









            // $operator = OperatorDetail::where('status', 1)
            //     ->whereNotNull('lat')
            //     ->whereNotNull('lng')
            //     ->where('city_id', $branch->city_id)
            //     ->whereDoesntHave('operator.orders', function ($query) {
            //         $query->whereIn('status', [2, 13]);
            //     })
            //     ->orWhere(function ($query) use ( $branch, $settings, $currentTime, $branchLatitude, $branchLongitude) {
            //         $query
            //             ->where('city_id', $branch->city_id)
            //             ->whereHas('operator.driverOrders', function ($subQuery) use ( $settings,$branch, $currentTime) {
            //                 $subQuery->whereHas('order', function ($orderQuery) use ($branch) {
            //                     $orderQuery->where('ingr_branch_id', $branch->id);
            //                 })
            //                     ->whereHas('driver.operator', function ($operatorQuery) {
            //                         $operatorQuery->where('status', '!=', 4)
            //                             ->whereNotNull('lat')
            //                             ->whereNotNull('lng');
            //                     })
            //                     ->whereRaw('DATE_ADD(created_at, INTERVAL ? MINUTE) >= ?', [$settings->time_multi_order_assign, $currentTime])
            //                     ->havingRaw('(SELECT COUNT(*) FROM order_drivers od INNER JOIN orders o ON od.order_id = o.id WHERE od.driver_id = order_drivers.driver_id AND o.ingr_branch_id = ? AND o.status NOT IN (9, 20, 10)) < ?', [$branch->id, $settings->max_driver_orders])
            //                     ->orderBy('created_at', 'desc')
            //                     ->limit(1);
            //             })
            //             ->orWhereHas('operator.driverOrders', function ($subQuery) use ($branch, $settings, $branchLatitude, $branchLongitude) {
            //                 $subQuery->whereHas('driver.operator', function ($operatorQuery) use ($branchLatitude, $branchLongitude) {
            //                     $operatorQuery->where('status', '!=', 4)
            //                         ->whereNotNull('lat')
            //                         ->whereNotNull('lng')
            //                         ->whereRaw(
            //                             "(6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) <= 1",
            //                             [$branchLatitude, $branchLongitude, $branchLatitude]
            //                         );
            //                 })
            //                     ->havingRaw('(SELECT COUNT(*) FROM order_drivers od INNER JOIN orders o ON od.order_id = o.id WHERE od.driver_id = order_drivers.driver_id AND o.ingr_branch_id = ? AND o.status NOT IN (9, 20, 10)) < ?', [$branch->id, $settings->max_driver_orders])
            //                     ->orderBy('created_at', 'desc')
            //                     ->limit(1);
            //             });
            //     })
            //     ->select(
            //         'operators.id',
            //         'operators.operator_id',
            //         'operators.status',
            //         'operators.lat',
            //         'operators.lng',
            //         DB::raw(
            //             "6371 * acos(cos(radians(" . $branchLatitude . "))
            //         * cos(radians(lat)) * cos(radians(lng) - radians(" . $branchLongitude . "))
            //         + sin(radians(" . $branchLatitude . ")) * sin(radians(lat))) AS distance"
            //         )
            //     )
            //     ->orderBy('distance', 'asc')
            //     ->first();


            // dd($operator);
            if ($operator) {
                //change order status
                $order->status = 2; //Pending driver acceptance
                $order->driver_id = $operator->operator_id;
                $order->save();
                // Update or create the OrderDriver record
                OrderDriver::updateOrCreate(
                    ['order_id' => $order->id], // Check for existing order_id
                    ['driver_id' => $operator->operator_id]   // Update with the new driver_id
                );
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
                $this->notificationService->send_for_specific('طلب جديد ', 'طلب جديد جاهز للتوصيل', $operator->operator_id,);

            }
        }
        $this->info('order assigned to new driver');
    }
}
