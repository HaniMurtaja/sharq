<?php


namespace App\Http\Requests\Accounting;

use Illuminate\Foundation\Http\FormRequest;


class SettingsUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermissionTo('accounting_access');
    }

    public function rules(): array
    {
        return [
            'company_name' => 'required|string|max:255',
            'tax_id' => 'required|string|max:100',
            'commercial_registration' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'bank_account' => 'nullable|string|max:100',
            'iban' => 'nullable|string|max:50',
            'payment_due_days' => 'required|integer|min:1|max:90',
            'additional_fields' => 'nullable|array'
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required' => 'Company name is required.',
            'tax_id.required' => 'Tax ID is required for ZATCA compliance.',
            'payment_due_days.required' => 'Payment due days is required.',
            'payment_due_days.min' => 'Payment due days must be at least 1 day.',
            'payment_due_days.max' => 'Payment due days cannot exceed 90 days.',
            'email.email' => 'Please enter a valid email address.',
            'iban.max' => 'IBAN cannot exceed 50 characters.',
            'bank_account.max' => 'Bank account cannot exceed 100 characters.'
        ];
    }
}