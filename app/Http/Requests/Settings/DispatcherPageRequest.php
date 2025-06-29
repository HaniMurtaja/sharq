<?php
namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class DispatcherPageRequest extends FormRequest
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
        return [
         
            'orders_sorting' => 'required|string',
            'orders_sorting_direction' => 'required|string',
            'show_All_filter' => 'required|in:On,Off',
            'new_orders_alert_sound' => 'nullable|in:0,1',
            'time_allowed_accept_more_than_order' => 'nullable|min:0|max:59',
            
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $redirect = redirect(route('settings').'#dispatcher')
            ->withErrors($validator)
            ->withInput();

        throw new \Illuminate\Validation\ValidationException($validator, $redirect);
    }
}
