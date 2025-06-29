<?php

namespace App\Http\Requests;

use App\Rules\KSAPhoneRule;
use Illuminate\Foundation\Http\FormRequest;

class ShiftRequest extends FormRequest
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
    public function rules() {
        return [
            'shift_id' => ['nullable', 'exists:shifts,id'],
            'shift_name' => ['required','string'],
            'shift_from' => ['required','string'],
            'shift_from_type' => ['required','string'],
            'shift_to' => ['required','string'],
            'shift_to_type' => ['required','string'],
        ];
    }

}
