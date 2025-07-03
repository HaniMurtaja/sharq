<?php

namespace App\Services;

use App\Models\ClientInvoice;
use App\Models\CompanyFinancialSetting;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoicePDFService
{
    protected $zatcaService;

    public function __construct(ZatcaQRCodeService $zatcaService = null)
    {
        $this->zatcaService = $zatcaService ?? new ZatcaQRCodeService();
    }

 
    public function generate(ClientInvoice $invoice)
    {
        $settings = CompanyFinancialSetting::getSettings();
        
        
        if (!$invoice->qr_code_path) {
            $this->zatcaService->generateQRCode($invoice);
            $invoice->refresh();
        }
        
        $qrCodeUrl = $this->zatcaService->getQRCodeUrl($invoice);
        
        $data = [
            'invoice' => $invoice,
            'settings' => $settings,
            'qrCodeUrl' => $qrCodeUrl
        ];
        
        return Pdf::loadView('admin.pages.accounting.invoice-pdf', $data)
            ->setPaper('a4')
            ->setOptions([
                'defaultFont' => 'Arial',
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
            ]);
    }

  
    
    public function generateAndSave(ClientInvoice $invoice): string
    {
        $pdf = $this->generate($invoice);
        
        $fileName = 'invoices/invoice_' . $invoice->invoice_number . '.pdf';
        $path = storage_path('app/public/' . $fileName);
        
       
        $directory = dirname($path);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        $pdf->save($path);
        
        return $fileName;
    }
}