<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'action',
        'user_id',
        'old_data',
        'new_data',
        'notes'
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array'
    ];


    public function invoice()
    {
        return $this->belongsTo(ClientInvoice::class, 'invoice_id');
    }

   
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

