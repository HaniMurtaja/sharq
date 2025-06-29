<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderRateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
                "distance"=> $this->distance,
                "duration"=>$this->duration,
                "fees"=>$this->service_fees??2.5,
                "balance"=>$this->distance??NULL,
                "expected_pickup"=> $this->expected_pickup??"2019-03-14 02:50:03",
                "expected_delivery"=>$this->expected_delivery??"2019-03-14 03:04:14"
        ];
    }
}
