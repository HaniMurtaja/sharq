<?php

namespace App\Http\Requests\Clients;

use App\Rules\KSAPhoneRule;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class BranchRequest extends FormRequest
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
           
            'branch_name' => ['required', 'string', 'min:3'],
            'driver_id' => ['required', 'exists:users,id'],
            'branch_d' => ['nullable', 'exists:branches,id'],
            
           
        ];
    }

    public function messages()
    {
        return [
            'driver_id.required' => 'The driver is required.',
        ];
    }
}
