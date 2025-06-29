<?php

namespace App\Http\Requests;


use App\Enum\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use App\Rules\KSAPhoneRule;
use Illuminate\Validation\Rule;
class UserRequest extends FormRequest
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
    public function rulesOLD()
    {
        // dd(request()->all());
        // dd(request()->input('edit_user_client_id'));
        $requestUrl = $this->url();
        $urlPath = $this->path();
        $lastSegment = basename($urlPath);



        $clientId = request()->input('user_id');

        $isEdit = request()->input('user_id') ? true : false;
        // dd($isEdit);
        $isPost = $this->isMethod('post') ? true : false;
        if (request()->input('user_id')) {
           $isPost = false;
        }
        if ($lastSegment == "update-client-user") {
            $clientId = request()->input('edit_user_client_id');
            $isEdit = true;
            $isPost = false;
        }

        return [
            'profile_photo' => 'nullable|image',
            'first_name' => 'required|string|min:2|max:255',
            'last_name' => 'required|string|min:2|max:255',
            'email' => [
                'required',
                'unique:users,email,' . $clientId,
                'email'
            ],
            'phone' => [
                'nullable',
                $isEdit ? Rule::unique('users')->ignore($clientId) : 'unique:users',
                new KSAPhoneRule()
            ],
            'groups' => 'array|nullable',

            'groups.*' => 'required|exists:groups,id',

            'locked' => 'nullable|boolean',
            'marketplace_access' => 'nullable|boolean',
            'mac_address' => 'nullable|string|max:255',
            'sim_card' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'request_per_second' => 'nullable|integer|min:1',
            'password' => [
                $isPost ? 'required' : 'nullable',
                Password::min(6)

            ],
            'user_id' => ['nullable', 'exists:users,id'],
            'role' => 'required|exists:roles,name',

        ];
    }


    public function rules()
    {
        // Debugging: Check the user role
        // dd($this->user()->user_role); // Uncomment this line to debug

        $requestUrl = $this->url();
        $urlPath = $this->path();
        $lastSegment = basename($urlPath);

        $clientId = request()->input('user_id');
        $isEdit = $clientId ? true : false;
        $isPost = $this->isMethod('post');

        if ($lastSegment == "update-client-user") {
            $clientId = request()->input('edit_user_client_id');
            $isEdit = true;
            $isPost = false;
        }

        // Initialize rules array
        $rules = [
            'profile_photo' => 'nullable|image',
            'first_name' => 'required|string|min:2|max:255',
            'last_name' => 'required|string|min:2|max:255',
            'email' => [
                'required',
                'unique:users,email,' . $clientId,
                'email'
            ],
            'phone' => [
                'nullable',
                $isEdit ? Rule::unique('users')->ignore($clientId) : 'unique:users',
                new KSAPhoneRule()
            ],
            'groups' => 'array|nullable',
            'groups.*' => 'required|exists:groups,id',
            'locked' => 'nullable|boolean',
            'marketplace_access' => 'nullable|boolean',
            'mac_address' => 'nullable|string|max:255',
            'sim_card' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'request_per_second' => 'nullable|integer|min:1',
            'password' => [
                $isPost ? 'required' : 'nullable',
                Password::min(6)
            ],
            'user_id' => ['nullable', 'exists:users,id'],
            'role' => 'required|exists:roles,name',
            'country_id' => 'required|exists:countries,id',
           
        ];

        // Add conditional validation for city_ids
        if ($this->user_role == UserRole::DISPATCHER->value) {
            $rules['city_ids'] = 'required|array';
        }

        //   if ($this->user_role == UserRole::ADMIN->value) {
              
            
        // }

        return $rules;
    }


    public function messages()
    {
        return [
      ];
    }
}
