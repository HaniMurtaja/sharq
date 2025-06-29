<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enum\DeleveryFeed;
class ClientsGroup extends Model
{
    use HasFactory;
    protected $fillable = ['name',	'calculation_method',	'default_delivery_fee',	'collection_amount',	'service_type'];
    protected $casts = [
        'calculation_method' => DeleveryFeed::class
    ];

    public function calculationMethod () {
        return $this->hasOne(GroupCalculationMethod::class, 'group_id');
    }
}
