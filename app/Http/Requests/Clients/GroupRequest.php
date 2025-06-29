<?php

namespace App\Http\Requests\Clients;

use App\Rules\KSAPhoneRule;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class GroupRequest extends FormRequest
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
        // dd(99);
        return [

            'group_name' => 'required|string|max:255',
            'calculation_method' => 'required|string|max:255',
            'default_delivery_fee' => 'required|numeric|min:0',
            'collection_amount' => 'required|numeric|min:0',
            'service_type' => 'required|string|max:255',
            'client_group_id' => 'nullable|exists:clients_groups,id'

        ];
    }

    public function messages()
    {
        return [
            'driver_id.required' => 'The driver is required.',
        ];
    }
}
