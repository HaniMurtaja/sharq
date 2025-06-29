<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class OperatorRequest extends FormRequest
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
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'phone' => [
                    'required',
                    'regex:/^\+\d{1,4}\d{6,14}$/',
                ],
                'password' => 'required|string|min:8', 
                'group_id' => 'nullable|integer|exists:groups,id', // Assuming group_id references the id in a 'groups' table
                'shift_id' => 'nullable|integer|exists:shifts,id', // Assuming shift_id references the id in a 'shifts' table
            ];
       
    }
}
