<?php


namespace App\Http\Requests\Accounting;

use Illuminate\Foundation\Http\FormRequest;


class InvoiceGenerateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermissionTo('accounting_access');
    }

    public function rules(): array
    {
        return [
            'month' => 'required|date_format:Y-m|before_or_equal:' . now()->format('Y-m'),
            'client_id' => 'nullable|exists:users,id'
        ];
    }

    public function messages(): array
    {
        return [
            'month.required' => 'Please select a month for invoice generation.',
            'month.date_format' => 'Month must be in YYYY-MM format.',
            'month.before_or_equal' => 'Cannot generate invoices for future months.',
            'client_id.exists' => 'The selected client does not exist.'
        ];
    }
}