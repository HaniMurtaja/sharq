<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientUsers extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function client () {
        return $this->belongsTo(Client::class);
    }

    public function user () {
        return $this->belongsTo(ClientUser::class, 'user_id');
    }
}
