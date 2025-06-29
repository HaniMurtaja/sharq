<?php

namespace App\Http\Requests\Clients;

use App\Rules\KSAPhoneRule;
use Illuminate\Foundation\Http\FormRequest;

class ZoneRequest extends FormRequest
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
        // dd(request()->all());
        return [
            'zone_name' => ['required', 'string'],
            'city.*' => ['nullable', 'exists:cities,id'],
            'area.*' => ['nullable', 'exists:areas,id'],
            'zone_id' => ['nullable', 'exists:zones,id'],

        ];
    }

    public function messages()
    {
        return [
            'zone_name.required' => 'The zone name is required.',
        ];
    }
}
