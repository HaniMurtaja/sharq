<?php

namespace App\Http\Resources\Maps;

use App\Http\Resources\Admin\Dispatcher\OrderHomeResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class MapsOrdersResource extends JsonResource
{
    public function toArray($request)
    {
        //dd( $this);
        return [
                'id' => $this->order_id,
                'finallat' => $this->order_lat,
                'finallng' => $this->order_lng,


        ];
    }
}

