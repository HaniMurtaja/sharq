<?php

namespace App\Http\Requests;

use App\Rules\KSAPhoneRule;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class OperatorRequest extends FormRequest
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
        $maxIntegerValue = 2147483647;
        // dd(request()->all());
        $vehicle =  [
            'vehicle_image' => ['nullable', 'image'],
            'id_card_image_vehicle' => ['nullable', 'image'],
            'name' => ['required', 'string'],
            'type' => ['required', 'string'],
            'plate_number' => ['required', 'string'],
            'vin_number' => ['nullable', 'string'],
            'make' => ['nullable', 'string'],
            'model' => ['nullable', 'string'],
            'year' => ['nullable', 'string'],
            'color' => ['nullable', 'string'],
            'vehicle_milage' => ['nullable', 'numeric', 'max:' . $maxIntegerValue],
            'last_service_milage' => ['nullable', 'numeric', 'max:' . $maxIntegerValue],
            'due_service_milage' => ['nullable', 'numeric', 'max:' . $maxIntegerValue],
            'service_milage_limit' => ['nullable', 'numeric', 'max:' . $maxIntegerValue],



            'vehicle_id' => 'nullable|exists:vehicles,id',

        ];
        // dd(request()->all());
        // dd(request()->input('operator_id'));


        $isEdit = request()->input('operator_id') ? true : false;
        $operatorId = request()->input('operator_id');
        // dd($isEdit);
        // dd( $isEdit ? Rule::unique('users')->ignore(request()->input('operator_id')) : 'unique:users');

        $rules = [
            'password' => [
                $isEdit ? 'nullable' : 'required',
                Password::min(6)

            ],
            'email' => [
                'required',
                'unique:users,email,' . $operatorId,
                'email'
            ],
            'profile_image' => [$isEdit ? 'nullable' : 'nullable', 'image'],
            'first_name' => ['required', 'string', 'min:3'],
            'last_name' => ['required', 'string', 'min:3'],
            'phone' => [
                'required',
                $isEdit ? Rule::unique('users')->ignore(request()->input('operator_id')) : 'unique:users',
                new KSAPhoneRule()
            ],
            'birth_date' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) {
                    $age = \Carbon\Carbon::parse($value)->age;
                    if ($age < 21) {
                        $fail('You must be at least 21 years old.');
                    }
                }
            ],
            'id_card_image' => ['nullable', 'file'],
            'emergency_contact_name' => ['nullable', 'string', 'min:3'],

            'emergency_contact_phone' => [
                'nullable',
                new KSAPhoneRule()
            ],
            'social_id_no' => ['nullable', 'string'],

            'city' => 'required|array',
            'city.*' => 'exists:cities,id',
            'license_front_image' => ['nullable', 'file'],
            'license_back_image' => ['nullable', 'file'],
            'iban' => ['nullable', 'string', 'min:3'],
            'group_id' => ['nullable', 'exists:groups,id'],
            'branch_group_id' => ['nullable', 'exists:groups,id'],
            'shift_id' => ['nullable', 'exists:shifts,id'],
            'days_off' => ['nullable'],
            'jop_type' => 'nullable',
            'order_value' => ['nullable', 'numeric', 'max:100000'],
        ];

        if (request()->input('car_type') === 'driver') {
            $rules = array_merge($rules, $vehicle);
        }

        if (request()->input('car_type') === 'company') {
            $rules['company_vehicle_id'] = ['required', 'exists:vehicles,id'];
        }

        return $rules;
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'password.required' => 'The password is required.',
            'password.min' => 'The password must be at least 6 characters.',
            'profile_image.required' => 'The profile image is required.',
            'profile_image.image' => 'The profile image must be an image file.',
            'first_name.required' => 'The first name is required.',
            'first_name.min' => 'The first name must be at least 3 characters.',
            'last_name.required' => 'The last name is required.',
            'last_name.min' => 'The last name must be at least 3 characters.',
            'phone.required' => 'The phone number is required.',
            'phone.unique' => 'The phone number has already been taken.',
            'birth_date.required' => 'The birth date is required.',
            'id_card_image.image' => 'The ID card image must be an image file.',
            'emergency_contact_name.required' => 'The emergency contact name is required.',
            'emergency_contact_name.min' => 'The emergency contact name must be at least 3 characters.',
            'emergency_contact_phone.required' => 'The emergency contact phone number is required.',
            'social_id_no.required' => 'The social ID number is required.',
            'city.required' => 'The city is required.',
            'city.exists' => 'The selected city is invalid.',
            'license_front_image.image' => 'The front license image must be an image file.',
            'license_back_image.image' => 'The back license image must be an image file.',
            'iban.required' => 'The IBAN is required.',
            'iban.min' => 'The IBAN must be at least 3 characters.',
            'group_id.required' => 'The group  is required.',
            'group_id.exists' => 'The selected group  is invalid.',
            'branch_group_id.required' => 'The branch group  is required.',
            'branch_group_id.exists' => 'The selected branch group  is invalid.',
            'shift_id.required' => 'The shift  is required.',
            'shift_id.exists' => 'The selected shift  is invalid.',
        ];
    }
}
