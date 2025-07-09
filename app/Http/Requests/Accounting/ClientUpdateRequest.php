<?php

namespace App\Http\Requests\Accounting;

use Illuminate\Foundation\Http\FormRequest;

class ClientUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermissionTo('accounting_access');
    }

    public function rules(): array
    {
        return [
            'billing_emails' => 'nullable|array|max:10',
            'billing_emails.*' => 'email|max:255',
            'auto_generate_invoice' => 'boolean',
            'invoice_template_notes' => 'nullable|string|max:1000',
            'payment_terms' => 'nullable|string|max:500'
        ];
    }

    public function messages(): array
    {
        return [
            'billing_emails.*.email' => 'Each billing email must be a valid email address.',
            'billing_emails.max' => 'You can only add up to 10 billing emails.',
            'invoice_template_notes.max' => 'Invoice template notes cannot exceed 1000 characters.',
            'payment_terms.max' => 'Payment terms cannot exceed 500 characters.'
        ];
    }
}