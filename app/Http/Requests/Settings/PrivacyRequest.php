<?php

namespace App\Http\Requests\Settings;

use App\Rules\KSAPhoneRule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash; // Import Hash facade

class PrivacyRequest extends FormRequest
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
        $rules = [
            'current_password' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, $this->user()->password)) {
                        $fail('The current password is incorrect.');
                    }
                },
            ],
            'new_password' => [
                'nullable',
                Password::min(6)
                   
            ],
            'password_confirmation' => [
                'nullable',
            ],
        ];

        if ($this->filled('new_password')) {
            $rules['password_confirmation'][] = 'required';
            $rules['password_confirmation'][] = 'same:new_password';
        }

        return $rules;
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // Redirect back with errors and hash fragment
        $redirect = redirect(route('settings').'#privacy')
            ->withErrors($validator)
            ->withInput();

        throw new \Illuminate\Validation\ValidationException($validator, $redirect);
    }
}
