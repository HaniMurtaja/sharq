<?php

namespace App\Models;

use App\Enum\ReportStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportHistory extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'order_statuses' => 'json',
        'status' => ReportStatus::class
    ];
}
