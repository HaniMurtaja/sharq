<?php

namespace App\Http\Resources\Admin\Dispatcher;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverResource extends JsonResource
{
    public function toArray($request)
    {
        // dd($this->orders()->count());
        $orders_count =  $this->orders()->whereDate('orders.created_at', Carbon::yesterday())
                ->orWhereDate('orders.created_at', Carbon::today())->count();
        //    dd($orders_count);
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'phone' => $this->phone,
            'lat' => @$this->operator->lat,
            'lng' => @$this->operator->lng,
            'order_count' => $orders_count,
            'photo' => $this->image,

        ];
    }
}

