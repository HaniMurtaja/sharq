<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Enum\UserRole;
use App\Models\Client;
use App\Models\Operator;
use App\Models\OrderLog;
use App\Enum\OrderStatus;
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
use App\Repositories\FirebaseRepository;
use App\Http\Resources\Api\OrderResource;
use Illuminate\Database\Eloquent\Builder;
use  App\Http\Services\NotificationService;
use App\Traits\OrderCreationDateValidation;
use App\Http\Services\AutoDispatcherService;

class OrderController extends Controller
{
    use OrderCreationDateValidation;
    public $current_step = 1;
    public $total_steps = 3;
    protected $notificationService;

    public function __construct(NotificationService $notificationService, AutoDispatcherService $AutoDispatcherService)
    {
        $this->notificationService = $notificationService;
        $this->firebaseRepository = App::make(FirebaseRepository::class);
        $this->AutoDispatcherService = $AutoDispatcherService;
    }






    public function save(OrderRequest $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('upload_orders'), 403, 'You do not have permission to view this page.');

        $client_id = null ;

        if (auth()->user()->user_role == UserRole::CLIENT) {
            $client_id = auth()->id();
            $branch_id = $request->branch_id;
        } elseif (auth()->user()->user_role == UserRole::BRANCH) {
            $client_id = auth()->user()->client_id;
            $branch_id = auth()->user()->branch_id;
        } else {
            $client_id = $request->client_id;
            $branch_id = $request->branch_id;
        }
        $client = Client::findOrFail($client_id);

        if ($client->is_active != 1) {
            return response()->json(['message' => 'Unactive user'], 400);
        }

        $pickup_id = ClientBranches::where('id', $branch_id)->first();
        // dd($pickup_id->is_active);
        if (! $pickup_id) {
            return response()->json(['message' => 'Location not found'], 400);
        }

        if ($pickup_id->is_active != 1) {
            return response()->json(['message' => 'Unactive branch'], 400);
        }




        if ($this->isWithinBusinessHours($client_id) == false) {
            return response()->json(['message' => 'system closed'], 400);
           
        }


        $mysqlDateTime = null;
        if ($request->date_time) {
            $mysqlDateTime = Carbon::createFromFormat('m/d/Y h:i A', $request->date_time)->format('Y-m-d H:i:s');
        }
        $ClientDetail = ClientDetail::where('user_id', $client_id)->first();

        $order = Order::create([
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'status' => OrderStatus::CREATED,
            'instruction' => $request->instructions,
            'ingr_shop_id' => $client_id,
            'ingr_branch_id' => $branch_id,
            'client_order_id_string' => $request->client_order_id,
            'items_no' => $request->items_no,
            'value' => round($request->order_value, 3),
            'integration_id' => $ClientDetail->integration_id,
            'payment_type' => $request->payment_method,
            'proof_of_action' => $request->proof_action,
            'vehicle_id' => $request->vehicle_id,
            'pickup_instruction' => $request->pickup_instructions,
            'details' => $request->order_details,
            'preparation_time' => $request->preperation_time ?? 0,
            'deliver_at' => $mysqlDateTime,
            'driver_arrive_time' => $request->arive_in ?? 0,
            'delivered_in' => $request->driver_in,
            'service_fees' => $request->service_fees ?? 0,
            'lat' => $request->lat_order_hidden,
            'lng' => $request->lng_order_hidden,
            'distance' => $request->distance,
            'customer_address' => $request->customer_address,

        ]);

       
        //fathy// 09-02-2025
        //        $currentStep = session()->get('current_step', 1);
        //        $currentStep = 1;
        //        session()->put('current_step', $currentStep);

        //        try {
        //firebase notification
        $title = 'New Order';
        $body = 'New order created with id: ' . $order->id;
        //            $users = User::whereHas("roles", fn($q) => $q->whereIn('name', ['dispatcher', 'admin']))->get();
        //            foreach ($users as $user) {
        //                // $user_id, $title, $body, $notification_count, $redirect = NULL, $click = NULL, $icon = NULL
        //                $this->notificationService->sendOrderNotifications($user->id, $title, $body, $order->id, 'orders', '');
        //            }
        //        } catch (\Exception $e) {
        //            // Handle the exception (log it, show a message, etc.)
        //            Log::info($e);
        //        }

