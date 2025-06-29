<?php

namespace App\Http\Requests;

use App\Rules\KSAPhoneRule;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class AreaRequest extends FormRequest
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
           
            'area_name' => ['required', 'string', 'min:3'], 
            'city_id'   => ['required', 'exists:cities,id'],
            'area_id' => 'nullable|exists:areas,id',  
        ];
    }

    public function messages()
    {
        return [
            'city_id.required' => 'The city is required.',
        ];
    }

    
}
