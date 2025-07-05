<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_number',
        'company_name',
        'billing_emails',
        'auto_generate_invoice',
        'invoice_template_notes',
        'payment_terms',
        'currency',
        'last_invoice_date'
    ];

    protected $casts = [
        'billing_emails' => 'array',
        'auto_generate_invoice' => 'boolean',
        'last_invoice_date' => 'datetime'
    ];

   
    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'id');
    }
}
