<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialBusinessHours extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