        //auto dispatch logic
        // try {

        //        $client = ClientDetail::where('user_id', $client_id)->first();
        //        if ($client) {
        //            $order->refresh();
        //            if ($client->auto_dispatch == 1 && $order->driver_id == null) {
        //                $this->AutoDispatcherService->autoDispatch($order);
        //            }
        //        }

        // } catch (\Exception $e) {
        //     // Handle the exception (log it, show a message, etc.)
        //     Log::info($e);
        // }
        //fathy// 09-02-2025
        return response()->json(['success' => true]);
    }

    public function clientBranches(Request $request)
    {
        // dd($request->clientID);
        // dd(Client::findOrFail($request->clientID)->client);
        $fees = @Client::findOrFail($request->clientID)->client?->clienGroup?->default_delivery_fee;
        $branches = ClientBranches::where('client_id', $request->clientID)->where('is_active', 1)->get();
        // dd($fees);

        // $fees = 8;

        //        if (auth()->user()->role_user->value == 5) {
        //
        //        }
        return response()->json(['branches' => $branches, 'fees' => $fees]);
    }

    public function orderList(Request $request)
    {

        abort_unless(auth()->user()->hasPermissionTo('orders_basic_view'), 403, 'You do not have permission to view this page.');

        $columns = ['id', 'customer_name', 'customer_phone', 'driver', 'shop', 'branch', 'status', 'created_at', 'order_value', 'fees', 'total'];

        $query = Order::query();

        if (auth()->user()->hasRole('Client')) {
            $query->where('ingr_shop_id', auth()->id());
        }
        if (auth()->user()->user_role == UserRole::CLIENT) {
            $query->where('ingr_shop_id', auth()->id());
        }

        if (auth()->user()->user_role == UserRole::BRANCH) {
            $query->where('ingr_branch_id', auth()->user()->branch_id);
        }


        $totalData = $query->count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start', 0);
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'asc');

        $order = $columns[$orderColumn] ?? $columns[0];
        // dd( $request->status);
        if ($request->filled('status') && $request->status[0] != -1) {
            $query->whereIn('status', $request->input('status'));
        }

        if ($request->filled('date')) {
            // Split the input date string
            $dates = explode(' to ', $request->input('date'));

            // Ensure the format matches the input
            $startDate = Carbon::createFromFormat('Y-m-d', $dates[0])->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $dates[1])->endOfDay();

            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($request->filled('client_id') && !auth()->user()->hasRole('Client')) {

            $query->where('ingr_shop_id', $request->input('client_id'));
        }


        if ($request->filled('driver_id')) {
            $query->whereHas('drivers', function ($q) use ($request) {
                $q->where('order_drivers.driver_id', $request->input('driver_id'));
            });
        }

        if ($request->filled('search2')) {
            $search = $request->input('search2');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                    ->orWhere('ingr_shop_id', 'LIKE', "%{$search}%");
            });
        }


        if ($request->filled('search')) {

            $search = $request->input('search.value');
            // dd($search);
            $query->where(function (Builder $query) use ($search) {
                $query->where('client_order_id', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%')
                    ->orWhere('customer_phone', 'like', '%' . $search . '%')
                    ->orWhere('customer_name', 'like', '%' . $search . '%');
                // ->orWhereHas('shop', function ($query) use ($search) {
                //     $query->where('users.first_name', 'like', '%' . $search . '%')
                //     ->orWhere('users.phone', 'like', '%' . $search . '%');
                // })
                // ->orWhereHas('driver', function ($query) use ($search) {
                //     $query->whereHas('driver', function ($query) use ($search) {
                //         $query->whereRaw("CONCAT(users.first_name, ' ', users.last_name) LIKE ?", ['%' . $search . '%'])
                //             ->orWhere('users.phone', 'like', '%' . $search . '%');
                //     });
                // })
                // ->orWhereHas('branch', function ($query) use ($search) {
                //     $query->where('client_branches.name', 'like', '%' . $search . '%')
                //     ->orWhere('client_branches.phone', 'like', '%' . $search . '%');
                // })
                // ->orWhereHas('branchIntegration', function ($query) use ($search) {
                //     $query->where('client_branches.name', 'like', '%' . $search . '%')
                //     ->orWhere('client_branches.phone', 'like', '%' . $search . '%');
                // });
            });
        }


        $totalFiltered = $query->count();


        $orders = $query->offset($start)
            ->limit($limit)
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [];

        foreach ($orders as $order) {
            $driver = $order->drivers()->orderBy('created_at', 'desc')->first()?->driver?->full_name;
            $nestedData['id'] = $order->id;
            $nestedData['customer_name'] = $order->customer_name;
            $nestedData['customer_phone'] = $order->customer_phone;
            $nestedData['driver'] = $driver;
            $nestedData['shop'] =  $order->shop?->first_name ?? $order->branchIntegration?->client?->first_name;
            $nestedData['branch'] = $order->branch?->name ?? $order->branchIntegration?->name;
            $nestedData['status'] = $order->status->getLabel();
            $nestedData['created_at'] = $order->created_at->format('Y:m:d');
            $nestedData['order_value'] = $order->value;
            $nestedData['fees'] = $order->service_fees;
            $nestedData['total'] = $order->service_fees + $order->value;
            $data[] = $nestedData;
        }

        $json_data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        ];

        return response()->json($json_data);
    }



    public function auto_dispatch($request, $order)
    {
        $settings = new GeneralSettings();
        $currentTime = Carbon::now('Asia/Riyadh');
        // dd($settings->time_multi_order_assign);
        $client = ClientDetail::where('user_id', $request->client_id)->first();
        if ($client) {
            if ($client->auto_dispatch == 1) {
                $branch = ClientBranches::find($request->branch_id);
                $branchLatitude = $branch->lat ?? 0;
                $branchLongitude = $branch->lng ?? 0;

                // $operator = OperatorDetail::where('status', 1)
                //     ->whereNotNull('lat')
                //     ->whereNotNull('lng')
                //     ->whereDoesntHave('operator.orders', function ($query) {
                //         $query->whereIn('status', [2, 13]);
                //     })
                //     ->orWhereHas('operator.driverOrders', function ($query) use ($request, $settings, $currentTime) {
                //         $query->whereHas('order', function ($query) use ($request) {
                //             $query->where('ingr_branch_id', $request->branch_id);
                //         })
                //             // select2 ajax

                //             ->whereHas('driver.operator', function ($query) {
                //                 $query->where('status', '!=', 4)
                //                     ->whereNotNull('lat')
                //                     ->whereNotNull('lng');
                //             })

                //             ->whereRaw('DATE_ADD(created_at, INTERVAL ? MINUTE) >= ?', [$settings->time_multi_order_assign, $currentTime])
                //             ->havingRaw('(SELECT COUNT(*) FROM order_drivers od INNER JOIN orders o ON od.order_id = o.id WHERE od.driver_id = order_drivers.driver_id AND o.ingr_branch_id = ?  AND o.status NOT IN (9, 20, 10)) < ?', [$request->branch_id, $settings->max_driver_orders])

                //             ->orderBy('created_at', 'desc')
                //             ->limit(1);
                //     })



                //     ->orWhereHas('operator.driverOrders', function ($query) use ($request, $settings, $currentTime, $branchLatitude, $branchLongitude) {
                //             $query->whereHas('driver.operator', function ($query) use ($branchLatitude, $branchLongitude) {
                //                 $query->where('status', '!=', 4)
                //                     ->whereNotNull('lat')
                //                     ->whereNotNull('lng')
                //                     ->whereRaw(
                //                         "(6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) <= 1",
                //                         [$branchLatitude, $branchLongitude, $branchLatitude]
                //                     );
                //             })


                //             ->havingRaw('(SELECT COUNT(*) FROM order_drivers od INNER JOIN orders o ON od.order_id = o.id WHERE od.driver_id = order_drivers.driver_id AND o.ingr_branch_id = ?  AND o.status NOT IN (9, 20, 10)) < ?', [$request->branch_id, $settings->max_driver_orders])

                //             ->orderBy('created_at', 'desc')
                //             ->limit(1);
                //     })
                //     ->where('city_id', $branch->city_id)

                //     ->select(
                //         'operators.id',
                //         'operators.operator_id',
                //         'operators.status',
                //         'operators.lat',
                //         'operators.lng',
                //         DB::raw(
                //             "6371 * acos(cos(radians(" . $branchLatitude . "))
                //             * cos(radians(lat)) * cos(radians(lng) - radians(" . $branchLongitude . "))
                //             + sin(radians(" . $branchLatitude . ")) * sin(radians(lat))) AS distance"
                //         )
                //     )

                //     ->orderBy('distance', 'asc')
                //     ->get();


                $operator = OperatorDetail::where('status', 1)
                    ->whereNotNull('lat')
                    ->whereNotNull('lng')
                    ->where('city_id', $branch->city_id)
                    ->whereRaw(
                        "(6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) <= 7",
                        [$branchLatitude, $branchLongitude, $branchLatitude]
                    )
                    ->whereDoesntHave('operator.orders', function ($query) {
                        $query->whereIn('status', [2, 13]);
                    })

                    ->orWhere(function ($query) use ($request, $branch, $settings, $currentTime, $branchLatitude, $branchLongitude) {
                        $query
                            ->where('city_id', $branch->city_id)

                            ->Where(function ($query) use ($request, $branch, $settings, $currentTime, $branchLatitude, $branchLongitude) {
                                $query->whereHas('operator.orders', function ($query) {
                                    $query->where('status', 8);
                                })
                                    ->orWhereHas('operator.driverOrders', function ($subQuery) use ($request, $settings, $currentTime) {
                                        $subQuery->whereHas('order', function ($orderQuery) use ($request) {
                                            $orderQuery->where('ingr_branch_id', $request->branch_id);
                                        })
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
                                    })->orWhereHas('operator.driverOrders', function ($subQuery) use ($request, $settings, $branchLatitude, $branchLongitude) {
                                        $subQuery->whereHas('order')
                                            ->whereHas('driver.operator', function ($operatorQuery) use ($branchLatitude, $branchLongitude) {
                                                $operatorQuery->where('status', '!=', 4)
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

                // dd($operator);
                if ($operator) {
                    // dd('found operator');
                    $order->status = OrderStatus::PENDINE_DRIVER_ACCEPTANCE; //Pending driver acceptance
                    $order->save();
                    // Update or create the OrderDriver record
                    OrderDriver::updateOrCreate(
                        ['order_id' => $order->id], // Check for existing order_id
                        [
                            'driver_id' => $operator->operator_id,
                            'distance' => $operator->distance
                        ],   // Update with the new driver_id

                    );
                    $orderResource = new OrderResource($order);
                    $orderData = $orderResource->toArray(request());
                    //try save firebase
                    try {
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
                        'status' => OrderStatus::PENDINE_DRIVER_ACCEPTANCE,
                        'action' => 'Assgin Order',
                        'description' => 'Auto dispatch assign order to driver ' . $driver->full_name,
                    ]);
                    $this->notificationService->send_for_specific('طلب جديد ', 'طلب جديد جاهز للتوصيل', $operator->operator_id,);
                } else {
                    // dd('not foun operator');
                    $order->status = 13; //DRIVER_ACCEPTANCE_TIMEOUT
                    $order->save();
                }
            }
        }
    }



    public function clientCancelOrder(Request $request)
    {
        $request->validate([
            'reason_id' => 'required|exists:reasons,id',
        ]);

        $order_ids = explode(',', $request->order_id_cancel_reason);
        $order_ids = array_map('trim', $order_ids);

        $results = [];

        foreach ($order_ids as $order_id) {
            $order = Order::find($order_id);

            if (!$order) {
                $results[$order_id] = 'Order not found';
                continue;
            }

            if ($order->status == OrderStatus::PENDING_CLIENT_ORDER_CANCELLATION) {
                $results[$order_id] = 'Already pending cancellation';
                continue;
            }

            $order->status = OrderStatus::PENDING_CLIENT_ORDER_CANCELLATION;
            $order->reason_id = $request->reason_id;
            $order->save();

            OrderLog::create([
                'order_id' => $order->id,
                'status' => OrderStatus::PENDING_CLIENT_ORDER_CANCELLATION,
                'action' => 'Request Cancel Order',
                'user_id' => auth()->id(),
                'description' => auth()->user()->first_name . ' requested to cancel the order',
            ]);

            $results[$order_id] = 'Cancellation request submitted successfully';
        }

        return response()->json(['success' => true, 'results' => $results]);
    }
}
