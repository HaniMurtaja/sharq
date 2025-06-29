<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZoneDetail extends Model
{
    use HasFactory;
    protected $fillable = ['zone_id','city_id', 'area_id'];
}
