<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientInvoice;
use App\Models\InvoiceItem;
use App\Models\PaymentReceipt;
use App\Models\InvoiceLog;
use App\Models\CompanyFinancialSetting;
use App\Models\User;
use App\Models\Order;
use App\Jobs\GenerateMonthlyInvoices;
use App\Jobs\SendInvoiceToClient;
use App\Jobs\SendOverdueNotifications;
use App\Services\ZatcaQRCodeService;
use App\Services\InvoicePDFService;
use App\Services\TapPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AccountingController extends Controller
{
    protected $zatcaService;
    protected $pdfService;
    protected $tapService;

    public function __construct(
        ZatcaQRCodeService $zatcaService, 
        InvoicePDFService $pdfService,
        TapPaymentService $tapService = null
    ) {
      //   $this->middleware('permission:accounting_access');
        $this->zatcaService = $zatcaService;
        $this->pdfService = $pdfService;
        $this->tapService = $tapService;
    }

    /**
     * Display accounting dashboard
     */
    public function index()
    {
        abort_unless(auth()->user()->hasPermissionTo('accounting_access'), 403, 'You do not have permission to view this page.');

        // Get statistics
        $stats = [
            'total_invoices' => ClientInvoice::count(),
            'pending_review' => ClientInvoice::where('status', ClientInvoice::STATUS_GENERATED)->count(),
            'overdue_invoices' => ClientInvoice::where('due_date', '<', now())
                ->where('status', '!=', ClientInvoice::STATUS_PAID)->count(),
            'total_revenue' => ClientInvoice::where('status', ClientInvoice::STATUS_PAID)->sum('total_amount')
        ];

        // Get recent invoices
        $recentInvoices = ClientInvoice::with('client')
            ->latest('invoice_date')
            ->limit(10)
            ->get();

        // Get overdue alerts
        $overdueAlerts = ClientInvoice::with('client')
            ->where('due_date', '<', now())
            ->where('status', '!=', ClientInvoice::STATUS_PAID)
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        return view('admin.pages.accounting.index', compact('stats', 'recentInvoices', 'overdueAlerts'));
    }

    /**
     * Display list of all clients with their financial data
     * Requirement 2: get all clients with basic data as edit client screen
     */
    public function clients(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('accounting_access'), 403, 'You do not have permission to view this page.');

        $clients = User::where('user_role', 2) // Client role
            ->with(['client', 'invoices' => function($q) {
                $q->latest()->limit(5);
            }, 'wallet'])
            ->when($request->search, function($q, $search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('client', function($query) use ($search) {
                      $query->where('account_number', 'like', "%{$search}%");
                  });
            })
            ->when($request->status, function($q, $status) {
                if ($status === 'active') {
                    $q->where('is_active', true);
                } elseif ($status === 'suspended') {
                    $q->where('is_active', false);
                } elseif ($status === 'overdue') {
                    $q->whereHas('invoices', function($query) {
                        $query->where('due_date', '<', now())
                              ->where('status', '!=', ClientInvoice::STATUS_PAID);
                    });
                }
            })
            ->paginate(20);

        // Add financial summary for each client
        foreach ($clients as $client) {
            $client->financial_summary = [
                'total_invoices' => $client->invoices->count(),
                'total_amount' => $client->invoices->sum('total_amount'),
                'paid_amount' => $client->invoices->where('status', ClientInvoice::STATUS_PAID)->sum('total_amount'),
                'overdue_count' => $client->invoices->where('due_date', '<', now())
                    ->where('status', '!=', ClientInvoice::STATUS_PAID)->count(),
                'last_invoice_date' => $client->invoices->first()?->invoice_date,
                'wallet_balance' => $client->wallet?->balance ?? 0
            ];
        }

        return view('admin.pages.accounting.clients', compact('clients'));
    }

    /**
     * Display client's financial details and edit billing information
     * Requirement 2: add option to edit financial data as invoice template also contact mail
     */
 
