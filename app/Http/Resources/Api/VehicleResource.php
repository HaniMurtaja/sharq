<?php
    namespace App\Http\Resources\Api;

    use Illuminate\Http\Resources\Json\JsonResource;
    use Illuminate\Support\Facades\URL;

    class VehicleResource extends JsonResource
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
                'vehicle_image' => $this->getFirstMediaUrl('vehicle_image'),
                'id_card_image' => $this->getFirstMediaUrl('id_card_image'),
                'name' => $this->name,
                'type' => $this->type,
                'plate_number' => $this->plate_number,
                'vin_number' => $this->vin_number,
                'make' => $this->make,
                'model' => $this->model,
                'year' => $this->year,
                'color' => $this->color,
                'vehicle_milage' => $this->vehicle_milage,
                'last_service_milage' => $this->last_service_milage,
                'due_service_milage' => $this->due_service_milage,
                'service_milage_limit' => $this->service_milage_limit,
            ];
        }
    }
