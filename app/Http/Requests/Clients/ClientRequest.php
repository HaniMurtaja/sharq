<?php

namespace App\Http\Requests\Clients;


use App\Rules\KSAPhoneRule;
use App\Rules\UniquePhoneForRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ClientRequest extends FormRequest
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
    public function rules()
    {
        // dd(request()->all());
        $clientId = request()->route('id'); 
        // dd($clientId);
        $isEdit = $clientId ? true : false;
        return [
            'profile_photo' => $clientId ?  'nullable|image' : 'required|image',
            'name' => 'required|string|min:2|max:255',
            'phone' => [
                'nullable',
                new UniquePhoneForRole($clientId),
                new KSAPhoneRule(),
            ],
            'email' => [
                'required',
                'unique:users,email,' . $clientId,
                'email'
            ],

            'password' => [
                $clientId ? 'nullable' : 'required',
                Password::min(6)

            ],
            'country_id' => 'nullable|exists:cities,id',
            'city_id' => 'required|exists:cities,id',
            'currency' => 'nullable|string|max:3',
            'default_prepartion_time' => 'required|integer|min:0',
            'min_prepartion_time' => 'required|integer|min:0',
            'partial_pay' => 'nullable|numeric|min:0',
            'note' => 'nullable|string',
            'client_group_id' => 'required|exists:clients_groups,id',
            'driver_group_id' => 'required|exists:groups,id',
            'auto_dispatch' => 'nullable|boolean',
            'is_integration' => 'nullable|boolean',
            'integration_id' => [
                'nullable',
                'required_if:is_integration,1',
                'exists:integration_companies,id'
            ],



        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => 'The user field is required.',
            'user_id.exists' => 'The selected user does not exist.',
            'country.string' => 'The country must be a string.',
            'country.max' => 'The country must not exceed 255 characters.',
            'city_id.required' => 'The city field is required.',
            'city_id.exists' => 'The selected city does not exist.',
            'currency.string' => 'The currency must be a string.',
            'currency.max' => 'The currency must not exceed 3 characters.',
            'default_preparation_time.required' => 'The default preparation time is required.',
            'default_preparation_time.integer' => 'The default preparation time must be an integer.',
            'default_preparation_time.min' => 'The default preparation time must be at least 1.',
            'min_preparation_time.required' => 'The minimum preparation time is required.',
            'min_preparation_time.integer' => 'The minimum preparation time must be an integer.',
            'min_preparation_time.min' => 'The minimum preparation time must be at least 1.',
            'partial_pay.numeric' => 'The partial pay must be a number.',
            'partial_pay.min' => 'The partial pay must be at least 0.',
            'partial_pay.max' => 'The partial pay must not exceed 100.',
            'note.string' => 'The note must be a string.',
            'client_group_id.exists' => 'The selected client group does not exist.',
            'driver_group_id.exists' => 'The selected driver group does not exist.',
        ];
    }
}
