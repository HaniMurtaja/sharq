<?php

namespace App\Http\Resources\Api;

use App\Enum\DriverStatus;
use App\Settings\GeneralSettings;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliverectOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $settings = new GeneralSettings();
        $eta = $settings->eta;
        $display_order_id = @$this->additional_details['deliveryLocations']['0']['channelOrderDisplayId'];
        return [


            'jobId' => $this->jop_id ?? 0,
            "canDeliver" => true,
            "pickupTimeETA" =>  Carbon::parse($this->create_at, 'UTC')->addMinutes((int)$eta['default_arrive_to_pickup_time'])->format('Y-m-d\TH:i:s.u\Z'),
            "externalJobId" =>  $this->pickup_id,
            "distance" => '0',
            "price" => [
                "price" => $this->value ? $this->value . '' : '0',
                "taxRate" => $this->service_fees ? $this->service_fees . '' : '0',
            ],



            'courier' => $this->driver ? [
                'id' => $this->driver?->driver?->id,
                'name' =>  $this->driver?->driver?->full_name,
                'phone' =>  $this->driver?->driver?->phone,
                'status' => DriverStatus::tryFrom($this->driver?->driver?->operator?->status)?->getLabel(),
            ] : NULL,




            "deliveryLocations" => [
                [
                  "deliveryId" => "$this->client_order_id_string",
                  "orderId" => $this->id,
                  "channelOrderDisplayId" => $display_order_id,
                  "deliveryTimeETA" => Carbon::parse($this->create_at, 'UTC')->addMinutes((int)($eta['default_arrive_to_pickup_time']+$eta['default_arrive_to_dropoff_time']))->format('Y-m-d\TH:i:s.u\Z'),
                  "deliveryRemarks" => ""
                ]
              ]


        ];
    }
}
