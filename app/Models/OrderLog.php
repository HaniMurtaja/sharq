<?php

namespace App\Models;

use App\Enum\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'action',
        'driver_id',
        'user_id',
        'status',
        'description',
        'lat',
        'lng'
    ];





    public function driver() {
        return $this->belongsTo(Operator::class, 'driver_id');
    }

    protected $casts = [
        'status' => OrderStatus::class
    ];
    protected $appends = ['date_log'];

    public function getDateLogAttribute()
    {
        return $this->created_at->format('Y-m-d H:i:s');
    }
}
