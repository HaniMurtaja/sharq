<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    use HasFactory;
    //sms_logs
    protected $fillable = [
        'number',
        'message',
        'correlation_id',
        'request_fields',
        'response_body'
    ];
    protected $casts = [
        'request_fields' => 'array',
        'response_body' => 'array',
    ];
}
