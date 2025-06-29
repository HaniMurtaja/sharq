<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
class Vehicle extends Model implements HasMedia 
{
    use HasFactory, InteractsWithMedia;
    protected $fillable = [
        'name',
    	'type',	
        'plate_number',	
        'vin_number',	
        'make',	
        'model',
        'year',	
        'color',	
        'vehicle_milage',	
        'last_service_milage',	
        'due_service_milage',	
        'service_milage_limit',	
        'operator_id',
        'owner'
    ];

    public function operator () {
        return $this->belongsTo(Operator::class, 'operator_id');
    }
    
}
