<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class ServicesRequest extends FormRequest
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
        // dd(99);
        return [
            
            'service_hours_toggle' => 'nullable|in:0,1',
            'open_hour' => 'required|numeric|min:1|max:12',
            'open_minute' => 'required|numeric|min:0|max:59',
            'open_period' => 'required|in:AM,PM',
            'close_hour' => 'required|numeric|min:1|max:12',
            'close_minute' => 'required|numeric|min:0|max:59',
            'close_period' => 'required|in:AM,PM',
            'api_key' => 'required|string',

            'operators_service_hours_toggle' => 'nullable|in:0,1',
            'start_hour' => 'required|numeric|min:1|max:12',
            'start_minute' => 'required|numeric|min:0|max:59',
            'start_period' => 'required|in:AM,PM',
            'end_hour' => 'required|numeric|min:1|max:12',
            'end_minute' => 'required|numeric|min:0|max:59',
            'end_period' => 'required|in:AM,PM',

            'service_availability_toggle' => 'nullable|in:0,1',
            'write_message' => 'nullable|string',
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
        $redirect = redirect(route('settings') . '#service')
            ->withErrors($validator)
            ->withInput();

        throw new \Illuminate\Validation\ValidationException($validator, $redirect);
    }
   
}
