<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class KSAPhoneRule implements Rule {
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
        $regex = '^(5)(\d){8}$';
        return preg_match("~$regex~", $value) != 0;

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string {
        return __('Invalid phone number');
    }
}
