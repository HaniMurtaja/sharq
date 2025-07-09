<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendPaymentReceiptEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $receipt;
    public $timeout = 120;

    public function __construct(\App\Models\PaymentReceipt $receipt)
    {
        $this->receipt = $receipt;
    }

    public function handle()
    {
        try {
            $emailList = $this->receipt->invoice->getEmailList();
            
           
            $billingEmails = [
                'billing@alshrouqexpress.com',
                'finance@alshrouqexpress.com'
            ];
            
            $allEmails = array_unique(array_merge($emailList, $billingEmails));
            
            foreach ($allEmails as $email) {
                \Mail::to($email)->send(new \App\Mail\PaymentReceiptEmail($this->receipt));
            }
            
           
            \App\Models\InvoiceLog::create([
                'invoice_id' => $this->receipt->invoice_id,
                'action' => 'receipt_email_sent',
                'user_id' => null,
                'new_data' => [
                    'receipt_id' => $this->receipt->id,
                    'sent_to' => $allEmails,
                    'sent_at' => now()
                ],
                'notes' => "Payment receipt email sent to " . implode(', ', $allEmails)
            ]);
            
        } catch (\Exception $e) {
            \Log::error("Payment receipt email failed for receipt {$this->receipt->id}: " . $e->getMessage());
            throw $e;
        }
    }
}
