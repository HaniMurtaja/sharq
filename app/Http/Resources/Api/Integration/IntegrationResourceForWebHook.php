<?php
namespace App\Http\Resources\Api\Integration;

use App\Enum\DriverStatus;
use Illuminate\Http\Resources\Json\JsonResource;

class IntegrationResourceForWebHook extends JsonResource
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
            'client_order_id'    => $this->client_order_id_string ?? $this->client_order_id ?? $this->id,
            'customer_name'      => $this->customer_name ?? '',
            'customer_phone'     => $this->customer_phone ?? '',
            'customer_lat'       => $this->lat ?? '',
            'customer_lng'       => $this->lng ?? '',
            'customer_address'   => $this->address,
            'branch_name'        => $this->branchIntegration->name,
            'branch_id'          => $this->pickup_id,
            'fees'               => $this->service_fees ? $this->service_fees . '' : '0',
            'distance'           => $this->distance ?? '0',
            'status_id'          => 3,
            'status_label'       => 'The order has been accepted',
            'value'              => $this->value ? $this->value . '' : '0',
            'cod'                => ($this->value + $this->service_fees) . '',
            'payment_type_label' => $this->payment_type ? $this->payment_type->getLabel() : '',
            'payment_type'       => $this->payment_type->value ?? 1,
            'details'            => $this->details ?? '',
            'created_at'         => $this->created_at->format('Y-m-d g:i A'),
            'driver'             => $this->DriverData2 ? [
                'id'        => $this->DriverData2?->id,
                'name'      => $this->DriverData2?->full_name,
                'phone'     => $this->DriverData2?->phone,
                'latitude'  => $this->OperatorDetail?->lat,
                'longitude' => $this->OperatorDetail?->lng,
                'status'    => DriverStatus::tryFrom($this->DriverData2?->operator?->status)?->getLabel(),
            ] : null,
            'has_otp'            => $this->otp ? true : false,
            'invoice_url'        => $this->full_invoice_url ?? '',
        ];
    }
}
