<?php

namespace App\Http\Resources\Api\Integration;

use App\Enum\DriverStatus;
use Illuminate\Http\Resources\Json\JsonResource;

class TrackOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'order_id' => $this->id,
            'client_order_id' => $this->client_order_id_string ?? $this->client_order_id ?? $this->id,
            "branch" => [
                "lat" => $this->branch?->lat,
                "lng" => $this->branch?->lng
            ],
            "customer" => [
                "lat" => $this->lat,
                "lng" => $this->lng
            ],
            "driver" => $this->driver ? [
                "lat" => $this->driver->lat,
                "lng" => $this->driver->lng
            ] : NULL,
        ];
    }
}
