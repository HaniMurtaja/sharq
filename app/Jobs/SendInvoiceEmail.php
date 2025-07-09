<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendInvoiceEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $invoice;
    protected $type;
    public $timeout = 120;

    public function __construct(ClientInvoice $invoice, string $type = 'invoice')
    {
        $this->invoice = $invoice;
        $this->type = $type; 
    }

    public function handle()
    {
        try {
            $emailList = $this->invoice->getEmailList();
            
            foreach ($emailList as $email) {
                \Mail::to($email)->send(new \App\Mail\InvoiceEmail($this->invoice, $this->type));
            }
            
            
            \App\Models\InvoiceLog::create([
                'invoice_id' => $this->invoice->id,
                'action' => 'email_sent',
                'user_id' => null,
                'new_data' => [
                    'type' => $this->type,
                    'sent_to' => $emailList,
                    'sent_at' => now()
                ],
                'notes' => "Invoice {$this->type} email sent to " . implode(', ', $emailList)
            ]);
            
        } catch (\Exception $e) {
            \Log::error("Email sending failed for invoice {$this->invoice->id}: " . $e->getMessage());
            throw $e;
        }
    }
}
