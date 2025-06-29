<?php

namespace App\Models;

use App\Enum\DriverStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperatorStatus extends Model
{
    use HasFactory;
    protected $fillable = [
        'operator_id',
        'status',
    ];

    protected $casts = [
        'status' => DriverStatus::class,
    ];

    public function operator () {
        return $this->belongsTo(Operator::class, 'operator_id');
    }
}
