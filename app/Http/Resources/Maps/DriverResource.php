<?php

namespace App\Http\Resources\Maps;

use App\Http\Resources\Admin\Dispatcher\OrderHomeResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->operator->id,
            'full_name' => $this->operator->full_name,
            'phone' => $this->operator->phone,
            'lat' => @$this->lat,
            'lng' => @$this->lng,
            'status' => @$this->status,
            'order_count' => $this->order_count,
            'photo' => $this->operator->image_url,
            'orders' => OrderHomeResource::collection($this->DriverOrders), // Optimized order retrieval


        ];
    }
}

