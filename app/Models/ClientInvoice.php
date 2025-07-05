<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ClientInvoice extends Model
{
    use HasFactory;

    const STATUS_GENERATED = 'generated_under_review';
    const STATUS_CONFIRMED = 'confirmed_sent_unpaid';
    const STATUS_PAID = 'paid';

    protected $fillable = [
        'client_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'status',
        'subtotal',
        'tax_amount',
        'total_amount',
        'currency',
        'client_emails',
        'notes',
        'payment_token',
        'zatca_qr_code'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'client_emails' => 'array'
    ];

    /**
     * Get the client that owns this invoice
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the invoice items
     */
    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }

    /**
     * Get the payment receipts
     */
    public function paymentReceipts()
    {
        return $this->hasMany(PaymentReceipt::class, 'invoice_id');
    }

    /**
     * Get the invoice logs
     */
    public function logs()
    {
        return $this->hasMany(InvoiceLog::class, 'invoice_id')->orderBy('created_at', 'desc');
    }

    /**
     * Get orders related to this invoice
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'invoice_id');
    }

    /**
     * Check if invoice is overdue
     */
    public function isOverdue()
    {
        return $this->due_date < now() && $this->status !== self::STATUS_PAID;
    }

    /**
     * Get days overdue
     */
    public function getDaysOverdue()
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        return now()->diffInDays($this->due_date);
    }

    /**
     * Get total paid amount
     */
    public function getTotalPaidAmount()
    {
        return $this->paymentReceipts()
            ->where('status', PaymentReceipt::STATUS_CONFIRMED)
            ->sum('amount_paid');
    }

    /**
     * Get remaining amount to be paid
     */
    public function getRemainingAmount()
    {
        return $this->total_amount - $this->getTotalPaidAmount();
    }

    /**
     * Get email list for sending invoice
     */
    public function getEmailList()
    {
        return $this->client_emails ?: [$this->client->email];
    }
}

