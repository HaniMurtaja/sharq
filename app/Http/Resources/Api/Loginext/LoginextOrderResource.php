<?php

namespace App\Http\Resources\Api\Loginext;

use App\Enum\DriverStatus;
use App\Enum\LoginextOrderStatus;
use App\Models\OrderLog;
use App\Settings\GeneralSettings;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginextOrderResource extends JsonResource
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
        $settings = new GeneralSettings();
        $eta = $settings->eta;

        // ✅ تحويل القيم إلى أرقام لحل مشكلة Carbon
        $pickupMinutes = (int) ($eta['default_arrive_to_pickup_time'] ?? 0);
        $dropoffMinutes = (int) ($eta['default_arrive_to_dropoff_time'] ?? 0);

        return [
            "order_id" => $this->id,
            "customer_id" => 1,
            "client_order_id" => $this->client_order_id_string ?? $this->client_order_id ?? $this->id,
            "customer_name" => $this->customer_name ?? '',
            "customer_phone" => "0" . $this->customer_phone,
            "city" => $this->cityData->name ?? "",
            "shop" => $this->shop->full_name ?? "",
            "branch" => $this->branch->name ?? "",
            "branch_id" => $this->ingr_branch_id ?? '',
            "branch_area" => $this->cityData->name ?? "",
            "dropoff_area" => $this->dropoff_area ?? '',
            "dropoff_lat" => $this->lat ?? '',
            "dropoff_lng" => $this->lng ?? '',
            "tracking_code" => $tracking_code,
            "tracking_url" => route('track_order', $tracking_code),
            "expected_pickup" => Carbon::parse($this->created_at)->addMinutes($pickupMinutes)->format('Y-m-d H:i:s'),
            "expected_delivery" => Carbon::parse($this->created_at)->addMinutes($pickupMinutes + $dropoffMinutes)->format('Y-m-d H:i:s'),
            "at_pickup_at" => $this->arrived_to_pickup_time ? Carbon::parse($this->arrived_to_pickup_time)->format('Y-m-d H:i:s') : null,
            "pickup_at" => $this->picked_up_time ? Carbon::parse($this->picked_up_time)->format('Y-m-d H:i:s') : null,
            "at_dropoff_at" => $this->arrived_to_dropoff_time ? Carbon::parse($this->arrived_to_dropoff_time)->format('Y-m-d H:i:s') : null,
            "dropoff_at" => $this->delivered_at ? Carbon::parse($this->delivered_at)->format('Y-m-d H:i:s') : null,
            "fees" => $this->service_fees ? (string) $this->service_fees : '0.00',
            "distance" => 4.4, // ثابت مؤقتًا
            "status" => LoginextOrderStatus::getLabelByStatus($this->status),
            "status_id" => $this->status ?? null,
            "value" => $this->value ?? null,
            "payment_type" => 'Paid',
            "details" => $this->additional_details['details'] ?? '',
            "currency" => 'SAR',
            "driver" => $this->DriverData2 ? [
                "id" => $this->DriverData2?->id,
                "name" => $this->DriverData2?->full_name,
                "phone" => "0" . $this->DriverData2?->phone,
                "photo" => $this->DriverData2->image ?? null,
                "status" => DriverStatus::tryFrom($this->DriverData2->operator?->status)?->getLabel(),
            ] : null,
            "created_at" => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
