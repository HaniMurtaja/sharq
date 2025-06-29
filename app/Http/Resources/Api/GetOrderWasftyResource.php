<?php

namespace App\Http\Resources\Api;

use App\Enum\DriverStatus;
use App\Enum\OrderStatus;
use App\Models\OrderLog;
use App\Settings\GeneralSettings;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class GetOrderWasftyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $tracking_code = $this->id.$this->ingr_branch_id.$this->ingr_shop_id;
        $settings = new GeneralSettings();
        $eta = $settings->eta;
        $OrderLog = OrderLog::where('order_id', $this->id)->
        whereIn('status', [OrderStatus::PICKED_UP->value, OrderStatus::ARRIVED_TO_DROPOFF->value])->count();
        //ToDo: check first if status is 10 return ( cancel or return)
        return [
            'order_id' => $this->id, ///
            'customer_id' => $this->customer_id ?? 1,
            'client_order_id' => $this->client_order_id_string ?? $this->client_order_id ?? $this->id,
            'customer_name' => $this->customer_name ?? '',
            'customer_phone' => $this->customer_phone ?? '',
            'city' => $this->cityData->name ?? '',
            'shop' => $this->shop?->first_name ?? $this->branchIntegration?->client->full_name ?? '',
            'branch' => $this->branch->name ?? $this->branchIntegration->name ?? '',
            'branch_id' => $this->ingr_branch_id ?? $this->pickup_id,
            'branch_area' => $this->branch_area ?? '',
            'dropoff_area' => $this->dropoff_area ?? '',
            'dropoff_lat' => $this->lat ?? '',
            'dropoff_lng' => $this->lng ?? '',
            'tracking_code' => $tracking_code,
            "tracking_url" => route('track_order', $tracking_code),
            "expected_pickup" => Carbon::parse($this->created_at)->addMinutes(10)->format('Y-m-d H:i:s'),
            "expected_delivery" => Carbon::parse($this->created_at)->addMinutes((20))->format('Y-m-d H:i:s'),
            "at_pickup" => (isset($this->arrived_to_pickup_time)) ?Carbon::parse($this->arrived_to_pickup_time)->format('Y-m-d H:i:s'):null,
            "pickup_at" => (isset($this->picked_up_time)) ?Carbon::parse($this->picked_up_time)->format('Y-m-d H:i:s'):null,
            "at_dropoff_at" => (isset($this->arrived_to_dropoff_time)) ?Carbon::parse($this->arrived_to_dropoff_time)->format('Y-m-d H:i:s'):null,
            "dropoff_at" => (isset($this->delivered_at)) ?Carbon::parse($this->delivered_at)->format('Y-m-d H:i:s'):null,
            'fees' =>  $this->service_fees ? $this->service_fees . '' : '0',  ////service_fees
            'distance' => $this->distance ?? '0',
            'status_id' => ($OrderLog && $this->status->value == 10) ? 22 : $this->status->value,
            'cancel_reason' => @$this->cancel_reason->name,

            'status' =>($OrderLog && $this->status->value == 10) ? "Return":$this->status->getLabel() ,
            'value' => $this->value ? $this->value . '' : '0', //// ,
            'payment_type' => $this->payment_type->value ?? 1, ////
            'currency' => $this->currency ?? 'SAR',

            'details' => $this->details ?? '',
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d g:i A') : '',
            'pickup_poa' => $this->pickup_poa ?? 0,
            'pickup_poa_qrcode' => $this->pickup_poa_qrcode ?? '',
            'dropoff_poa' => $this->dropoff_poa ?? 0,
            'dropoff_poa_qrcode' => $this->dropoff_poa_qrcode ?? '',
            'pickup_lat' => $this->pickup_lat ?? '',
            'pickup_lng' => $this->pickup_lng ?? '',
            'lat' => $this->lat ?? '',
            'lng' => $this->lng ?? '',
            'driver' => $this->driver ? [
                'id' => $this->driver?->driver?->id,
                'name' =>  $this->driver?->driver?->full_name,
                'phone' =>  $this->driver?->driver?->phone,
                'status' => DriverStatus::tryFrom($this->driver?->driver?->operator?->status)?->getLabel(),
            ] : NULL,
            'is_current' => $this->status?->value ? in_array($this->status->value, ['1', '2', '13']) ? false : true : NULL,
            'has_otp' => $this->otp ? true : false,



        ];
    }
}
