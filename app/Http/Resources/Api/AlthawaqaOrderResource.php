<?php

namespace App\Http\Resources\Api;

use App\Enum\DriverStatus;
use App\Settings\GeneralSettings;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class AlthawaqaOrderResource extends JsonResource
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
        return [
            'order_id' => $this->id, ///

            'customer_id' => $this->customer_id ?? 1 ,
            'client_order_id' =>  (string) ($this->client_order_id_string ?? $this->client_order_id ?? $this->id),
            'customer_name' => $this->customer_name ?? '',
            'customer_phone' => $this->customer_phone ?? '',

            'city' => '',
            'shop' => $this->shop
                ? $this->shop->first_name .' '. $this->shop->last_name
                : ($this->branchIntegration?->client?->name ?? ''),

            'branch' => $this->branch
                ? $this->branch->name
                : ($this->branchIntegration?->client?->name ?? ''),

            'branch_id' => (int) ($this->ingr_branch_id ?? $this->pickup_id),
            'branch_area' => $this->branch_area ?? '',
            'dropoff_area' => $this->dropoff_area ?? '',
            'dropoff_lat' => $this->lat ?? '',
            'dropoff_lng' => $this->lng ?? '',
            'tracking_code' => $this->tracking_code ?? '',
            'tracking_url' => $this->tracking_url ?? '',
            'expected_pickup' =>  Carbon::now('Asia/Riyadh')->addMinutes(10),
            'expected_delivery' =>  Carbon::now('Asia/Riyadh')->addMinutes(20),
            'at_pickup_at' => $this->at_pickup ?? null,
            'pickup_at' => $this->pickup ?? null,
            'at_dropoff_at' => $this->at_dropoff_at ?? null,
            'dropoff_at' => $this->dropoff_at ?? null,
            'fees' =>  $this->service_fees ? (double) $this->service_fees : 0.0,
            'value' =>  $this->value ? (double) $this->value : 0.0,
            'distance' => $this->distance ?? 0,
            'status' => $this->status ? $this->status->getLabel() : '',
            'status_id' =>$this->status->value ?? 1,
            'payment_type' => $this->payment_type->value ? $this->payment_type->getLabel() : '',
            'details' => $this->details ?? '',
            'currency' => $this->currency ?? '',
            'driver' => $this->driver ? [
                'id' => $this->driver?->driver?->id,
                'name' =>  $this->driver?->driver?->full_name,
                'phone' =>  $this->driver?->driver?->phone,
                'status' => DriverStatus::tryFrom($this->driver?->driver?->operator?->status)?->getLabel(),
            ] : NULL,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : '',

        ];
    }
}
