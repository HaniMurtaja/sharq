<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class , 'user_id');
    }
}
