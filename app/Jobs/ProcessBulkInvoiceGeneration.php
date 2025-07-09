<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessBulkInvoiceGeneration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $month;
    protected $clientIds;
    public $timeout = 1800; // 30 minutes for bulk processing

    public function __construct(string $month, array $clientIds = [])
    {
        $this->month = $month;
        $this->clientIds = $clientIds;
    }

    public function handle(\App\Services\AccountingService $accountingService)
    {
        try {
            $result = $accountingService->generateMonthlyInvoices([
                'month' => $this->month,
                'client_ids' => $this->clientIds
            ]);
            
            
            \Mail::to(config('mail.admin_email'))->send(
                new \App\Mail\BulkInvoiceGenerationComplete($result)
            );
            
        } catch (\Exception $e) {
            \Log::error("Bulk invoice generation failed: " . $e->getMessage());
            
            
            \Mail::to(config('mail.admin_email'))->send(
                new \App\Mail\BulkInvoiceGenerationFailed($e->getMessage())
            );
            
            throw $e;
        }
    }
}
