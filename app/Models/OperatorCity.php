<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperatorCity extends Model
{
    protected $fillable = ['operator_id', 'city_id'];

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}
