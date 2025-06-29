<?php

namespace App\Http\Resources\Api\americana;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => @$this->id,
            'first_name' => @$this->first_name,
            'last_name' => @$this->last_name,
            'email' => @$this->email,
            'created_at' => @$this->created_at,
            'jwt_token' => @$this->integration_token,
            'api_key' => @$this->integration_token,
            'photo' => @$this->Image,
            'country' => [
                'code' => "SA",
                'name' => @$this->client->country->name,
                'timezone' => "Asia/Riyadh",
                'ISD' => "+966",
                'currency' => "SAR",
            ],
        ];

    }
}
