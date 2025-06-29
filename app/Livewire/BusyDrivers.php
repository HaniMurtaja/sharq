<?php

namespace App\Livewire;
use Livewire\Component;
use App\Models\Operator;
use App\Enum\DriverStatus;
use Livewire\WithPagination;
use Carbon\Carbon;
class BusyDrivers extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $busy_drivers = 0;
    public function render()
    {
        $this->busy_drivers = Operator::whereHas('operator', function ($q) {
            $q->where('status', DriverStatus::BUSY);
        })->count();





        $drivers = Operator::query();
       
    
        // Fetch and map drivers
        $drivers = $drivers->whereHas('operator', function ($q) {
            $q->where('status', DriverStatus::BUSY);
        })->paginate(10);
        
        $drivers->getCollection()->transform(function ($driver) {
            // Count tasks based on order status and creation date
            $tasks = $driver->orders()
                ->where(function ($query) {
                    $query->whereDate('orders.created_at', Carbon::yesterday())
                          ->orWhereDate('orders.created_at', Carbon::today());
                })
                ->whereNotIn('status', [9, 10])
                ->count();
    
            // Map each order's relevant details
            $orders = $driver->orders()
                ->where(function ($query) {
                    $query->whereDate('orders.created_at', Carbon::yesterday())
                          ->orWhereDate('orders.created_at', Carbon::today());
                })
                ->get()
                ->map(function ($order) {
                    return [
                        'order_number' => $order->order_number,
                        'shop_name' => $order->shop?->first_name ?? $order->branchIntegration?->client?->first_name,
                        'branch_name' => $order->branch?->name ?? $order->branchIntegration?->name,
                        'branch_photo' => $order->shop?->getFirstMediaUrl('profile', 'thumb'),
                        'status' => $order->status->getLabel(),
                        'area' => $order->branch?->area?->name ?? $order->branchIntegration?->area?->name,
                        'customer_phone' => $order->customer_phone,
                        'client_order_id' => $order->client_order_id,
                        'id' => $order->id,
                    ];
                });
    
            // Return driver data for the map
            return [
                'lat' => $driver->operator?->lat,
                'lng' => $driver->operator?->lng,
                'profile_image' => $driver->getFirstMediaUrl('profile', 'thumb'),
                'full_name' => $driver?->full_name,
                'phone' => $driver->phone,
                'tasks' => $tasks,
                'infoWindowContent' => view('admin.pages.dispatchers.driver-popup', [
                    'driver' => $driver,
                    'tasks' => $tasks,
                    'orders' => $orders
                ])->render(),
            ];
        });


        return view('livewire.busy-drivers', [
            'drivers' => $drivers,
            'busy_drivers' => $this->busy_drivers,
        ]);
    }
}
