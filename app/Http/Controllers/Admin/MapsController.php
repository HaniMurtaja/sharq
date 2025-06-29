<?php
namespace App\Http\Controllers\Admin;

use App\Enum\OrderStatus;
use App\Enum\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\Maps\BranchResource;
use App\Http\Resources\Maps\DriverResource;
use App\Models\Client;
use App\Models\ClientBranches;
use App\Models\Operator;
use App\Models\OperatorDetail;
use App\Models\Order;
use App\Models\OrderLog;
use App\Models\Reason;
use App\Models\UserCitys;
use App\Repositories\FirebaseRepositoryTest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MapsController extends Controller
{
    public function Maps()
    {
        $branches      = [];
        $fees          = 0;
        $clients       = Client::select('id', 'first_name')->get();
        $branchesQuery = ClientBranches::has('getOrders');
        if (auth()->user()->user_role == UserRole::CLIENT) {
            $fees = Client::findOrFail(auth()->user()->id)->client?->clienGroup?->default_delivery_fee;
            $branchesQuery->where('client_id', auth()->user()->id);
        }
        if (auth()->user()->user_role == UserRole::BRANCH) {
            $fees = Client::findOrFail(auth()->user()->client_id)->client?->clienGroup?->default_delivery_fee;
        }
        $order_count = Order::where(function ($q) {
            $q->whereDate('created_at', Carbon::yesterday())
                ->orWhereDate('created_at', Carbon::today());
        })->whereNotIn('status', [OrderStatus::CANCELED, OrderStatus::DELIVERED]);
        if (auth()->user()->user_role == UserRole::CLIENT) {
            $order_count = $order_count->where('ingr_shop_id', auth()->id());
        }

        if (auth()->user()->user_role == UserRole::BRANCH) {
            $order_count = $order_count->where('ingr_branch_id', auth()->user()->branch_id);
        }
        $order_count = $order_count->count();
        $auth_name   = auth()->user()->full_name;
        if (auth()->user()->user_role == UserRole::BRANCH) {
            $auth_name = auth()->user()->branch?->client?->full_name . ' - ' . auth()->user()->branch?->name;
        }
        $InitializingMap = $this->InitializingMap();
        $query           = Operator::whereHas('operator', function ($q) {
            $q->whereIn('status', [1, 2]);
        })->get(); // المناديب
        $delegates = \App\Http\Resources\Admin\Dispatcher\DriverHomeResource::collection($query);
        //dd($branches, $delegates);
        $branches = $branchesQuery->get();
        return view('admin.pages.dispatchers.versiontest.index', compact('branches', 'delegates', 'InitializingMap', 'clients', 'auth_name', 'fees', 'order_count'));
    }

    public static function InitializingMap()
    {
        if (auth()->user()->user_role == UserRole::CLIENT) {
            // dd('clieent');
            $branch = ClientBranches::where('client_id', auth()->user()->id)->orderBy('created_at', 'asc')->first();
            $result = [
                'lat'  => $branch->lat,
                'lng'  => $branch->lng,
                'zoom' => 8,
            ];
        } elseif (auth()->user()->user_role == UserRole::BRANCH) {
            $branch = ClientBranches::where('id', auth()->user()->branch_id)->orderBy('created_at', 'asc')->first();
            $result = [
                'lat'  => $branch->lat,
                'lng'  => $branch->lng,
                'zoom' => 12,
            ];
        } else {
            $result = [
                'lat'  => 23.8859,
                'lng'  => 45.0792,
                'zoom' => 6,
            ];
        }

        return $result;
    }

    public function getMapData()
    {
        // return collect();
        $firebase = new FirebaseRepositoryTest();

        $filters  = [];
        $shopId   = null;
        $branchId = null;

        if (auth()->user()->user_role == UserRole::CLIENT) {
            $shopId               = auth()->id();
            $filters['client_id'] = $shopId;
        }

        if (auth()->user()->user_role == UserRole::BRANCH) {
            $branchId      = auth()->user()->branch_id;
            $filters['id'] = $branchId;
        }

        $mapData = $firebase->getMapDataForTodayAndYesterday($shopId, $branchId);

        //    dd($mapData);
        $branches = $firebase->getFilteredBranches($filters);

        return response()->json([
            'initializingMap' => $this->InitializingMap(),
            'branches'        => $branches,
            'delegates'       => $mapData,
        ]);
    }

    public function getMapDataOld()
    {
        //$mapData = MapView::whereBetween(DB::raw('DATE(created_at)'), [now()->subDay()->toDateString(), now()->toDateString()])->get();
        // $mapData = MapView::get();

        //dd( $mapData );

        $branchesQuery = ClientBranches::whereNotNull('lat')
            ->whereNotNull('lng')
            ->has('getOrders')
            ->select('id', 'client_id', 'lat', 'lng');

        $InitializingMap = $this->InitializingMap();
        $queryDrivers    = OperatorDetail::whereIn('status', [1, 2])
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->withCount([
                'DriverOrders as order_count' => function ($query) {
                    $query->where('created_at', '>=', Carbon::yesterday())
                        ->where('created_at', '<=', Carbon::today());
                },
            ])
            ->with([
                'DriverOrders' => function ($query) {
                    $query->where('created_at', '>=', Carbon::yesterday())
                        ->where('created_at', '<=', Carbon::today())
                        ->select(['id', 'driver_id', 'order_id', 'created_at']);
                },
            ]);

        if (auth()->user()->user_role == UserRole::CLIENT) {
            $queryDrivers->whereHas('OrdersDateWithStatusAndWithoutDELIVERED', function ($query) {
                $query->where('orders.ingr_shop_id', auth()->id());
            });
            $branchesQuery->where('client_id', auth()->id());
        }
        if (auth()->user()->user_role == UserRole::BRANCH) {
            $queryDrivers->whereHas('OrdersDateWithStatusAndWithoutDELIVERED', function ($query) {
                $query->where('orders.ingr_branch_id', auth()->user()->branch_id);
            });
            $branchesQuery->where('id', auth()->user()->branch_id);
        }
        DispatcherController::ActionRoleQueryWhere(null, null, $queryDrivers);
        $branches  = BranchResource::collection($branchesQuery->get());
        $delegates = DriverResource::collection($queryDrivers->limit(500)->get());
        return response()->json([
            'initializingMap' => $InitializingMap,
            'branches'        => $branches,
            'delegates'       => $delegates,
        ]);
    }

    // public static function geoapify()
    // {
    //     // dd(9);
    //     abort_unless(auth()->user()->hasPermissionTo('basic_dispatcher_view'), 403, 'You do not have permission to view this page.');
    //     $branches  = [];
    //     $fees = 0;
    //     $clientsQuery = Client::orderbydesc('id');
    //     if (auth()->user()->user_role == UserRole::CLIENT) {
    //         $fees = Client::findOrFail(auth()->user()->id)->client?->clienGroup?->default_delivery_fee;
    //         $firebase = new FirebaseRepositoryTest();
    //         $branches = $firebase->getBranchesByClientId(auth()->user()->id);
    //     }

    //     if (auth()->user()->user_role == UserRole::BRANCH) {
    //         $fees = Client::findOrFail(auth()->user()->client_id)->client?->clienGroup?->default_delivery_fee;
    //     }
    //     $order_count = Order::where(function ($q) {
    //         $q->whereDate('created_at', Carbon::yesterday())
    //             ->orWhereDate('created_at', Carbon::today());
    //     })->whereNotIn('status', [OrderStatus::CANCELED, OrderStatus::DELIVERED]);
    //     if (auth()->user()->user_role == UserRole::CLIENT) {
    //         $order_count = $order_count->where('ingr_shop_id', auth()->id());
    //     }
    //     if (auth()->user()->user_role == UserRole::BRANCH) {
    //         $order_count = $order_count->where('ingr_branch_id', auth()->user()->branch_id);
    //     }
    //     $order_count = $order_count->count();
    //     $auth_name = auth()->user()->full_name;
    //     if (auth()->user()->user_role == UserRole::BRANCH) {
    //         $auth_name = auth()->user()->branch?->client?->full_name . ' - ' .  auth()->user()->branch?->name;
    //     }
    //     $InitializingMap = self::InitializingMap();
    //     $allow_city = UserCitys::where('user_id', auth()->id())->pluck('city_id')->toArray();
    //     $clients = $clientsQuery->get();

    //     return view('admin.pages.geoapify.leaflet', compact('clients', 'branches', 'fees', 'auth_name', 'order_count', 'InitializingMap', 'allow_city'));
    // }

    public function geoapify()
    {

        abort_unless(auth()->user()->hasPermissionTo('basic_dispatcher_view'), 403, 'You do not have permission to view this page.');

        $branches = [];
        $fees     = 0;

        $filters  = [];
        $shopId   = null;
        $branchId = null;
        $userId   = auth()->id();
        $user     = auth()->user();

        $city_ids   = [];
        $country_id = null;

        if (auth()->user()->user_role == UserRole::CLIENT) {
            $shopId               = auth()->id();
            $filters['client_id'] = $shopId;
        }

        if (auth()->user()->user_role == UserRole::BRANCH) {
            $branchId      = auth()->user()->branch_id;
            $filters['id'] = $branchId;
        }

        $clients = Client::query();

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

            $clients->whereHas('getUserCitys', function ($sub) use ($city_ids) {
                $sub->whereIn('city_id', $city_ids);
            });
        }

        if ($user->user_role === UserRole::ADMIN && $user->country_id) {

            $country_id = $user->country_id;
            $clients->whereHas('client.city', function ($sub) use ($user) {
                $sub->where('country_id', $user->country_id);
            });
        }

        $clients = $clients->get();

        if (auth()->user()->user_role == UserRole::CLIENT) {
            //return MapsController::geoapify();
            $fees     = Client::findOrFail(auth()->user()->id)->client?->clienGroup?->default_delivery_fee;
            $branches = ClientBranches::where('client_id', auth()->user()->id)->get();
        }
        if (auth()->user()->user_role == UserRole::BRANCH) {
            //        return MapsController::geoapify();
            $fees = Client::findOrFail(auth()->user()->client_id)->client?->clienGroup?->default_delivery_fee;
        }
        $order_count = Order::where(function ($q) {
            $q->whereDate('created_at', Carbon::yesterday())
                ->orWhereDate('created_at', Carbon::today());
        })->whereNotIn('status', [OrderStatus::CANCELED, OrderStatus::DELIVERED]);
        if (auth()->user()->user_role == UserRole::CLIENT) {
            $order_count = $order_count->where('ingr_shop_id', auth()->id());
        }
        if (auth()->user()->user_role == UserRole::BRANCH) {
            $order_count = $order_count->where('ingr_branch_id', auth()->user()->branch_id);
        }
        DispatcherController::ActionRoleQueryWhere($order_count, null, null);
        $order_count = $order_count->count();
        $auth_name   = auth()->user()->full_name;
        if (auth()->user()->user_role == UserRole::BRANCH) {
            $auth_name = auth()->user()->branch?->client?->full_name . ' - ' . auth()->user()->branch?->name;
        }

        $InitializingMap = $this->InitializingMap();

        $allow_city = UserCitys::where('user_id', auth()->id())->pluck('city_id')->toArray();

        $cancel_reasons = Reason::all();

        $user_role = $user->user_role->value;
        // dd($user_role);

        if (auth()->user()->user_role === \App\Enum\UserRole::DISPATCHER) {
            return view('admin.pages.dispatchers.index', compact(['user_role', 'filters', 'shopId', 'branchId', 'city_ids', 'country_id', 'order_count', 'cancel_reasons', 'fees', 'branches', 'clients', 'auth_name', 'InitializingMap', 'allow_city']));

        } else {
            return view('admin.pages.geoapify.leaflet', compact(['user_role', 'filters', 'shopId', 'branchId', 'city_ids', 'country_id', 'order_count', 'cancel_reasons', 'fees', 'branches', 'clients', 'auth_name', 'InitializingMap', 'allow_city']));
        }
    }

    public function google()
    {
        return view('admin.pages.geoapify.index');
    }
    public function googlemap()
    {
        return view('admin.pages.googlemap.index');
    }

    public function getOrderPopupNewMap(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('basic_dispatcher_view'), 403, 'You do not have permission to view this page.');

        $user_role = auth()->user()->user_role?->value;

        $order = Order::findOrFail($request->id);

        // Transform the order data
        $order->shop_name     = $order->shop?->full_name;
        $order->branch_name   = $order->branch?->name;
        $order->branch_lat    = $order->branch?->lat;
        $order->branch_lng    = $order->branch?->lng;
        $order->order_number  = $order->order_number;
        $order->driver_name   = $order->DriverData2?->full_name;
        $order->driver_phone  = $order->DriverData2?->phone;
        $order->driver_photo  = $order->DriverData2?->image;
        $order->order_address = $order->branch ?
        $order->branch?->city?->name . ' ' . $order->branch?->street :
        $order->branchIntegration?->city?->name . ' ' . $order->branch?->street;

        // Add order log date values
        $order->created_time       = $order->created_at->format('h:i a');
        $order->assign_date        = $order->created_at->format('Y-m-d h:i a');
        $order->accept_date        = $order->created_at->format('Y-m-d h:i a');
        $order->arrive_branch_date = $order->created_at->format('Y-m-d h:i a');
        $order->recive_date        = $order->created_at->format('Y-m-d h:i a');
        $order->arrive_client_date = $order->created_at->format('Y-m-d h:i a');
        $order->delivery_date      = $order->created_at->format('Y-m-d h:i a');
        $order->created_date       = $order->created_at->format('Y-m-d h:i a');

        // Additional order details
        $order->shop_name    = $order->shop?->first_name ?? $order->branchIntegration?->client?->first_name;
        $order->branch_name  = $order->branch?->name ?? $order->branchIntegration?->name;
        $order->branch_phone = $order->branch?->phone ?? $order->branchIntegration?->phone;
        $order->branch_area  = $order->branch?->area?->name ?? $order->branchIntegration?->area?->name;

        $order->shop_profile       = $order->shop?->image;
        $order->status_label       = $order->status->getLabel();
        $order->payment_type_label = $order->payment_type ? $order->payment_type->getLabel() : '---';
        $order->vehicle_type       = $order->vehicle?->type;
        $dataInfoDriver            = [
            'id'  => $order->DriverData2?->id,
            'lat' => $order->OperatorDetail?->lat,
            'lng' => $order->OperatorDetail?->lng,
        ];

        return response()->json([
            "infoDriver"        => $dataInfoDriver,
            'infoWindowContent' => view('admin.pages.dispatchers.popup', ['order' => $order, 'user_role' => $user_role])->render(),
        ]);
    }

    private function getOrderLogDate($orderId, $status)
    {
        return OrderLog::where('order_id', $orderId)
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->first()?->created_at->format('Y-m-d h:i a');
    }
}
