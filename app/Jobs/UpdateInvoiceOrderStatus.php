<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateInvoiceOrderStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $invoiceId;
    public $timeout = 300;

    public function __construct(int $invoiceId)
    {
        $this->invoiceId = $invoiceId;
    }

    public function handle()
    {
        try {
            $invoice = ClientInvoice::findOrFail($this->invoiceId);
            
            // Update related orders as invoiced
            \DB::table('orders')
                ->where('client_id', $invoice->client_id)
                ->whereYear('created_at', $invoice->invoice_date->year)
                ->whereMonth('created_at', $invoice->invoice_date->month)
                ->where('invoiced', false)
                ->update([
                    'invoiced' => true,
                    'invoice_id' => $invoice->id,
                    'updated_at' => now()
                ]);
                
            \Log::info("Orders marked as invoiced for invoice {$this->invoiceId}");
            
        } catch (\Exception $e) {
            \Log::error("Failed to update order status for invoice {$this->invoiceId}: " . $e->getMessage());
            throw $e;
        }
    }
}
