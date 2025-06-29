<?php

namespace App\Http\Requests\Api\FoodicsOnline;

use Illuminate\Foundation\Http\FormRequest;

class AddWebhookRequest extends FormRequest
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
                'name' => 'required',
                'url' => 'required|url',
                'type' => 'required|in:order_created,order_updated,order_cancelled',
            ];

    }
}
