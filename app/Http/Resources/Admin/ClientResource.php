<?php

namespace App\Http\Resources\Admin;

use App\Enum\DriverStatus;
use App\Enum\OrderStatus;
use App\Models\OrderLog;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
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
        'full_name' => $this->full_name,   
        'total_balance' => $this->wallet?->balance ?? 0,
        'total_orders' => $this->orders_count,
        'total_branches' => $this->branches()?->count(),
        'country' => $this->client?->country?->name,
        'currency' => $this->client?->currency?->getLabel(),
        'client_partial_pay' => $this->client?->partial_pay,
        'client_default_preperation_time' => $this->client?->default_prepartion_time,
        'client_min_preperation_time' => $this->client?->min_prepartion_time,
        'client_client_group' => $this->client?->clienGroup?->name,
        'client_operator_group' => $this->client?->driverGroup?->name,
        'city' => $this->client?->city?->name ?? '-',
        'account_number' => $this->client?->account_number,
        'price_order' => $this->client?->price_order,
        'integration_token' => $this->integration_token,
        'is_active' => $this->is_active,

        ];
    }


}
