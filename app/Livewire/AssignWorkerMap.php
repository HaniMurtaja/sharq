<?php

namespace App\Livewire;

use App\Enum\OrderStatus;
use App\Models\Operator;
use App\Models\Order;
use App\Models\OrderLog;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class AssignWorkerMap extends Component
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

                return [
                    'lat' => $driver->operator?->lat,
                    'lng' => $driver->operator->lng,
                    'profile_image' => $driver->getFirstMediaUrl('profile', 'thumb'),
                    'full_name' => $driver?->full_name,
                    'phone' => $driver->phone,
                    'infoWindowContent' => view('admin.pages.dispatchers.driver-popup', ['driver' => $driver])->render(),
                    'orders' => $driver->orders->map(function ($order) {
                        return [
                            'order_number' => $order->order_number,
                            'shop_name' => $order->shop?->full_name,
                            'branch_name' => $order->branch?->name,
                            'status' => $order->status->getLabel(),
                        ];
                    }),
                ];
            });


        
         

        // dd($drivers);
        return view('livewire.assign-worker-map', [
           
            'drivers' => $drivers

        ]);
    }
}
