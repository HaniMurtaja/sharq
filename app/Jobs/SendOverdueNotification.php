<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendOverdueNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $invoice;
    public $timeout = 60;

    public function __construct(ClientInvoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function handle()
    {
        try {
            $emailList = $this->invoice->getEmailList();
            
            foreach ($emailList as $email) {
                \Mail::to($email)->send(new \App\Mail\OverdueNotificationEmail($this->invoice));
            }
            
            
            \App\Models\InvoiceLog::create([
                'invoice_id' => $this->invoice->id,
                'action' => 'overdue_notification_sent',
                'user_id' => null,
                'new_data' => [
                    'sent_to' => $emailList,
                    'sent_at' => now(),
                    'days_overdue' => $this->invoice->getDaysOverdue()
                ],
                'notes' => "Overdue notification sent to " . implode(', ', $emailList)
            ]);
            
        } catch (\Exception $e) {
            \Log::error("Overdue notification failed for invoice {$this->invoice->id}: " . $e->getMessage());
            throw $e;
        }
    }
}
