<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReport extends Model
{
    use HasFactory;
    protected $fillable = [
        'description',
        'order_id',
        'driver_id',
        'reason',
      
    ];

    public function driver () {
        return $this->belongsTo(Operator::class, 'driver_id');
    }

    public function order () {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
