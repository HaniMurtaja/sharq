<?php

namespace App\Livewire;

use App\Http\Services\NotificationService;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Operator;
use App\Models\OrderLog;
use App\Enum\OrderStatus;
use App\Enum\DriverStatus;
use App\Models\OrderDriver;
use Livewire\Attributes\Url;
use App\Models\ClientBranches;
use App\Models\OperatorDetail;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use LivewireUI\Modal\ModalComponent;
use App\Repositories\FirebaseRepository;
use App\Http\Resources\Api\OrderResource;

class AssignWorker extends ModalComponent
{
    public $driver_id = "";
    public $order_id;
    #[Url(history: true)]
    public    $searchName    = '';
    public    $order_drivers = [];
    public    $order;
    public    $drivers;
    protected $firebaseRepository;
    protected $notificationService;

    public function __construct()
    {
        $this->firebaseRepository = App::make(FirebaseRepository::class);
        $this->notificationService = App::make(NotificationService::class);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function delete()
    {

        $this->reset('searchName');
    }

    public function getValidationAttributes()
    {
        return [
            'driver_id' => 'driver',
        ];
    }

    public function getRules()
    {
        return [
            'driver_id' => ['required', 'exists:users,id'],
        ];
    }

    public function mount($order)
    {
        $this->order = $order;
    }

    public function render()
    {
        $settings = new GeneralSettings();
        $currentTime = Carbon::now('Asia/Riyadh');
        $this->order_drivers = Order::findOrFail($this->order['id'])->drivers;

        $order = Order::findOrFail($this->order['id']);



        $branch = ClientBranches::find($this->order['ingr_branch_id']);
        $branchLatitude = $branch?->lat;
        $branchLongitude = $branch?->lng;
        $branchLatitude = $branchLatitude ?? 0;
        $branchLongitude = $branchLongitude ?? 0;




        $this->drivers = OperatorDetail::where('operators.status', '!=', 4)
            ->join('users', 'users.id', '=', 'operators.operator_id')
            ->leftJoin('order_drivers', 'order_drivers.driver_id', '=', 'operators.operator_id')
            ->leftJoin('orders', 'orders.id', '=', 'order_drivers.order_id')
            ->select(
                'users.id as user_id',
                DB::raw("CONCAT(users.first_name, ' ', users.last_name) as full_name"),
                DB::raw(
                    "6371 * acos(cos(radians(" . $branchLatitude . ")) * cos(radians(operators.lat))
                    * cos(radians(operators.lng) - radians(" . $branchLongitude . "))
                    + sin(radians(" . $branchLatitude . ")) * sin(radians(operators.lat))) AS distance"
                ),
                DB::raw("COUNT(CASE WHEN orders.status NOT IN (9, 20, 10, 13) AND DATE(orders.created_at) = CURDATE() THEN order_drivers.id END) as orders_count")

            )
            ->groupBy('users.id', 'users.first_name', 'users.last_name', 'operators.lat', 'operators.lng')
            ->orderBy('distance', 'asc')
            ->get();



        // $this->drivers =  Operator::whereHas('operator', function ($q) {
        //     $q->where('status', DriverStatus::AVAILABLE);
        // })->get();

        //    dd($this->drivers);
        return view('livewire.assign-worker', [
            'drivers' => $this->drivers,
            'order' => $order,
            'branch_name' => $order->branch?->area?->name ?? $order->branchIntegration?->area?->name,
        ]);
    }

    public function assignDriver()
    {
        $this->validate();
        /*OrderDriver::create([
                'order_id' => $this->order['id'],
                'driver_id' => $this->driver_id
            ]);*/
        //find if order has driver before
        $prev_order_driver = OrderDriver::where('order_id', $this->order['id'])->first();
        if ($prev_order_driver) {
            //try delete firebase
            try {
                // Attempt to save to Firebase
                $this->firebaseRepository->delete_driver_order($prev_order_driver->driver_id, $this->order['id']);
            } catch (\Exception $e) {
                // Handle the exception (log it, show a message, etc.)
                Log::info($e);
            }
        }
        // Update or create the OrderDriver record


        //find order
        $order = Order::with('shop', 'branch')->where('id', $this->order['id'])->first();
        if ($order) {
            $order->status = 2; //Pending driver acceptance
            $order->driver_id = $this->driver_id;
            $order->save();


            $branch = $order->branch ?? $order->branchIntegration;
            $branchLatitude = $branch->lat ?? 0;
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
                ->where('operator_id', $this->driver_id)
                ->first();

            // dd((string) $operator->distance);

            OrderDriver::updateOrCreate(
                ['order_id' => $this->order['id']], // Check for existing order_id
                [
                    'driver_id' => $this->driver_id,  // Update with the new driver_id
                    'distance' => (string) $operator->distance // Update with the calculated distance
                ]
            );




            $orderResource = new OrderResource($order);
            $orderData = $orderResource->toArray(request());
            //try save firebase
            try {
                // Attempt to save to Firebase
                $this->firebaseRepository->save_driver_order($this->driver_id, $orderData);
                //send notification
                $title = 'new order';
                $body = 'you have a new order assigned to you';
//                $this->notificationService->send_for_specific('طلب جديد ', 'طلب جديد جاهز للتوصيل', $this->driver_id);
                $this->notificationService->send_for_specific('طلب جديد ', 'طلب جديد جاهز للتوصيل', $this->driver_id);

//                $this->notificationService->send_for_specific($this->driver_id, $title, $body, $this->order['id'], 'orders');
            } catch (\Exception $e) {
                // Handle the exception (log it, show a message, etc.)
                Log::info($e);
            }
        }
        $driver = Operator::findOrFail($this->driver_id);
        OrderLog::create([
            'order_id' =>  $this->order['id'],
            'status' => OrderStatus::PENDINE_DRIVER_ACCEPTANCE,
            'action' => 'Assgin Order',
            'driver_id' => $this->driver_id,
            'user_id' => auth()->id(),
            'description' => auth()->user()->first_name . ' assign order to driver ' . $driver->full_name,
        ]);

        $this->closeModal();
    }
}
