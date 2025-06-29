<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class OrderRateFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
                "pickup_lat" => ['required_without:pickup_id','numeric','between:-90,90'],
                "pickup_lng"=>['required_without:pickup_id','numeric','between:-180,180'],
                "pickup_id"=> ['required_without:pickup_lat,pickup_lng','integer'],
                "preparation_time"=> ['nullable','integer','min:0'],
                "lat"=> ['nullable','required_without:address,city','numeric'],
                "lng"=> ['nullable','required_without:address,city','numeric'],
                "address"=> ['nullable','required_without:lat,lng','string'],
                "city"=>['nullable','required_without:lat,lng','string']      
        ];
    }

    public function messages()
    {
        return [
            'pickup_lat.required_without' => 'Pickup latitude is required when pickup ID is not provided.',
            'pickup_lat.numeric' => 'Pickup latitude must be a numeric value.',
            'pickup_lat.between' => 'Pickup latitude must be between -90 and 90.',
            'pickup_lng.required_without' => 'Pickup longitude is required when pickup ID is not provided.',
            'pickup_lng.numeric' => 'Pickup longitude must be a numeric value.',
            'pickup_lng.between' => 'Pickup longitude must be between -180 and 180.',
            'pickup_id.required_without' => 'Pickup ID is required when pickup latitude and longitude are not provided.',
            'pickup_id.integer' => 'Pickup ID must be an integer.',
            'preparation_time.required' => 'Preparation time is required.',
            'preparation_time.integer' => 'Preparation time must be an integer.',
            'preparation_time.min' => 'Preparation time must be at least 0.',
            'lat.required_without' => 'Latitude is required when address or city are not provided.',
            'lat.numeric' => 'Latitude must be a numeric value.',
            'lng.required_without' => 'Longitude is required when address or city are not provided.',
            'lng.numeric' => 'Longitude must be a numeric value.',
            'address.required_without' => 'Address is required when latitude or longitude are not provided.',
            'address.string' => 'Address must be a string.',
            'city.required_without' => 'City is required when latitude or longitude are not provided.',
            'city.string' => 'City must be a string.',
        ];
    }
}
