<?php

namespace App\Http\Resources\Admin\Dispatcher;

use App\Enum\DriverStatus;
use App\Enum\OrderStatus;
use App\Models\OrderLog;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderHomeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        if($this->status?->value == 2 || $this->status?->value == 16 || $this->status?->value == 17){
        $finallat = $this->branch?->lat;
        $finallng = $this->branch?->lng;
        }else{
            $finallat =  $this->lat;
            $finallng =  $this->lng;
        }
        return [
            'id' => $this->id,
            'finallat' => $finallat,
            'finallng' => $finallng,
        ];
    }

    private function getOrderLogDate($orderId, $status)
    {
        return OrderLog::where('order_id', $orderId)
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->first()?->created_at->format('Y-m-d h:i a');
    }
}
