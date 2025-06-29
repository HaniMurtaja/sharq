<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UniquePhoneForRole implements Rule
{
    protected $clientId;

    public function __construct($clientId = null)
    {
        $this->clientId = $clientId;
    }

    public function passes($attribute, $value)
    {
        
        $exists = DB::table('users')
            ->where('phone', $value)
            ->where('user_role', 3)
            ->when($this->clientId, function ($query) {
                $query->where('id', '<>', $this->clientId);
            })
            ->exists();

        return !$exists; 
    }

    public function message()
    {
        return 'The phone number must be unique for users with the role of operator.';
    }
}
