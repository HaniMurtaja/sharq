<?php
namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class OperatorsRequest extends FormRequest
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
            'task_sorting' => 'nullable|in:0,1',
            'Km_reminding_operator_update' => 'required|numeric',
            'drop_off_buffer_time' => 'nullable|numeric',
            'drop_off_handling_time' => 'nullable|numeric',
            'pickup_whats_app' => 'nullable|string',
            'drop_of_whats_app' => 'nullable|string',
            'hide_dropoff_area' => 'nullable|in:0,1',
            'enable_milage_tracking' => 'nullable|in:0,1',
            'enable_inspection_form_filling' => 'nullable|in:0,1',
            'enable_accessories' => 'nullable|in:0,1',
            'accessories' => 'required|string',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // Redirect back with errors and hash fragment
        $redirect = redirect(route('settings').'#operators')
            ->withErrors($validator)
            ->withInput();

        throw new \Illuminate\Validation\ValidationException($validator, $redirect);
    }
}
