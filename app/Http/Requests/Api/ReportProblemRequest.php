<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ReportProblemRequest extends FormRequest
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
            
            'description' => 'nullable|string',
            'order_id' => ['required', 'exists:orders,id'],
            'driver_id'=>['required', 'exists:users,id'],
            'reason' => 'required|string',
        ];
    }

    public function messages()
    {
        return [];
    }
}
