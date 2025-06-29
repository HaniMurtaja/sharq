<?php

namespace App\Livewire;

use App\Enum\DriverStatus;
use App\Enum\OrderStatus;
use App\Models\Operator;
use App\Models\Order;
use App\Models\OrderLog;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class Dispatcher extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public    $lat             = 24.7136;
    public    $lng             = 46.6753;
    public    $orders_count    = 0;
    public $orders33;

    public function render()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $drivers = Operator::whereHas('operator', function ($q) {
            $q->where('status', '!=', 4)
                ->whereNotNull('lat')
                ->whereNotNull('lng');
        })
            ->with(['orders' => function ($query) use ($today, $yesterday) {
                $query->whereDate('orders.created_at', $today)
                    ->orWhereDate('orders.created_at', $yesterday)
                    ->with(['shop', 'branch']);
            }])
            ->get()
            ->map(function ($driver) {
                // Check if orders are being retrieved correctly
                //                \Log::info('Driver Orders', ['driver_id' => $driver->id, 'orders' => $driver->orders]);


                $tasks = $driver->orders()
                    ->where(function ($query) {
                        $query->whereDate('orders.created_at', Carbon::yesterday())
                            ->orWhereDate('orders.created_at', Carbon::today());
                    })
                    ->whereNotIn('status', [9, 10])
                    ->count() * 2 ?? 0;

                $orders = $driver->orders()->whereDate('orders.created_at', Carbon::yesterday())->orWhereDate('orders.created_at', Carbon::today())->get()->map(function ($order) {
                    return [
                        'order_number' => $order->order_number,
                        'shop_name' => $order->shop?->first_name ?? $order->branchIntegration?->client?->first_name,
                        'branch_name' => $order->branch?->name ?? $order->branchIntegration?->name,
                        'branch_photo' =>   $order->shop?->image,
                        'status' => $order->status->getLabel(),
                        'area' => $order->branch?->area?->name ?? $order->branchIntegration?->area?->name,
                        'customer_phone' => $order->customer_phone,
                        'client_order_id' => $order->client_order_id,
                        'id' => $order->id
                    ];
                });

                return [
                    'lat' => $driver->operator?->lat,
                    'lng' => $driver->operator->lng,
                    'profile_image' => $driver?->image,
                    'full_name' => $driver?->full_name,
                    'phone' => $driver->phone,
                    'status' =>  @DriverStatus::tryFrom(@@$driver->operator?->status)->value,

                    'infoWindowContent' => view('admin.pages.dispatchers.driver-popup', ['driver' => $driver, 'tasks' => $tasks, 'orders' => $orders])->render(),
                ];
            });

        $user_role = auth()->user()->user_role?->value;
        $orders = Order::whereDate('created_at', Carbon::yesterday())
            ->orWhereDate('created_at', Carbon::today())
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->with(['branch', 'shop', 'driver.driver'])
            // ->limit(10)
            ->get()
            ->map(function ($order) use($user_role) {
                $order->shop_name = $order->shop?->full_name;
                $order->branch_name = $order->branch?->name;
                $order->branch_lat = $order->branch?->lat;
                $order->branch_lng = $order->branch?->lng;
                $order->order_number = $order->order_number;
                $order->driver_name = $order->driver?->driver?->full_name;
                $order->driver_phone = $order->driver?->driver?->phone;
                $order->driver_photo = @$order->driver?->driver?->image;


                $order->order_address = $order->branch ? $order->branch?->city?->name . ' ' . $order->branch?->street : $order->branchIntegration?->city?->name . ' ' . $order->branch?->street;

                $order->assign_date = OrderLog::where('order_id', $order->id)->where('status', OrderStatus::PENDINE_DRIVER_ACCEPTANCE)->orderBy('created_at', 'desc')->first()?->created_at->format('Y-m-d h:i a');
                $order->accept_date = OrderLog::where('order_id', $order->id)->where('status', OrderStatus::DRIVER_ACCEPTED)->orderBy('created_at', 'desc')->first()?->created_at->format('Y-m-d h:i a');
                $order->arrive_branch_date = OrderLog::where('order_id', $order->id)->where('status', OrderStatus::ARRIVED_PICK_UP)->orderBy('created_at', 'desc')->first()?->created_at->format('Y-m-d h:i a');
                $order->recive_date = OrderLog::where('order_id', $order->id)->where('status', OrderStatus::PICKED_UP)->orderBy('created_at', 'desc')->first()?->created_at->format('Y-m-d h:i a');
                $order->arrive_client_date = OrderLog::where('order_id', $order->id)->where('status', OrderStatus::ARRIVED_TO_DROPOFF)->orderBy('created_at', 'desc')->first()?->created_at->format('Y-m-d h:i a');
                $order->delivery_date = OrderLog::where('order_id', $order->id)->where('status', OrderStatus::DELIVERED)->orderBy('created_at', 'desc')->first()?->created_at->format('Y-m-d h:i a');
                $order->created_date = $order->created_at->format('Y-m-d h:i a');

                $order->shop_name =  $order->shop?->first_name ?? $order->branchIntegration?->client?->first_name;
                $order->branch_name =  $order->branch?->name ?? $order->branchIntegration?->name;
                $order->branch_phone =  $order->branch?->phone ?? $order->branchIntegration?->phone;
                $order->branch_area = $order->branch?->area?->name ?? $order->branchIntegration?->area?->name;

                $order->shop_profile = $order->shop?->image;
                $order->status_label = $order->status->getLabel();
                $order->payment_type_label = $order->payment_type ? $order->payment_type->getLabel() : '---';
                $order->vehicle_type = $order->vehicle?->type;
                // Render the infoWindow content using a Blade view

                $order->infoWindowContent = view('admin.pages.dispatchers.popup', ['order' => $order, 'user_role' =>  $user_role])->render();

                return $order;
            });

        // dd($drivers);
        return view('livewire.dispatcher', [
            'orders' => $orders,
            'drivers' => $drivers

        ]);
    }
}
