<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $guarded = [];
    // protected $casts = [
    //     'system_billing' => 'json',
    //     'clients'=> 'json', 
    //     'dashboard' => 'json',	
    //     'dispatcher' => 'json', 	
    //     'customers' => 'json',	
    //     'users'	reports	drivers	driver	operator_billings	orders	road_assistance
    // ];
}
