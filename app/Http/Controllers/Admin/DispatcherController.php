<?php
namespace App\Http\Controllers\Admin;

use App\Enum\BlinkOrderStatus;
use App\Enum\DeliverectOrderStatus;
use App\Enum\DriverStatus;
use App\Enum\FoodicsOrderStatus;
use App\Enum\LuluMarketOrderStatus;
use App\Enum\LyveOrderStatus;
use App\Enum\OrderStatus;
use App\Enum\UserRole;
use App\Events\OrderUpdateStatusWidget;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Dispatcher\AssignOrderDetailsResource;
use App\Http\Resources\Admin\Dispatcher\AssignOrderDriverResource;
use App\Http\Resources\Admin\Dispatcher\ClientOrderSummaryResource;
use App\Http\Resources\Admin\Dispatcher\OrderPopupResource;
use App\Http\Resources\Api\AmericanaWebHookRequestResource;
use App\Http\Resources\Api\Integration\IntegrationResource;
use App\Http\Resources\Api\Loginext\LoginextOrderResource;
use App\Http\Resources\Api\OperatorResource;
use App\Http\Resources\Api\OrderResource;
use App\Http\Services\NotificationService;
use App\Models\Client as ModelsClient;
use App\Models\ClientBranches;
use App\Models\DriverOrderView;
use App\Models\Operator;
use App\Models\OperatorDetail;
use App\Models\Order;
use App\Models\OrderDriver;
use App\Models\OrderLog;
use App\Models\User;
use App\Models\UserCitys;
use App\Models\ViewSearchOrder;
use App\Models\WebHook;
use App\Repositories\FirebaseRepository;
use App\Traits\OrderCreationDateValidation;
use Carbon\Carbon;
use DB;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class DispatcherController extends Controller
{
    use OrderCreationDateValidation;
    protected $firebaseRepository;
    protected $notificationService;

    public function __construct()
    {
        $this->firebaseRepository  = App::make(FirebaseRepository::class);
        $this->notificationService = App::make(NotificationService::class);
    }

    public function getAssignData(Request $request)
    {
        // dd($request->all());
        abort_unless(auth()->user()->hasPermissionTo('can_assign_orders'), 403, 'You do not have permission to view this page.');
        // $order = Order::with('branch')->findOrFail($request->id);

        $orderId = is_array($request->id) ? $request->id[0] : $request->id;

        $order = Order::with('branch')->findOrFail($orderId);

        // dd($order);
        $branch_name     = $order->branch?->name ?? $order->branchIntegration?->name;
        $branch          = $order->branch;
        $branchLatitude  = $branch?->lat ?? 0;
        $branchLongitude = $branch?->lng ?? 0;
        $radiusKm        = (isset($request->radiusKm)) ? (int) $request->radiusKm : 10;
        $latDelta        = $radiusKm / 111; // 1 degree ≈ 111 km
        $lngDelta        = $latDelta / cos(deg2rad($branchLatitude));

        $drivers = OperatorDetail::whereHas('operator.cities', function ($q) use ($order) {
            $q->where('city_id', $order->city);
        })
            ->whereIn('status', [DriverStatus::AVAILABLE->value, DriverStatus::BUSY->value])
            ->where('lat', '!=', 0)
            ->where('lng', '!=', 0)
            ->whereRaw('ST_Contains(
         ST_MakeEnvelope(
             POINT(? - ?, ? - ?),
             POINT(? + ?, ? + ?)
         ),
         location
     )', [
                $branchLongitude,
                $lngDelta,
                $branchLatitude,
                $latDelta,
                $branchLongitude,
                $lngDelta,
                $branchLatitude,
                $latDelta,
            ])
            ->select(
                'operators.id',
                'operators.operator_id',
                'operators.status',
                'operators.lat',
                'operators.lng',
                // 'operator_cities.city_id',
                \DB::raw("ST_Distance_Sphere(location, POINT($branchLongitude, $branchLatitude)) / 1000 AS distance")
            )
        // ->having('distance', '<=', $radiusKm)
            ->orderBy('distance')
            ->with([
                'operator',
                'TodayOrdersDate'        => function ($query) {
                    $query->select('id', 'driver_id', 'status', 'ingr_shop_id', 'ingr_branch_id');
                },
                'TodayOrdersDate.shop'   => function ($query) {
                    $query->select('id', 'first_name');
                },
                'TodayOrdersDate.branch' => function ($query) {
                    $query->select('id', 'name');
                },
            ])
            ->get();

        // dd($drivers);
        return response()->json([
            'order'       => new AssignOrderDetailsResource($order),
            'branch_name' => $branch_name,
            'drivers'     => AssignOrderDriverResource::collection($drivers),
            'radiusKm'    => $radiusKm,
        ]);
    }

    public function assignDriver(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('can_assign_orders'), 403, 'You do not have permission to view this page.');

        $orderIds = is_array($request->order_id)
        ? $request->order_id
        : explode(',', $request->order_id);
        $orderIds = array_map('trim', $orderIds);

        $results = [];
        // dd($orderIds); 132 233
        foreach ($orderIds as $orderId) {
            $order = Order::with('shop', 'branch')->where('id', $orderId)->first();

            if ($order && $order->driver_id != null) {
                $results[$orderId] = 'Error: Please Unassign driver first to reassign order';
                continue;
            }

            if ($order) {

                if ($order->status != OrderStatus::DRIVER_ACCEPTANCE_TIMEOUT) {
                    $order->status = OrderStatus::PENDINE_DRIVER_ACCEPTANCE;
                }
                $order->driver_id          = $request->driver_id;
                $order->driver_assigned_at = now();
                $order->assign_try_count   = 4;
                $order->assigned_by        = auth()->id();
                $order->save();

                $branch          = $order->branch ?? $order->branchIntegration;
                $branchLatitude  = $branch->lat ?? 0;
                $branchLongitude = $branch->lng ?? 0;

                $operator = OperatorDetail::selectRaw("
                id, lat, lng,
                6371 * acos(
                    cos(radians(?))
                    * cos(radians(lat))
                    * cos(radians(lng) - radians(?))
                    + sin(radians(?))
                    * sin(radians(lat))
                ) AS distance
            ", [$branchLatitude, $branchLongitude, $branchLatitude])
                    ->where('operator_id', $request->driver_id)
                    ->first();

                OrderDriver::create([
                    'order_id'  => $order->id,
                    'driver_id' => $request->driver_id,
                    'distance'  => (string) $operator->distance,
                ]);

                $orderResource = new OrderResource($order);
                $orderData     = $orderResource->toArray(request());

                try {
                    $this->firebaseRepository->save_driver_order($request->driver_id, $orderData);
                    $this->notificationService->send_for_specific('طلب جديد ', 'طلب جديد جاهز للتوصيل', $request->driver_id);
                } catch (\Exception $e) {
                    Log::info($e);
                }

                $driver = Operator::findOrFail($request->driver_id);
                OrderLog::create([
                    'order_id'    => $order->id,
                    'status'      => OrderStatus::PENDINE_DRIVER_ACCEPTANCE,
                    'action'      => 'Assign Order',
                    'driver_id'   => $request->driver_id,
                    'user_id'     => auth()->id(),
                    'description' => auth()->user()->first_name . ' assign order to driver ' . $driver->full_name,
                ]);

                $client = $order->shop?->client ?? $order->branchIntegration?->client?->client;
                if ($client?->integration && $client->integration->id == 22) {
                    $webhook = WebHook::where('integration_company_id', $client->integration->id)
                        ->where('type', 'order_created')
                        ->first();
                    if ($webhook && $webhook->url) {
                        $order->status = OrderStatus::PENDINE_DRIVER_ACCEPTANCE;
                        $orderData     = new LoginextOrderResource($order);
                        $this->sendOrderToWebhook($webhook->url, $orderData);
                    }
                }
                $results[$orderId] = 'Driver assigned successfully';
            } else {
                $results[$orderId] = 'Order not found';
            }
        }

        return response()->json([
            'status'  => 200,
            'results' => $results,
        ]);
    }

    public function UnassignDriver(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('can_unassign_orders'), 403, 'You do not have permission to view this page.');

        $order_ids = is_array($request->order_id) ? $request->order_id : [$request->order_id];

        foreach ($order_ids as $order_id) {
            $order_id = intval($order_id);
            $order    = Order::where('id', $order_id)->first();

            if (! $order) {
                continue;
            }

            if ($order->status == OrderStatus::DELIVERED) {
                return response()->json("Can't unassign order #$order_id, because it is already delivered");
            }

            if (isset($order->driver_id) && $order->driver_id != null) {
                try {
                    $this->firebaseRepository->delete_driver_order($order->driver_id, $order_id);

                    $firebase = new \App\Repositories\FirebaseRepositoryTest();
                    $firebase->deleteMapOrder($order->id);
                } catch (\Exception $e) {
                    Log::info($e);
                }
            }

            $order->driver_id = null;
            $order->status    = OrderStatus::DRIVER_ACCEPTANCE_TIMEOUT;
            $order->save();

            OrderLog::create([
                'order_id'    => $order_id,
                'status'      => $order->status,
                'action'      => 'Unassign Order',
                'driver_id'   => 0,
                'user_id'     => auth()->id(),
                'description' => auth()->user()->first_name . ' unassigned driver from order #' . $order_id,
            ]);
        }

        return response()->json('success');
    }

    public function ChangeStatusToDelivered(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('can_change_status_to_delivered_orders'), 403, 'You do not have permission to view this page.');

        $order_ids = is_array($request->order_id) ? $request->order_id : [$request->order_id];

        $results = [];

        foreach ($order_ids as $order_id) {
            $order_id = intval($order_id);
            $order    = Order::where('id', $order_id)->first();

            if (! $order) {
                $results[$order_id] = 'Order not found';
                continue;
            }
            if ($order->status == OrderStatus::DELIVERED->value) {
                $results[$order_id] = 'Already delivered';
                continue;
            }

            if (isset($order->driver_id) && $order->driver_id != null) {
                try {
                    $this->firebaseRepository->delete_driver_order($order->driver_id, $order_id);
                } catch (\Exception $e) {
                    Log::info($e);
                }
            }

            $order->status       = OrderStatus::DELIVERED;
            $order->delivered_at = Carbon::now('Asia/Riyadh');
            $order->save();
            $this->sendDeliverectOrderStatus($order);
            OrderLog::create([
                'order_id'    => $order_id,
                'status'      => $order->status,
                'action'      => 'Order delivered',
                'driver_id'   => $order->driver_id,
                'user_id'     => auth()->id(),
                'description' => auth()->user()->first_name . ' has changed order status to delivered',
            ]);

            $results[$order_id] = 'Delivered successfully';
        }

        return response()->json($results);
    }

    private function sendDeliverectOrderStatus($order)
    {
        $client = $order->shop?->client ?? $order->branchIntegration?->client?->client;
        if ($client?->integration) {
            $webhook = WebHook::where('integration_company_id', $client?->integration?->id)->where('type', 'order_updated')->first();
            if ($webhook && $webhook->url) {

                if ($webhook->integration_company_id == 10) {
                    if ($order->status == OrderStatus::ARRIVED_PICK_UP || $order->status == OrderStatus::PICKED_UP) {
                        $lat = $order->branch->lat;
                        $lng = $order->branch->lng;
                    } elseif ($order->status == OrderStatus::ARRIVED_TO_DROPOFF || $order->status == OrderStatus::DELIVERED) {
                        $lat = $order->lat;
                        $lng = $order->lng;
                    } else {
                        $lat = $order->OperatorDetail->lat;
                        $lng = $order->OperatorDetail->lng;
                    }
                    $orderData = [
                        "order_id"        => $order->id,
                        'status_id'       => $order->status->value,
                        'client_order_id' => $order->client_order_id_string,
                        'status'          => $order->status->getLabel(),
                        'created_at'      => Carbon::parse($order->created_at)->format('Y-m-d H:i:s'),
                        'driver'          => $order->driver ? [
                            'id'           => $order->driver?->driver?->id,
                            'name'         => $order->driver?->driver?->full_name,
                            'phone'        => $order->driver?->driver?->phone,
                            'status'       => DriverStatus::tryFrom($order->driver?->driver?->operator?->status)?->getLabel(),
                            'tracking_url' => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",
                            'location'     => [
                                'lat' => $lat,
                                'lng' => $lng,
                            ],
                        ] : null,
                    ];
                    $this->sendOrderToWebhook($webhook->url, $orderData);
                } elseif ($client?->integration?->id == 128 || $client?->integration?->id == 146) {

                    $orderData = [
                        "order_id"        => $order->id,
                        'status_id'       => $order->status->value,
                        'client_order_id' => $order->client_order_id_string,
                        'status'          => $order->status->getLabel(),
                        'created_at'      => $order->created_at,
                        'invoice_url'     => $order->PdfUrl,
                        'tracking_url'    => "https://www.google.com/maps?q={$order->DriverData2?->operator?->lat},{$order->DriverData2?->operator?->lng}",
                        'driver'          => $order->DriverData2 ? [
                            'id'           => $order->DriverData2?->id,
                            'name'         => $order->DriverData2?->full_name,
                            'phone'        => $order->DriverData2?->phone,
                            'status'       => DriverStatus::tryFrom($order->DriverData2?->operator?->status)?->getLabel(),
                            'tracking_url' => "https://www.google.com/maps?q={$order->DriverData2?->operator?->lat},{$order->DriverData2?->operator?->lng}",

                        ] : null,
                    ];
                    $this->sendToWasftyWebhook($webhook->url, $orderData, $webhook->token);
                } elseif ($client?->integration?->id == 15) {
                    //                    $orderData = new \App\Http\Resources\Api\americana\OrderResource($order);
                    $orderData = new AmericanaWebHookRequestResource($order);

                    $this->sendOrderToWebhookamericana($webhook->url, $orderData);
                } elseif ($client?->integration?->id == 153) {
                    $token = $order->additional_details['task_id'];

                    $orderData = [
                        'task_id'       => $token,
                        'status'        => LuluMarketOrderStatus::GetStatus($order->status),
                        'delivery_info' => $order->driver ? [
                            'name'      => $order->driver?->driver?->full_name,
                            'phone'     => $order->driver?->driver?->phone,
                            'photo_url' => $order->DriverData2?->ImageUrl,
                        ] : null,
                    ];
                    $this->sendOrderToWebhook($webhook->url, $orderData);
                } elseif ($client?->integration?->id == 120) {
                    $job_id = $order->jop_id;
                    $lat    = $order->driver?->driver?->operator?->lat;
                    $lng    = $order->driver?->driver?->operator?->lng;
                    $data   = [
                        "deliveryJobId"   => "$job_id",
                        "deliveryTimeETA" => Carbon::parse($order->driver_accepted_time, 'UTC')->addSeconds($order->pickup_duration),
                        "transportType"   => "bicycle",
                        "trackingUrl"     => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",
                        "courier"         => [
                            "name"      => auth()->user()->full_name,
                            "phone"     => auth()->user()->phone,
                            "longitude" => "$lng",
                            "latitude"  => "$lat",
                        ],
                        "locations"       => [
                            [
                                "orderId"         => "$order->client_order_id_string",
                                "status"          => DeliverectOrderStatus::GetStatus($order->status),
                                "deliveryTimeETA" => Carbon::parse($order->driver_accepted_time, 'UTC')->addSeconds($order->pickup_duration + $order->delivery_duration),
                            ],
                        ],
                    ];
                    $this->sendOrderToWebhook("https://api.deliverect.com/fulfillment/generic/events", $data);
                } elseif ($client?->integration?->id == 121 || $client?->integration?->id == 140) {
                    $driver_id = auth()->user()->id;
                    $data      = [
                        "order_id"      => "$order->id",
                        "status"        => LyveOrderStatus::GetStatus($order->status),
                        "driver"        => [
                            "id"           => "$driver_id",
                            "name"         => auth()->user()->full_name,
                            "phone_number" => auth()->user()->phone,
                            "vehicle_type" => "Bike",
                        ],
                        'eta'           => [
                            'pickup'   => Carbon::parse($order->driver_accepted_time)->addSeconds($order->pickup_duration)->timestamp,
                            'delivery' => Carbon::parse($order->driver_accepted_time)->addSeconds($order->pickup_duration + $order->delivery_duration)->timestamp,
                        ],
                        "timestamp"     => Carbon::now()->timestamp,
                        "tracking_link" => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",
                    ];
                    $token = $order->additional_details['callback_token'];
                    $this->SendToLyve($token, $data);
                } elseif ($client?->integration?->id == 14) {
                    $this->FoodicsWebhook($order->client_order_id_string, $order->shop?->client->foodics_token, [
                        "driver_assigned_at" => Carbon::now()->format('Y-m-d H:i:s'),
                        "dispatched_at"      => Carbon::parse($order->updated_at)->format('Y-m-d H:i:s'),
                        "delivered_at"       => Carbon::parse($order->created_at)->addMinutes(30)->format('Y-m-d H:i:s'),
                        "driver_id"          => auth()->user()->id,
                        "delivery_status"    => FoodicsOrderStatus::GetStatus($order->status),
                    ]);
                } elseif ($client?->integration?->id == 13) {
                    //blink
                    $data = [
                        "blink_order_id" => "$order->client_order_id_string",
                        "status"         => BlinkOrderStatus::GetStatus($order->status),
                    ];
                    $this->blinkHook($webhook->url, $data);
                    //blink
                    $this->sendOrderToWebhook($webhook->url, $data);
                } elseif ($client?->integration?->id == 125 || $client?->integration?->id == 126 || $client?->integration?->id == 127) {
                    $orderData = [
                        'order_id'        => $order->id,
                        'status_id'       => $order->status->value,
                        'client_order_id' => $order->client_order_id_string,
                        'status_label'    => $order->status->getLabel(),
                        'driver'          => $order->driver ? [
                            'id'           => $order->driver?->driver?->id,
                            'name'         => $order->driver?->driver?->full_name,
                            'phone'        => $order->driver?->driver?->phone,
                            'status'       => DriverStatus::tryFrom($order->driver?->driver?->operator?->status)?->getLabel(),
                            'tracking_url' => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",
                        ] : null,
                    ];
                    $this->sendOrderToWebhook($webhook->url, $orderData);
                } elseif ($webhook->integration_company_id == 21) {
                    $webhook = WebHook::where('integration_company_id', $client?->integration?->id)->where('type', 'order_updated')->first();

                    $orderData = [
                        "order_id"        => $order->id,
                        'status_id'       => $order->status->value,
                        'client_order_id' => $order->client_order_id_string,
                        'status'          => $order->status->getLabel(),
                        'created_at'      => Carbon::parse($order->created_at)->format('Y-m-d H:i:s'),
                        'driver'          => $order->driver ? [
                            'id'           => $order->driver?->driver?->id,
                            'name'         => $order->driver?->driver?->full_name,
                            'phone'        => $order->driver?->driver?->phone,
                            'status'       => DriverStatus::tryFrom($order->driver?->driver?->operator?->status)?->getLabel(),
                            'tracking_url' => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",

                        ] : null,
                    ];
                    $this->sendOrderToWebhook($webhook->url, $orderData);
                } elseif ($webhook->integration_company_id == 22) {
                    $webhook = WebHook::where('integration_company_id', $client?->integration?->id)->where('type', 'order_updated')->first();

                    $orderData = new LoginextOrderResource($order);

                    //                    if ($order->status != OrderStatus::ARRIVED_PICK_UP) {
                    $this->sendOrderToWebhookLoginNext($webhook->url, $orderData);
                    //                    }

                } else {
                    if ($webhook->client_type == 1) {

                        $orderData = new IntegrationResource($order);
                        $this->sendOrderIntegrationToWebhook($webhook, $orderData, $webhook->token);
                    } else {
                        $orderData = [
                            "order_id"        => $order->id,
                            'status'          => $order->status->value,
                            'client_order_id' => $order->client_order_id_string ?? $order->client_order_id ?? $order->id,
                            'status_label'    => $order->status->getLabel(),
                            'driver'          => $order->driver ? [
                                'id'           => $order->driver?->driver?->id,
                                'name'         => $order->driver?->driver?->full_name,
                                'phone'        => $order->driver?->driver?->phone,
                                'status'       => DriverStatus::tryFrom($order->driver?->driver?->operator?->status)?->getLabel(),
                                'tracking_url' => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",
                                'latitude'     => $order->DriverData2?->operator?->lat,
                                'longitude'    => $order->DriverData2?->operator?->lng,
                            ] : null,
                        ];
                        $this->sendOrderToWebhook($webhook->url, $orderData);
                    }
                }
                // dd($webhook->url);

                //               $this->sendOrderToWebhook('https://webhook.site/329479cd-aea3-4e39-a77d-5384ceeab723', $orderData);
            }
        }
    }

    public function getOrderHistory(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('basic_dispatcher_view'), 403, 'You do not have permission to view this page.');

        $histories = OrderLog::orderby('id')->where('order_id', $request->order_id)->get();
        $order     = Order::findOrFail($request->order_id);

        $brand  = $order->shop?->first_name ?? $order->branchIntegration?->client?->first_name;
        $branch = $order->branch?->name ?? $order->branchIntegration?->name;

        $cancel_reason = $order->cancel_reason?->name ?? '---';
        return response()->json(['histories' => $histories, 'order_number' => $order->order_number, 'order' => $order, 'cancel_reason' => $cancel_reason, 'branch' => $branch, 'brand' => $brand]);
    }

    public function getStatistics(Request $request)
    {
        // dd(9);
        // التحقق من الصلاحيات
        abort_unless(auth()->user()->hasPermissionTo('basic_dispatcher_view'), 403);

        // مفتاح التخزين المؤقت
        $cacheKey = "order_stats_" . auth()->id() . "_" . md5(json_encode($request->all()));

        // التحقق من وجود طلب تحديث قسري
        $forceRefresh = $request->has('force_refresh') && $request->force_refresh;

        // إذا كان هناك طلب تحديث قسري، قم بحذف التخزين المؤقت
        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        // استخدام التخزين المؤقت لمدة 10 ثانية
        return Cache::remember($cacheKey, 10, function () use ($request) {
            try {
                // بناء الاستعلام الأساسي
                $getDateTime = $this->getBusinessHoursIfNowWithinRange();

                $query = Order::whereBetween('created_at', [
                    $getDateTime['start'],
                    $getDateTime['end'],
                ]);
                $this->ActionRoleQueryWhere($query, null, null);
                // تطبيق الفلاتر
                if ($request->has('filter') && $request->filter == 0) {
                    $query->where('created_at', '<=', Carbon::now()->subMinutes(3));
                }

                // تطبيق فلاتر الأدوار
                $user = auth()->user();
                if ($user->user_role == UserRole::CLIENT) {
                    $query->where('ingr_shop_id', $user->id);
                } elseif ($user->user_role == UserRole::BRANCH) {
                    $query->where('ingr_branch_id', $user->branch_id);
                }

                // استعلام واحد للحصول على جميع الإحصائيات
                $stats = $query->selectRaw('
                   SUM(CASE WHEN status IN (?, ?) THEN 1 ELSE 0 END) as pending_order,
                   SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as auto_dispatch,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as time_out,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as accept_order,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as DriverAtPickup,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as OrderpickedOrder,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as DriverAtDropOffOrder,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as CancellationOrder,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as ClientCancellationRequest,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as FailedOrder,
            SUM(CASE WHEN status NOT IN (?, ?) THEN 1 ELSE 0 END) as all_order

                ', [
                    OrderStatus::CREATED,
                    OrderStatus::PENDINE_DRIVER_ACCEPTANCE,
                    OrderStatus::AUTO_DISPATCH,
                    OrderStatus::DRIVER_ACCEPTANCE_TIMEOUT,
                    OrderStatus::DRIVER_ACCEPTED,
                    OrderStatus::ARRIVED_PICK_UP,
                    OrderStatus::PICKED_UP,
                    OrderStatus::ARRIVED_TO_DROPOFF,
                    OrderStatus::PENDING_ORDER_CANCELLATION,
                    OrderStatus::PENDING_CLIENT_ORDER_CANCELLATION,
                    OrderStatus::FAILED,
                    OrderStatus::DELIVERED,
                    OrderStatus::CANCELED,
                ])->first();

                return response()->json([
                    'All-orders'                   => $stats->all_order,
                    'pending-order'                => $stats->pending_order,
                    'auto-dispatch'                => $stats->auto_dispatch,
                    'time-out-order'               => $stats->time_out,
                    'accepted-order'               => $stats->accept_order,
                    'driver-at-pickup-order'       => $stats->DriverAtPickup,
                    'picked-order'                 => $stats->OrderpickedOrder,
                    'driver-at-dropoff-order'      => $stats->DriverAtDropOffOrder,
                    'completed-order'              => 0,
                    'cancellation-requests'        => $stats->CancellationOrder,
                    'client-cancellation-requests' => $stats->ClientCancellationRequest,
                    'cancelled-order'              => 0,
                    'failed-order'                 => $stats->FailedOrder,
                    'cache_time'                   => now()->toDateTimeString(),
                    'cache_expires'                => now()->addSeconds(10)->toDateTimeString(),
                ]);
            } catch (\Exception $e) {

                return response()->json([
                    'error'   => 'حدث خطأ أثناء جلب الإحصائيات',
                    'message' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }
        });
    }

    public function getStatisticsOld()
    {
        abort_unless(auth()->user()->hasPermissionTo('basic_dispatcher_view'), 403, 'You do not have permission to view this page.');

        $getDateTime = $this->getBusinessHoursIfNowWithinRange();
        $ordersQuery = Order::query()->whereBetween('created_at', [
            $getDateTime['start'],
            $getDateTime['end'],
        ]);
        // dd(request()->filter);
        if (request()->has('filter') && request()->filter == 0) {

            $ordersQuery->where('created_at', '<=', Carbon::now()->subMinutes(3));
        }

        if (auth()->user()->user_role == UserRole::CLIENT) {
            $ordersQuery->where('ingr_shop_id', auth()->id());
        }

        if (auth()->user()->user_role == UserRole::BRANCH) {
            $ordersQuery->where('ingr_branch_id', auth()->user()->branch_id);
        }
        $this->ActionRoleQueryWhere($ordersQuery, null, null);

        $statusCounts = $ordersQuery
            ->selectRaw('
            SUM(CASE WHEN status IN (?, ?) THEN 1 ELSE 0 END) as pending_order,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as time_out,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as auto_dispatch,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as accept_order,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as DriverAtPickup,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as OrderpickedOrder,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as DriverAtDropOffOrder,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as CancellationOrder,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as ClientCancellationRequest,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as FailedOrder,
            SUM(CASE WHEN status NOT IN (?, ?) THEN 1 ELSE 0 END) as all_order
        ', [
                OrderStatus::CREATED,
                OrderStatus::PENDINE_DRIVER_ACCEPTANCE,         // pending_order
                OrderStatus::DRIVER_ACCEPTANCE_TIMEOUT,         // time_out
                OrderStatus::AUTO_DISPATCH,                     // auto_dispatch
                OrderStatus::DRIVER_ACCEPTED,                   // accept_order
                OrderStatus::ARRIVED_PICK_UP,                   // DriverAtPickup
                OrderStatus::PICKED_UP,                         // OrderpickedOrder
                OrderStatus::ARRIVED_TO_DROPOFF,                // DriverAtDropOffOrder
                OrderStatus::PENDING_ORDER_CANCELLATION,        // CancellationOrder
                OrderStatus::PENDING_CLIENT_ORDER_CANCELLATION, // ClientCancellationRequest
                OrderStatus::FAILED,                            // FailedOrder
                OrderStatus::CANCELED,
                OrderStatus::DELIVERED, // all_order
            ])
            ->first();

        return response()->json([
            'All-orders'                   => $statusCounts->all_order,
            'pending-order'                => $statusCounts->pending_order,
            'time-out-order'               => $statusCounts->time_out,
            'auto_dispatch'                => $statusCounts->auto_dispatch,
            'accepted-order'               => $statusCounts->accept_order,
            'driver-at-pickup-order'       => $statusCounts->DriverAtPickup,
            'picked-order'                 => $statusCounts->OrderpickedOrder,
            'driver-at-dropoff-order'      => $statusCounts->DriverAtDropOffOrder,
            'completed-order'              => 0,
            'cancellation-requests'        => $statusCounts->CancellationOrder,
            'client-cancellation-requests' => $statusCounts->ClientCancellationRequest,
            'cancelled-order'              => 0,
            'failed-order'                 => $statusCounts->FailedOrder,
        ]);
    }

    public function getDriverOrders(Request $request)
    {
        // dd($request->all());
        abort_unless(auth()->user()->hasPermissionTo('can_get_driver'), 403, 'You do not have permission to view this page.');

        $order = Order::findOrFail($request->order_id);

        $driver = Operator::findOrFail($request->driver_id);
        // dd($driver);

        // $driver->OrderData()
        // ->where(function ($query) {
        //     $query->whereDate('orders.created_at', Carbon::yesterday())
        //         ->orWhereDate('orders.created_at', Carbon::today());
        // })
        // ->whereNotIn('status', [9, 10])
        // ->count() * 2;

        $orders = $driver->OrderData()
            ->where(function ($query) {
                $query->whereDate('orders.created_at', Carbon::yesterday())
                    ->orWhereDate('orders.created_at', Carbon::today());
            })
            ->whereIn('status', [17, 16, 6, 4, 8, 21, 22])
            ->orderBy('created_at', 'desc')->get()
            ->map(function ($order) {
                $order->jop_id = $order->client_order_id ?? $order->id;

                $order->shop_name    = $order->shop?->first_name ?? $order->branchIntegration?->client?->first_name;
                $order->branch_name  = $order->branch?->name ?? $order->branchIntegration?->name;
                $order->branch_phone = $order->branch?->phone ?? $order->branchIntegration?->phone;
                $order->branch_area  = $order->branch?->area?->name ?? $order->branchIntegration?->area?->name;

                $order->shop_profile = $order->shop?->image;
                $order->status_label = $order->status->getLabel();
                $order->order_number = $order->order_number;

                return $order;
            });
        // dd($orders);
        return response()->json([
            'order'  => $order,
            'driver' => $driver,
            'orders' => $orders,
        ]);
    }

    public function GetOrdersData()
    {
        // dd(9);
        abort_unless(auth()->user()->hasPermissionTo('basic_dispatcher_view'), 403, 'You do not have permission to view this page.');
    $getDateTime = $this->getBusinessHoursIfNowWithinRange();

        if (request()->has('filter') && request()->filter == 0) {

            $statuses = [
                'pending-order' => [OrderStatus::CREATED, OrderStatus::PENDINE_DRIVER_ACCEPTANCE],
            ];

            $ordersQuery = Order::orderBy('created_at', 'asc')
                ->where('created_at', '<=', Carbon::now()->subMinutes(3))
                ->whereBetween('created_at', [
                    $getDateTime['start'],
                    $getDateTime['end'],
                ]);
        } else {

            $statuses = [
                'pending-order'                => [OrderStatus::CREATED, OrderStatus::PENDINE_DRIVER_ACCEPTANCE],
                'time-out-order'               => [OrderStatus::DRIVER_ACCEPTANCE_TIMEOUT],
                'auto-dispatch'               => [OrderStatus::AUTO_DISPATCH],
                'accepted-order'               => [OrderStatus::DRIVER_ACCEPTED],
                'driver-at-pickup-order'       => [OrderStatus::ARRIVED_PICK_UP],
                'picked-order'                 => [OrderStatus::PICKED_UP],
                'driver-at-dropoff-order'      => [OrderStatus::ARRIVED_TO_DROPOFF],
                'completed-order'              => [OrderStatus::DELIVERED],
                'cancellation-requests'        => [OrderStatus::PENDING_ORDER_CANCELLATION],
                'client-cancellation-requests' => [OrderStatus::PENDING_CLIENT_ORDER_CANCELLATION],
                'cancelled-order'              => [OrderStatus::CANCELED],
                'failed-order'                 => [OrderStatus::FAILED],
            ];

            $ordersQuery = Order::orderBy('created_at', 'asc')
                ->whereBetween('created_at', [
                    $getDateTime['start'],
                    $getDateTime['end'],
                ]);
        }

        DispatcherController::ActionRoleQueryWhere($ordersQuery, null, null);

        if (in_array(request()->status, array_keys($statuses))) {
            $ordersQuery->whereIn('status', $statuses[request()->status]);
        } else {
            $ordersQuery->whereNotIn('status', [OrderStatus::CANCELED, OrderStatus::DELIVERED]);
        }

        if (auth()->user()->user_role == UserRole::CLIENT) {
            $ordersQuery->where('ingr_shop_id', auth()->id());
        }

        if (auth()->user()->user_role == UserRole::BRANCH) {
            $ordersQuery->where('ingr_branch_id', auth()->user()->branch_id);
        }

        $orders = $ordersQuery
            ->with(['branch', 'shop', 'driver.driver'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'orders'    => \App\Http\Resources\Admin\Dispatcher\OrderResource::collection($orders),
            'user_role' => auth()->user()->user_role->value,
        ]);
    }

    public function getOrderPopup(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('basic_dispatcher_view'), 403, 'You do not have permission to view this page.');

        $order = Order::with(['shop', 'branch', 'DriverData2'])->findOrFail($request->id);

        // Transform the order data
        //        $order->shop_name = $order->shop?->full_name;
        //        $order->branch_name = $order->branch?->name;
        //        $order->branch_lat = $order->branch?->lat;
        //        $order->branch_lng = $order->branch?->lng;
        //        //        $order->order_number = $order->order_number;
        //        $order->driver_name = $order->DriverData2?->full_name;
        //        $order->driver_phone = $order->DriverData2?->phone;
        //        $order->driver_photo =  $order->DriverData2?->image;
        //        $order->order_address = $order->branch ?
        //            $order->branch?->city?->name . ' ' . $order->branch?->street :
        //            $order->branchIntegration?->city?->name . ' ' . $order->branch?->street;
        //
        //        // Add order log date values
        //        $order->created_time = $order->created_at->format('h:i a');
        //        $order->assign_date = $order->driver_assigned_at ? $order->driver_assigned_at->format('Y-m-d h:i a') : null;
        //        $order->accept_date = $order->driver_accepted_time ? $order->driver_accepted_time->format('Y-m-d h:i a') : null;
        //        $order->arrive_branch_date = $order->arrived_to_pickup_time ? $order->arrived_to_pickup_time->format('Y-m-d h:i a') : null;
        //        $order->recive_date = $order->picked_up_time ? $order->picked_up_time->format('Y-m-d h:i a') : null;
        //        $order->arrive_client_date = $order->arrived_to_dropoff_time ? $order->arrived_to_dropoff_time->format('Y-m-d h:i a') : null;
        //        $order->delivery_date = $order->delivered_at ? $order->delivered_at->format('Y-m-d h:i a') : null;
        //        $order->created_date = $order->created_at ? $order->created_at->format('Y-m-d h:i a') : null;
        //
        //        // Additional order details
        //        $order->shop_name = $order->shop?->first_name ?? $order->branchIntegration?->client?->first_name;
        //        $order->branch_name = $order->branch?->name ?? $order->branchIntegration?->name;
        //        $order->branch_phone = $order->branch?->phone ?? $order->branchIntegration?->phone;
        //        $order->branch_area = $order->branch?->area?->name ?? $order->branchIntegration?->area?->name;
        //
        //        $order->shop_profile = $order->shop?->image;
        //        $order->status_label = $order->status->getLabel();
        //        $order->payment_type_label = $order->payment_type ? $order->payment_type->getLabel() : '---';
        //        $order->vehicle_type = $order->vehicle?->type;

        return response()->json(['order' => new OrderPopupResource($order)]);
    }

    private function getOrderLogDate($orderId, $status)
    {
        return OrderLog::where('order_id', $orderId)
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->first()?->created_at->format('Y-m-d h:i a');
    }

    // public function GetSearchOrdersData(Request $request): \Illuminate\Http\JsonResponse
    // {
    //     abort_unless(auth()->user()->hasPermissionTo('basic_dispatcher_view'), 403, 'You do not have permission to view this page.');
    //     // dd($search);
    //     $ordersQuery = Order::orderByDesc('id');

    //     $search = $request->search;

    //     if ($search) {

    //         $ordersQuery->where(function (Builder $query) use ($search) {
    //             $query->where('client_order_id', 'like', '%' . $search . '%')
    //                 ->orWhere('id', 'like', '%' . $search . '%')
    //                 ->orWhere('customer_phone', 'like', '%' . $search . '%')
    //                 ->orWhere('customer_name', 'like', '%' . $search . '%')
    //                 ->orWhereHas('shop', function ($query) use ($search) {
    //                     $query->where('users.first_name', 'like', '%' . $search . '%')
    //                         ->orWhere('users.phone', 'like', '%' . $search . '%');
    //                 })
    //                 ->orWhereHas('driver', function ($query) use ($search) {
    //                     $query->whereHas('driver', function ($query) use ($search) {
    //                         $query->whereRaw("CONCAT(users.first_name, ' ', users.last_name) LIKE ?", ['%' . $search . '%'])
    //                             ->orWhere('users.phone', 'like', '%' . $search . '%');
    //                     });
    //                 })
    //                 ->orWhereHas('branch', function ($query) use ($search) {
    //                     $query->where('client_branches.name', 'like', '%' . $search . '%')
    //                         ->orWhere('client_branches.phone', 'like', '%' . $search . '%');
    //                 })
    //                 ->orWhereHas('branchIntegration', function ($query) use ($search) {
    //                     $query->where('client_branches.name', 'like', '%' . $search . '%')
    //                         ->orWhere('client_branches.phone', 'like', '%' . $search . '%');
    //                 });
    //         });
    //     }

    //     if (auth()->user()->user_role == UserRole::CLIENT) {
    //         $ordersQuery->where('ingr_shop_id', auth()->id());
    //     }

    //     if (auth()->user()->user_role == UserRole::BRANCH) {
    //         $ordersQuery->where('ingr_branch_id', auth()->user()->branch_id);
    //     }

    //     $statuses = [
    //         'Pending_order' => [OrderStatus::CREATED, OrderStatus::PENDINE_DRIVER_ACCEPTANCE, OrderStatus::DRIVER_ACCEPTANCE_TIMEOUT],
    //         'active_order' => [OrderStatus::DRIVER_ACCEPTED, OrderStatus::ARRIVED_TO_DROPOFF, OrderStatus::ARRIVED_PICK_UP, OrderStatus::PICKED_UP],
    //         'CancelledOrder' => [OrderStatus::CANCELED],
    //         'completed_order' => [OrderStatus::DELIVERED],
    //     ];

    //     $order_count = $ordersQuery->count();

    //     foreach ($statuses as $key => $status) {
    //         $$key = (clone $ordersQuery)->whereIn('status', $status)->orderBy('created_at', 'desc')
    //             ->paginate(10);
    //     }
    //     return response()->json([
    //         'order_data' => ['order_count' => $order_count,   'user_role' => auth()->user()->user_role->value,],

    //         'pending-order' => \App\Http\Resources\Admin\Dispatcher\OrderResource::collection($Pending_order),
    //         'active_order' => \App\Http\Resources\Admin\Dispatcher\OrderResource::collection($active_order),
    //         'CancelledOrder' => \App\Http\Resources\Admin\Dispatcher\OrderResource::collection($CancelledOrder),
    //         'completed_order' => \App\Http\Resources\Admin\Dispatcher\OrderResource::collection($completed_order),

    //     ]);
    // }

    public function GetSearchOrdersDataNewNot(Request $request): \Illuminate\Http\JsonResponse
    {
        abort_unless(auth()->user()->hasPermissionTo('basic_dispatcher_view'), 403, 'You do not have permission to view this page.');

        $search     = $request->search;
        $searchTerm = $request->search;
        //if($request->ajax() && $searchTerm){
        if ($search) {
            $ordersQuery = Order::orderByDesc('id');
            if ($search) {
                $ordersQuery = $ordersQuery->where(function ($query) use ($search) {
                    $query->where('client_order_id_string', 'like', '%' . $search . '%')
                        ->orWhere('customer_phone', 'like', '%' . $search . '%')
                        ->orWhere('customer_name', 'like', '%' . $search . '%')
                    //                    ->orWhereHas('shop', fn($q) => $q->where('users.first_name', 'like', '%' . $search . '%')
                    //                        ->orWhere('users.phone', 'like', '%' . $search . '%'))
                        ->orWhereHas('shop', function ($query3) use ($search) {
                            $query3->orWhere('first_name', 'like', '%' . $search . '%')
                                ->orWhere('phone', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('DriverDataSearch', function ($query2) use ($search) {
                            $query2->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $search . '%'])
                                ->orWhere('phone', 'like', '%' . $search . '%');
                        })

                        ->orWhereHas('branch', fn($q) => $q->where('client_branches.name', 'like', '%' . $search . '%')
                                ->orWhere('client_branches.phone', 'like', '%' . $search . '%'))
                        ->orWhereHas('branchIntegration', fn($q) => $q->where('client_branches.name', 'like', '%' . $search . '%')
                                ->orWhere('client_branches.phone', 'like', '%' . $search . '%'));
                    // البحث  فقط إذا كانت القيمة رقمية
                    if (ctype_digit($search)) {
                        $query->orWhere('id', 'like', '%' . $search . '%')
                            ->orWhere('client_order_id', 'like', '%' . $search . '%');
                    }
                })->take(10);
            }

            if (auth()->user()->user_role == UserRole::CLIENT) {
                $ordersQuery->where('ingr_shop_id', auth()->id());
            }

            if (auth()->user()->user_role == UserRole::BRANCH) {
                $ordersQuery->where('ingr_branch_id', auth()->user()->branch_id);
            }

            $statuses = [
                'pending-order'   => [OrderStatus::CREATED, OrderStatus::PENDINE_DRIVER_ACCEPTANCE, OrderStatus::DRIVER_ACCEPTANCE_TIMEOUT],
                'active_order'    => [OrderStatus::DRIVER_ACCEPTED, OrderStatus::ARRIVED_TO_DROPOFF, OrderStatus::ARRIVED_PICK_UP, OrderStatus::PICKED_UP],
                'CancelledOrder'  => [OrderStatus::CANCELED],
                'completed_order' => [OrderStatus::DELIVERED],
            ];

            $order_count = $ordersQuery->count();
            $results     = [];
            foreach ($statuses as $key => $status) {
                $queryClone    = clone $ordersQuery;
                $results[$key] = \App\Http\Resources\Admin\Dispatcher\OrderResource::collection(
                    $queryClone->whereIn('status', $status)->paginate(10)
                );
            }
            //dd($ordersQuery->get());
            //dd($results);
            // dd(array_merge(
            //     ['order_data' => ['order_count' => $order_count, 'user_role' => auth()->user()->user_role->value]],
            //     $results
            // ));
            return response()->json(array_merge(
                ['order_data' => ['order_count' => $order_count, 'user_role' => auth()->user()->user_role->value]],
                $results
            ));
        }
        return response()->json();
    }
    //    public function GetSearchOrdersData(Request $request): \Illuminate\Http\JsonResponse
    //    {
    //        abort_unless(auth()->user()->hasPermissionTo('basic_dispatcher_view'), 403, 'You do not have permission to view this page.');
    //        $today = Carbon::today();
    //        $yesterday = Carbon::yesterday();
    //
    //        $search = $request->search;
    //        $searchTerm = $request->search;
    //        //if($request->ajax() && $searchTerm){
    //        if ($search) {
    //            $search = $request->search;
    //
    //            // Base query
    //            $ordersQuery = Order::whereDate('created_at', '>=', Carbon::yesterday())->with(['shop', 'driver', 'branch', 'branchIntegration'])
    //                ->orderByDesc('id');
    //
    //            // Optimize search condition if provided
    //            if ($search) {
    //                $ordersQuery->where(function (Builder $query) use ($search) {
    //                    // Reduce repetition of similar orWhere clauses by grouping fields together
    //                    $query->where('client_order_id', 'like', '%' . $search . '%')
    //                        ->orWhere('id', 'like', '%' . $search . '%')
    //                        ->orWhere('customer_phone', 'like', '%' . $search . '%')
    //                        ->orWhere('customer_name', 'like', '%' . $search . '%');
    //
    //                    // Optimize `orWhereHas` for relationships
    //                    $query->orWhereHas('shop', fn($q) => $q->where(function ($query) use ($search) {
    //                        $query->where('users.first_name', 'like', '%' . $search . '%')
    //                            ->orWhere('users.phone', 'like', '%' . $search . '%');
    //                    }));
    //
    //                    $query->orWhereHas('driver', fn($q) => $q->whereHas('driver', function ($query) use ($search) {
    //                        $query->whereRaw("CONCAT(users.first_name, ' ', users.last_name) LIKE ?", ['%' . $search . '%'])
    //                            ->orWhere('users.phone', 'like', '%' . $search . '%');
    //                    }));
    //
    //                    $query->orWhereHas('branch', fn($q) => $q->where(function ($q) use ($search) {
    //                        $q->where('client_branches.name', 'like', '%' . $search . '%')
    //                            ->orWhere('client_branches.phone', 'like', '%' . $search . '%');
    //                    }));
    //
    //                    $query->orWhereHas('branchIntegration', fn($q) => $q->where(function ($q) use ($search) {
    //                        $q->where('client_branches.name', 'like', '%' . $search . '%')
    //                            ->orWhere('client_branches.phone', 'like', '%' . $search . '%');
    //                    }));
    //                });
    //            }
    //
    //
    //
    //
    //
    //            if (auth()->user()->user_role == UserRole::CLIENT) {
    //                $ordersQuery->where('ingr_shop_id', auth()->id());
    //            }
    //
    //            if (auth()->user()->user_role == UserRole::BRANCH) {
    //                $ordersQuery->where('ingr_branch_id', auth()->user()->branch_id);
    //            }
    //
    //            $statuses = [
    //                'pending-order' => [OrderStatus::CREATED, OrderStatus::PENDINE_DRIVER_ACCEPTANCE, OrderStatus::DRIVER_ACCEPTANCE_TIMEOUT],
    //                'active_order' => [OrderStatus::DRIVER_ACCEPTED, OrderStatus::ARRIVED_TO_DROPOFF, OrderStatus::ARRIVED_PICK_UP, OrderStatus::PICKED_UP],
    //                'CancelledOrder' => [OrderStatus::CANCELED],
    //                'completed_order' => [OrderStatus::DELIVERED],
    //            ];
    //
    //
    //            $order_count = $ordersQuery->count();
    //            $results = [];
    //            foreach ($statuses as $key => $status) {
    //                $queryClone = clone $ordersQuery;
    //                $results[$key] = \App\Http\Resources\Admin\Dispatcher\OrderResource::collection(
    //                    $queryClone->whereIn('status', $status)->paginate(10)
    //                );
    //            }
    //            //dd($ordersQuery->get());
    //            //dd($results);
    //            // dd(array_merge(
    //            //     ['order_data' => ['order_count' => $order_count, 'user_role' => auth()->user()->user_role->value]],
    //            //     $results
    //            // ));
    //            return response()->json(array_merge(
    //                ['order_data' => ['order_count' => $order_count, 'user_role' => auth()->user()->user_role->value]],
    //                $results
    //            ));
    //        }
    //        return response()->json();
    //    }

    public function GetSearchOrdersDatatest(Request $request): \Illuminate\Http\JsonResponse
    {
        abort_unless(auth()->user()->hasPermissionTo('basic_dispatcher_view'), 403, 'You do not have permission to view this page.');

        $search      = $request->search;
        $order_count = 0;
        $results     = collect();

        if ($search) {
            $results = ViewSearchOrder::where('status', '!=', \App\Enum\OrderStatus::CANCELED)
                ->where(function ($q) use ($search) {
                    $q->where('id', 'like', "%$search%")
                        ->orWhere('client_order_id_string', 'like', "%$search%");
                    // ->orWhere('client_order_id', 'like', "%$search%")
                    // ->orWhere('customer_phone', 'like', "%$search%")
                    //  ->orWhere('customer_name', 'like', "%$search%")
                    //  ->orWhere('branch_name', 'like', "%$search%")
                    //  ->orWhere('branch_phone', 'like', "%$search%")
                    // ->orWhere('shop_first_name', 'like', "%$search%")
                    // ->orWhere('shop_last_name', 'like', "%$search%");
                })
                ->orderByDesc('id')
                ->paginate(10);
            //dd($results);
            // $results = \App\Http\Resources\Admin\Dispatcher\OrderResource::collection(collect($data));
            // $results = collect($data);
        }

        return response()->json([
            'order_count' => $order_count,
            'user_role'   => auth()->user()->user_role->value,
            'result'      => $results,
        ]);
    }

    public function GetSearchOrdersDataNotUSe(Request $request): \Illuminate\Http\JsonResponse
    {

        // Check user permissions
        abort_unless(auth()->user()->hasPermissionTo('basic_dispatcher_view'), 403, 'You do not have permission to view this page.');

        $search      = $request->search;
        $order_count = 0;
        // $results     = collect();

        if ($search) {
            // Base query with filters for created_at and eager loading relationships
            $ordersQuery = Order::whereDate('created_at', Carbon::yesterday()->toDateString())
                ->WhereDate('created_at', Carbon::today()->toDateString());

            if ($request->tapFilter == null) {
                $ordersQuery->whereNotIn('status', [OrderStatus::CANCELED, OrderStatus::DELIVERED]);
            } else {
                $ordersQuery->where('status', 9);
            }
            dd($ordersQuery->toSql(), $ordersQuery->getBindings());

            $ordersQuery->with([
                'ShopDetail:id,first_name,last_name,shop_id',
                'driver:id,first_name,last_name,phone',
                'branch:id,name,phone',
                'branchIntegration:id,branch_id,external_id',
            ]);

            if (auth()->user()->user_role == UserRole::CLIENT) {
                $ordersQuery->where('ingr_shop_id', auth()->id());
            }

            if (auth()->user()->user_role == UserRole::BRANCH) {
                $ordersQuery->where('ingr_branch_id', auth()->user()->branch_id);
            }
            $this->ActionRoleQueryWhere($ordersQuery, null, null);

            dd($ordersQuery->count());
            $order_count = 0;

            $results = \App\Http\Resources\Admin\Dispatcher\OrderResource::collection($ordersQuery->orderByDesc('id')->paginate(10));
        }

        // Return JSON response
        return response()->json([
            'order_count' => $order_count,
            'user_role'   => auth()->user()->user_role->value,
            'result'      => $results,
        ]);
    }
    public function GetSearchOrdersData(Request $request): \Illuminate\Http\JsonResponse
    {

        // Check user permissions
        abort_unless(auth()->user()->hasPermissionTo('basic_dispatcher_view'), 403, 'You do not have permission to view this page.');

        $search      = $request->search;
        $order_count = 0;
        $results     = collect();

        if ($search) {
            // Base query with filters for created_at and eager loading relationships
            $ordersQuery = Order::query();
            // Additional filters based on user roles
            if (auth()->user()->user_role == UserRole::CLIENT) {
                $ordersQuery->where('ingr_shop_id', auth()->id());
            }

            if (auth()->user()->user_role == UserRole::BRANCH) {
                $ordersQuery->where('ingr_branch_id', auth()->user()->branch_id);
            }
            $this->ActionRoleQueryWhere($ordersQuery, null, null);
            $ordersQuery->where(function ($q) {
                $q->whereDate('created_at', Carbon::yesterday())
                    ->orWhereDate('created_at', Carbon::today());
            });
            //dd($request->tapFilter);
            $getDateTime = $this->getBusinessHoursIfNowWithinRange();
            if ($request->tapFilter == null) {
                $ordersQuery->whereNotIn('status', [OrderStatus::CANCELED, OrderStatus::DELIVERED]);
            } else {

                //   dd($getDateTime);
                $ordersQuery->where('status', OrderStatus::DELIVERED)
                    ->whereBetween('created_at', [
                        @$getDateTime['start'],
                        @$getDateTime['end'],
                    ]);
            }
            // dd($ordersQuery->get());
            $ordersQuery->with(['ShopDetail', 'driver', 'branch', 'branchIntegration'])->orderByDesc('id');

            //            dd($ordersQuery->take(10)->get());

            // Apply search conditions
            $ordersQuery->when($search, function (Builder $query, $search) {
                $query->where(function (Builder $q) use ($search) {
                    $q->where(function ($b) use ($search) {
                        $b->Where('id', 'like', '%' . $search . '%')
                            ->orWhere('client_order_id_string', 'like', '%' . $search . '%')
                            ->orWhere('client_order_id', 'like', '%' . $search . '%');
                    })

                        ->orWhere(function ($query) use ($search) {
                            $query->where('customer_phone', 'like', '%' . $search . '%')
                                ->orWhere('customer_name', 'like', '%' . $search . '%');
                        });
                    //                    $q->orWhereHas()
                    //                    $q->orWhereHas('shop', fn($q) => $q->where('first_name', 'like', '%' . $search . '%')
                    //                        ->orWhere('phone', 'like', '%' . $search . '%'));
                    //
                    //                    $q->orWhereHas('driver', fn($q) => $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $search . '%'])
                    //                        ->orWhere('phone', 'like', '%' . $search . '%'));
                    //
                    $q->orWhereHas('branch', fn($q) =>
                        $q->where('client_branches.name', 'like', '%' . $search . '%')
                            ->orWhere('client_branches.phone', 'like', '%' . $search . '%'));
                    //
                    $q->orWhereHas('ShopDetail', fn($q) =>
                        $q->where('first_name', 'like', '%' . $search . '%')
                            ->orWhere('last_name', 'like', '%' . $search . '%'));
                });
            });

            // Get order count and paginate results
            $order_count = $ordersQuery->count();
            $results     = \App\Http\Resources\Admin\Dispatcher\OrderResource::collection($ordersQuery->paginate(10));
        }

        // Return JSON response
        return response()->json([
            'order_count' => $order_count,
            'user_role'   => auth()->user()->user_role->value,
            'result'      => $results,
            // 'getDateTime'      => $getDateTime,
            // 'yesterday'      => Carbon::yesterday()->toDateString(),
            // 'today'      => Carbon::today()->toDateString(),

        ]);
    }

    public function GetSearchOrdersDataOLDAO(Request $request)
    {
        try {
            // التحقق من صلاحيات المستخدم
            abort_unless(auth()->user()->hasPermissionTo('basic_dispatcher_view'), 403, 'You do not have permission to view this page.');

            $search      = $request->search;
            $perPage     = $request->input('per_page', 10);
            $results     = collect();
            $order_count = 0;

            if ($search) {
                // استعلام أساسي مع فلاتر
                $ordersQuery = Order::query();
                $this->ActionRoleQueryWhere($ordersQuery, null, null);
                // تطبيق فلتر الحالة
                if ($request->tapFilter == null) {
                    $ordersQuery->whereNotIn('status', [OrderStatus::CANCELED, OrderStatus::DELIVERED]);
                } else {
                    $ordersQuery->whereIn('status', [OrderStatus::DELIVERED]);
                }

                // تطبيق فلتر التاريخ
                $ordersQuery->where(function ($q) {
                    $q->whereDate('created_at', Carbon::yesterday())
                        ->orWhereDate('created_at', Carbon::today());
                });

                // تحميل العلاقات فقط عند الحاجة
                $neededRelations = [];

                // تحديد العلاقات المطلوبة بناءً على معايير البحث
                if (strpos($search, '@') !== false || is_numeric($search)) {
                    $neededRelations[] = 'ShopDetail';
                    $neededRelations[] = 'branch';
                } else {
                    // تحميل جميع العلاقات فقط إذا كان البحث نصياً
                    $neededRelations = ['ShopDetail', 'driver', 'branch', 'branchIntegration'];
                }

                if (! empty($neededRelations)) {
                    $ordersQuery->with($neededRelations);
                }

                // تطبيق شروط البحث بطريقة أكثر كفاءة
                $this->applyOptimizedSearchConditions($ordersQuery, $search);

                // تطبيق فلاتر الأدوار
                $user = auth()->user();
                if ($user->user_role == UserRole::CLIENT) {
                    $ordersQuery->where('ingr_shop_id', $user->id);
                } elseif ($user->user_role == UserRole::BRANCH) {
                    $ordersQuery->where('ingr_branch_id', $user->branch_id);
                }

                // استخدام simplePaginate بدلاً من paginate لتجنب استعلام العد
                $results = \App\Http\Resources\Admin\Dispatcher\OrderResource::collection(
                    $ordersQuery->orderByDesc('id')->simplePaginate($perPage)
                );

                // تقدير عدد الطلبات بدلاً من العد الدقيق
                // يمكنك إزالة هذا إذا لم تكن بحاجة إلى العدد الدقيق
                $order_count = $ordersQuery->limit(1000)->count();
            }

            // إرجاع استجابة JSON
            return response()->json([
                'success'     => true,
                'order_count' => $order_count,
                'user_role'   => auth()->user()->user_role->value,
                'result'      => $results,
                'has_more'    => $results->hasMorePages(), // إضافة مؤشر لوجود المزيد من الصفحات
            ]);
        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * تطبيق شروط البحث بطريقة محسنة
     */
    private function applyOptimizedSearchConditions($query, $search): void
    {
        // تحسين البحث باستخدام فهارس أفضل
        if (is_numeric($search)) {
            // إذا كان البحث رقمياً، ابحث في الحقول الرقمية أولاً
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhere('client_order_id', $search)
                    ->orWhere('customer_phone', 'like', $search . '%'); // استخدام prefix search بدلاً من wildcard
            });
        } else {
            // البحث النصي
            $query->where(function ($q) use ($search) {
                                                                           // البحث في معرفات الطلب
                $q->where('client_order_id_string', 'like', $search . '%') // استخدام prefix search
                    ->orWhere('customer_name', 'like', $search . '%');

                // البحث في الفروع فقط إذا كان البحث أطول من 3 أحرف
                if (strlen($search) > 3) {
                    $q->orWhereHas('branch', function ($subq) use ($search) {
                        $subq->where('client_branches.name', 'like', $search . '%')
                            ->orWhere('client_branches.phone', 'like', $search . '%');
                    });

                    $q->orWhereHas('ShopDetail', function ($subq) use ($search) {
                        $subq->where('first_name', 'like', $search . '%')
                            ->orWhere('last_name', 'like', $search . '%');
                    });
                }
            });
        }
    }

    /**
     * Apply status filter to the query
     */
    private function applyStatusFilter($query, $tapFilter): void
    {
        if ($tapFilter === null) {
            $query->whereNotIn('status', [OrderStatus::CANCELED, OrderStatus::DELIVERED]);
        } else {
            $query->whereIn('status', [OrderStatus::DELIVERED]);
        }
    }

    /**
     * Apply search conditions to the query
     */
    private function applySearchConditions($query, $search): void
    {
        $query->where(function (Builder $q) use ($search) {
            // Search in order IDs
            $q->where(function ($b) use ($search) {
                $b->where('id', 'like', '%' . $search . '%')
                    ->orWhere('client_order_id_string', 'like', '%' . $search . '%')
                    ->orWhere('client_order_id', 'like', '%' . $search . '%');
            });

            // Search in customer information
            $q->orWhere(function ($query) use ($search) {
                $query->where('customer_phone', 'like', '%' . $search . '%')
                    ->orWhere('customer_name', 'like', '%' . $search . '%');
            });

            // Search in related branch
            $q->orWhereHas('branch', function ($q) use ($search) {
                $q->where('client_branches.name', 'like', '%' . $search . '%')
                    ->orWhere('client_branches.phone', 'like', '%' . $search . '%');
            });

            // Search in shop details
            $q->orWhereHas('ShopDetail', function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%');
            });

            // Uncomment and modify these if needed
            /*
            $q->orWhereHas('driver', function($q) use ($search) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $search . '%'])
                  ->orWhere('phone', 'like', '%' . $search . '%');
            });

            $q->orWhereHas('shop', function($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
            });
            */
        });
    }

    /**
     * Apply role-based filters to the query
     */
    private function applyRoleFilters($query): void
    {
        $user = auth()->user();

        if ($user->user_role == UserRole::CLIENT) {
            $query->where('ingr_shop_id', $user->id);
        }

        if ($user->user_role == UserRole::BRANCH) {
            $query->where('ingr_branch_id', $user->branch_id);
        }
    }

    public function GetSearchOrdersDataOld(Request $request): \Illuminate\Http\JsonResponse
    {
        abort_unless(auth()->user()->hasPermissionTo('basic_dispatcher_view'), 403, 'You do not have permission to view this page.');

        $search = $request->search;

        // Base query
        $ordersQuery = Order::with(['shop', 'driver', 'branch', 'branchIntegration'])
            ->orderByDesc('id');

        if ($search) {
            $ordersQuery->where(function (Builder $query) use ($search) {
                $query->where('client_order_id', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%')
                    ->orWhere('customer_phone', 'like', '%' . $search . '%')
                    ->orWhere('customer_name', 'like', '%' . $search . '%')
                    ->orWhereHas('shop', fn($q) => $q->where('users.first_name', 'like', '%' . $search . '%')
                            ->orWhere('users.phone', 'like', '%' . $search . '%'))
                    ->orWhereHas('driver', function ($query) use ($search) {
                        $query->whereHas('driver', function ($query) use ($search) {
                            $query->whereRaw("CONCAT(users.first_name, ' ', users.last_name) LIKE ?", ['%' . $search . '%'])
                                ->orWhere('users.phone', 'like', '%' . $search . '%');
                        });
                    })
                    ->orWhereHas('branch', fn($q) => $q->where('client_branches.name', 'like', '%' . $search . '%')
                            ->orWhere('client_branches.phone', 'like', '%' . $search . '%'))
                    ->orWhereHas('branchIntegration', fn($q) => $q->where('client_branches.name', 'like', '%' . $search . '%')
                            ->orWhere('client_branches.phone', 'like', '%' . $search . '%'));
            });
        }

        if (auth()->user()->user_role == UserRole::CLIENT) {
            $ordersQuery->where('ingr_shop_id', auth()->id());
        }

        if (auth()->user()->user_role == UserRole::BRANCH) {
            $ordersQuery->where('ingr_branch_id', auth()->user()->branch_id);
        }

        $statuses = [
            'pending-order'   => [OrderStatus::CREATED, OrderStatus::PENDINE_DRIVER_ACCEPTANCE, OrderStatus::DRIVER_ACCEPTANCE_TIMEOUT],
            'active_order'    => [OrderStatus::DRIVER_ACCEPTED, OrderStatus::ARRIVED_TO_DROPOFF, OrderStatus::ARRIVED_PICK_UP, OrderStatus::PICKED_UP],
            'CancelledOrder'  => [OrderStatus::CANCELED],
            'completed_order' => [OrderStatus::DELIVERED],
        ];

        $order_count = $ordersQuery->count();

        $results = [];
        foreach ($statuses as $key => $status) {
            $queryClone    = clone $ordersQuery;
            $results[$key] = \App\Http\Resources\Admin\Dispatcher\OrderResource::collection(
                $ordersQuery->whereIn('status', $status)->paginate(10)
            );
        }
        // dd(array_merge(
        //     ['order_data' => ['order_count' => $order_count, 'user_role' => auth()->user()->user_role->value]],
        //     $results
        // ));
        return response()->json(array_merge(
            ['order_data' => ['order_count' => $order_count, 'user_role' => auth()->user()->user_role->value]],
            $results
        ));
    }

    public function acceptCancelRequest(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('can_accept_cancel_request'), 403, 'You do not have permission to view this page.');

        $order = Order::findOrFail($request->id);

        if ($order->status == OrderStatus::CANCELED) {
            return response()->json(['message' => 'Order is not cancelled'], 400);
        }
        $order->status = OrderStatus::CANCELED;

        $driver_id = $order->driver_id;
        if ($order->driver_id != null) {
            try {
                $this->firebaseRepository->delete_driver_order($order->driver_id, $order->id);
                //send notification
                $title = 'order cancelled';
                $body  = 'you have an order cancelled';

                $this->notificationService->sendOrderNotifications($order->driver_id, $title, $body, $order->id, 'orders');
            } catch (\Exception $e) {
                Log::info($e);
            }
        }
        $order->driver_id = null;
        $order->save();
        OrderLog::create([
            'order_id'    => $order->id,
            'status'      => OrderStatus::CANCELED,
            'action'      => 'Cancel Order',

            'user_id'     => auth()->id(),
            'description' => auth()->user()->first_name . ' cancel order ',
        ]);

        $operator = OperatorDetail::where('operator_id', $driver_id)->first();

        if ($operator) {
            $operator->status = 1;
            $operator->save();
            $user = Operator::with('operator')->find($driver_id);

            //            OperatorStatus::create(['operator_id' => $driver_id, 'status' => 1]);
            $operatorResource = new OperatorResource($user);
            $operatorData     = $operatorResource->toArray(request());
            //try save firebase
            try {
                // Attempt to save to Firebase
                $this->firebaseRepository->save_driver($driver_id, $operatorData);
            } catch (\Exception $e) {
                // Handle the exception (log it, show a message, etc.)
                Log::info($e);
            }
        }
        $client = $order->shop?->client ?? $order->branchIntegration?->client?->client;

        if ($client?->integration) {

            $webhook = WebHook::where('integration_company_id', $client?->integration?->id)->where('type', 'order_cancelled')->first();
            if ($webhook && $webhook->url) {
                if ($client?->integration?->id == 21) {

                    $orderData = [
                        "order_id"        => $order->id,
                        'client_order_id' => $order->client_order_id_string,
                        'status_id'       => $order->status->value,
                        'status'          => $order->status->getLabel(),
                        'driver'          => $order->driver ? [
                            'id'           => $order->driver?->driver?->id,
                            'name'         => $order->driver?->driver?->full_name,
                            'phone'        => $order->driver?->driver?->phone,
                            'status'       => DriverStatus::tryFrom($order->driver?->driver?->operator?->status)?->getLabel(),
                            'tracking_url' => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",

                        ] : null,
                    ];
                } elseif ($client?->integration?->id == 121) {
                    $driver_id = $order->driver_id;
                    $data      = [
                        "order_id"        => "$order->id",
                        "status"          => "CANCELLED",
                        "client_order_id" => "$order->client_order_id_string",

                        "driver"          => [
                            "id"           => "$driver_id",
                            "name"         => @$order->DriverData2->full_name,
                            "phone_number" => @$order->DriverData2->phone,
                            "vehicle_type" => "Bike",
                        ],
                        "timestamp"       => Carbon::now()->timestamp,
                        "tracking_link"   => "https://www.google.com/maps?q={@$order->driver?->driver?->operator?->lat},{@$order->driver?->driver?->operator?->lng}",
                    ];
                    $token = $order->additional_details['callback_token'];
                    $this->SendToLyve($token, $data);
                } elseif ($client?->integration?->id == 128 || $client?->integration?->id == 146) {
                    $driver_id = $order->driver_id;
                    $OrderLog  = OrderLog::where('order_id', $order->id)->whereIn('status', [OrderStatus::PICKED_UP->value, OrderStatus::ARRIVED_TO_DROPOFF->value])->count();

                    $data = [
                        "order_id"        => $order->id,
                        'status_id'       => ($OrderLog) ? 22 : $order->status->value,
                        'client_order_id' => $order->client_order_id_string,
                        "status"          => ($OrderLog) ? "Return" : "CANCELLED",
                        'cancel_reason'   => @$order->cancel_reason->name,
                        'created_at'      => $order->created_at,
                        'invoice_url'     => $order->PdfUrl,
                        'tracking_url'    => "",
                        'driver'          => null,
                    ];
                    $this->sendToWasftyWebhook($webhook->url, $data, $webhook->token);
                } elseif ($client?->integration?->id == 125 || $client?->integration?->id == 126 || $client?->integration?->id == 127) {
                    $orderData = [
                        'order_id'        => $order->id,
                        'status_id'       => $order->status->value,
                        "client_order_id" => "$order->client_order_id_string",
                        'status_label'    => $order->status->getLabel(),
                        'driver'          => $order->driver ? [
                            'id'           => $order->driver?->driver?->id,
                            'name'         => $order->driver?->driver?->full_name,
                            'phone'        => $order->driver?->driver?->phone,
                            'status'       => DriverStatus::tryFrom($order->driver?->driver?->operator?->status)?->getLabel(),
                            'tracking_url' => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",
                        ] : null,
                    ];
                    $this->sendOrderToWebhook($webhook->url, $orderData);
                } elseif ($client?->integration?->id == 15) {
                    $orderData = [
                        "order_id"        => $order->id,
                        'client_order_id' => $order->client_order_id_string,
                        'status_id'       => $order->status->value,
                        'status_label'    => $order->status->getLabel(),
                        'value'           => $order->value, // requested on 2-06-2025
                        'driver'          => null,          // requested on 2-06-2025
                    ];
                    $this->sendOrderToWebhook($webhook->url, $orderData);
                } else {
                    $orderData = [
                        "order_id"        => $order->id,
                        'client_order_id' => $order->client_order_id_string,
                        'status'          => $order->status->value,
                        'status_label'    => $order->status->getLabel(),
                        'driver'          => $order->driver ? [
                            'id'           => $order->driver?->driver?->id,
                            'name'         => $order->driver?->driver?->full_name,
                            'phone'        => $order->driver?->driver?->phone,
                            'status'       => DriverStatus::tryFrom($order->driver?->driver?->operator?->status)?->getLabel(),
                            'tracking_url' => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",

                        ] : null,
                    ];
                    $this->sendOrderToWebhook($webhook->url, $orderData);
                }
                // dd($webhook->url);
                //                $this->sendOrderToWebhook('https://webhook.site/329479cd-aea3-4e39-a77d-5384ceeab723', $orderData);
            }
        }

        return response()->json('success');
    }

    private function sendOrderToWebhook($url, $data)
    {
        $jsonData = json_encode($data);
        // dd($jsonData);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            Log::error('Webhook delivery failed', [
                'url'       => $url,
                'response'  => $response,
                'http_code' => $httpCode,
                'sent_data' => $jsonData,
            ]);
        } else {
            Log::info('Webhook delivered successfully', [
                'url'       => $url,
                'response'  => $response,
                'http_code' => $httpCode,
                'sent_data' => $jsonData,
            ]);
        }
    }
    private function sendToWasftyWebhook($url, $data, $token)
    {
        $jsonData = json_encode($data);
        // dd($jsonData);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
            'api-key: ' . $token,
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            Log::error('Webhook Wasfty  failed', [
                'url'       => $url,
                'response'  => $response,
                'http_code' => $httpCode,
                'sent_data' => $jsonData,
            ]);
        } else {
            Log::info('Webhook Wasfty successfully', [
                'url'       => $url,
                'response'  => $response,
                'http_code' => $httpCode,
                'sent_data' => $jsonData,
            ]);
        }
    }

    public function SendToLyve($token, $data)
    {

        $client       = new Client();
        $request_data = [
            'body'    => json_encode($data),
            'headers' => [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json',
            ],
        ];

        Log::info('Lyve api', [
            $request_data,
            $token,
            $data,
        ]);
        $response = $client->request('POST', 'https://delivery-partner.webhook.manage.lyve.global/v1/feedbacks', $request_data);

        $result = $response->getBody();
        Log::info('Lyve delivered successfully', [
            $result,
        ]);
    }

    public function GetDriversData(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('can_get_driver'), 403, 'You do not have permission to view this page.');
        return "";
        $search = $request->search;
        $type   = $request->type;

        // Define the statuses
        $statuses = [
            1 => DriverStatus::AVAILABLE,
            2 => DriverStatus::BUSY,
            4 => DriverStatus::OFFLINE,
        ];

        $ordersQuery = DriverOrderView::orderbydesc('id');
        if ($type && array_key_exists($type, $statuses)) {
            $ordersQuery->where('status', $statuses[$type]);
        }

        $counts = Operator::selectRaw("
            SUM(CASE WHEN o.status = ? THEN 1 ELSE 0 END) as available_count,
            SUM(CASE WHEN o.status = ? THEN 1 ELSE 0 END) as busy_count,
            SUM(CASE WHEN o.status = ? THEN 1 ELSE 0 END) as offline_count
        ", [DriverStatus::AVAILABLE, DriverStatus::BUSY, DriverStatus::OFFLINE])
            ->join('operators as o', 'o.operator_id', '=', 'users.id') // Ensure correct table joins
            ->first();

        // Paginate drivers
        $drivers = $ordersQuery->paginate(20);

        return response()->json([
            'available'       => \App\Http\Resources\Admin\Dispatcher\DriverMapResource::collection(
                $drivers->filter(fn($driver) => @$driver->status == DriverStatus::AVAILABLE->value)
            ),
            'busy'            => \App\Http\Resources\Admin\Dispatcher\DriverMapResource::collection(
                $drivers->filter(fn($driver) => @$driver->status == DriverStatus::BUSY->value)
            ),
            'offline'         => \App\Http\Resources\Admin\Dispatcher\DriverMapResource::collection(
                $drivers->filter(fn($driver) => @$driver->status == DriverStatus::OFFLINE->value)
            ),
            'operator_counts' => [
                'available' => $counts->available_count ?? 0,
                'busy'      => $counts->busy_count ?? 0,
                'offline'   => $counts->offline_count ?? 0,
            ],
        ]);
    }

    public function GetDriversDataOld(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('can_get_driver'), 403, 'You do not have permission to view this page.');

        $counts = [];

        // Base query for operators
        $ordersQuery = Operator::whereHas('operator', function ($q) {
            $q->whereIn('status', [1, 2, 4]);
        });

        $search = $request->search;

        if ($search) {
            $ordersQuery->where(function (Builder $query) use ($search) {
                $query->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        $statuses = [
            'available' => DriverStatus::AVAILABLE,
            'busy'      => DriverStatus::BUSY,
            'offline'   => DriverStatus::OFFLINE,

        ];

        foreach ($statuses as $key => $status) {
            $queryClone = clone $ordersQuery;

            $counts[$key] = $queryClone->whereHas('operator', function ($query) use ($status) {
                $query->where('status', $status);
            })->count();

            $$key = $queryClone->whereHas('operator', function ($query) use ($status) {
                $query->where('status', $status);
            })->paginate(10);
        }

        return response()->json([
            'available'       => \App\Http\Resources\Admin\Dispatcher\DriverResource::collection($available),
            'busy'            => \App\Http\Resources\Admin\Dispatcher\DriverResource::collection($busy),
            'offline'         => \App\Http\Resources\Admin\Dispatcher\DriverResource::collection($offline),
            'operator_counts' => $counts,
        ]);
    }

    public function getDriverPopupOld(Request $request)
    {
        // dd($request->all());
        abort_unless(auth()->user()->hasPermissionTo('can_get_driver'), 403, 'You do not have permission to view this page.');

        $today     = Carbon::today();
        $yesterday = Carbon::yesterday();

        $driver = Operator::where('id', $request->id)
            ->whereHas('operator', function ($q) {
                $q->where('status', '!=', 4)
                    ->whereNotNull('lat')
                    ->whereNotNull('lng');
            })

            ->first();

        if (! $driver) {
            return response()->json(['error' => 'Driver not found'], 404);
        }

        $tasks = $driver->orders()
            ->where(function ($query) use ($today, $yesterday) {
                $query->whereDate('orders.created_at', $yesterday)
                    ->orWhereDate('orders.created_at', $today);
            })
            ->whereNotIn('status', [9, 10])
            ->count() * 2 ?? 0;

        $orders = $driver->orders()
            ->whereDate('orders.created_at', $yesterday)->orWhereDate('orders.created_at', $today)
            ->get()->map(function ($order) {
            return [
                'order_number'    => $order->order_number,
                'shop_name'       => $order->shop?->first_name ?? $order->branchIntegration?->client?->first_name,
                'branch_name'     => $order->branch?->name ?? $order->branchIntegration?->name,
                'branch_photo'    => $order->shop?->image,
                'status'          => $order->status->getLabel(),
                'area'            => $order->branch?->area?->name ?? $order->branchIntegration?->area?->name,
                'customer_phone'  => $order->customer_phone,
                'client_order_id' => $order->client_order_id,
                'id'              => $order->id,
                'lat_order'       => $order->lat,
                'lng_order'       => $order->lng,
            ];
        });

        $driver->lat           = $driver->operator?->lat;
        $driver->lng           = $driver->operator->lng;
        $driver->profile_image = $driver?->image;
        $driver->full_name     = $driver?->full_name;
        $driver->phone         = $driver->phone;
        $driver->status        = DriverStatus::tryFrom(@$driver->operator?->status)->value;

        //     'infoWindowContent' => view('admin.pages.dispatchers.driver-popup', ['driver' => $driver, 'tasks' => $tasks, 'orders' => $orders])->render(),
        // ];

        // dd($driver);

        return response()->json([
            'infoWindowContent' => view(
                'admin.pages.dispatchers.driver-popup',
                ['driver' => $driver, 'tasks' => $tasks, 'orders' => $orders]
            )->render(),
            'orders'            => $orders,
        ]);
    }
    public function getDriverPopup(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('can_get_driver'), 403, 'You do not have permission to view this page.');
        $today     = Carbon::today();
        $yesterday = Carbon::yesterday();
        $driver    = Operator::where('id', $request->id)
            ->whereHas('operator', function ($q) {
                $q->where('status', '!=', 4)
                    ->whereNotNull('lat')
                    ->whereNotNull('lng');
            })->first();
        if (! $driver) {
            return response()->json(['error' => 'Driver not found'], 404);
        }
        $orderQuery = Order::where('driver_id', $driver->id)
            ->whereNotIn('status', [9, 10]);
        $tasks = $orderQuery
            ->count() * 2 ?? 0;
        $orders = $orderQuery
            ->get()->map(function ($order) {
            return [
                'order_number'    => $order->order_number,
                'shop_name'       => $order->shop?->first_name ?? $order->branchIntegration?->client?->first_name,
                'branch_name'     => $order->branch?->name ?? $order->branchIntegration?->name,
                'branch_photo'    => $order->shop?->image,
                'status'          => $order->status->getLabel(),
                'area'            => $order->branch?->area?->name ?? $order->branchIntegration?->area?->name,
                'customer_phone'  => $order->customer_phone,
                'client_order_id' => $order->client_order_id,
                'id'              => $order->id,
                'lat_order'       => $order->lat,
                'lng_order'       => $order->lng,
            ];
        });
        $driver->lat           = $driver->operator?->lat;
        $driver->lng           = $driver->operator->lng;
        $driver->profile_image = $driver?->image;
        $driver->full_name     = $driver?->full_name;
        $driver->phone         = $driver->phone;
        $driver->status        = DriverStatus::tryFrom(@$driver->operator?->status)->value;

        //     'infoWindowContent' => view('admin.pages.dispatchers.driver-popup', ['driver' => $driver, 'tasks' => $tasks, 'orders' => $orders])->render(),
        // ];

        // dd($driver);

        return response()->json([
            'infoWindowContent' => view(
                'admin.pages.dispatchers.driver-popup',
                ['driver' => $driver, 'tasks' => $tasks, 'orders' => $orders]
            )->render(),
            'orders'            => $orders,
        ]);
    }

    // public function getMapData(Request $request)
    // {

    //     abort_unless(auth()->user()->hasPermissionTo('basic_dispatcher_view'), 403, 'You do not have permission to view this page.');

    //     $drivers = Operator::whereHas('operator', function ($q) {
    //         $q->where('status', '!=', 4)
    //             ->whereNotNull('lat')
    //             ->whereNotNull('lng');
    //     });

    //     $branches = ClientBranches::whereNotNull('lat')
    //         ->whereNotNull('lng');
    //     // dd($branches->get());

    //     $orders = Order::whereDate('created_at', '>=', Carbon::yesterday())
    //         ->whereDate('created_at', '<=', Carbon::today())
    //         ->whereNotNull('lat')
    //         ->whereNotNull('lng')
    //         ->whereNotIn('status', [9, 10]);

    //     if (auth()->user()->user_role == UserRole::CLIENT) {
    //         $orders = $orders->where('ingr_shop_id', auth()->id());

    //         $drivers = $drivers->whereHas('orders', function ($query) {
    //             $query->whereDate('orders.created_at', '>=', Carbon::yesterday())
    //                 ->whereDate('orders.created_at', '<=', Carbon::today())
    //                 ->whereNotIn('orders.status', [9, 10, 2])
    //                 ->where('orders.ingr_shop_id', auth()->id());
    //         });

    //         $branches = $branches->where('client_id',  auth()->id());
    //     }

    //     if (auth()->user()->user_role == UserRole::BRANCH) {
    //         $orders = $orders->where('ingr_branch_id', auth()->user()->branch_id);

    //         $drivers = $drivers->whereHas('orders', function ($query) {
    //             $query->whereDate('orders.created_at', '>=', Carbon::yesterday())
    //                 ->whereDate('orders.created_at', '<=', Carbon::today())
    //                 ->whereNotIn('orders.status', [9, 10, 2])
    //                 ->where('orders.ingr_branch_id', auth()->user()->branch_id);
    //         });

    //         $branches = $branches->where('id',  auth()->user()->branch_id);
    //     }

    //     $orders = $orders->with(['shop'])
    //         ->get()
    //         ->map(function ($order) {
    //             $order->branch_lat = $order->branch?->lat;
    //             $order->branch_lng = $order->branch?->lng;
    //             $order->userRole = auth()->user()->user_role?->value;
    //             $order->shop_profile = $order->shop?->image;
    //             return $order;
    //         });

    //     $branches = $branches->get()->map(function ($branch) {
    //         $branch->branch_lat = $branch->lat;
    //         $branch->branch_lng = $branch->lng;

    //         $branch->shop_profile = $branch->client?->image;
    //         return $branch;
    //     });

    //     $drivers = $drivers->get()->map(function ($driver) {

    //         return [
    //             'id' => $driver->id,
    //             'lat' => $driver->operator?->lat,
    //             'lng' => $driver->operator->lng,
    //             'profile_image' => $driver?->image,
    //             'full_name' => $driver?->full_name,
    //             'phone' => $driver->phone,
    //             'status' =>  @DriverStatus::tryFrom(@@$driver->operator?->status)->value,
    //         ];
    //     });

    //     // dd($branches);
    //     $orderLocations = $orders->map(fn($order) => [
    //         'lat' => $order->branch_lat,
    //         'lng' => $order->branch_lng
    //     ])->filter(fn($location) => $location['lat'] && $location['lng'])->values();

    //     $branchLocations = $branches->map(fn($branch) => [
    //         'lat' => $branch->branch_lat,
    //         'lng' => $branch->branch_lng
    //     ])->filter(fn($location) => $location['lat'] && $location['lng'])->values();

    //     $driverLocations = $drivers->map(fn($driver) => [
    //         'lat' => $driver['lat'],
    //         'lng' => $driver['lng']
    //     ])->filter(fn($location) => $location['lat'] && $location['lng'])->values();
    //     // dd($orderLocations, $driverLocations, $branchLocations);
    //     return response()->json([
    //         'orders' => $orders,
    //         'drivers' => $drivers,
    //         'branches' => $branches,
    //         'orderLocations' => $orderLocations,
    //         'driverLocations' => $driverLocations,
    //         'branchLocations' => $branchLocations
    //     ]);
    // }

    public function getMapData(Request $request)
    {
        dd('dd');

        abort_unless(auth()->user()->hasPermissionTo('basic_dispatcher_view'), 403, 'You do not have permission to view this page.');

        $drivers = Operator::whereHas('operator', function ($q) {
            // $q->where('status', '!=', 4)
            $q->whereIn('status', [1, 2])
                ->whereNotNull('lat')
                ->whereNotNull('lng');
        });

        $branches = ClientBranches::has('getOrders')->whereNotNull('lat')
            ->whereNotNull('lng');
        // dd($branches->get());

        $orders = Order::whereDate('created_at', '>=', Carbon::yesterday())
            ->whereDate('created_at', '<=', Carbon::today())
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->whereNotIn('status', [9, 10]);

        $map_center = [
            'lat' => 23.8859,
            'lng' => 45.0792,
        ];

        if (auth()->user()->user_role == UserRole::CLIENT) {
            $orders = $orders->where('ingr_shop_id', auth()->id());

            $drivers = $drivers->whereHas('orders', function ($query) {
                $query->whereDate('orders.created_at', '>=', Carbon::yesterday())
                    ->whereDate('orders.created_at', '<=', Carbon::today())
                    ->whereNotIn('orders.status', [9, 10, 2])
                    ->where('orders.ingr_shop_id', auth()->id());
            });

            $branches          = $branches->where('client_id', auth()->id());
            $branch            = ClientBranches::where('client_id', auth()->user()->id)->orderBy('created_at', 'asc')->first();
            $map_center['lat'] = $branch->lat;
            $map_center['lng'] = $branch->lng;
        }

        if (auth()->user()->user_role == UserRole::BRANCH) {
            $orders = $orders->where('ingr_branch_id', auth()->user()->branch_id);

            $drivers = $drivers->whereHas('orders', function ($query) {
                $query->whereDate('orders.created_at', '>=', Carbon::yesterday())
                    ->whereDate('orders.created_at', '<=', Carbon::today())
                    ->whereNotIn('orders.status', [9, 10, 2])
                    ->where('orders.ingr_branch_id', auth()->user()->branch_id);
            });

            $branches          = $branches->where('id', auth()->user()->branch_id);
            $branch            = ClientBranches::where('client_id', auth()->user()->branch_id)->orderBy('created_at', 'asc')->first();
            $map_center['lat'] = $branch?->lat;
            $map_center['lng'] = $branch?->lng;
        }
        $this->ActionRoleQueryWhere($orders, $branches, $drivers);

        $orders = $orders->with(['shop', 'branch'])
            ->get()
            ->groupBy(fn($order) => $order->branch?->city_id)
            ->map(function ($ordersGroup, $city_id) {
                                                              // dd( $ordersGroup->first(),  $ordersGroup->first()->branch);
                $city = $ordersGroup->first()->branch?->city; // Get the city info from the first order in the group
                return [
                    'city_lat' => $city?->lat, // City latitude as key
                    'city_lng' => $city?->lng, // City longitude as key
                    'city_id'  => $city?->id,
                    'orders'   => $ordersGroup->map(function ($order) {
                        return [
                            'id'           => $order->id,
                            'branch_lat'   => $order->branch?->lat,
                            'branch_lng'   => $order->branch?->lng,
                            'userRole'     => auth()->user()->user_role?->value,
                            'shop_profile' => $order->shop?->image,
                        ];
                    }),
                ];
            });

        $branches = $branches->get()->map(function ($branch) {
            // dd($branch);
            $branch->branch_lat = $branch?->lat;
            $branch->branch_lng = $branch?->lng;

            $branch->shop_profile = $branch?->client?->image;
            return $branch;
        });

        // $drivers = $drivers->get()->map(function ($driver) {

        //     return [
        //         'id' => $driver->id,
        //         'lat' => $driver->operator?->lat,
        //         'lng' => $driver->operator->lng,
        //         'profile_image' => $driver?->image,
        //         'full_name' => $driver?->full_name,
        //         'phone' => $driver->phone,
        //         'status' =>  @DriverStatus::tryFrom(@@$driver->operator?->status)->value,
        //     ];
        // });

        $drivers = $drivers->with('operator')
            ->get()
            ->groupBy(fn($operator) => $operator->operator->city_id)
            ->map(function ($groupedDrivers, $city_id) {
                $city = $groupedDrivers->first()->operator->city;
                return [
                    'city_lat' => $city?->lat,
                    'city_lng' => $city?->lng,
                    'city_id'  => $city?->id,
                    'drivers'  => $groupedDrivers->map(function ($driver) {
                        return [
                            'id'            => $driver->id,
                            'lat'           => $driver->operator?->lat,
                            'lng'           => $driver->operator?->lng,
                            'profile_image' => $driver->image,
                            'full_name'     => $driver->full_name,
                            'phone'         => $driver->phone,
                            'status'        => DriverStatus::tryFrom($driver->operator?->status)?->value,
                        ];
                    }),
                ];
            });

        // dd($orders, $drivers);
        $orderLocations = $orders->map(function ($order) {
            // dd($order);
            return [
                'lat' => $order['city_lat'],
                'lng' => $order['city_lng'],
            ];
        })->filter(fn($location) => $location['lat'] && $location['lng'])->values();

        $driverLocations = $drivers->map(function ($groupedDrivers) {
            return [
                'lat' => $groupedDrivers['city_lat'],
                'lng' => $groupedDrivers['city_lng'],
            ];
        })->filter(fn($location) => $location['lat'] && $location['lng'])->values();

        $branchLocations = $branches->map(fn($branch) => [
            'lat' => $branch->branch_lat,
            'lng' => $branch->branch_lng,
        ])->filter(fn($location) => $location['lat'] && $location['lng'])->values();

        // dd($branches);
        // dd($orderLocations, $driverLocations, $branchLocations);
        return response()->json([
            'orders'          => $orders,
            'drivers'         => $drivers,
            'branches'        => $branches,
            'orderLocations'  => $orderLocations,
            'driverLocations' => $driverLocations,
            'branchLocations' => $branchLocations,
            'map_center'      => $map_center,
        ]);
    }

    public function order_status_change($order_id, $status)
    {
        Order::find($order_id)->update([
            'status' => $status,
        ]);
        $order = Order::find($order_id);
        $data  = new \App\Http\Resources\Admin\Dispatcher\OrderResource($order);
        event(new OrderUpdateStatusWidget($data));
        return response()->json(['success' => 'Order status changed successfully']);
    }
    public static function ActionRoleQueryWhereOld($queryOrders, $queryBranches, $queryDrivers)
    {

        if (auth()->user()->user_role == UserRole::DISPATCHER) {
            $city_ids = UserCitys::where('user_id', auth()->user()->id)->pluck('city_id')->toArray();

            if ($queryOrders != null) {
                $queryOrders->where(function ($query) use ($city_ids) {
                    $query->whereIn('city', $city_ids)
                        ->orWhereNull('city');
                });
            }
            if ($queryBranches != null) {
                $queryBranches->whereIn('city_id', $city_ids);
            }
            if ($queryDrivers != null) {
                $queryDrivers->whereHas('operator', function ($q) use ($city_ids) {
                    $q->whereIn('city_id', $city_ids);
                });
            }
        }
    }
    public static function ActionRoleQueryWhere($queryOrders, $queryBranches, $queryDrivers)
    {

        if (auth()->user()->user_role != UserRole::DISPATCHER && auth()->user()->user_role != UserRole::ADMIN) {
            return;
        }

        $userId = auth()->id();
        $user   = auth()->user();
        if ($user->user_role === UserRole::DISPATCHER) {
            $cacheKey = "user_cities_{$userId}";

            $city_ids = Cache::remember($cacheKey, now()->addHours(1), function () use ($userId) {
                return UserCitys::where('user_id', $userId)
                    ->pluck('city_id')
                    ->toArray();
            });

            if (empty($city_ids)) {
                return;
            }

            if ($queryOrders !== null) {
                $queryOrders->where(function ($query) use ($city_ids) {
                    $query->whereIn('city', $city_ids)
                        ->orWhereNull('city');
                });
            }

            if ($queryBranches !== null) {
                $queryBranches->whereIn('city_id', $city_ids);
            }

            if ($queryDrivers !== null) {

                $queryDrivers->whereHas('operator', function ($q) use ($city_ids) {
                    $q->whereIn('city_id', $city_ids);
                }, '>=', 1);
            }

            return;
        }

        if ($user->user_role === UserRole::ADMIN && $user->country_id) {
            $countryId = $user->country_id;

            if ($queryOrders !== null) {
                // dd(7);
                $queryOrders->whereHas('cityData', function ($q) use ($countryId) {
                    $q->where('country_id', $countryId);
                });
            }

            if ($queryBranches !== null) {
                $queryBranches->whereHas('city', function ($q) use ($countryId) {
                    $q->where('country_id', $countryId);
                });
            }

            if ($queryDrivers !== null) {
                $queryDrivers->whereHas('operator.city', function ($q) use ($countryId) {
                    $q->where('country_id', $countryId);
                });
            }
        }
    }

    public function getOrderSummeryData()
    {
        $startDate = Carbon::yesterday()->startOfDay();
        $endDate   = Carbon::today()->endOfDay();

        $clientsQuery = ModelsClient::whereHas('orders', function ($query) use ($startDate, $endDate) {
            // $query->whereBetween('created_at', [$startDate, $endDate]);
        })
            ->withCount(['orders' => function ($query) use ($startDate, $endDate) {
                // $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->with(['orders' => function ($query) use ($startDate, $endDate) {
                $query
                // ->whereBetween('created_at', [$startDate, $endDate])
                    ->select('id', 'ingr_shop_id', 'status', 'picked_up_time', 'arrived_to_pickup_time', 'arrived_to_dropoff_time', 'driver_accepted_time');
            }])
            ->orderByDesc('orders_count')
            ->orderByDesc('created_at');

        if (auth()->user()->user_role == UserRole::CLIENT) {
            $clientsQuery->whereHas('orders', function ($q) {
                $q->where('ingr_shop_id', auth()->id());
            });
        }

        if (auth()->user()->user_role == UserRole::BRANCH) {
            $clientsQuery->whereHas('orders', function ($q) {
                $q->where('ingr_branch_id', auth()->user()->branch_id);
            });
        }

        $clients = $clientsQuery->get();

        $totalOrders = $clients->sum(function ($client) {
            return $client->orders->count();
        });

        $deliveredOrders = $clients->sum(function ($client) {
            return $client->orders->where('status', OrderStatus::DELIVERED)->count();
        });

        $successRate = $totalOrders > 0 ? ($deliveredOrders / $totalOrders) * 100 : 0;

        $response = [
            'clients'          => ClientOrderSummaryResource::collection($clients),
            'total_orders'     => $totalOrders,
            'delivered_orders' => $deliveredOrders,
            'success_rate'     => round($successRate, 2),
        ];

        return response()->json($response);
    }

    public function getOperatorsWithAvgPendingMoreThanTwoMinutes()
    {
        $now = Carbon::now('Asia/Riyadh')->toDateTimeString();
        // dd($now,Carbon::now('Asia/Riyadh')->addMinutes(2));
        $orders = Order::leftJoin('cities', 'orders.city', '=', 'cities.id')
            ->leftJoin('users as u', 'orders.driver_id', '=', 'u.id')
            ->where('status', OrderStatus::PENDINE_DRIVER_ACCEPTANCE)
            ->whereNotNull('orders.driver_assigned_at')
            ->where('orders.created_at', '<=', Carbon::now('Asia/Riyadh')->subMinutes(3))
            ->whereBetween('orders.created_at', [
                Carbon::yesterday('Asia/Riyadh')->startOfDay(),
                Carbon::today('Asia/Riyadh')->endOfDay(),
            ])
            ->where('orders.driver_assigned_at', '<=', Carbon::now('Asia/Riyadh')->subMinutes(2))
            ->select([
                'u.id',
                'u.first_name',
                'u.last_name',
                'cities.name as city_name',
                DB::raw('COUNT(orders.id) as pending_orders_count'),
                DB::raw("(TIMESTAMPDIFF(SECOND, orders.driver_assigned_at, '$now')) as avg_pending_seconds"),
            ])
            ->groupBy([
                'u.id',
                'u.first_name',
                'u.last_name',
                'orders.driver_assigned_at',
                'cities.name',
            ]);

        // $date =[
        //     'now'=>$now ,
        //     'after2min'=> Carbon::now('Asia/Riyadh')->addMinutes(2),
        //     'data'=> $orders->get(),
        // ];
        // return   $date;

        if (auth()->user()->user_role == UserRole::DISPATCHER) {
            $city_ids = UserCitys::where('user_id', auth()->user()->id)->pluck('city_id')->toArray();
            $orders->whereIn('orders.city_id', $city_ids);
        }

        return DataTables::of($orders)
            ->editColumn('id', fn($row) => $row->id)
            ->editColumn('full_name', fn($row) => "{$row->first_name} {$row->last_name}")
            ->editColumn('pending_orders_count', fn($row) => $row->pending_orders_count)
            ->editColumn('city_name', fn($row) => $row->city_name)
            ->editColumn(
                'avg_pending_time',
                fn($row) =>
                $row->avg_pending_seconds
                ? sprintf(
                    '%02d:%02d:%02d',
                    floor($row->avg_pending_seconds / 3600),
                    ($row->avg_pending_seconds / 60) % 60,
                    $row->avg_pending_seconds % 60
                )
                : '00:00:00'
            )
            ->addColumn('action', function ($row) {
                return '<div class="d-flex justify-content-center">
                <a href="#" data-id="' . $row->id . '" class="flex items-center order-driver2-btn justify-center w-8 h-8 text-white border rounded-lg border-gray10">
                    <img src="' . asset('new/src/assets/icons/view.svg') . '" alt="View" />
                </a>
            </div>';
            })
            ->orderColumn('pending_orders_count', 'pending_orders_count $1')
            ->orderColumn('avg_pending_time', 'avg_pending_seconds $1')
            ->make(true);
    }

    public function getOperatorsWithAvgPendingMoreThanTwoMinutesOld()
    {
        $now = Carbon::now('Asia/Riyadh')->toDateTimeString();
        // dd($now);

        $operators = User::query()
            ->leftJoin('operators', 'operators.operator_id', '=', 'users.id')
            ->leftJoin('cities', 'cities.id', '=', 'operators.city_id')
            ->leftJoin('orders', function ($join) {
                $join->on('users.id', '=', 'orders.driver_id')
                    ->where('orders.status', 2) // Only pending orders
                    ->whereNotNull('orders.driver_assigned_at')
                    ->where('orders.created_at', '<=', Carbon::now('Asia/Riyadh')->subMinutes(3))
                    ->whereDate('orders.created_at', '>=', Carbon::yesterday('Asia/Riyadh'))
                    ->whereDate('orders.created_at', '<=', Carbon::today('Asia/Riyadh'))
                // ->whereDate('orders.created_at', Carbon::today('Asia/Riyadh'))

                ;
            })
            ->where('users.user_role', 3)
            ->select([
                'users.id',
                'users.first_name',
                'users.last_name',
                'operators.id as operator_id',
                'cities.name as city_name',
                DB::raw('COUNT(orders.id) as pending_orders_count'),
                DB::raw("AVG(TIMESTAMPDIFF(SECOND, orders.driver_assigned_at, '$now')) as avg_pending_seconds"),

            ])
            ->groupBy([
                'users.id',
                'users.first_name',
                'users.last_name',
                'operators.id',
                'cities.name',
            ])
            ->havingRaw('COALESCE(avg_pending_seconds, 0) > ?', [120]);

        if (auth()->user()->user_role == UserRole::DISPATCHER) {
            $city_ids = UserCitys::where('user_id', auth()->user()->id)->pluck('city_id')->toArray();
            $operators->whereIn('operators.city_id', $city_ids);
        }

        return DataTables::of($operators)
            ->editColumn('id', fn($row) => $row->id)
            ->editColumn('full_name', fn($row) => "{$row->first_name} {$row->last_name}")
            ->editColumn('pending_orders_count', fn($row) => $row->pending_orders_count)
            ->editColumn('city_name', fn($row) => $row->city_name)
            ->editColumn(
                'avg_pending_time',
                fn($row) =>
                $row->avg_pending_seconds
                ? sprintf(
                    '%02d:%02d:%02d',
                    floor($row->avg_pending_seconds / 3600),
                    ($row->avg_pending_seconds / 60) % 60,
                    $row->avg_pending_seconds % 60
                )
                : '00:00:00'
            )
            ->addColumn('action', function ($row) {
                return '<div class="d-flex justify-content-center">
                <a href="#" data-id="' . $row->id . '" class="flex items-center order-driver2-btn justify-center w-8 h-8 text-white border rounded-lg border-gray10">
                    <img src="' . asset('new/src/assets/icons/view.svg') . '" alt="View" />
                </a>
            </div>';
            })
            ->orderColumn('pending_orders_count', 'pending_orders_count $1')
            ->orderColumn('avg_pending_time', 'avg_pending_seconds $1')
            ->make(true);
    }

    public function getOperatorsAcceptanceRateLessTwo()
    {
        $operators = User::query()
            ->leftJoin('operators', 'operators.operator_id', '=', 'users.id')
            ->leftJoin('cities', 'cities.id', '=', 'operators.city_id')
            ->leftJoin('orders', 'users.id', '=', 'orders.driver_id')
            ->where('users.user_role', 3)
            ->whereNotNull('orders.driver_accepted_time')
        // ->whereDate('orders.created_at', Carbon::today())
            ->where('orders.created_at', '<=', Carbon::now('Asia/Riyadh')->subMinutes(3))
            ->whereDate('orders.created_at', '>=', Carbon::yesterday('Asia/Riyadh'))
            ->whereDate('orders.created_at', '<=', Carbon::today('Asia/Riyadh'))
            ->select([
                'users.id',
                'users.first_name',
                'users.last_name',
                'operators.id as operator_id',
                'cities.name as city_name',
                DB::raw('COUNT(orders.id) as orders_count'),
                DB::raw('AVG(TIMESTAMPDIFF(SECOND, orders.driver_assigned_at, orders.driver_accepted_time)) as avg_accept_seconds'),
            ])

            ->groupBy([
                'users.id',
                'users.first_name',
                'users.last_name',
                'operators.id',
                'cities.name',
            ]);

        $operators->havingRaw('COALESCE(avg_accept_seconds, 0) <= ?', [120]);

        // dd($operators->get());

        if (auth()->user()->user_role == UserRole::DISPATCHER) {
            $city_ids = UserCitys::where('user_id', auth()->user()->id)->pluck('city_id')->toArray();

            $operators->whereIn('orders.city', $city_ids);
        }
        return DataTables::of($operators)
            ->editColumn('id', fn($row) => $row->id)
            ->editColumn('full_name', fn($row) => "{$row->first_name} {$row->last_name}") // Compute full_name in PHP
            ->editColumn('orders_count', fn($row) => $row->orders_count)
            ->editColumn('city_name', fn($row) => $row->city_name)
            ->editColumn(
                'avg_accept_time',
                fn($row) =>
                $row->avg_accept_seconds
                ? sprintf(
                    '%02d:%02d:%02d',
                    floor($row->avg_accept_seconds / 3600),
                    ($row->avg_accept_seconds / 60) % 60,
                    $row->avg_accept_seconds % 60
                )
                : '00:00:00'
            )

            ->addColumn('action', function ($row) {
                return '<div class="d-flex justify-content-center">
            <a href="#" data-id="' . $row->id . '"

                class="flex items-center order-driver-btn justify-center w-8 h-8 text-white border rounded-lg border-gray10">
                <img src="' . asset('new/src/assets/icons/view.svg') . '" alt="View" />
            </a>
        </div>';
            })
            ->orderColumn('orders_count', 'orders_count $1')
            ->orderColumn('avg_accept_time', 'avg_accept_seconds $1')
            ->make(true);
    }

    public function getOperatorsAcceptanceRateMoreTwo()
    {
        $operators = User::query()
            ->leftJoin('operators', 'operators.operator_id', '=', 'users.id')
            ->leftJoin('cities', 'cities.id', '=', 'operators.city_id')
            ->leftJoin('orders', 'users.id', '=', 'orders.driver_id')
            ->where('users.user_role', 3)
            ->whereNotNull('orders.driver_accepted_time')
        // ->whereDate('orders.created_at', Carbon::today())
            ->where('orders.created_at', '<=', Carbon::now('Asia/Riyadh')->subMinutes(3))
            ->whereDate('orders.created_at', '>=', Carbon::yesterday('Asia/Riyadh'))
            ->whereDate('orders.created_at', '<=', Carbon::today('Asia/Riyadh'))
            ->select([
                'users.id',
                'users.first_name',
                'users.last_name',
                'operators.id as operator_id',
                'cities.name as city_name',
                DB::raw('COUNT(orders.id) as orders_count'),
                DB::raw('AVG(TIMESTAMPDIFF(SECOND, orders.driver_assigned_at, orders.driver_accepted_time)) as avg_accept_seconds'),
            ])

            ->groupBy([
                'users.id',
                'users.first_name',
                'users.last_name',
                'operators.id',
                'cities.name',
            ]);
        // dd($operators->get());

        $operators->havingRaw('COALESCE(avg_accept_seconds, 0) > ?', [120]);

        // dd($operators->get());

        if (auth()->user()->user_role == UserRole::DISPATCHER) {
            $city_ids = UserCitys::where('user_id', auth()->user()->id)->pluck('city_id')->toArray();

            $operators->whereIn('orders.city', $city_ids);
        }

        return DataTables::of($operators)
            ->editColumn('id', fn($row) => $row->id)
            ->editColumn('full_name', fn($row) => "{$row->first_name} {$row->last_name}") // Compute full_name in PHP
            ->editColumn('orders_count', fn($row) => $row->orders_count)
            ->editColumn('city_name', fn($row) => $row->city_name)
            ->editColumn(
                'avg_accept_time',
                fn($row) =>
                $row->avg_accept_seconds
                ? sprintf(
                    '%02d:%02d:%02d',
                    floor($row->avg_accept_seconds / 3600),
                    ($row->avg_accept_seconds / 60) % 60,
                    $row->avg_accept_seconds % 60
                )
                : '00:00:00'

            )

            ->addColumn('action', function ($row) {
                return '<div class="d-flex justify-content-center">
            <a href="#" data-id="' . $row->id . '"

                class="flex items-center order-driver-btn justify-center w-8 h-8 text-white border rounded-lg border-gray10">
                <img src="' . asset('new/src/assets/icons/view.svg') . '" alt="View" />
            </a>
        </div>';
            })
            ->orderColumn('orders_count', 'orders_count $1')
            ->orderColumn('avg_accept_time', 'avg_accept_seconds $1')
            ->make(true);
    }

    public function getOperatorsPendingRateDetailesOld(Request $request)
    {
        $query = ModelsClient::query()
            ->whereHas('orders', function ($query) use ($request) {
                $query->where('driver_id', $request->driver_id)
                    ->where('status', 2) // Pending
                    ->whereNotNull('driver_assigned_at')
                // ->whereDate('created_at', Carbon::today())
                    ->where('created_at', '<=', Carbon::now('Asia/Riyadh')->subMinutes(3))
                    ->whereDate('created_at', '>=', Carbon::yesterday('Asia/Riyadh'))
                    ->whereDate('created_at', '<=', Carbon::today('Asia/Riyadh'))
                ;

                if (auth()->user()->user_role == UserRole::DISPATCHER) {
                    $city_ids = UserCitys::where('user_id', auth()->user()->id)->pluck('city_id')->toArray();
                    $query->whereIn('city', $city_ids);
                }
            })
            ->with(['orders' => function ($query) use ($request) {
                $query->select('id', 'ingr_shop_id', 'driver_assigned_at', 'status')
                    ->where('driver_id', $request->driver_id)
                    ->where('status', 2) // Pending
                    ->whereNotNull('driver_assigned_at')
                // ->whereDate('created_at', Carbon::today())
                    ->where('created_at', '<=', Carbon::now('Asia/Riyadh')->subMinutes(3))
                    ->whereDate('created_at', '>=', Carbon::yesterday('Asia/Riyadh'))
                    ->whereDate('created_at', '<=', Carbon::today('Asia/Riyadh'))
                ;

                if (auth()->user()->user_role == UserRole::DISPATCHER) {
                    $city_ids = UserCitys::where('user_id', auth()->user()->id)->pluck('city_id')->toArray();
                    $query->whereIn('city', $city_ids);
                }
            }]);

        return DataTables::of($query)
            ->addColumn('full_name', fn($row) => $row->first_name . ' ' . $row->last_name)
            ->addColumn('client_id', fn($row) => $row->id)
            ->addColumn('pending_orders_count', fn($row) => $row->orders->count())
            ->addColumn('avg_pending_time', function ($row) {
                if ($row->orders->isEmpty()) {
                    return '00:00:00';
                }

                $totalSeconds = $row->orders->sum(function ($order) {
                    return Carbon::now('Asia/Riyadh')->diffInSeconds(Carbon::parse($order->driver_assigned_at));
                });

                $avgSeconds = $totalSeconds / $row->orders->count();

                return sprintf(
                    '%02d:%02d:%02d',
                    floor($avgSeconds / 3600),
                    ($avgSeconds / 60) % 60,
                    $avgSeconds % 60
                );
            })
            ->make(true);
    }

    public function getOperatorsPendingRateDetailes(Request $request)
    {
        $now = Carbon::now('Asia/Riyadh')->toDateTimeString();

        $query = Order::leftJoin('users', 'orders.ingr_shop_id', '=', 'users.id')
            ->leftJoin('cities', 'orders.city', '=', 'cities.id')
            ->where('orders.driver_id', $request->driver_id)
            ->where('orders.status', OrderStatus::PENDINE_DRIVER_ACCEPTANCE)
            ->whereNotNull('orders.driver_assigned_at')
            ->where('orders.created_at', '<=', Carbon::now('Asia/Riyadh')->subMinutes(3))
            ->whereBetween('orders.created_at', [
                Carbon::yesterday('Asia/Riyadh')->startOfDay(),
                Carbon::today('Asia/Riyadh')->endOfDay(),
            ])
            ->select([
                'users.id',
                'users.first_name',
                'users.last_name',
                'cities.name as city_name',
                DB::raw('COUNT(orders.id) as pending_orders_count'),
                DB::raw("(TIMESTAMPDIFF(SECOND, orders.driver_assigned_at, '$now')) as pending_seconds"),
            ])
            ->groupBy([
                'users.id',
                'users.first_name',
                'users.last_name',
                'cities.name',
                'orders.driver_assigned_at',
            ]);

        if (auth()->user()->user_role == UserRole::DISPATCHER) {
            $city_ids = UserCitys::where('user_id', auth()->user()->id)->pluck('city_id')->toArray();
            $query->whereIn('orders.city', $city_ids);
        }

        return DataTables::of($query)
            ->addColumn('full_name', fn($row) => "{$row->first_name} {$row->last_name}")
            ->addColumn('client_id', fn($row) => $row->id)
            ->addColumn('pending_orders_count', fn($row) => $row->pending_orders_count)
            ->addColumn('avg_pending_time', function ($row) {
                if (! $row->pending_seconds) {
                    return '00:00:00';
                }

                return sprintf(
                    '%02d:%02d:%02d',
                    floor($row->pending_seconds / 3600),
                    ($row->pending_seconds / 60) % 60,
                    $row->pending_seconds % 60
                );
            })
            ->make(true);
    }

    public function getOperatorsAcceptanceRateDetailes(Request $request)
    {
        $query = ModelsClient::query()
            ->whereHas('orders', function ($query) use ($request) {
                $query->where('driver_id', $request->driver_id)
                    ->whereNotNull('driver_accepted_time')
                // ->whereDate('created_at', Carbon::today())
                    ->where('created_at', '<=', Carbon::now('Asia/Riyadh')->subMinutes(3))
                    ->whereDate('created_at', '>=', Carbon::yesterday('Asia/Riyadh'))
                    ->whereDate('created_at', '<=', Carbon::today('Asia/Riyadh'))
                ;
                if (auth()->user()->user_role == UserRole::DISPATCHER) {
                    $city_ids = UserCitys::where('user_id', auth()->user()->id)->pluck('city_id')->toArray();
                    $query->whereIn('city', $city_ids);
                }
            })
            ->with(['orders' => function ($query) use ($request) {
                $query->select('id', 'ingr_shop_id', 'driver_assigned_at', 'driver_accepted_time')
                    ->where('driver_id', $request->driver_id)
                    ->whereNotNull('driver_accepted_time')
                // ->whereDate('created_at', Carbon::today())
                    ->where('created_at', '<=', Carbon::now('Asia/Riyadh')->subMinutes(3))
                    ->whereDate('created_at', '>=', Carbon::yesterday('Asia/Riyadh'))
                    ->whereDate('created_at', '<=', Carbon::today('Asia/Riyadh'))
                ;
                // ;
                if (auth()->user()->user_role == UserRole::DISPATCHER) {
                    $city_ids = UserCitys::where('user_id', auth()->user()->id)->pluck('city_id')->toArray();
                    $query->whereIn('city', $city_ids);
                }
            }]);

        return DataTables::of($query)

            ->addColumn('full_name', fn($row) => $row->first_name . ' ' . $row->last_name)
            ->addColumn('client_id', fn($row) => $row->id)
            ->addColumn('orders_count', fn($row) => $row->orders->count())

            ->addColumn('avg_accept_time', fn($row) => $row->calculateAvgTime('driver_assigned_at', 'driver_accepted_time'))

            ->make(true);
    }

    public function FoodicsWebhook($orderId, $token, $payload)
    {
        $url = "https://api-sandbox.foodics.com/v5/orders/{$orderId}";

        $payload = [
            "driver_assigned_at" => "2019-12-19 11:15:30",
            "dispatched_at"      => "2019-12-19 11:14:30",
            "delivered_at"       => "2019-12-19 11:45:30",
            "driver_id"          => "8f12f639",
            "delivery_status"    => 2,
        ];

        $response = Http::withHeaders([
            'Accept'        => 'application/json',
            'Authorization' => "Bearer {$token}",
        ])

            ->timeout(10)
            ->put($url, $payload);

        if ($response->successful()) {
            return $response->json();
        } else {
            // Handle errors
            return [
                'status' => $response->status(),
                'error'  => $response->body(),
            ];
        }
    }

    public function blinkHook($url, $data)
    {
        $token        = env('BLINK_TOKEN');
        $client       = new Client();
        $request_data = [
            'body'    => json_encode($data),
            'headers' => [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json',
            ],
            'timeout' => 10,
        ];

        Log::info('BLINK api', [
            $request_data,
            $token,
            $data,
        ]);
        $response = $client->request('POST', $url, $request_data);

        $result = $response->getBody();
        Log::info('BLINK delivered successfully', [
            $result,
        ]);
    }

    private function sendOrderToWebhookamericana($url, $data)
    {
        $jsonData = json_encode($data);
        // dd($jsonData);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            Log::error('Webhook delivery failed', [
                'url'       => $url,
                'response'  => $response,
                'http_code' => $httpCode,
                'sent_data' => $jsonData,
            ]);
        } else {
            Log::info('Webhook delivered successfully', [
                'url'       => $url,
                'response'  => $response,
                'http_code' => $httpCode,
                'sent_data' => $jsonData,
            ]);
        }
    }
    private function sendOrderToWebhooktry($url, $data)
    {
        $jsonData = json_encode($data);
        // dd($jsonData);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    }
    private function sendOrderToWebhookLoginNext($url, $data)
    {
        $jsonData = json_encode($data);
        // dd($jsonData);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 700);
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode !== 200) {
            Log::error('Webhook delivery failed', [
                'url'       => $url,
                'response'  => $response,
                'http_code' => $httpCode,
                'sent_data' => $jsonData,
            ]);
        } else {
            Log::info('Webhook delivered successfully', [
                'url'       => $url,
                'response'  => $response,
                'http_code' => $httpCode,
                'sent_data' => $jsonData,
            ]);
        }
    }
}
