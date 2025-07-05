<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'account_number',
        'company_name',
        'billing_emails',
        'auto_generate_invoice',
        'invoice_template_notes',
        'payment_terms',
        'currency',
        'last_invoice_date',
       
        'city_id',
        'default_prepartion_time',
        'min_prepartion_time',
        'partial_pay',
        'note',
        'client_group_id',
        'driver_group_id'
    ];

    protected $casts = [
        'billing_emails' => 'array',
        'auto_generate_invoice' => 'boolean',
        'last_invoice_date' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

 
    public function invoices()
    {
        return $this->hasMany(ClientInvoice::class, 'client_id', 'user_id');
    }

   
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}
