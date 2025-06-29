<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "operator_id" => $this->operator_id ? $this->operator_id : null ,
            "balance" => $this->balance.'',
            "currency" => $this->currency ?? "AED"
        ];
    }
}
