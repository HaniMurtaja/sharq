<?php

namespace App\Livewire;

use App\Enum\DriverStatus;
use App\Models\Operator;
use Livewire\Component;

class AllDrivers extends Component
{
    public $available_orders = 0;
    public $busy_orers = 0;
    public $away_orders = 0;
    public $offline_orders = 0;
    public function render()
    {
        $this->available_orders = Operator::whereHas('operator', function ($q) {
            $q->where('status', DriverStatus::AVAILABLE);
        })->count();
        $this->busy_orers = Operator::whereHas('operator', function ($q) {
            $q->where('status', DriverStatus::BUSY);
        })->count();
        $this->away_orders = Operator::whereHas('operator', function ($q) {
            $q->where('status', DriverStatus::AWAY);
        })->count();
        $this->offline_orders = Operator::whereHas('operator', function ($q) {
            $q->where('status', DriverStatus::OFFLINE);
        })->count();

        return view('livewire.all-drivers', [
            'available_orders' => $this->available_orders,
            'busy_orers' => $this->busy_orers,
            'away_orders' => $this->away_orders,
            'offline_orders' => $this->offline_orders
        ]);
    }
}
