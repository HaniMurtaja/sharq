<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDriver extends Model
{
    use HasFactory;
    protected $fillable = ['order_id', 'driver_id', 'distance','operator_details'];

    protected $casts = [
        'operator_details' => 'json'
    ];
    public function driver() {
        return $this->belongsTo(Operator::class, 'driver_id')->withDefault();
    }

    public function order() {
        return $this->belongsTo(Order::class, 'order_id')->withDefault();
    }
}
