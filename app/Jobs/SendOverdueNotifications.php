<?php

namespace App\Jobs;

use App\Models\ClientInvoice;
use App\Mail\OverdueInvoiceEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOverdueNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $overdueInvoices = ClientInvoice::with(['client'])
            ->where('due_date', '<', now())
            ->where('status', '!=', ClientInvoice::STATUS_PAID)
            ->get();

        foreach ($overdueInvoices as $invoice) {
            
            $clientEmails = $invoice->getEmailList();

            foreach ($clientEmails as $email) {

                try {
                    
                    Mail::to($email)->send(new OverdueInvoiceEmail($invoice));

                } catch (\Exception $e) {

                    \Log::error("Failed to send overdue notification for invoice {$invoice->invoice_number} to {$email}: " . $e->getMessage());
                }
            }

           
            $billingEmails = [
                'billing@alshrouqexpress.com',
                'finance@alshrouqexpress.com'
            ];

            foreach ($billingEmails as $email) {

                try {

                    Mail::to($email)->send(new OverdueInvoiceEmail($invoice, true)); 

                } catch (\Exception $e) {

                    \Log::error("Failed to send overdue notification to billing {$email}: " . $e->getMessage());
                }
            }

          
            $daysPastDue = now()->diffInDays($invoice->due_date);

            if ($daysPastDue > 7) { 

                $invoice->client->update(['is_active' => false]);
            }
        }
    }
}
