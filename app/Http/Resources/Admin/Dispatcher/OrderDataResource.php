<?php

namespace App\Http\Resources\Admin\Dispatcher;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDataResource extends JsonResource
{

    public function toArray($request)
    {

        return [
            'id'                 => $this->id,
            'shop_name'          => $this->shop?->first_name ?? $this->branchIntegration?->client?->first_name,

            'branch_name'        => $this->branch?->name ?? $this->branchIntegration?->name,
            'branch_phone'       => $this->branch?->phone ?? $this->branchIntegration?->phone,
            'branch_area'        => $this->branch?->city?->name ?? $this->branchIntegration?->city?->name,
            'branch_lat'         => $this->branch?->lat,
            'branch_lng'         => $this->branch?->lng,
            'driver_name'        => $this->DriverData2?->full_name,
            'driver_phone'       => $this->DriverData2?->phone,
            'driver_photo'       => $this->DriverData2?->image,
            'order_address'      => $this->branch
                ? ($this->branch?->city?->name . ' ' . $this->branch?->street)
                : ($this->branchIntegration?->city?->name . ' ' . $this->branch?->street),
            'created_time'       => $this->created_at->format('Y-m-d h:i a'),
            'assign_date'        => (isset($this->driver_assigned_at) ? Carbon::parse($this->driver_assigned_at)->format('Y-m-d h:i a') : null),
            'accept_date'        => (isset($this->driver_accepted_time) ? Carbon::parse($this->driver_accepted_time)->format('Y-m-d h:i a') : null),
            'arrive_branch_date' => (isset($this->arrived_to_pickup_time) ? Carbon::parse($this->arrived_to_pickup_time)->format('Y-m-d h:i a') : null),
            'recive_date'        => (isset($this->picked_up_time) ? Carbon::parse($this->picked_up_time)->format('Y-m-d h:i a') : null),
            'arrive_client_date' => (isset($this->arrived_to_dropoff_time) ? Carbon::parse($this->arrived_to_dropoff_time)->format('Y-m-d h:i a') : null),
            'delivery_date'      => (isset($this->delivered_at) ? Carbon::parse($this->delivered_at)->format('Y-m-d h:i a') : null),
            'created_date'       => $this->created_at?->format('Y-m-d h:i a'),
            'status_label'       => $this->status->getLabel(),
            'payment_label'      => $this->payment_type?->getLabel() ?? '---',
            'customer_name'      => $this->customer_name,
            'customer_phone'     => $this->customer_phone,
            'delivered_in'       => $this->delivered_in,
            'distance'           => $this->distance,
            'value'              => $this->value ?? 0,
            'service_fees'       => $this->service_fees,
            'preparation_time'   => $this->preparation_time,
            'order_id'           => $this->order_number,
            'cancel_reason' => $this->cancel_reason?->name ?? '---',
            'cancel_time' => $this->cancel_reason?->name ?? '---',
        ];
    }
}
