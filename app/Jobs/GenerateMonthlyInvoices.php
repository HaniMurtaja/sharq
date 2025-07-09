<?php

namespace App\Jobs;

use App\Models\ClientInvoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceLog;
use App\Models\User;
use App\Models\Order;
use App\Models\CompanyFinancialSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GenerateMonthlyInvoices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $invoice;
    public $timeout = 300; // 5 minutes timeout

    public function __construct(ClientInvoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function handle(PdfGenerationService $pdfService)
    {
        try {
            $pdfContent = $pdfService->generateInvoicePdf($this->invoice);
            
            
            $fileName = "invoices/invoice_{$this->invoice->invoice_number}.pdf";
            Storage::disk('s3')->put($fileName, $pdfContent);
            
            
            $this->invoice->update(['pdf_path' => $fileName]);
            
        } catch (\Exception $e) {
            \Log::error("PDF generation failed for invoice {$this->invoice->id}: " . $e->getMessage());
            throw $e;
        }
    }
}






