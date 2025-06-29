<?php

namespace App\Http\Resources\Maps;

use App\Enum\DriverStatus;
use App\Models\Order;
use App\Settings\GeneralSettings;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
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
            'id'=>$this->id,
            'lat'=>$this->lat,
            'lng'=>$this->lng,
            'name'=>@$this->name_branch,
            'image_url'=>@$this->ImageUrl,
            'orders_count'=>@$this->OrdersCount,

        ];
    }
}
