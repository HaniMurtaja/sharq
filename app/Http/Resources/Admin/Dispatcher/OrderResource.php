<?php

namespace App\Http\Resources\Admin\Dispatcher;

use App\Enum\DriverStatus;
use App\Enum\OrderStatus;
use App\Models\OrderLog;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'id' => $this->id,
            'shop_name' => $this->shop?->full_name,
            'shop_profile' =>  $this->shop?->image,
            'branch_name' => $this->branch?->name,
            'branch_lat' => $this->branch?->lat,
            'branch_id' => $this->branch?->id,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'branch_lng' => $this->branch?->lng,
            'ingr_shop_id' => $this->ingr_shop_id,
            'branch_phone' => $this->branch?->phone ?? $this->branchIntegration?->phone,
            'branch_area' => $this->branch?->area?->name ?? $this->branchIntegration?->area?->name,
            'driver_name' => $this->DriverData2?->full_name,
            'driver_phone' => $this->DriverData2?->phone,
            'driver_photo' =>  $this->DriverData2?->image,
            'order_address' => $this->branch
                ? $this->branch?->city?->name . ' ' . $this->branch?->street
                : $this->branchIntegration?->city?->name . ' ' . $this->branch?->street,
            'created_time' => $this->created_at->format('h:i a'),
            'assign_date' => ($this->driver_assigned_at != null) ?$this->driver_assigned_at:null,
            'accept_date' => ($this->driver_accepted_time != null) ?$this->driver_accepted_time:null,
            'arrive_branch_date' => ($this->arrived_to_pickup_time != null) ?$this->arrived_to_pickup_time:null,
            'recive_date' => ($this->picked_up_time != null) ?$this->picked_up_time:null,
            'arrive_client_date' => ($this->arrived_to_dropoff_time != null) ?$this->arrived_to_dropoff_time:null,
            'delivery_date' => ($this->delivered_at != null) ?$this->delivered_at:null,
            //we ill-handle this late//
            'cancelled_date' => null,
            //we ill-handle this late//
            'created_date' => $this->created_at->format('Y-m-d h:i a'),
            'status_label' => $this->status?->getLabel(),
            'status_value' => $this->status?->value,
            'payment_type_label' => $this->payment_type ? $this->payment_type->getLabel() : '---',
            'vehicle_type' => $this->vehicle?->type,
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'delivered_in' => $this->delivered_in,
            'distance' => $this->distance,
            'value' => $this->value,
            'city' => $this->city,
            'customer_address' => $this->customer_address,
            'service_fees' => $this->service_fees,
            'preparation_time' => $this->preparation_time,
            'order_id' => $this->client_order_id_string ?? $this->client_order_id ?? $this->id,
            'order_label'=>OrderStatus::GetStatusLabel($this->status)

        ];
    }


}
