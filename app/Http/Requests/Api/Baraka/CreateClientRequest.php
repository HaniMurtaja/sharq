<?php

namespace App\Http\Requests\Api\Baraka;

use App\Rules\KSAPhoneRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateClientRequest extends FormRequest
{

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
                    'unique:users,phone',
                    new KSAPhoneRule()
                ],
                'password' => 'required|string|min:8|confirmed',
                'email' => 'required|string|email|max:255|unique:users,email',
                'profile_photo'=>'required|image|mimes:jpeg,png,jpg,gif|max:5000',
            ];

    }
}
