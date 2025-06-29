<?php

namespace App\Http\Resources\Admin\Dispatcher;

use App\Enum\DriverStatus;
use App\Enum\OrderStatus;
use App\Models\OrderLog;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class FirbaseBranchesResource extends JsonResource
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
            'lat' => $this->lat,
            'lng' => $this->lng,
            'image_url' => $this->image_url,
            'orders_count' => $this->orders_count ?? 0,
            'client_id' => $this->client_id,
            'name' => $this->name,
            'created_at'  => Carbon::now('Asia/Riyadh'),
            'city_id' => $this->city_id,
            'country_id' => $this->city?->country_id,


        ];
    }
}
