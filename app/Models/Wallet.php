<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',     
        'operator_id',
        'balance',
        'currency'
    ];

    protected $casts = [
        'balance' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

  
    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }


    public function scopeForClients($query)
    {
        return $query->whereNotNull('user_id');
    }

  
    public function scopeForOperators($query)
    {
        return $query->whereNotNull('operator_id');
    }
}
