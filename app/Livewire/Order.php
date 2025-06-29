<?php

namespace App\Livewire;

use App\Enum\UserRole;
use Carbon\Carbon;
use Livewire\Component;
use App\Settings\GeneralSettings;
use Livewire\Attributes\Computed;
use App\Models\Order as OrderModal;

class Order extends Component
{
    public $orders_count = 0;

    #[Computed]
    public function count()
    {
        return $this->orders_count = OrderModal::count();
    }
    public function render()
    {
        // $this->

        $ordersQuery = OrderModal::query();




        $ordersQuery->whereDate('created_at', Carbon::yesterday())
            ->orWhereDate('created_at', Carbon::today());



        if (auth()->user()->user_role == UserRole::CLIENT) {
            $ordersQuery->where('ingr_shop_id', auth()->id());
        }

        if (auth()->user()->user_role == UserRole::BRANCH) {
            $ordersQuery->where('ingr_branch_id', auth()->user()->branch_id);
        }

        $this->orders_count = $ordersQuery->count();

        return view('livewire.order', [
            'orders_count' => $this->orders_count
        ]);
    }
}
