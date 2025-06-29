<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class WebHookResource extends JsonResource
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
            'id' => $this->id, ///
            'name' => $this->name,
            'type' => $this->type,
            'url' => $this->url,
            'format' => $this->format,
            'created_at' => $this->created_at->format('Y-m-d h:i a'),
        ];
    }
}
