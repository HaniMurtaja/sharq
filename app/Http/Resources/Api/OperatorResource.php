<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;
use App\Enum\DriverStatus;

class OperatorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        // dd(($this->operator?->status));
        $status = 4;  // Default status
        $status_label = "Offline";  // Default label

        if ($this->operator) {
            // Check if status is an object (like an enum) or an integer
            if (is_object($this->operator->status)) {
                // If it's an object, safely get the value and label
                $status = (int) $this->operator->status->value;
                $status_label = $this->operator->status->getLabel();
            } else {
                // If it's an integer, use it directly
                $status = (int) $this->operator->status;
                $status_label = DriverStatus::tryFrom($status)?->getLabel() ?? 'Unknown';
            }

            // Handle the case where status is 0 (e.g., "Inactive" or "Unavailable")
            if ($status === 0) {
                $status = 4;  // Default status
                $status_label = "Offline";
            }
        }






        return [
            'id' => $this->id,
            'first_name' => $this->first_name ?? '',
            'last_name' => $this->last_name ?? '',
            'email' => $this->email ?? '',
            'phone' => $this->phone ?? '',
            'birth_date' => $this->operator ? $this->operator->birth_date : '',
            'emergency_contact_name' => $this->operator ? $this->operator->emergency_contact_name : '',
            'emergency_contact_phone' => $this->operator ? $this->operator->emergency_contact_phone : '',
            'social_id_no' => $this->operator ? $this->operator->social_id_no : '',
            'iban' => $this->operator ? $this->operator->iban : '',
            'lat' => $this->operator ? $this->operator->lat : '',
            'lng' => $this->operator ? $this->operator->lng : '',
            'status' => $status,
            'order_value' => $this->operator ? $this->operator->order_value . '' : '',
            'status_label' => $status_label,
            'token' => $this->access_token ? $this->access_token : '',
            'id_card_image_front' => $this->operator?->id_card_image_front,
            'id_card_image_back' => $this->operator?->id_card_image_back,

            'url_license_front_image' => $this->operator?->license_front_image ,
            'url_license_back_image' => $this->operator?->license_back_image ,

        ];
    }
}
