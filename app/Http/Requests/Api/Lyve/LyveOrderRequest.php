<?php

namespace App\Http\Requests\Api\Lyve;

use Illuminate\Foundation\Http\FormRequest;

class LyveOrderRequest extends FormRequest
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
            'client_order_id' => 'required|string',
            'order_number' => 'nullable|string',
            'sender.name' => 'nullable|string',
            'sender.phone_number' => 'nullable|string',
            'sender.location.address' => 'nullable|string',
            'sender.location.latitude' => 'nullable|numeric',
            'sender.location.longitude' => 'nullable|numeric',
            'sender.notes' => 'nullable|string',
            'recipient.name' => 'required|string',
            'recipient.phone_number' => 'required|string',
            'recipient.location.address' => 'required|string',
            'recipient.location.latitude' => 'required|numeric',
            'recipient.location.longitude' => 'required|numeric',
            'recipient.notes' => 'nullable|string',
            'amount' => 'required|numeric',
            'payment_method' => 'required|string',
            'callback_token' => 'required|string',
        ];

    }
}
