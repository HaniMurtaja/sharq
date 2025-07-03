<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'client_id',
        'invoice_date',
        'due_date',
        'status',
        'subtotal',
        'tax_amount',
        'total_amount',
        'currency',
        'notes',
        'zatca_qr_data',
        'qr_code_path',
        'client_emails'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'zatca_qr_data' => 'array',
        'client_emails' => 'array'
    ];

    const STATUS_GENERATED = 'generated_under_review';
    const STATUS_CONFIRMED = 'confirmed_sent_unpaid';
    const STATUS_PAID = 'paid';

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }

    public function paymentReceipts(): HasMany
    {
        return $this->hasMany(PaymentReceipt::class, 'invoice_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(InvoiceLog::class, 'invoice_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'invoice_id');
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function isOverdue(): bool
    {
        return $this->due_date < now()->toDateString() && !$this->isPaid();
    }

    public function getDaysOverdue(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        return now()->diffInDays($this->due_date);
    }

    public function getTotalPaidAmount(): float
    {
        return $this->paymentReceipts()
            ->where('status', 'confirmed')
            ->sum('amount_paid');
    }

    public function getRemainingAmount(): float
    {
        return $this->total_amount - $this->getTotalPaidAmount();
    }

    public function getEmailList(): array
    {
        $emails = $this->client_emails ?? [];
        
        // Add default client email if not in the list
        if ($this->client?->email && !in_array($this->client->email, $emails)) {
            $emails[] = $this->client->email;
        }

        // Add billing emails from client
        if ($this->client?->client?->billing_emails) {
            $billingEmails = $this->client->client->billing_emails;
            $emails = array_merge($emails, $billingEmails);
        }

        return array_unique(array_filter($emails));
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = self::generateInvoiceNumber();
            }
        });
    }

    public static function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $prefix = "INV-{$year}{$month}-";
        
        $lastInvoice = self::where('invoice_number', 'like', $prefix . '%')
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, strlen($prefix));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}

