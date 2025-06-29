<?php

namespace App\Http\Requests\Api\Foodics;

use App\Traits\HandleResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class FoodicsOrderRequest extends FormRequest
{
    use HandleResponse;
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
            'event' => 'required',
            'business.reference' => 'required|integer',
            'order.check_number' => 'required',
            'order.branch.id' => 'required',
            'order.branch.name' => 'required',
            'order.branch.latitude' => 'nullable',
            'order.branch.longitude' => 'nullable',
            'order.customer.name' => 'required',
            'order.customer.phone' => 'required',
            'order.customer_address.description' => 'required',
            'order.customer_address.latitude' => 'nullable',
            'order.customer_address.longitude' => 'nullable',
            'order.payments.payment_method.name' => 'nullable',
            'order.total_price' => 'required',
            'order.tags' => 'nullable',

        ];

    }


    protected function failedValidation(Validator $validator)
    {

        throw new HttpResponseException($this->send_response(TRUE, 200, 'success',$validator->errors()->first()));

    }

}
