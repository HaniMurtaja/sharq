<?php

namespace App\Http\Resources\Api;

use App\Enum\DriverStatus;
use Illuminate\Http\Resources\Json\JsonResource;

class WasftyOrderResource extends JsonResource
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
            'order_id' => $this->id, ///

            'customer_id' => $this->customer_id ?? '',
            'client_order_id' => $this->client_order_id_string ?? $this->client_order_id ?? $this->id,
            'customer_name' => $this->customer_name ?? '',
            'customer_phone' => $this->customer_phone ?? '',
            'pickup_lat' => $this->pickup_lat ?? '',
            'pickup_lng' => $this->pickup_lng ?? '',
            'lat' => $this->lat ?? '',
            'lng' => $this->lng ?? '',
            'city' => NULL,
            'shop' => $this->shop ?? $this->branchIntegration?->client ,
            'branch' => $this->branch ?? $this->branchIntegration,
            'branch_id' => $this->ingr_branch_id ?? $this->pickup_id,
            'branch_area' => $this->branch_area ?? '',
            'dropoff_area' => $this->dropoff_area ?? '',
            'dropoff_lat' => $this->dropoff_lat ?? '',
            'dropoff_lng' => $this->dropoff_lng ?? '',
            'tracking_code' => $this->tracking_code ?? '',
            'tracking_url' => $this->tracking_url ?? '',
            'expected_pickup' => $this->expected_pickup ?? '',
            'expected_delivery' => $this->expected_delivery ?? '',
            'at_pickup' => $this->at_pickup ?? '',
            'pickup' => $this->pickup ?? '',
            'at_dropoff_at' => $this->at_dropoff_at ?? '',
            'dropoff_at' => $this->dropoff_at ?? '',
            'fees' =>  $this->service_fees ?$this->service_fees .'': '0',  ////service_fees

            'distance' => $this->distance ?? '0',

            'status' => $this->status->value ?? 0,
            'status_id' => $this->status_id ?? '',
            'status_label' => $this->status ? $this->status->getLabel() : '',
            'value' => $this->value ?$this->value .'': '0', //// ,
            'cod' =>($this->value + $this->service_fees).'' , //// ,
            'payment_type_label' => $this->payment_type ? $this->payment_type->getLabel() : '',
            'payment_type' => $this->payment_type->value ?? 1, ////
            'currency' => $this->currency ?? '',
            'details' => $this->details ?? '',
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d g:i A') : '',

            'pickup_poa' => $this->pickup_poa ?? 0,
            'pickup_poa_qrcode' => $this->pickup_poa_qrcode ?? '',
            'dropoff_poa' => $this->dropoff_poa ?? 0,
            'dropoff_poa_qrcode' => $this->dropoff_poa_qrcode ?? '',
            'driver' => $this->driver ? [
                'id' => $this->driver?->driver?->id,
                'name' =>  $this->driver?->driver?->full_name,
                'phone' =>  $this->driver?->driver?->phone,
                'status' => DriverStatus::tryFrom($this->driver?->driver?->operator?->status)?->getLabel(),
            ] : NULL,
            'is_current' => $this->status ?->value ? in_array($this->status->value, ['1', '2', '13']) ? false : true : NULL,
            'has_otp' => $this->otp ?true : false,
            'invoice_url' => $this->full_invoice_url ?? '',
        ];
    }
}
