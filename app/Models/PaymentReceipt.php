<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_number',
        'invoice_id',
        'amount_paid',
        'payment_date',
        'payment_method',
        'transaction_reference',
        'payment_details',
        'status',
        'notes'
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'payment_date' => 'date',
        'payment_details' => 'array'
    ];

    const STATUS_UNDER_REVIEW = 'under_review';
    const STATUS_CONFIRMED = 'confirmed';

    const METHOD_TAP_GATEWAY = 'tap_gateway';
    const METHOD_BANK_TRANSFER = 'bank_transfer';
    const METHOD_CASH = 'cash';
    const METHOD_OTHER = 'other';

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(ClientInvoice::class, 'invoice_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($receipt) {
            if (empty($receipt->receipt_number)) {
                $receipt->receipt_number = self::generateReceiptNumber();
            }
        });
    }

    public static function generateReceiptNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $prefix = "RCP-{$year}{$month}-";
        
        $lastReceipt = self::where('receipt_number', 'like', $prefix . '%')
            ->orderBy('receipt_number', 'desc')
            ->first();

        if ($lastReceipt) {
            $lastNumber = (int) substr($lastReceipt->receipt_number, strlen($prefix));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