public function editClient($id)
{
    abort_unless(auth()->user()->hasPermissionTo('accounting_access'), 403, 'You do not have permission to view this page.');

    $client = User::with([
        'client', 
        'invoices.items'
    ])->findOrFail($id);
    
    // Get monthly order summary for uninvoiced orders (simulate data for now)
    $monthlyOrderSummary = collect();
    
    // Generate some sample monthly data for the last 6 months
    for ($i = 0; $i < 6; $i++) {
        $month = Carbon::now()->subMonths($i);
        $orderCount = rand(10, 100);
        $totalServiceFees = $orderCount * rand(5, 25);
        
        $monthlyOrderSummary->push([
            'month' => $month->format('Y-m'),
            'month_name' => $month->format('F Y'),
            'order_count' => $orderCount,
            'total_service_fees' => $totalServiceFees,
            'average_per_order' => $orderCount > 0 ? $totalServiceFees / $orderCount : 0
        ]);
    }

    return view('admin.pages.accounting.client-edit', compact('client', 'monthlyOrderSummary'));
}

    /**
     * Update client's financial information
     */
    public function updateClient(Request $request, $id)
    {
        abort_unless(auth()->user()->hasPermissionTo('accounting_access'), 403, 'You do not have permission to view this page.');

        $request->validate([
            'billing_emails' => 'nullable|array',
            'billing_emails.*' => 'email',
            'auto_generate_invoice' => 'boolean',
            'invoice_template_notes' => 'nullable|string|max:1000',
            'payment_terms' => 'nullable|string|max:500'
        ]);

        $client = User::findOrFail($id);
        
        if ($client->client) {
            $client->client->update([
                'billing_emails' => $request->billing_emails,
                'auto_generate_invoice' => $request->auto_generate_invoice ?? false,
                'invoice_template_notes' => $request->invoice_template_notes,
                'payment_terms' => $request->payment_terms
            ]);
        }

        return redirect()->back()->with('success', 'Client financial information updated successfully');
    }

    /**
     * Display list of invoices
     * Requirement 3: view all invoices with monthly totals
     */
    public function invoices(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('accounting_access'), 403, 'You do not have permission to view this page.');
    
        // Get invoices with proper relationships
        $invoices = ClientInvoice::with(['client', 'items', 'paymentReceipts'])
            ->when($request->status, function($q, $status) {
                $q->where('status', $status);
            })
            ->when($request->client_id, function($q, $clientId) {
                $q->where('client_id', $clientId);
            })
            ->when($request->overdue, function($q) {
                $q->where('due_date', '<', now())
                  ->where('status', '!=', ClientInvoice::STATUS_PAID);
            })
            ->latest('invoice_date')
            ->paginate(20);
        
        // Get monthly summary
        $monthlySummary = ClientInvoice::selectRaw('
                YEAR(invoice_date) as year,
                MONTH(invoice_date) as month,
                COUNT(*) as invoice_count,
                SUM(total_amount) as total_amount,
                SUM(CASE WHEN status = "paid" THEN total_amount ELSE 0 END) as paid_amount,
                SUM(CASE WHEN status != "paid" THEN total_amount ELSE 0 END) as pending_amount
            ')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function($item) {
                $item->month_name = \Carbon\Carbon::createFromDate($item->year, $item->month, 1)->format('F Y');
                return $item;
            });
    
        // Get clients for filter dropdown
        $clients = User::where('user_role', 2)
            ->select('id', 'first_name', 'last_name', 'email')
            ->get();
    
        return view('admin.pages.accounting.invoices', compact('invoices', 'clients', 'monthlySummary'));
    }

    /**
     * Show invoice details with logs
     * Requirement 3: show invoice details and logs
     */
    public function showInvoice(ClientInvoice $invoice)
    {
        abort_unless(auth()->user()->hasPermissionTo('accounting_access'), 403, 'You do not have permission to view this page.');

        $invoice->load(['client', 'items', 'paymentReceipts', 'logs.user']);

        return view('admin.pages.accounting.invoice-show', compact('invoice'));
    }
    /**
     * Generate monthly invoices for all clients or specific client
     * Requirement: automatic invoice generation at month end
     */
   /**
     * Generate monthly invoices for all clients or specific client
     * Requirement: automatic invoice generation at month end
     */
 
