<?php

namespace App\Http\Resources\Admin\Dispatcher;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverHomeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'phone' => $this->phone,
            'lat' => @$this->operator->lat,
            'lng' => @$this->operator->lng,
            'status' => @$this->operator->status,
            'order_count' => count($this->DriverBranchAndClient),
            'photo' => $this->image_url,
            'orders' =>  OrderHomeResource::collection($this->DriverBranchAndClient),

        ];
    }
}

