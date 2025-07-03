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

    protected $month;
    protected $clientId;

    public function __construct(Carbon $month, $clientId = null)
    {
        $this->month = $month;
        $this->clientId = $clientId;
    }

    public function handle()
    {
        if ($this->clientId) {

            $clients = User::where('id', $this->clientId)->where('user_role', 2)->get();

        } else {

            $clients = User::where('user_role', 2)
                ->whereHas('client', function($q) {
                    $q->where('auto_generate_invoice', true);
                })
                ->get();
        }

        foreach ($clients as $client) {

            try {

                $this->generateInvoiceForClient($client);

            } catch (\Exception $e) {

                \Log::error("Failed to generate invoice for client {$client->id}: " . $e->getMessage());
            }
        }
    }

    private function generateInvoiceForClient(User $client)
    {
        
        $existingInvoice = ClientInvoice::where('client_id', $client->id)
            ->whereYear('invoice_date', $this->month->year)
            ->whereMonth('invoice_date', $this->month->month)
            ->first();

        if ($existingInvoice) {
            return; 
        }

       
        $orders = Order::where('ingr_shop_id', $client->id)
            ->whereYear('created_at', $this->month->year)
            ->whereMonth('created_at', $this->month->month)
            ->where('invoiced', false)
            ->get();

        if ($orders->isEmpty()) {
            
            return; 
        }

        DB::transaction(function() use ($client, $orders) {
            $settings = CompanyFinancialSetting::getSettings();
            
            
            $invoice = ClientInvoice::create([
                'client_id' => $client->id,
                'invoice_date' => now(),
                'due_date' => now()->addDays($settings->payment_due_days),
                'status' => ClientInvoice::STATUS_GENERATED,
                'currency' => $client->client?->currency ?? 'SAR'
            ]);

            $subtotal = 0;
            $totalOrders = $orders->count();
            $totalServiceFees = $orders->sum('service_fees');

            
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => "Delivery services for " . $this->month->format('F Y') . " ({$totalOrders} orders)",
                'quantity' => $totalOrders,
                'unit_price' => $totalOrders > 0 ? $totalServiceFees / $totalOrders : 0,
                'total_price' => $totalServiceFees,
                'service_month' => $this->month->format('Y-m-01')
            ]);

            $subtotal = $totalServiceFees;

            
            $taxRate = 0.15;
            $taxAmount = $subtotal * $taxRate;
            $totalAmount = $subtotal + $taxAmount;

            
            $invoice->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount
            ]);

          
            Order::whereIn('id', $orders->pluck('id'))->update([
                'invoiced' => true,
                'invoice_id' => $invoice->id
            ]);

            
            InvoiceLog::create([
                'invoice_id' => $invoice->id,
                'action' => 'created',
                'user_id' => null, 
                'new_data' => $invoice->toArray(),
                'notes' => 'Automatically generated monthly invoice for ' . $this->month->format('F Y')
            ]);

           
            if ($client->client) {
                $client->client->update(['last_invoice_date' => $invoice->invoice_date]);
            }
        });
    }
}
