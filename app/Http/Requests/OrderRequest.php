<?php

namespace App\Http\Requests;

use App\Rules\KSAPhoneRule;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
    public $current_step = 1;
    public function rules()
    {



        $validates = [

            'customer_phone' => [
                'required',
                new KSAPhoneRule()
            ],
            'customer_name' => 'nullable|string',
            'lat_order_hidden' => 'nullable',
            'lng_order_hidden' => 'nullable',
            'customer_address' => 'nullable',
            'branch_id' => [
                Rule::requiredIf(\Auth::user()->user_role->value == 2),
                'exists:client_branches,id'
            ],
//            'client_id' => [
//                Rule::requiredIf(\Auth::user()->user_role->value == 2),
//                'exists:users,id'
//            ],
            'client_order_id' => 'required',

            'order_value' => ['nullable', 'numeric', Rule::requiredIf(in_array(request()->payment_method, [2, 1]))],
            'payment_method' => ['required', 'integer'],
        ];





        return $validates;
    }




    public function messages()
    {
        return [
            'client_order_id.required' => 'Client order ID is required',
            'order_value.required' => 'Order value is required',
            'payment_method.required' => 'Payment method is required',
            'preperation_time.required' => 'Preparation time is required',
            'branch_id.required' => 'Branch is required',
            'client_id.required' => 'Client is required',
            'customer_phone.required' => 'Customer phone is required',
            'customer_name.required' => 'Customer name is required',
            'date_time.required' => 'Date and time are required',
            'order_details.required' => 'Order details are required',
            'instructions.required' => 'Instructions are required',
            'pickup_instructions.required' => 'Pickup instructions are required',
            'proof_action.required' => 'Proof of action is required',
            'items_no.required' => 'Number of items is required',
            'vehicle_id.required' => 'Vehicle is required',
            'arive_in.required' => 'Arrival time is required',
            'service_fees.required' => 'Service fees are required',
            'driver_in.required' => 'Driver time is required',
        ];
    }
}
