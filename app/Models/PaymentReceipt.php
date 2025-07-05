<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentReceipt extends Model
{
    use HasFactory;

    const STATUS_UNDER_REVIEW = 'under_review';
    const STATUS_CONFIRMED = 'confirmed';
    
    const METHOD_BANK_TRANSFER = 'bank_transfer';
    const METHOD_CASH = 'cash';
    const METHOD_TAP_GATEWAY = 'tap_gateway';
    const METHOD_OTHER = 'other';

    protected $fillable = [
        'invoice_id',
        'receipt_number',
        'amount_paid',
        'payment_date',
        'payment_method',
        'transaction_reference',
        'status',
        'notes',
        'payment_details'
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'payment_date' => 'date',
        'payment_details' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($receipt) {
            if (!$receipt->receipt_number) {
                $receipt->receipt_number = 'REC-' . date('Ym') . '-' . str_pad(static::count() + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }


    public function invoice()
    {
        return $this->belongsTo(ClientInvoice::class, 'invoice_id');
    }
}
