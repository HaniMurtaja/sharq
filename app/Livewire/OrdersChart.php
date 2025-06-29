<?php

namespace App\Livewire;

use App\Enum\OrderStatus;
use Livewire\Component;
use App\Models\Order;
class OrdersChart extends Component
{
    public $orders = [];
    public function render()
    {
        $this->orders = [
            'created' => Order::where('status', OrderStatus::CREATED)->count(), 
            'cancelled' => Order::where('status', OrderStatus::CANCELED)->count(),
            'failed' =>  Order::where('status', OrderStatus::FAILED)->count(),
            'Unassigned' => Order::where('status', OrderStatus::UNASSIGNED)->count(),
            'Driver rejected' => Order::where('status', OrderStatus::DRIVER_REJECTED)->count(),
        ];
        return view('livewire.orders-chart');
    }
}
