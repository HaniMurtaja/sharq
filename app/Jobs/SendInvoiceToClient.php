<?php

namespace App\Jobs;

use App\Models\ClientInvoice;
use App\Mail\InvoiceEmail;
use App\Services\InvoicePDFService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendInvoiceToClient implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $invoice;

    public function __construct(ClientInvoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function handle()
    {
        $emails = $this->invoice->getEmailList();
        
        if (empty($emails)) {
            
            \Log::warning("No emails found for invoice {$this->invoice->invoice_number}");
            return;
        }

        $pdfService = new InvoicePDFService();

        $pdf = $pdfService->generate($this->invoice);

        foreach ($emails as $email) {

            try {
                Mail::to($email)->send(new InvoiceEmail($this->invoice, $pdf));

            } catch (\Exception $e) {

                \Log::error("Failed to send invoice {$this->invoice->invoice_number} to {$email}: " . $e->getMessage());
            }
        }
    }
}

