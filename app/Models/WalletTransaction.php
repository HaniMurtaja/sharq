<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class WalletTransaction extends Model
{
    protected $fillable=[
        'wallet_id',
        'amount',
        'type',
        'model_id',
        'model_type',
        'description'
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class , 'wallet_id');
    }

    public function model()
    {
        return $this->morphTo();
    }

}
