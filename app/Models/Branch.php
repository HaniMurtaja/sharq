<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;
    protected $fillable = ['name',	'driver_id'];

    public function driver () {
        return $this->belongsTo(Operator::class, 'driver_id');
    }
}
