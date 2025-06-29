<?php

namespace App\Http\Requests\Clients;

use App\Rules\KSAPhoneRule;
use App\Rules\UniquePhoneForRole;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class ClientBranchesRequest extends FormRequest
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
        $isEdit = request()->branch_id  ? true : false;
        $branchId = request()->user_branch_id ;
        // dd($branchId);
        return [
            'branch_id' => 'nullable|exists:client_branches,id',
            'branch_client_id' => 'required|exists:users,id',
            'branch_name' => 'required|string|max:255',
            'pickup_id' => 'required',
            'custom_id' => 'nullable',
          

            'branch_phone' => [
                'nullable',
                new UniquePhoneForRole($branchId),
                new KSAPhoneRule(),
            ],

            'branch_password' =>  [
                $isEdit ? 'nullable' : 'nullable',
                Password::min(6)

            ],
            'branch_email'=> [
                'nullable',
                'unique:users,email,' . $branchId,
                'email'
            ],
            'client_group_id' => 'nullable|exists:branches,id',
            'driver_group_id' => 'nullable|exists:groups,id',
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
            'country' => 'nullable|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'area_id' => 'nullable|exists:areas,id',
            'street' => 'nullable|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'building' => 'nullable|string|max:255',
            'floor' => 'nullable|string|max:255',
            'apartment' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'business_hours' => 'nullable|array',
            'business_hours.*.day' => 'nullable|string|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'business_hours.*.start' => 'nullable',
            'business_hours.*.end' => 'nullable',

        ];
    }

    public function messages()
    {
        return [
            'branch_name.required' => 'The branch name is required.',
            'branch_name.string' => 'The branch name must be a string.',
            'branch_name.max' => 'The branch name may not be greater than 255 characters.',

            'branch_phone.required' => 'The branch phone is required.',
            'branch_phone' => 'The branch phone is invalid.',

            'client_group_id.exists' => 'The selected client group is invalid.',
            'driver_group_id.exists' => 'The selected driver group is invalid.',

            'lat.required' => 'The latitude is required.',
            'lat.numeric' => 'The latitude must be a number.',

            'lng.required' => 'The longitude is required.',
            'lng.numeric' => 'The longitude must be a number.',

            'country.string' => 'The country must be a string.',
            'country.max' => 'The country may not be greater than 255 characters.',

            'city_id.exists' => 'The selected city is invalid.',
            'area_id.exists' => 'The selected area is invalid.',

            'street.required' => 'The street is required.',
            'street.string' => 'The street must be a string.',
            'street.max' => 'The street may not be greater than 255 characters.',

            'landmark.string' => 'The landmark must be a string.',
            'landmark.max' => 'The landmark may not be greater than 255 characters.',

            'building.string' => 'The building must be a string.',
            'building.max' => 'The building may not be greater than 255 characters.',

            'floor.string' => 'The floor must be a string.',
            'floor.max' => 'The floor may not be greater than 255 characters.',

            'apartment.string' => 'The apartment must be a string.',
            'apartment.max' => 'The apartment may not be greater than 255 characters.',

            'description.string' => 'The description must be a string.',

            'business_hours.array' => 'The business hours must be an array.',
            'business_hours.*.day.in' => 'The day must be one of the following: Sunday, Monday, Tuesday, Wednesday, Thursday, Friday, Saturday.',
            'business_hours.*.start.date_format' => 'The start time must be in the format HH:mm.',
            'business_hours.*.end.date_format' => 'The end time must be in the format HH:mm.',
            'business_hours.*.end.after' => 'The end time must be after the start time.',
        ];
    }
}
