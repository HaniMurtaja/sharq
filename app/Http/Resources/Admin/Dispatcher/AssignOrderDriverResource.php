<?php

namespace App\Http\Resources\Admin\Dispatcher;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignOrderDriverResource extends JsonResource
{
    public function toArray($request): array
    {

//        id	2046
//lat	"27.5009926"
//lng	"41.6998975"
//status	2
//distance	3.74
//profile_image	"https://alshrouqdelivery.b-cdn.net/storage/images/2046/999919517375780579495fce084d1f0ce5cda42ec78e948f6.png"
//full_name	"Mohamed Nasr Elsenousy"
//phone	"531432349"
//vehicle	null
//completed_jops	11
//tasks	2

        return [
            'id' => $this->operator_id,
            'lat' => @$this->lat,
            'lng' => @$this->lng,
            'status' => @$this->status,
            'distance' => @round($this->distance,3),
            'profile_image' => @$this->operator->image_url,
            'full_name' => @$this->operator->full_name,
            'phone' => @$this->operator->phone,
            'completed_jops' => @$this->completed_jobs(),
            'tasks' => @$this->tasks(),
            'orders'=>AssignOrderResource::collection($this->TodayOrdersDate)

        ];
    }
}

