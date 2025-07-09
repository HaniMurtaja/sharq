<?php


namespace App\Http\Requests\Accounting;

use Illuminate\Foundation\Http\FormRequest;


class MarkAsPaidRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermissionTo('accounting_access');
    }

    public function rules(): array
    {
        return [
            'payment_method' => 'required|in:bank_transfer,cash,tap_gateway,other',
            'payment_date' => 'required|date|before_or_equal:today',
            'amount_paid' => 'required|numeric|min:0.01',
            'transaction_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000'
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.required' => 'Please select a payment method.',
            'payment_method.in' => 'Invalid payment method selected.',
            'payment_date.required' => 'Please provide the payment date.',
            'payment_date.before_or_equal' => 'Payment date cannot be in the future.',
            'amount_paid.required' => 'Please enter the amount paid.',
            'amount_paid.min' => 'Amount paid must be greater than 0.',
            'transaction_reference.max' => 'Transaction reference cannot exceed 255 characters.',
            'notes.max' => 'Notes cannot exceed 1000 characters.'
        ];
    }
}