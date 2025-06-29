<?php

namespace App\Http\Requests\Settings;

use App\Rules\KSAPhoneRule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash; // Import Hash facade

class TaxesRequest extends FormRequest
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
           'income_tax' => 'required|numeric',
            'income_tax_start_year' => 'required|integer|min:1900|max:9999',
            'income_tax_start_month' => 'required|integer|min:1|max:12',
            'income_tax_start_day' => 'required|integer|min:1|max:31',
            'income_tax_end_year' => 'required|integer|min:1900|max:9999',
            'income_tax_end_month' => 'required|integer|min:1|max:12',
            'income_tax_end_day' => 'required|integer|min:1|max:31',

            'sales_tax' => 'required|numeric',
            'sales_tax_start_year' => 'required|integer|min:1900|max:9999',
            'sales_tax_start_month' => 'required|integer|min:1|max:12',
            'sales_tax_start_day' => 'required|integer|min:1|max:31',
            'sales_tax_end_year' => 'required|integer|min:1900|max:9999',
            'sales_tax_end_month' => 'required|integer|min:1|max:12',
            'sales_tax_end_day' => 'required|integer|min:1|max:31',
        ];

        return $rules;
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // Redirect back with errors and hash fragment
        $redirect = redirect(route('settings').'#taxes')
            ->withErrors($validator)
            ->withInput();

        throw new \Illuminate\Validation\ValidationException($validator, $redirect);
    }
}
