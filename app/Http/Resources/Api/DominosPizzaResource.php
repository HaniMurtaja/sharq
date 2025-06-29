<?php

namespace App\Http\Resources\Api;

use App\Enum\DriverStatus;
use App\Enum\OrderStatus;
use Illuminate\Http\Resources\Json\JsonResource;

class DominosPizzaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        if ($this->status == OrderStatus::ARRIVED_PICK_UP ){
            $lat = $this->branch?->lat;
            $lng = $this->branch?->lng;
        }elseif ($this->status == OrderStatus::ARRIVED_TO_DROPOFF || $this->status == OrderStatus::DELIVERED){
            $lat = $this->lat;
            $lng = $this->lng;
        }else{
            $lat = @$this->OperatorDetail?->lat;
            $lng = @$this->OperatorDetail?->lng;
        }


        return [
            'parent_id' => $this->id,
            'driver_id' => $this->driver_id,
            'lat' => $lat,
            'lng' => $lng,
            'driver_status_id' => $this->OperatorDetail?->status,
            'driver_name' => $this->OperatorDetail?->operator?->full_name,
            'driver_phone' => $this->OperatorDetail?->operator?->phone,
            'orderId' => $this->id,
            'clientOrderId' => $this->client_order_id_string,
        ];
    }



}
