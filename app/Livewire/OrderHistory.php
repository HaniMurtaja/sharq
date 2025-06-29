<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\OrderLog;
use Illuminate\Support\Facades\App;

use LivewireUI\Modal\ModalComponent;

class OrderHistory extends ModalComponent
{
    public $histories;
    public $order_id;
    public $order;
    public function __construct() {}

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function delete()
    {

        $this->reset('searchName');
    }





    public function render()
    {
        //   dd('assa');
        $this->order = Order::findOrFail($this->order_id);
        $this->histories = OrderLog::where('order_id', $this->order->id)->get();

        return view('livewire.order-history', [
            'histories' => $this->histories,
            'order' => $this->order,

        ]);
    }
}
