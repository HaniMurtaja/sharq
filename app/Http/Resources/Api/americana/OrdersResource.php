<?php

namespace App\Http\Resources\Api\americana;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrdersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $driver_lat = $this->OperatorDetail->lat;
        $driver_lng = $this->OperatorDetail->lng;
        $driver_status = $this->OperatorDetail->status;
        return [
            "parent_id"=> $this->id,
            "driver_id"=> $this->driver_id,
            "lat"=> $driver_lat,
            "lng"=> $driver_lng,
            "driver_status_id"=> "$driver_status"
        ];

    }
}
