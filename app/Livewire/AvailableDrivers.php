<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Operator;
use App\Enum\DriverStatus;
use Livewire\WithPagination;
use Carbon\Carbon;
class AvailableDrivers extends Component
{
    use WithPagination;
    public $available_orders = 0;
    protected $paginationTheme = 'bootstrap';
    public function render()
    {
        // Count available orders
        $drivers = Operator::query();
        $this->available_orders = Operator::whereHas('operator', function ($q) {
            $q->where('status', DriverStatus::AVAILABLE);
        })->count();
    
        // Fetch and map drivers
        $drivers = $drivers->whereHas('operator', function ($q) {
            $q->where('status', DriverStatus::AVAILABLE);
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
    
        // Return the view with the mapped drivers and available orders count
        return view('livewire.available-drivers', [
            'drivers' => $drivers,
            'available_orders' => $this->available_orders,
        ]);
    }
    
}
