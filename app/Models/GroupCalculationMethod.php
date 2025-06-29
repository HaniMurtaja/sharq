<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupCalculationMethod extends Model
{
    use HasFactory;
    protected $fillable = ['group_id', 'data'];
    protected $casts = [
        'data' => 'json'
    ];
    
}
