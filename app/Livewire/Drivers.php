<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Operator;
use App\Enum\DriverStatus;
use Livewire\WithPagination;

class Drivers extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $drivers_count = 0;

    public function render()
    {
        $this->drivers_count = Operator::whereHas('operator', function ($query) {
            $query->whereIn('status', [DriverStatus::AVAILABLE, DriverStatus::AWAY, DriverStatus::BUSY, DriverStatus::OFFLINE]);
        })->count();
        
        return view('livewire.drivers',[
            'drivers_count' => $this->drivers_count
        ]);
    }
}
