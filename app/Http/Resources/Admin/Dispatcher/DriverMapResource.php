<?php

namespace App\Http\Resources\Admin\Dispatcher;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverMapResource extends JsonResource
{
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'phone' => $this->mobiledriver,
            'lat' => @$this->operator_lat,
            'lng' => @$this->operator_lng,
            'order_count' => @$this->total_orders_yesterday_today ,
            'photo' => $this->driver_photo,

        ];
    }
}

