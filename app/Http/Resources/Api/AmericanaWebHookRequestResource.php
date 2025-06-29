<?php
namespace App\Http\Resources\Api;

use App\Enum\AmericanaOrderStatus;
use App\Enum\DriverStatus;
use Illuminate\Http\Resources\Json\JsonResource;

class AmericanaWebHookRequestResource extends JsonResource
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
            'order_id'           => $this->id, ///

            'customer_id'        => $this->customer_id ?? '',
            'client_order_id'    => $this->client_order_id_string ?? $this->client_order_id ?? $this->id,
            'customer_name'      => $this->customer_name ?? '',
            'customer_phone'     => $this->customer_phone ?? '',
            'pickup_lat'         => $this->pickup_lat ?? '',
            'pickup_lng'         => $this->pickup_lng ?? '',
            'lat'                => $this->lat ?? '',
            'lng'                => $this->lng ?? '',
            'city'               => null,
            'shop'               => $this->shop?->full_name ?? $this->branchIntegration?->client?->full_name,
            'branch'             => $this->branch?->name ?? $this->branchIntegration?->name,
            'branch_id'          => $this->ingr_branch_id ?? $this->pickup_id,
            'branch_area'        => $this->branch_area ?? '',
            'dropoff_area'       => $this->dropoff_area ?? '',
            'dropoff_lat'        => $this->dropoff_lat ?? '',
            'dropoff_lng'        => $this->dropoff_lng ?? '',
            'tracking_code'      => $this->tracking_code ?? '',
            'tracking_url'       => $this->tracking_url ?? '',
            'expected_pickup'    => $this->expected_pickup ?? '',
            'expected_delivery'  => $this->expected_delivery ?? '',
            'at_pickup'          => $this->at_pickup ?? '',
            'pickup'             => $this->pickup ?? '',
            'at_dropoff_at'      => $this->at_dropoff_at ?? '',
            'dropoff_at'         => $this->dropoff_at ?? '',
            'fees'               => $this->service_fees ? $this->service_fees . '' : '0', ////service_fees

            'distance'           => $this->distance ?? '0',

            'status'             => AmericanaOrderStatus::mainStatus($this->status->value),
            'status_id'          => AmericanaOrderStatus::mainStatus($this->status->value),
            'status_label'       => $this->status ? $this->status->getLabel() : '',
            'value'              => $this->value ? $this->value . '' : '0',    //// ,
            'cod'                => ($this->value + $this->service_fees) . '', //// ,
            'payment_type_label' => $this->payment_type ? $this->payment_type->getLabel() : '',
            'payment_type'       => $this->payment_type->value ?? 1, ////
            'currency'           => $this->currency ?? '',
            'details'            => $this->details ?? '',
            'created_at'         => $this->created_at ? $this->created_at->format('Y-m-d g:i A') : '',
            'customer_address'   => $this->customer_address,
            'pickup_poa'         => $this->pickup_poa ?? 0,
            'pickup_poa_qrcode'  => $this->pickup_poa_qrcode ?? '',
            'dropoff_poa'        => $this->dropoff_poa ?? 0,
            'dropoff_poa_qrcode' => $this->dropoff_poa_qrcode ?? '',
            'driver'             => $this->DriverData2 ? [
                'id'       => $this->DriverData2?->id,
                'name'     => $this->DriverData2?->full_name,
                'phone'    => $this->DriverData2?->phone,
                'location' => [
                    'lat' => $this->DriverData2?->operator?->lat ?? '',
                    'lng' => $this->DriverData2?->operator?->lng ?? '',
                ],
                'status'   => DriverStatus::tryFrom($this->DriverData2?->operator?->status)?->getLabel(),
            ] : null,
            'is_current'         => $this->status?->value ? in_array($this->status->value, ['1', '2', '13']) ? false : true : null,
            'has_otp'            => $this->otp ? true : false,
        ];
    }
}
