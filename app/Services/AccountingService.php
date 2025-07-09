<?php

namespace App\Services;

use App\Models\ClientInvoice;
use App\Models\InvoiceItem;
use App\Models\PaymentReceipt;
use App\Models\InvoiceLog;
use App\Models\CompanyFinancialSetting;
use App\Models\User;
use App\Repositories\ClientRepository;
use App\Repositories\InvoiceRepository;
use App\Jobs\GenerateInvoicePDF;
use App\Jobs\SendInvoiceEmail;
use App\Jobs\SendOverdueNotification;
use App\Exports\ClientsExport;
use App\Exports\InvoicesExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class AccountingService
{
    protected $clientRepository;
    protected $invoiceRepository;

    public function __construct(
        ClientRepository $clientRepository,
        InvoiceRepository $invoiceRepository
    ) {
        $this->clientRepository = $clientRepository;
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats(): array
    {
        return $this->invoiceRepository->getDashboardStats();
    }

    /**
     * Get monthly order summary for client
     */
    public function getMonthlyOrderSummary(int $clientId): array
    {
        $monthlyData = [];
        
        // Get last 6 months data
        for ($i = 0; $i < 6; $i++) {
            $month = Carbon::now()->subMonths($i);
            
            // Check if invoice already exists for this month
            if ($this->invoiceRepository->invoiceExistsForMonth($clientId, $month)) {
                continue;
            }
            
            // Get uninvoiced orders count (this would be implemented based on your Order model)
            $orderCount = $this->invoiceRepository->getUninvoicedOrdersCount($clientId, $month);
            
            if ($orderCount > 0) {
                $totalServiceFees = $orderCount * rand(5, 25); // Replace with actual calculation
                
                $monthlyData[] = [
                    'month' => $month->format('Y-m'),
                    'month_name' => $month->format('F Y'),
                    'order_count' => $orderCount,
                    'total_service_fees' => $totalServiceFees,
                    'average_per_order' => $totalServiceFees / $orderCount
                ];
            }
        }
        
        return $monthlyData;
    }

    /**
     * Generate monthly invoices with batch processing
     */
    public function generateMonthlyInvoices(array $data): array
    {
        $month = Carbon::createFromFormat('Y-m', $data['month']);
        $clientId = $data['client_id'] ?? null;
        
        DB::beginTransaction();
        
        try {
            if ($clientId) {
                $invoice = $this->generateInvoiceForClient($clientId, $month);
                $result = [
                    'message' => 'Invoice generated successfully for the selected client',
                    'data' => ['invoices_generated' => 1, 'client_id' => $clientId]
                ];
            } else {
                $result = $this->generateInvoicesForAllClients($month);
            }
            
            DB::commit();
            return $result;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Generate invoice for specific client
     */
    protected function generateInvoiceForClient(int $clientId, Carbon $month): ClientInvoice
    {
        $client = User::findOrFail($clientId);
        
        // Check if invoice already exists
        if ($this->invoiceRepository->invoiceExistsForMonth($clientId, $month)) {
            throw new \Exception('Invoice already exists for this client and month');
        }
        
        // Get uninvoiced orders (implement based on your Order model)
        $orderCount = $this->invoiceRepository->getUninvoicedOrdersCount($clientId, $month);
        
        if ($orderCount === 0) {
            throw new \Exception('No uninvoiced orders found for this client and month');
        }
        
        // Calculate totals
        $totalServiceFees = $orderCount * rand(5, 25); // Replace with actual calculation
        $settings = CompanyFinancialSetting::getSettings();
        
        // Create invoice data
        $invoiceData = [
            'client_id' => $clientId,
            'invoice_number' => $this->generateUniqueInvoiceNumber($month, $clientId),
            'invoice_date' => $month->endOfMonth(),
            'due_date' => $month->copy()->endOfMonth()->addDays($settings->payment_due_days ?? 30),
            'status' => ClientInvoice::STATUS_GENERATED,
            'currency' => $client->client?->currency ?? 'SAR',
            'subtotal' => $totalServiceFees,
            'tax_amount' => $totalServiceFees * 0.15, // 15% VAT
            'total_amount' => $totalServiceFees * 1.15,
            'payment_token' => Str::random(32)
        ];
        
        // Create invoice items
        $items = [[
            'description' => "Delivery services for " . $month->format('F Y') . " ({$orderCount} orders)",
            'quantity' => $orderCount,
            'unit_price' => $orderCount > 0 ? $totalServiceFees / $orderCount : 0,
            'total_price' => $totalServiceFees,
            'service_month' => $month->format('Y-m-01')
        ]];
        
        $invoice = $this->invoiceRepository->createInvoiceWithItems($invoiceData, $items);
        
        // Log creation
        $this->logInvoiceAction($invoice->id, 'created', $invoice->toArray(), 
            'Invoice generated for ' . $month->format('F Y') . ' by ' . Auth::user()->full_name);
        
        // Update client's last invoice date
        if ($client->client) {
            $client->client->update(['last_invoice_date' => $invoice->invoice_date]);
        }
        
        return $invoice;
    }

    /**
     * Generate invoices for all clients with uninvoiced orders
     */
    protected function generateInvoicesForAllClients(Carbon $month): array
    {
        $clients = User::where('user_role', 2)
            ->whereHas('orders', function ($q) use ($month) {
                $q->whereYear('created_at', $month->year)
                  ->whereMonth('created_at', $month->month)
                  ->where('invoiced', false)
                  ->where('status', 9); // Only delivered orders
            })
            ->get();
        
        $generatedCount = 0;
        $errors = [];
        
        foreach ($clients as $client) {
            try {
                $this->generateInvoiceForClient($client->id, $month);
                $generatedCount++;
            } catch (\Exception $e) {
                $errors[] = "Client {$client->id}: " . $e->getMessage();
            }
        }
        
        if ($generatedCount === 0 && empty($errors)) {
            throw new \Exception('No uninvoiced orders found for the selected month');
        }
        
        return [
            'message' => "Generated {$generatedCount} invoices successfully",
            'data' => [
                'invoices_generated' => $generatedCount,
                'errors' => $errors
            ]
        ];
    }

    /**
     * Generate unique invoice number
     */
    protected function generateUniqueInvoiceNumber(Carbon $month, int $clientId): string
    {
        $yearMonth = $month->format('Ym');
        $attempt = 1;
        
        do {
            $suffix = str_pad($clientId, 2, '0', STR_PAD_LEFT) . str_pad($attempt, 2, '0', STR_PAD_LEFT);
            $invoiceNumber = "INV-{$yearMonth}-{$suffix}";
            
            $exists = ClientInvoice::where('invoice_number', $invoiceNumber)->exists();
            
            if ($exists) {
                $attempt++;
            }
        } while ($exists && $attempt <= 99);
        
        if ($attempt > 99) {
            $invoiceNumber = "INV-{$yearMonth}-" . time() . rand(10, 99);
        }
        
        return $invoiceNumber;
    }

    /**
     * Confirm invoice and send to client
     */
    public function confirmInvoice(int $invoiceId): ClientInvoice
    {
        $invoice = ClientInvoice::findOrFail($invoiceId);
        
        if ($invoice->status !== ClientInvoice::STATUS_GENERATED) {
            throw new \Exception('Invoice can only be confirmed if it is in "generated under review" status.');
        }
        
        DB::transaction(function () use ($invoice) {
            // Update invoice status
            $invoice->update([
                'status' => ClientInvoice::STATUS_CONFIRMED,
                'client_emails' => $invoice->client->client?->billing_emails ?? [$invoice->client->email]
            ]);
            
            // Log the action
            $this->logInvoiceAction($invoice->id, 'confirmed', 
                ['status' => ClientInvoice::STATUS_CONFIRMED],
                'Invoice confirmed and sent by ' . Auth::user()->full_name);
            
            // Queue email sending
            Queue::push(new SendInvoiceEmail($invoice));
        });
        
        return $invoice;
    }

    /**
     * Mark invoice as paid
     */
    public function markInvoiceAsPaid(int $invoiceId, array $paymentData): void
    {
        $invoice = ClientInvoice::findOrFail($invoiceId);
        
        if ($invoice->status === ClientInvoice::STATUS_PAID) {
            throw new \Exception('Invoice is already marked as paid.');
        }
        
        DB::transaction(function () use ($invoice, $paymentData) {
            // Create payment receipt
            $receipt = PaymentReceipt::create([
                'invoice_id' => $invoice->id,
                'amount_paid' => $paymentData['amount_paid'],
                'payment_date' => $paymentData['payment_date'],
                'payment_method' => $paymentData['payment_method'],
                'transaction_reference' => $paymentData['transaction_reference'] ?? null,
                'status' => PaymentReceipt::STATUS_CONFIRMED,
                'notes' => $paymentData['notes'] ?? null,
                'payment_details' => [
                    'method' => $paymentData['payment_method'],
                    'recorded_by' => Auth::user()->full_name,
                    'recorded_at' => now()
                ]
            ]);
            
            // Check if fully paid
            $totalPaid = $invoice->paymentReceipts()->sum('amount_paid');
            if ($totalPaid >= $invoice->total_amount) {
                $invoice->update(['status' => ClientInvoice::STATUS_PAID]);
                
                $this->logInvoiceAction($invoice->id, 'marked_paid',
                    ['status' => ClientInvoice::STATUS_PAID],
                    'Invoice marked as paid after payment confirmation');
            }
            
            // Log payment
            $this->logInvoiceAction($invoice->id, 'payment_recorded',
                $receipt->toArray(),
                'Payment recorded via ' . $paymentData['payment_method'] . ' by ' . Auth::user()->full_name);
        });
    }

    /**
     * Suspend client account
     */
    public function suspendClient(int $clientId): void
    {
        $client = User::findOrFail($clientId);
        
        // Check if client has overdue invoices
        $overdueInvoices = ClientInvoice::where('client_id', $clientId)
            ->where('due_date', '<', now())
            ->where('status', '!=', ClientInvoice::STATUS_PAID)
            ->get();
        
        if ($overdueInvoices->count() === 0) {
            throw new \Exception('Client has no overdue payments');
        }
        
        DB::transaction(function () use ($client, $overdueInvoices) {
            $client->update(['is_active' => false]);
            
            // Log suspension for all overdue invoices
            foreach ($overdueInvoices as $invoice) {
                $this->logInvoiceAction($invoice->id, 'client_suspended', null,
                    'Client account suspended due to overdue payment by ' . Auth::user()->full_name);
            }
        });
    }

    /**
     * Reactivate client account
     */
    public function reactivateClient(int $clientId): void
    {
        $client = User::findOrFail($clientId);
        $client->update(['is_active' => true]);
    }

    /**
     * Send overdue notifications
     */
    public function sendOverdueNotifications(): array
    {
        $overdueInvoices = $this->invoiceRepository->getOverdueInvoices(100); // Get more for batch processing
        
        $notificationCount = 0;
        
        foreach ($overdueInvoices as $invoice) {
            // Queue notification
            Queue::push(new SendOverdueNotification($invoice));
            
            // Log the notification
            $this->logInvoiceAction($invoice->id, 'overdue_notification_sent', 
                ['notification_sent_at' => now()],
                'Overdue notification sent by ' . Auth::user()->full_name);
            
            $notificationCount++;
        }
        
        return ['count' => $notificationCount];
    }

    /**
     * Send client reminder
     */
    public function sendClientReminder(int $clientId): array
    {
        $client = User::findOrFail($clientId);
        
        $unpaidInvoices = ClientInvoice::where('client_id', $clientId)
            ->where('status', '!=', ClientInvoice::STATUS_PAID)
            ->get();
        
        if ($unpaidInvoices->isEmpty()) {
            throw new \Exception('No unpaid invoices found for this client.');
        }
        
        foreach ($unpaidInvoices as $invoice) {
            Queue::push(new SendInvoiceEmail($invoice, 'reminder'));
            
            $this->logInvoiceAction($invoice->id, 'reminder_sent',
                ['reminder_sent_at' => now()],
                'Payment reminder sent by ' . Auth::user()->full_name);
        }
        
        return [
            'message' => "Payment reminder sent successfully for {$unpaidInvoices->count()} unpaid invoices!"
        ];
    }

    /**
     * Resend invoice
     */
    public function resendInvoice(int $invoiceId): void
    {
        $invoice = ClientInvoice::findOrFail($invoiceId);
        
        Queue::push(new SendInvoiceEmail($invoice, 'resend'));
        
        $this->logInvoiceAction($invoice->id, 'resent',
            ['resent_at' => now()],
            'Invoice resent to client by ' . Auth::user()->full_name);
    }

    /**
     * Generate invoice PDF
     */
    public function generateInvoicePdf(int $invoiceId)
    {
        $invoice = $this->invoiceRepository->findWithAllRelations($invoiceId);
        
        if (!$invoice) {
            throw new \Exception('Invoice not found');
        }
        
        // Queue PDF generation for better performance
        Queue::push(new GenerateInvoicePDF($invoice));
        
        return response()->json([
            'success' => true,
            'message' => 'PDF generation queued. You will receive it shortly.'
        ]);
    }

    /**
     * Export clients
     */
    public function exportClients(array $filters = [])
    {
        return Excel::download(new ClientsExport($filters), 'clients_export_' . now()->format('Y_m_d_H_i_s') . '.xlsx');
    }

    /**
     * Export invoices
     */
    public function exportInvoices(array $filters = [])
    {
        return Excel::download(new InvoicesExport($filters), 'invoices_export_' . now()->format('Y_m_d_H_i_s') . '.xlsx');
    }

    /**
     * Get settings
     */
    public function getSettings(): CompanyFinancialSetting
    {
        return CompanyFinancialSetting::getSettings();
    }

    /**
     * Update settings
     */
    public function updateSettings(array $data): CompanyFinancialSetting
    {
        $settings = CompanyFinancialSetting::getSettings();
        $settings->update($data);
        
        return $settings;
    }

    /**
     * Get dashboard data
     */
    public function getDashboardData(): array
    {
        $currentMonth = now();
        
        return [
            'current_month' => [
                'invoices_generated' => ClientInvoice::whereMonth('created_at', $currentMonth->month)
                    ->whereYear('created_at', $currentMonth->year)->count(),
                'revenue' => ClientInvoice::whereMonth('created_at', $currentMonth->month)
                    ->whereYear('created_at', $currentMonth->year)
                    ->where('status', ClientInvoice::STATUS_PAID)->sum('total_amount'),
                'pending_amount' => ClientInvoice::whereMonth('created_at', $currentMonth->month)
                    ->whereYear('created_at', $currentMonth->year)
                    ->where('status', '!=', ClientInvoice::STATUS_PAID)->sum('total_amount')
            ],
            'overdue_alerts' => $this->invoiceRepository->getOverdueInvoices(10),
            'recent_payments' => PaymentReceipt::where('status', PaymentReceipt::STATUS_CONFIRMED)
                ->with(['invoice.client'])
                ->latest()
                ->limit(10)
                ->get(),
            'invoices_requiring_attention' => $this->invoiceRepository->getInvoicesRequiringAttention()
        ];
    }

    /**
     * Get accounting reports
     */
    public function getAccountingReports(Carbon $startDate, Carbon $endDate): array
    {
        return [
            'revenue_by_month' => $this->invoiceRepository->getRevenueByMonth($startDate, $endDate),
            'client_performance' => $this->invoiceRepository->getClientPerformance($startDate, $endDate),
            'payment_methods' => $this->invoiceRepository->getPaymentMethodsSummary($startDate, $endDate)
        ];
    }

    /**
     * Log invoice action
     */
    protected function logInvoiceAction(int $invoiceId, string $action, ?array $data = null, ?string $notes = null): void
    {
        InvoiceLog::create([
            'invoice_id' => $invoiceId,
            'action' => $action,
            'user_id' => Auth::id(),
            'new_data' => $data,
            'notes' => $notes
        ]);
    }
}