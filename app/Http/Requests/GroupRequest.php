<?php

namespace App\Http\Requests;

use App\Rules\KSAPhoneRule;
use Illuminate\Foundation\Http\FormRequest;

class GroupRequest extends FormRequest
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
            'group_name' => ['required', 'string'],
            'min_feed_order' => ['required', 'numeric'],
            'type_feed_order' => ['required', 'string'],
            'from.*' => ['nullable', 'numeric'],
            'to.*' => ['nullable', 'numeric'],
            'percentage.*' => ['nullable', 'numeric'],
            'additional_type_feed' => ['required', 'string'],
            'additional_from.*' => ['nullable', 'numeric'],
            'additional_to.*' => ['nullable', 'numeric'],
            'additional_percentage.*' => ['nullable', 'numeric'],
            'additional_type.*' =>['nullable', 'string'],
            'type.*' => ['nullable', 'string'],
            'group_id' => ['nullable', 'exists:groups,id']
        ];
    }

    public function messages()
    {
        return [
            'group_name.required' => 'The group name is required.',
            'min_feed_order.required' => 'The minimum feed per order is required.',
            'min_feed_order.numeric' => 'The minimum feed per order must be a number.',
            'type_feed_order.required' => 'The type feed per order is required.',
            'from.*.required' => 'The from field is required.',
            'from.*.numeric' => 'The from field must be a number.',
            'to.*.required' => 'The to field is required.',
            'to.*.numeric' => 'The to field must be a number.',
            'percentage.*.required' => 'The percentage field is required.',
            'percentage.*.numeric' => 'The percentage field must be a number.',
            'additional_type_feed.required' => 'The additional type feed is required.',
            'additional_from.*.required' => 'The additional from field is required.',
            'additional_from.*.numeric' => 'The additional from field must be a number.',
            'additional_to.*.required' => 'The additional to field is required.',
            'additional_to.*.numeric' => 'The additional to field must be a number.',
            'additional_percentage.*.required' => 'The additional percentage field is required.',
            'additional_percentage.*.numeric' => 'The additional percentage field must be a number.',
        ];
    }

}
