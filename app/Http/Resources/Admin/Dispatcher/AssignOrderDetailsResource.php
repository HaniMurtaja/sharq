<?php

namespace App\Http\Resources\Admin\Dispatcher;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignOrderDetailsResource extends JsonResource
{
    public function toArray($request): array
    {


        return [
            'id' => $this->id,
        ];
    }
}

