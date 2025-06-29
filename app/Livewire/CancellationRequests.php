<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\WebHook;
use Livewire\Component;
use App\Models\OrderLog;
use App\Enum\OrderStatus;
use App\Enum\DriverStatus;
use App\Models\OrderDriver;
use Illuminate\Http\Request;
use Livewire\WithPagination;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Repositories\FirebaseRepository;
use App\Http\Resources\Api\OrderResource;
use App\Http\Services\NotificationService;

class CancellationRequests extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $orders_count = 0;

    protected $firebaseRepository;
    protected $notificationService;

    public function __construct()
    {
        $this->firebaseRepository = App::make(FirebaseRepository::class);
        $this->notificationService = App::make(NotificationService::class);
    }



    public function render()
    {
        $ordersQuery = Order::query();




        $ordersQuery->where(function ($query) {
            $query->whereDate('created_at', Carbon::yesterday())
                ->orWhereDate('created_at', Carbon::today());
        })->where('status', OrderStatus::PENDING_ORDER_CANCELLATION);


        if (auth()->user()->hasRole('Client')) {
            $ordersQuery->where('ingr_shop_id', auth()->id());
        }

        $this->orders_count = $ordersQuery->count();

        $orders = $ordersQuery->orderBy('created_at', 'desc')->with(['branch', 'shop', 'driver.driver']) // Eager load relationships
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Map over the paginated collection
        $orders->getCollection()->transform(function ($order) {
            $order->shop_name = $order->shop?->full_name;
            $order->branch_name = $order->branch?->name;
            $order->order_number = $order->order_number;
            $order->driver_name = $order->driver?->driver?->full_name;
            $order->driver_phone = $order->driver?->driver?->phone;
            $order->driver_photo = $order->driver?->driver?->getFirstMediaUrl('profile');
            $order->order_address = $order->branch ?
                $order->branch?->city?->name . ' ' . $order->branch?->street :
                $order->branchIntegration?->city?->name . ' ' . $order->branch?->street;

            // Add order log date values
            $order->created_time = $order->created_at->format('h:i a');


            $order->assign_date = OrderLog::where('order_id', $order->id)
                ->where('status', OrderStatus::PENDINE_DRIVER_ACCEPTANCE)
                ->orderBy('created_at', 'desc')->first()?->created_at->format('Y-m-d h:i a');
            $order->accept_date = OrderLog::where('order_id', $order->id)
                ->where('status', OrderStatus::DRIVER_ACCEPTED)
                ->orderBy('created_at', 'desc')->first()?->created_at->format('Y-m-d h:i a');
            $order->arrive_branch_date = OrderLog::where('order_id', $order->id)
                ->where('status', OrderStatus::ARRIVED_PICK_UP)
                ->orderBy('created_at', 'desc')->first()?->created_at->format('Y-m-d h:i a');
            $order->recive_date = OrderLog::where('order_id', $order->id)
                ->where('status', OrderStatus::PICKED_UP)
                ->orderBy('created_at', 'desc')->first()?->created_at->format('Y-m-d h:i a');
            $order->arrive_client_date = OrderLog::where('order_id', $order->id)
                ->where('status', OrderStatus::ARRIVED_TO_DROPOFF)
                ->orderBy('created_at', 'desc')->first()?->created_at->format('Y-m-d h:i a');
            $order->delivery_date = OrderLog::where('order_id', $order->id)
                ->where('status', OrderStatus::DELIVERED)
                ->orderBy('created_at', 'desc')->first()?->created_at->format('Y-m-d h:i a');
            $order->created_date = $order->created_at->format('Y-m-d h:i a');

            // Additional order details
            $order->shop_name =  $order->shop?->first_name ?? $order->branchIntegration?->client?->first_name;
            $order->branch_name =  $order->branch?->name ?? $order->branchIntegration?->name;
            $order->branch_lat = $order->branch?->lat;
            $order->branch_lng = $order->branch?->lng;
            $order->branch_phone =  $order->branch?->phone ?? $order->branchIntegration?->phone;
            $order->branch_area = $order->branch?->area?->name ?? $order->branchIntegration?->area?->name;

            $order->shop_profile = $order->shop?->getFirstMediaUrl('profile', 'thumb');
            $order->status_label = $order->status->getLabel();
            $order->payment_type_label = $order->payment_type ? $order->payment_type->getLabel() : '---';
            $order->vehicle_type = $order->vehicle?->type;
            // Render the infoWindow content using a Blade view
            $order->infoWindowContent = view('admin.pages.dispatchers.popup', ['order' => $order])->render();

            return $order;
        });

        return view('livewire.cancellation-requests', [
            'orders_count' => $this->orders_count,
            'orders' => $orders,
        ]);
    }




    public function accept($order_id, Request $request)
    {
        $order = Order::findOrFail($order_id);
        $order->status = OrderStatus::CANCELED;
        $order->driver_id = null;
        $order->save();

        $order_driver = OrderDriver::where('order_id', $order_id)->first();
        if ($order_driver) {
            try {
                $this->firebaseRepository->delete_driver_order($order_driver->driver_id, $order->id);
                //send notification
                $title = 'order cancelled';
                $body = 'you have an order cancelled';

                $this->notificationService->sendOrderNotifications($order_driver->driver_id, $title, $body, $order_id, 'orders');
            } catch (\Exception $e) {
                Log::info($e);
            }
            $order_driver->delete();
        }

        OrderLog::create([
            'order_id' =>  $order->id,
            'status' => OrderStatus::CANCELED,
            'action' => 'Cancel Order',

            'user_id' => auth()->id(),
            'description' => auth()->user()->first_name . ' cancel order ',
        ]);




        $client = $order->shop?->client ?? $order->branchIntegration?->client?->client;

        if ($client?->integration) {

            $webhook = WebHook::where('integration_company_id', $client?->integration?->id)->first();
            if ($webhook && $webhook->url) {
                $orderData = [
                    "order_id" =>  $order->id,
                    'status' =>  $order->status->value,
                    'status_label' => $order->status->getLabel(),
                    'driver' => $order->driver ? [
                        'id' => $order->driver?->driver?->id,
                        'name' =>  $order->driver?->driver?->full_name,
                        'phone' =>  $order->driver?->driver?->phone,
                        'status' => DriverStatus::tryFrom($order->driver?->driver?->operator?->status)?->getLabel(),
                        'tracking_url' => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",

                    ] : NULL,
                ];
                // dd($webhook->url);

                $this->sendOrderToWebhook($webhook->url, $orderData);
                $this->sendOrderToWebhook('https://webhook.site/329479cd-aea3-4e39-a77d-5384ceeab723', $orderData);
            }
        }
    }



    private function sendOrderToWebhook($url, $data)
    {
        $jsonData = json_encode($data);
        // dd($jsonData);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);


        if ($httpCode !== 200) {
            Log::error('Webhook delivery failed', [
                'url' => $url,
                'response' => $response,
                'http_code' => $httpCode
            ]);
        } else {
            Log::info('Webhook delivered successfully', [
                'url' => $url,
                'response' => $response,
                'http_code' => $httpCode,
                'sent_data' => $jsonData
            ]);
        }
    }
}
