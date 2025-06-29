<?php

namespace App\Http\Resources\Api\Lyve;

use App\Enum\DriverStatus;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class   OrderResource extends JsonResource
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
            "order_id"=> "$this->id",
            "timestamp"=> Carbon::parse($this->created_at)->timestamp,
        ];
    }
}
