<?php

namespace App\Http\Resources\Maps;

use App\Http\Resources\Admin\Dispatcher\OrderHomeResource;
use App\Models\City;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class MapsResource extends JsonResource
{
    public function toArray($request)
    {
    //    dd( $this->first()->order_id);
        $order = Order::findOrFail($this->first()->order_id);
        $city = City::findOrFail($order->city);
        // dd($order, $city);
        return [
            'id' => $this->first()->driver_id,
            'full_name' => $this->first()->namedriver,
            'phone' => $this->first()->mobiledriver,
            'lat' => @$this->first()->operator_lat,
            'lng' => @$this->first()->operator_lng,
            'status' => @$this->first()->operator_status,
            'order_count' => $this->count(),
            'orders' => MapsOrdersResource::collection($this),
            'branch' => MapsBranchsResource::collection($this),
            'order_created_at' => $this->first()->created_at,
            'ingr_branch_id' => $this->first()->ingr_branch_id,
            'ingr_shop_id' => $this->first()->ingr_shop_id,
            'city_id' => $city?->id,
            'country_id' => $city?->country_id,
            'photo' => $this->first()->driver_photo,


        ];
    }
}