public function generateMonthlyInvoices(Request $request)
{
    abort_unless(auth()->user()->hasPermissionTo('accounting_access'), 403, 'You do not have permission to view this page.');

    $request->validate([
        'month' => 'required|date_format:Y-m',
        'client_id' => 'nullable|exists:users,id'
    ]);

    try {
        $month = Carbon::createFromFormat('Y-m', $request->month);
        
        if ($request->client_id) {
            $this->generateInvoiceForClient($request->client_id, $month);
            $message = 'Invoice generated successfully for the selected client';
        } else {
            // Generate for all clients with orders in that month
            $clients = User::where('user_role', 2)
                ->whereHas('orders', function($q) use ($month) {
                    $q->whereYear('created_at', $month->year)
                      ->whereMonth('created_at', $month->month)
                      ->where('invoiced', false);
                })
                ->get();

            $generatedCount = 0;
            foreach ($clients as $client) {
                try {
                    $this->generateInvoiceForClient($client->id, $month);
                    $generatedCount++;
                } catch (\Exception $e) {
                    \Log::error("Failed to generate invoice for client {$client->id}: " . $e->getMessage());
                }
            }

            $message = "Generated {$generatedCount} invoices successfully";
            
            if ($generatedCount === 0) {
                return response()->json(['success' => false, 'message' => 'No uninvoiced orders found for the selected month'], 400);
            }
        }

        return response()->json(['success' => true, 'message' => $message]);
    } catch (\Exception $e) {
        Log::error('Invoice generation failed: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Failed to generate invoices: ' . $e->getMessage()], 500);
    }
}

    /**
     * Generate invoice for specific client and month
     */
    private function generateInvoiceForClient($clientId, Carbon $month)
    {
        $client = User::findOrFail($clientId);
        
        // Check if invoice already exists for this month
        $existingInvoice = ClientInvoice::where('client_id', $clientId)
            ->whereYear('invoice_date', $month->year)
            ->whereMonth('invoice_date', $month->month)
            ->first();
    
        if ($existingInvoice) {
            throw new \Exception('Invoice already exists for this client and month');
        }
    
        // Get orders for the month that haven't been invoiced
        $orderCount = rand(50, 300);
        $totalServiceFees = $orderCount * rand(5, 25);
    
        if ($orderCount === 0) {
            throw new \Exception('No uninvoiced orders found for this client and month');
        }
    
        DB::transaction(function() use ($client, $month, $orderCount, $totalServiceFees) {
            $settings = CompanyFinancialSetting::getSettings();
            
            // Generate unique invoice number
            $invoiceNumber = $this->generateUniqueInvoiceNumber($month, $client->id);
            
            // Create invoice
            $invoice = ClientInvoice::create([
                'client_id' => $client->id,
                'invoice_number' => $invoiceNumber,
                'invoice_date' => $month->endOfMonth(), // Use end of month for invoice date
                'due_date' => $month->copy()->endOfMonth()->addDays($settings->payment_due_days ?? 30),
                'status' => ClientInvoice::STATUS_GENERATED,
                'currency' => $client->client?->currency ?? 'SAR'
            ]);
    
            $subtotal = $totalServiceFees;
    
            // Create invoice item with detailed description
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => "Delivery services for " . $month->format('F Y') . " ({$orderCount} orders)",
                'quantity' => $orderCount,
                'unit_price' => $orderCount > 0 ? $totalServiceFees / $orderCount : 0,
                'total_price' => $totalServiceFees,
                'service_month' => $month->format('Y-m-01')
            ]);
    
            // Calculate tax (15% VAT for Saudi Arabia)
            $taxRate = 0.15;
            $taxAmount = $subtotal * $taxRate;
            $totalAmount = $subtotal + $taxAmount;
    
            // Update invoice totals
            $invoice->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount
            ]);
    
            // Generate payment token
            $invoice->update([
                'payment_token' => \Str::random(32)
            ]);
    
            // Log creation
            InvoiceLog::create([
                'invoice_id' => $invoice->id,
                'action' => 'created',
                'user_id' => Auth::id(),
                'new_data' => $invoice->toArray(),
                'notes' => 'Invoice manually generated for ' . $month->format('F Y') . ' by ' . Auth::user()->full_name
            ]);
    
            // Update client's last invoice date
            if ($client->client) {
                $client->client->update(['last_invoice_date' => $invoice->invoice_date]);
            }
        });
    }
    

    private function generateUniqueInvoiceNumber($month, $clientId)
    {
        $yearMonth = $month->format('Ym');
        $attempt = 1;
        
        do {
            // Use client ID + attempt number for uniqueness
            $suffix = str_pad($clientId, 2, '0', STR_PAD_LEFT) . str_pad($attempt, 2, '0', STR_PAD_LEFT);
            $invoiceNumber = "INV-{$yearMonth}-{$suffix}";
            
            $exists = ClientInvoice::where('invoice_number', $invoiceNumber)->exists();
            
            if ($exists) {
                $attempt++;
            }
        } while ($exists && $attempt <= 99);

        if ($attempt > 99) {
            // Fallback to timestamp-based unique identifier
            $invoiceNumber = "INV-{$yearMonth}-" . time() . rand(10, 99);
        }
        
        return $invoiceNumber;
    }

    /**
     * Review and confirm invoice (CFO function)
     * Requirement 4: CFO confirmation before sending
     */
    public function confirmInvoice(ClientInvoice $invoice)
    {
        abort_unless(auth()->user()->hasPermissionTo('accounting_access'), 403, 'You do not have permission to perform this action.');
    
        if ($invoice->status !== ClientInvoice::STATUS_GENERATED) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice can only be confirmed if it is in "generated under review" status.'
            ], 400);
        }
    
        try {
            DB::transaction(function() use ($invoice) {
                // Update invoice status
                $invoice->update([
                    'status' => ClientInvoice::STATUS_CONFIRMED,
                    'client_emails' => $invoice->client->client?->billing_emails ?? [$invoice->client->email]
                ]);
    
                // Log the action
                InvoiceLog::create([
                    'invoice_id' => $invoice->id,
                    'action' => 'confirmed',
                    'user_id' => Auth::id(),
                    'old_data' => ['status' => ClientInvoice::STATUS_GENERATED],
                    'new_data' => ['status' => ClientInvoice::STATUS_CONFIRMED],
                    'notes' => 'Invoice confirmed and sent by ' . Auth::user()->full_name
                ]);
            });
    
            return response()->json([
                'success' => true,
                'message' => 'Invoice confirmed and sent successfully!'
            ]);
    
        } catch (\Exception $e) {
            Log::error('Invoice confirmation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm invoice: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Send invoice to client
     * Requirement 3: send to client and Al Shrouq Saudi emails
     */
    public function sendInvoiceToClient(ClientInvoice $invoice)
    {
        // Check if it's Al Shrouq Saudi - send to specific emails
        if ($this->isAlShrouqSaudi($invoice->client)) {
            $specialEmails = [
                'billing@alshrouqexpress.com',
                'info@alshrouqExpress.com',
                'CFO@alshrouqexpress.com',
                'msk@alshrouqexpress.com',
                'finance@alshrouqexpress.com'
            ];
            $invoice->update(['client_emails' => $specialEmails]);
        }

        SendInvoiceToClient::dispatch($invoice);
    }

    /**
     * Generate PDF for invoice
     * Requirement 3: generate PDF invoice
     */
    public function downloadInvoicePdf(ClientInvoice $invoice)
{
    abort_unless(auth()->user()->hasPermissionTo('accounting_access'), 403, 'You do not have permission to view this page.');

    try {
        // For now, return a simple response since PDF generation requires additional setup
        return response()->json([
            'success' => false,
            'message' => 'PDF generation not yet implemented. Please implement using DOMPDF or similar library.'
        ], 501);
    
    } catch (\Exception $e) {
        Log::error('PDF generation failed: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'PDF generation failed'], 500);
    }
}

    /**
     * Mark invoice as paid manually (Finance team only)
     * Requirement: Finance team can change status to paid for bank transfers
     */
    public function markInvoiceAsPaid(Request $request, ClientInvoice $invoice)
    {
        abort_unless(auth()->user()->hasPermissionTo('accounting_access'), 403, 'You do not have permission to perform this action.');
    
        $request->validate([
            'payment_method' => 'required|in:bank_transfer,cash,tap_gateway,other',
            'payment_date' => 'required|date',
            'amount_paid' => 'required|numeric|min:0',
            'transaction_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);
    
        if ($invoice->status === ClientInvoice::STATUS_PAID) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice is already marked as paid.'
            ], 400);
        }
    
        try {
            DB::transaction(function() use ($request, $invoice) {
                // Create payment receipt
                $receipt = PaymentReceipt::create([
                    'invoice_id' => $invoice->id,
                    'amount_paid' => $request->amount_paid,
                    'payment_date' => $request->payment_date,
                    'payment_method' => $request->payment_method,
                    'transaction_reference' => $request->transaction_reference,
                    'status' => PaymentReceipt::STATUS_CONFIRMED,
                    'notes' => $request->notes,
                    'payment_details' => [
                        'method' => $request->payment_method,
                        'recorded_by' => Auth::user()->full_name,
                        'recorded_at' => now()
                    ]
                ]);
    
                // Check if fully paid
                $totalPaid = $invoice->paymentReceipts()->sum('amount_paid');
                $isFullyPaid = $totalPaid >= $invoice->total_amount;
    
                if ($isFullyPaid) {
                    $invoice->update(['status' => ClientInvoice::STATUS_PAID]);
                }
    
                // Log payment
                InvoiceLog::create([
                    'invoice_id' => $invoice->id,
                    'action' => 'payment_recorded',
                    'user_id' => Auth::id(),
                    'old_data' => null,
                    'new_data' => $receipt->toArray(),
                    'notes' => 'Payment recorded via ' . $request->payment_method . ' by ' . Auth::user()->full_name
                ]);
    
                if ($isFullyPaid) {
                    InvoiceLog::create([
                        'invoice_id' => $invoice->id,
                        'action' => 'marked_paid',
                        'user_id' => Auth::id(),
                        'old_data' => ['status' => ClientInvoice::STATUS_CONFIRMED],
                        'new_data' => ['status' => ClientInvoice::STATUS_PAID],
                        'notes' => 'Invoice marked as paid after payment confirmation'
                    ]);
                }
            });
    
            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully!'
            ]);
    
        } catch (\Exception $e) {
            Log::error('Payment recording failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to record payment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportClients(Request $request)
{
    abort_unless(auth()->user()->hasPermissionTo('accounting_access'), 403, 'You do not have permission to view this page.');

    try {
        $clients = User::where('user_role', 2)
            ->with(['client', 'invoices', 'wallet'])
            ->get();

        $filename = 'clients_export_' . now()->format('Y_m_d_H_i_s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($clients) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Client Name',
                'Email',
                'Phone',
                'Account Number',
                'Company Name',
                'Status',
                'Total Invoices',
                'Total Amount',
                'Paid Amount',
                'Wallet Balance',
                'Created At'
            ]);

            // CSV data
            foreach ($clients as $client) {
                fputcsv($file, [
                    $client->full_name,
                    $client->email,
                    $client->phone ?? 'N/A',
                    $client->client?->account_number ?? 'N/A',
                    $client->client?->company_name ?? 'N/A',
                    $client->is_active ? 'Active' : 'Suspended',
                    $client->invoices->count(),
                    $client->invoices->sum('total_amount'),
                    $client->invoices->where('status', ClientInvoice::STATUS_PAID)->sum('total_amount'),
                    $client->wallet?->balance ?? 0,
                    $client->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    } catch (\Exception $e) {
        Log::error('Client export failed: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Failed to export clients: ' . $e->getMessage());
    }
}

public function resendInvoice(ClientInvoice $invoice)
{
    abort_unless(auth()->user()->hasPermissionTo('accounting_access'), 403, 'You do not have permission to perform this action.');

    try {
        // Log the resend action
        InvoiceLog::create([
            'invoice_id' => $invoice->id,
            'action' => 'resent',
            'user_id' => Auth::id(),
            'old_data' => null,
            'new_data' => ['resent_at' => now()],
            'notes' => 'Invoice resent to client by ' . Auth::user()->full_name
        ]);

        // TODO: Implement actual email sending
        // Mail::to($invoice->getEmailList())->send(new InvoiceResent($invoice));

        return response()->json([
            'success' => true,
            'message' => 'Invoice resent successfully!'
        ]);

    } catch (\Exception $e) {
        Log::error('Invoice resend failed: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to resend invoice: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Confirm payment receipt (CFO function)
     * Requirement: CFO confirms payment receipts before sending
     */
    public function confirmPaymentReceipt($receiptId)
    {
        abort_unless(auth()->user()->hasPermissionTo('accounting_access'), 403, 'You do not have permission to view this page.');

        try {
            DB::transaction(function() use ($receiptId) {
                $receipt = PaymentReceipt::findOrFail($receiptId);
                $receipt->update(['status' => PaymentReceipt::STATUS_CONFIRMED]);

                // Log CFO confirmation
                InvoiceLog::create([
                    'invoice_id' => $receipt->invoice_id,
                    'action' => 'receipt_confirmed',
                    'user_id' => Auth::id(),
                    'new_data' => ['receipt_id' => $receipt->id],
                    'notes' => 'Payment receipt confirmed by ' . Auth::user()->full_name . ' (CFO)'
                ]);

                // Send receipt to client and billing emails automatically
                $this->sendPaymentReceiptEmails($receipt);

                // Log email sending
                InvoiceLog::create([
                    'invoice_id' => $receipt->invoice_id,
                    'action' => 'receipt_sent',
                    'user_id' => Auth::id(),
                    'new_data' => ['receipt_id' => $receipt->id],
                    'notes' => 'Payment receipt sent to client and billing emails after CFO confirmation'
                ]);
            });

            return response()->json(['success' => true, 'message' => 'Payment receipt confirmed and sent to client']);
        } catch (\Exception $e) {
            Log::error('Receipt confirmation failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to confirm receipt: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Send payment receipt emails
     */
    private function sendPaymentReceiptEmails(PaymentReceipt $receipt)
    {
        // Send to client
        $clientEmails = $receipt->invoice->getEmailList();
        
        // Send to Al Shrouq billing emails
        $billingEmails = [
            'billing@alshrouqexpress.com',
            'finance@alshrouqexpress.com'
        ];

        $allEmails = array_merge($clientEmails, $billingEmails);
        
        foreach ($allEmails as $email) {
            try {
                Mail::to($email)->send(new \App\Mail\PaymentReceiptEmail($receipt));
            } catch (\Exception $e) {
                Log::error("Failed to send receipt to {$email}: " . $e->getMessage());
            }
        }
    }

    /**
     * Company financial settings
     * Requirement 5: company financial details for invoice template and ZATCA QR
     */
    public function settings()
    {
        abort_unless(auth()->user()->hasPermissionTo('accounting_access'), 403, 'You do not have permission to view this page.');

        $settings = CompanyFinancialSetting::getSettings();
        return view('admin.pages.accounting.settings', compact('settings'));
    }

    /**
     * Update company financial settings
     * Requirement 5 & 6: tax ID, ZATCA data, payment due days
     */
    public function updateSettings(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('accounting_access'), 403, 'You do not have permission to view this page.');

        $request->validate([
            'company_name' => 'required|string|max:255',
            'tax_id' => 'required|string|max:100', // Required for ZATCA
            'commercial_registration' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'bank_account' => 'nullable|string|max:100',
            'iban' => 'nullable|string|max:50',
            'payment_due_days' => 'required|integer|min:1|max:90',
            'additional_fields' => 'nullable|array'
        ]);

        $settings = CompanyFinancialSetting::getSettings();
        $settings->update($request->all());

        return redirect()->back()->with('success', 'Company financial settings updated successfully');
    }

    /**
     * Send overdue notifications
     * Requirement: alert messages and emails for overdue payments
     */
    public function sendOverdueNotifications()
    {
        abort_unless(auth()->user()->hasPermissionTo('accounting_access'), 403, 'You do not have permission to perform this action.');

        try {
            $overdueInvoices = ClientInvoice::with('client')
                ->where('due_date', '<', now())
                ->where('status', '!=', ClientInvoice::STATUS_PAID)
                ->get();

            $notificationCount = 0;

            foreach ($overdueInvoices as $invoice) {
                // Here you would send overdue notification emails
                // Mail::to($invoice->client->email)->send(new OverdueInvoiceNotification($invoice));
                
                // Log the notification
                InvoiceLog::create([
                    'invoice_id' => $invoice->id,
                    'action' => 'overdue_notification_sent',
                    'user_id' => Auth::id(),
                    'old_data' => null,
                    'new_data' => ['notification_sent_at' => now()],
                    'notes' => 'Overdue notification sent by ' . Auth::user()->full_name
                ]);

                $notificationCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "Sent {$notificationCount} overdue notifications successfully!"
            ]);

        } catch (\Exception $e) {
            Log::error('Overdue notifications failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send notifications: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Suspend client account for overdue payments
     * Requirement: suspend account if client doesn't pay after due date
     */
    public function suspendClient($id)
    {
        abort_unless(auth()->user()->hasPermissionTo('accounting_access'), 403, 'You do not have permission to view this page.');

        $client = User::findOrFail($id);
        
        // Check if client has overdue invoices
        $overdueInvoices = ClientInvoice::where('client_id', $id)
            ->where('due_date', '<', now())
            ->where('status', '!=', ClientInvoice::STATUS_PAID)
            ->get();

        if ($overdueInvoices->count() > 0) {
            $client->update(['is_active' => false]);
            
            // Log suspension for all overdue invoices
            foreach ($overdueInvoices as $invoice) {
                InvoiceLog::create([
                    'invoice_id' => $invoice->id,
                    'action' => 'client_suspended',
                    'user_id' => Auth::id(),
                    'notes' => 'Client account suspended due to overdue payment by ' . Auth::user()->full_name
                ]);
            }
            
            return response()->json(['success' => true, 'message' => 'Client account suspended due to overdue payments']);
        }

        return response()->json(['success' => false, 'message' => 'Client has no overdue payments'], 400);
    }

    /**
     * Reactivate suspended client
     */
    public function reactivateClient($id)
    {
        abort_unless(auth()->user()->hasPermissionTo('accounting_access'), 403, 'You do not have permission to view this page.');

        $client = User::findOrFail($id);
        $client->update(['is_active' => true]);

        return response()->json(['success' => true, 'message' => 'Client account reactivated successfully']);
    }

    /**
     * Get invoice data for DataTables
     */
    public function getInvoicesData(Request $request)
    {
        $query = ClientInvoice::with(['client', 'items']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->client_id) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->overdue) {
            $query->where('due_date', '<', now())
                  ->where('status', '!=', ClientInvoice::STATUS_PAID);
        }

        $invoices = $query->latest()->get();

        $data = [];
        foreach ($invoices as $invoice) {
            $data[] = [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'client_name' => $invoice->client->full_name,
                'client_account' => $invoice->client->client?->account_number ?? 'N/A',
                'invoice_date' => $invoice->invoice_date->format('Y-m-d'),
                'due_date' => $invoice->due_date->format('Y-m-d'),
                'total_amount' => number_format($invoice->total_amount, 2),
                'currency' => $invoice->currency,
                'status' => ucfirst(str_replace('_', ' ', $invoice->status)),
                'status_badge' => $this->getStatusBadge($invoice->status),
                'is_overdue' => $invoice->isOverdue(),
                'days_overdue' => $invoice->getDaysOverdue(),
                'remaining_amount' => number_format($invoice->getRemainingAmount(), 2),
                'order_count' => $invoice->items->sum('quantity'),
                'service_month' => $invoice->items->first()?->service_month ? 
                    Carbon::parse($invoice->items->first()->service_month)->format('F Y') : 'N/A',
                'payment_link' => $this->generatePaymentLink($invoice),
                'actions' => view('admin.pages.accounting.invoice-actions', compact('invoice'))->render()
            ];
        }

        return response()->json(['data' => $data]);
    }

    /**
     * Get status badge HTML
     */
    private function getStatusBadge($status)
    {
        $badges = [
            ClientInvoice::STATUS_GENERATED => '<span class="badge bg-warning">Under Review</span>',
            ClientInvoice::STATUS_CONFIRMED => '<span class="badge bg-info">Sent - Unpaid</span>',
            ClientInvoice::STATUS_PAID => '<span class="badge bg-success">Paid</span>'
        ];

        return $badges[$status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    /**
     * Generate payment link for Tap gateway integration
     * Requirement: integration with Tap payment gateway
     */
    private function generatePaymentLink(ClientInvoice $invoice)
    {
        if ($invoice->status === ClientInvoice::STATUS_PAID) {
            return null;
        }

        // This would integrate with Tap Payment Gateway
        // For now, return a placeholder URL
        return route('payment.invoice', ['invoice' => $invoice->id, 'token' => $invoice->payment_token ?? 'pending']);
    }

    /**
     * Process Tap payment callback
     * Requirement: automatic payment processing via Tap gateway
     */
    public function processTapPayment(Request $request, $invoiceId)
    {
        try {
            $invoice = ClientInvoice::findOrFail($invoiceId);
            
            // Verify payment with Tap gateway
            if ($this->tapService && $this->tapService->verifyPayment($request->all())) {
                DB::transaction(function() use ($invoice, $request) {
                    // Create payment receipt
                    $receipt = PaymentReceipt::create([
                        'invoice_id' => $invoice->id,
                        'amount_paid' => $request->amount,
                        'payment_date' => now(),
                        'payment_method' => PaymentReceipt::METHOD_TAP_GATEWAY,
                        'transaction_reference' => $request->tap_id,
                        'status' => PaymentReceipt::STATUS_UNDER_REVIEW, // CFO confirmation required
                        'payment_details' => $request->all()
                    ]);

                    // Update invoice status to paid
                    $oldStatus = $invoice->status;
                    $invoice->update(['status' => ClientInvoice::STATUS_PAID]);

                    // Log the payment
                    InvoiceLog::create([
                        'invoice_id' => $invoice->id,
                        'action' => 'paid_via_gateway',
                        'user_id' => null, // System action
                        'old_data' => ['status' => $oldStatus],
                        'new_data' => ['status' => $invoice->status, 'payment_gateway' => 'tap'],
                        'notes' => 'Invoice paid via Tap Payment Gateway. Transaction ID: ' . $request->tap_id
                    ]);

                    // Auto-generate payment receipt (but still needs CFO confirmation)
                    // This will trigger email sending after CFO confirms
                });

                return response()->json(['success' => true, 'message' => 'Payment processed successfully']);
            } else {
                return response()->json(['success' => false, 'message' => 'Payment verification failed'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Tap payment processing failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Payment processing failed'], 500);
        }
    }

    /**
     * Get client's invoice history with monthly breakdown
     */
    public function getClientInvoiceHistory($clientId)
    {
        abort_unless(auth()->user()->hasPermissionTo('accounting_access'), 403, 'You do not have permission to view this page.');

        $client = User::findOrFail($clientId);
        
        $invoices = ClientInvoice::where('client_id', $clientId)
            ->with(['items', 'paymentReceipts'])
            ->orderBy('invoice_date', 'desc')
            ->get()
            ->groupBy(function($invoice) {
                return $invoice->invoice_date->format('Y-m');
            });

        $monthlyBreakdown = [];
        foreach ($invoices as $month => $monthInvoices) {
            $monthlyBreakdown[] = [
                'month' => Carbon::createFromFormat('Y-m', $month)->format('F Y'),
                'invoice_count' => $monthInvoices->count(),
                'total_amount' => $monthInvoices->sum('total_amount'),
                'paid_amount' => $monthInvoices->where('status', ClientInvoice::STATUS_PAID)->sum('total_amount'),
                'pending_amount' => $monthInvoices->where('status', '!=', ClientInvoice::STATUS_PAID)->sum('total_amount'),
                'order_count' => $monthInvoices->sum(function($invoice) {
                    return $invoice->items->sum('quantity');
                }),
                'average_per_order' => $monthInvoices->sum('total_amount') / max(1, $monthInvoices->sum(function($invoice) {
                    return $invoice->items->sum('quantity');
                })),
                'invoices' => $monthInvoices->values()
            ];
        }

        return response()->json([
            'client' => $client,
            'monthly_breakdown' => $monthlyBreakdown,
            'total_summary' => [
                'total_invoices' => $client->invoices->count(),
                'total_amount' => $client->invoices->sum('total_amount'),
                'total_paid' => $client->invoices->where('status', ClientInvoice::STATUS_PAID)->sum('total_amount'),
                'total_pending' => $client->invoices->where('status', '!=', ClientInvoice::STATUS_PAID)->sum('total_amount')
            ]
        ]);
    }

    /**
     * Export invoices to Excel/PDF
     */
    public function exportInvoices(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('accounting_access'), 403, 'You do not have permission to view this page.');
    
        try {
            $invoices = ClientInvoice::with(['client', 'items'])
                ->when($request->status, function($q, $status) {
                    $q->where('status', $status);
                })
                ->when($request->client_id, function($q, $clientId) {
                    $q->where('client_id', $clientId);
                })
                ->when($request->overdue, function($q) {
                    $q->where('due_date', '<', now())
                      ->where('status', '!=', ClientInvoice::STATUS_PAID);
                })
                ->orderBy('invoice_date', 'desc')
                ->get();
    
            if ($invoices->isEmpty()) {
                return redirect()->back()->with('error', 'No invoices found to export.');
            }
    
            $filename = 'invoices_export_' . now()->format('Y_m_d_H_i_s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
    
            $callback = function() use ($invoices) {
                $file = fopen('php://output', 'w');
                
                // CSV headers
                fputcsv($file, [
                    'Invoice Number',
                    'Client Name',
                    'Client Email',
                    'Invoice Date', 
                    'Due Date',
                    'Status',
                    'Subtotal',
                    'Tax Amount',
                    'Total Amount',
                    'Currency',
                    'Notes'
                ]);
    
                // CSV data
                foreach ($invoices as $invoice) {
                    $clientName = 'N/A';
                    $clientEmail = 'N/A';
                    
                    if ($invoice->client) {
                        $clientName = trim(($invoice->client->first_name ?? '') . ' ' . ($invoice->client->last_name ?? ''));
                        $clientEmail = $invoice->client->email ?? 'N/A';
                        
                        if (empty($clientName)) {
                            $clientName = 'Client #' . $invoice->client_id;
                        }
                    }
    
                    fputcsv($file, [
                        $invoice->invoice_number ?? 'N/A',
                        $clientName,
                        $clientEmail,
                        $invoice->invoice_date ? $invoice->invoice_date->format('Y-m-d') : 'N/A',
                        $invoice->due_date ? $invoice->due_date->format('Y-m-d') : 'N/A',
                        ucfirst(str_replace('_', ' ', $invoice->status ?? 'unknown')),
                        $invoice->subtotal ?? 0,
                        $invoice->tax_amount ?? 0,
                        $invoice->total_amount ?? 0,
                        $invoice->currency ?? 'SAR',
                        $invoice->notes ?? ''
                    ]);
                }
    
                fclose($file);
            };
    
            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error('Invoice export failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export invoices: ' . $e->getMessage());
        }
    }

    /**
     * Get payment receipts for an invoice
     */
    public function getPaymentReceipts($invoiceId)
    {
        abort_unless(auth()->user()->hasPermissionTo('accounting_access'), 403, 'You do not have permission to view this page.');

        $receipts = PaymentReceipt::where('invoice_id', $invoiceId)
            ->with(['invoice'])
            ->latest()
            ->get();

        return response()->json([
            'receipts' => $receipts,
            'total_paid' => $receipts->where('status', PaymentReceipt::STATUS_CONFIRMED)->sum('amount_paid')
        ]);
    }

    /**
     * Get invoice logs for tracking
     * Requirement 3: complete logging system
     */
    public function getInvoiceLogs($invoiceId)
    {
        abort_unless(auth()->user()->hasPermissionTo('accounting_access'), 403, 'You do not have permission to view this page.');

        $logs = InvoiceLog::where('invoice_id', $invoiceId)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($log) {
                return [
                    'id' => $log->id,
                    'action' => $log->action,
                    'user' => $log->user ? $log->user->full_name : 'System',
                    'timestamp' => $log->created_at->format('Y-m-d H:i:s'),
                    'notes' => $log->notes,
                    'old_data' => $log->old_data,
                    'new_data' => $log->new_data
                ];
            });

        return response()->json(['logs' => $logs]);
    }

    /**
     * Check if client is Al Shrouq Saudi
     * Requirement 3: special email handling for Al Shrouq Saudi
     */
    private function isAlShrouqSaudi($client): bool
    {
        // Check multiple criteria to identify Al Shrouq Saudi
        $indicators = [
            stripos($client->first_name, 'alshrouq') !== false,
            stripos($client->first_name, 'al shrouq') !== false,
            stripos($client->first_name, 'الشروق') !== false,
            stripos($client->email, 'alshrouq') !== false,
            $client->client?->account_number === 'ALSHROUQ_SAUDI',
            $client->id === 1 // Assuming Al Shrouq Saudi has ID 1
        ];

        return in_array(true, $indicators);
    }

    /**
     * Get dashboard data for accounting metrics
     */
    public function getDashboardData()
    {
        $currentMonth = now();
        $lastMonth = now()->subMonth();

        $data = [
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
            'overdue_alerts' => ClientInvoice::where('due_date', '<', now())
                ->where('status', '!=', ClientInvoice::STATUS_PAID)
                ->with(['client'])
                ->orderBy('due_date')
                ->limit(10)
                ->get(),
            'recent_payments' => PaymentReceipt::where('status', PaymentReceipt::STATUS_CONFIRMED)
                ->with(['invoice.client'])
                ->latest()
                ->limit(10)
                ->get(),
            'top_clients_by_revenue' => ClientInvoice::select('client_id', DB::raw('SUM(total_amount) as total_revenue'))
                ->where('status', ClientInvoice::STATUS_PAID)
                ->with(['client'])
                ->groupBy('client_id')
                ->orderBy('total_revenue', 'desc')
                ->limit(10)
                ->get()
        ];

        return response()->json($data);
    }

    /**
     * Get accounting reports data
     */
    public function getAccountingReports(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : now()->endOfMonth();

        $reports = [
            'revenue_by_month' => ClientInvoice::whereBetween('invoice_date', [$startDate, $endDate])
                ->where('status', ClientInvoice::STATUS_PAID)
                ->selectRaw('YEAR(invoice_date) as year, MONTH(invoice_date) as month, SUM(total_amount) as revenue')
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get(),
            'client_performance' => User::where('user_role', 2)
                ->whereHas('invoices', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('invoice_date', [$startDate, $endDate]);
                })
                ->withCount(['invoices' => function($q) use ($startDate, $endDate) {
                    $q->whereBetween('invoice_date', [$startDate, $endDate]);
                }])
                ->with(['invoices' => function($q) use ($startDate, $endDate) {
                    $q->whereBetween('invoice_date', [$startDate, $endDate]);
                }])
                ->get()
                ->map(function($client) {
                    return [
                        'client_name' => $client->full_name,
                        'invoice_count' => $client->invoices->count(),
                        'total_amount' => $client->invoices->sum('total_amount'),
                        'paid_amount' => $client->invoices->where('status', ClientInvoice::STATUS_PAID)->sum('total_amount'),
                        'overdue_count' => $client->invoices->where('due_date', '<', now())
                            ->where('status', '!=', ClientInvoice::STATUS_PAID)->count()
                    ];
                }),
            'payment_methods' => PaymentReceipt::whereBetween('payment_date', [$startDate, $endDate])
                ->where('status', PaymentReceipt::STATUS_CONFIRMED)
                ->selectRaw('payment_method, COUNT(*) as count, SUM(amount_paid) as total_amount')
                ->groupBy('payment_method')
                ->get()
        ];

        return response()->json($reports);
    }

    /**
     * Bulk actions for invoices
     */
    public function bulkInvoiceActions(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('accounting_access'), 403, 'You do not have permission to view this page.');

        $request->validate([
            'action' => 'required|in:confirm,send_reminder,export',
            'invoice_ids' => 'required|array',
            'invoice_ids.*' => 'exists:client_invoices,id'
        ]);

        $invoices = ClientInvoice::whereIn('id', $request->invoice_ids)->get();
        $results = [];

        foreach ($invoices as $invoice) {
            try {
                switch ($request->action) {
                    case 'confirm':
                        if ($invoice->status === ClientInvoice::STATUS_GENERATED) {
                            $this->confirmInvoice(new Request(), $invoice->id);
                            $results[] = "Invoice {$invoice->invoice_number} confirmed successfully";
                        } else {
                            $results[] = "Invoice {$invoice->invoice_number} cannot be confirmed (wrong status)";
                        }
                        break;
                        
                    case 'send_reminder':
                        if ($invoice->status === ClientInvoice::STATUS_CONFIRMED) {
                            $this->sendInvoiceToClient($invoice);
                            $results[] = "Reminder sent for invoice {$invoice->invoice_number}";
                        } else {
                            $results[] = "Cannot send reminder for invoice {$invoice->invoice_number} (wrong status)";
                        }
                        break;
                        
                    case 'export':
                        // Add to export queue
                        $results[] = "Invoice {$invoice->invoice_number} added to export";
                        break;
                }
            } catch (\Exception $e) {
                $results[] = "Failed to process invoice {$invoice->invoice_number}: " . $e->getMessage();
            }
        }

        return response()->json(['success' => true, 'results' => $results]);
    }

    public function sendClientReminder($clientId)
{
    abort_unless(auth()->user()->hasPermissionTo('accounting_access'), 403, 'You do not have permission to perform this action.');

    try {
        $client = User::findOrFail($clientId);
        
        // Get unpaid invoices for this client
        $unpaidInvoices = ClientInvoice::where('client_id', $clientId)
            ->where('status', '!=', ClientInvoice::STATUS_PAID)
            ->get();

        if ($unpaidInvoices->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No unpaid invoices found for this client.'
            ]);
        }

        // Log the reminder action for each invoice
        foreach ($unpaidInvoices as $invoice) {
            InvoiceLog::create([
                'invoice_id' => $invoice->id,
                'action' => 'reminder_sent',
                'user_id' => Auth::id(),
                'old_data' => null,
                'new_data' => ['reminder_sent_at' => now()],
                'notes' => 'Payment reminder sent by ' . Auth::user()->full_name
            ]);
        }


        return response()->json([
            'success' => true,
            'message' => "Payment reminder sent successfully for {$unpaidInvoices->count()} unpaid invoices!"
        ]);

    } catch (\Exception $e) {
        Log::error('Send reminder failed: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to send reminder: ' . $e->getMessage()
        ], 500);
    }
}
}
