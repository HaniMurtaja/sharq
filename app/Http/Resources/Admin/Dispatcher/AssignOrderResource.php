<?php

namespace App\Http\Resources\Admin\Dispatcher;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignOrderResource extends JsonResource
{
    public function toArray($request): array
    {

//        order_number	"965371"
//shop_name	"Mcdonald's "
//branch_name	"106 Sedyan HAS"
//status	"Order delivered"

        //         'shop_name' => $order->shop?->full_name,
//                            'branch_name' => $order->branch?->name,
        return [
            'order_number' => $this->order_number,
            'shop_name' => @$this->shop?->full_name,
            'branch_name' => @$this->branch?->name,
            'status' => @$this->status->getLabel(),

        ];
    }
}

