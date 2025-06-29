<?php

namespace App\Http\Requests\Settings;

use App\Rules\KSAPhoneRule;
use Illuminate\Foundation\Http\FormRequest;

class AccountRequest extends FormRequest
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
        // dd(99);
        return [

            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['required',  'email'],
            'billing_vAT_no' =>  ['required', 'string'],
            'billing_name' =>  ['required', 'string'],
            'street_name'  =>  ['required', 'string'],
            'billing_bulding_no' =>  ['required', 'numeric'],
            'billing_district' =>  ['required', 'string'],
            'billing_city' =>  ['required', 'string'],
            'billing_email' => ['required',  'email'],

        ];
    }



    
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // Redirect back with errors and hash fragment
        $redirect = redirect(route('settings').'#account')
            ->withErrors($validator)
            ->withInput();

        throw new \Illuminate\Validation\ValidationException($validator, $redirect);
    }

   

}
