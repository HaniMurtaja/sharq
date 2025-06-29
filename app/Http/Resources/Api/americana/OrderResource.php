<?php
namespace App\Http\Resources\Api\americana;

use App\Settings\GeneralSettings;
use Carbon\Carbon;
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

        $tracking_code = $this->id . $this->ingr_branch_id . $this->ingr_shop_id;
        $settings      = new GeneralSettings();
        $eta           = $settings->eta;
        return [
            'order_id'           => $this->id, ///
            'customer_id'        => $this->customer_id ?? 1,
            'client_order_id'    => $this->client_order_id_string ?? $this->client_order_id ?? $this->id,
            'customer_name'      => $this->customer_name ?? '',
            'customer_phone'     => $this->customer_phone ?? '',
            'city'               => $this->cityData->name ?? "",
            'shop'               => $this->ShopDetail->name ?? "",
            'branch'             => $this->branch->name ?? "",
            'branch_id'          => $this->ingr_branch_id ?? $this->pickup_id,
            'branch_area'        => $this->branch->city->name ?? '',
            'dropoff_area'       => $this->dropoff_area ?? '',
            'dropoff_lat'        => $this->lat ?? '',
            'dropoff_lng'        => $this->lng ?? '',
            'tracking_code'      => $tracking_code,
            "tracking_url"       => route('track_order', $tracking_code),
            "expected_pickup"    => Carbon::parse($this->created_at)->addMinutes((int) $eta['default_arrive_to_pickup_time'])->format('Y-m-d H:i:s'),
            "expected_delivery"  => Carbon::parse($this->created_at)->addMinutes((int) ($eta['default_arrive_to_pickup_time'] + $eta['default_arrive_to_dropoff_time']))
                ->format('Y-m-d H:i:s'),
            'at_pickup'          => $this->at_pickup ?? null,
            'pickup'             => $this->pickup ?? null,
            'at_dropoff_at'      => $this->at_dropoff_at ?? null,
            'dropoff_at'         => $this->dropoff_at ?? null,
            'fees'               => $this->service_fees ? $this->service_fees . '' : '0', ////service_fees
            'distance'           => $this->distance ?? 0,
            'status'             => $this->status ? $this->status->getLabel() : '',
            'status_id'          => $this->status ?? 3,
            'value'              => $this->value ? $this->value . '' : '0', //// ,
            'payment_type'       => $this->payment_type ? $this->payment_type->getLabel() : '',
            'currency'           => 'SAR',
            'details'            => $this->details ?? '',
            'created_at'         => $this->created_at ? $this->created_at->format('Y-m-d g:i A') : '',
            'pickup_poa'         => $this->pickup_poa ?? "null",
            'pickup_poa_qrcode'  => $this->pickup_poa_qrcode ?? "",
            'dropoff_poa'        => $this->dropoff_poa ?? "",
            'dropoff_poa_qrcode' => $this->dropoff_poa_qrcode ?? "",
            'driver'             => $this->DriverData2 ? [
                'id'        => $this->DriverData2?->id,
                'name'      => $this->DriverData2?->full_name,
                'phone'     => $this->DriverData2?->phone,
                'rate'      => 4,
                'latitude'  => $this->OperatorDetail?->lat,
                'longitude' => $this->OperatorDetail?->lng,
                'location'  => [
                    'lat' => $this->OperatorDetail?->lat,
                    'lng' => $this->OperatorDetail?->lng,
                ],
            ] : null,
        ];
    }
}
