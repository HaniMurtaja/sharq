<?php

namespace App\Http\Requests;

use App\Rules\KSAPhoneRule;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class CityRequest extends FormRequest
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
    public function rules()
    {
        return [

            'city_name' => ['required', 'string', 'min:3'],
            'country_id'   => ['required', 'exists:countries,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
            'auto_dispatch' => 'nullable|boolean',

        ];
    }

    public function messages()
    {
        return [
            'country_id.required' => 'The country is required.',
            'lat.required' => 'The latitude is required.',
            'lat.numeric' => 'The latitude must be a number.',

            'lng.required' => 'The longitude is required.',
            'lng.numeric' => 'The longitude must be a number.',
        ];
    }
}
