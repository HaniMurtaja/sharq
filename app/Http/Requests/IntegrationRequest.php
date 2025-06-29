<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class IntegrationRequest extends FormRequest
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
        return [
            
            'name' => 'required',
            'has_cancel_reason' => 'nullable|in:0,1',
            // 'url' => 'required|url',
            // 'type' => 'required|in:order_created,order_updated,order_cancelled',
            'client_type' => 'required|in:0,1',
        ];
    }

    public function messages()
    {
        return [
           
        ];
    }

}
