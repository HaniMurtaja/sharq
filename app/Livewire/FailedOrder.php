<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Order;
use Livewire\Component;
use App\Models\OrderLog;
use App\Enum\OrderStatus;
use Livewire\WithPagination;
use App\Settings\GeneralSettings;

class FailedOrder extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $orders_count = 0;


    public function render()
    {
        $ordersQuery = Order::query();




        $ordersQuery->where(function ($query) {
            $query->whereDate('created_at', Carbon::yesterday())
                ->orWhereDate('created_at', Carbon::today());
        })->where('status', OrderStatus::FAILED);


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
            $order->branch_lat = $order->branch?->lat;
            $order->branch_lng = $order->branch?->lng;
            
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

        return view('livewire.failed-order', [
            'orders_count' => $this->orders_count,
            'orders' => $orders,
        ]);
    }
}
