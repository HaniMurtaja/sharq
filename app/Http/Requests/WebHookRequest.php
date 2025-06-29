<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class WebHookRequest extends FormRequest
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
            
            'name_webhook' => 'required',
            'url' => 'required|url',
            'type' => 'required|in:order_created,order_updated,order_cancelled',
            'format' => 'nullable|in:form-data,JSON',
            'integration_company_id' => 'required|exists:integration_companies,id'
        ];
    }

    public function messages()
    {
        return [
           
        ];
    }

}
