<?php

namespace App\Http\Requests;

use App\Rules\KSAPhoneRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class TemplateRequest extends FormRequest
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
        $isEdit = request()->input('template_id') ? true : false;
        $templateId = request()->input('template_id');
        return [

            'name' => [
                'required',
                'string',
                $isEdit ? Rule::unique('roles')->ignore($templateId) : 'unique:roles',
            ],

        ];
    }
}
