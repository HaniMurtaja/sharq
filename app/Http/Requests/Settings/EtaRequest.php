<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class EtaRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        // dd(888);
        return [

            'broadcast_delay' => 'required|numeric',
            'acceptance_buffer' => 'required|numeric',
            'pickup_buffer_time' => 'required|numeric',
            'pickup_handling_time' => 'required|numeric',
            'dropoff_buffer_time' => 'required|numeric',
            'dropoff_handling_time' => 'required|numeric',
            'acceptance_time_threshold' => 'required|numeric',
            'broadcast_time_before' => 'required|numeric',
            'default_arrive_to_pickup_time' => 'required|numeric',
            'default_arrive_to_dropoff_time' => 'required|numeric',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // Redirect back with errors and hash fragment
        $redirect = redirect(route('settings') . '#eta')
            ->withErrors($validator)
            ->withInput();

        throw new \Illuminate\Validation\ValidationException($validator, $redirect);
    }
   
}
