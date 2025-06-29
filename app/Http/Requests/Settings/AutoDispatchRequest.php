<?php
namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class AutoDispatchRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        // dd(9);
        return [
            'auto_dispatch' => 'nullable|in:0,1',
            'dispatch_radius' => 'required|numeric',
            'dispatch_driver_priority' => 'nullable|string',
            'dispatching_rounds_no' => 'required|numeric',
            'notify_failed_dispatching_orders' => 'nullable|in:0,1',
            'enable_clubbing'  => 'nullable|in:0,1',
        
            'clubbing_by' => 'nullable|string',
            'dispatch_service_providers' => 'nullable|in:0,1',
            'max_driver_orders' => 'required|min:1',
            'auto_dispatch_per_city' => 'nullable|array',
            'auto_dispatch_per_city.*' => 'exists:cities,id',
            'max_distance_per_city' => 'nullable|array',
            'max_distance_per_city.*.city' => 'nullable|exists:cities,id',
            'max_distance_per_city.*.distance' => 'nullable',
            

        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $redirect = redirect(route('settings').'#auto_dispatch')
            ->withErrors($validator)
            ->withInput();

        throw new \Illuminate\Validation\ValidationException($validator, $redirect);
    }
}
