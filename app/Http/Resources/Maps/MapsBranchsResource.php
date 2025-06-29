<?php

namespace App\Http\Resources\Maps;

use App\Http\Resources\Admin\Dispatcher\OrderHomeResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class MapsBranchsResource extends JsonResource
{
    public function toArray($request)
    {
        //dd( $this);
        return [
            'branch_name'=>$this->branch_name,
            'branch_phone'=>$this->branch_phone,
            'branch_lat'=>$this->branch_lat,
            'branch_lng'=>$this->branch_lng,
        ];
    }
}

