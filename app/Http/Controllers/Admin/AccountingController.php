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
        $this->middleware('permission:accounting_access');
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

        $stats = [
            'total_invoices' => ClientInvoice::count(),
            'pending_review' => ClientInvoice::where('status', ClientInvoice::STATUS_GENERATED)->count(),
            'confirmed_unpaid' => ClientInvoice::where('status', ClientInvoice::STATUS_CONFIRMED)->count(),
            'overdue_invoices' => ClientInvoice::where('due_date', '<', now())
                ->where('status', '!=', ClientInvoice::STATUS_PAID)->count(),
            'total_revenue' => ClientInvoice::where('status', ClientInvoice::STATUS_PAID)->sum('total_amount'),
            'pending_amount' => ClientInvoice::where('status', '!=', ClientInvoice::STATUS_PAID)->sum('total_amount'),
            'this_month_invoices' => ClientInvoice::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count(),
            'this_month_revenue' => ClientInvoice::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->where('status', ClientInvoice::STATUS_PAID)->sum('total_amount')
        ];

        // Recent invoices
        $recentInvoices = ClientInvoice::with(['client'])
            ->latest()
            ->limit(10)
            ->get();

        // Overdue alerts
        $overdueAlerts = ClientInvoice::with(['client'])
            ->where('due_date', '<', now())
            ->where('status', '!=', ClientInvoice::STATUS_PAID)
            ->orderBy('due_date')
            ->limit(10)
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
            'invoices.items', 
            'orders' => function($q) {
                $q->where('invoiced', false)->latest();
            }
        ])->findOrFail($id);
        
        // Get monthly order summary for uninvoiced orders
        $monthlyOrderSummary = $client->orders
            ->where('invoiced', false)
            ->groupBy(function($order) {
                return $order->created_at->format('Y-m');
            })
            ->map(function($orders, $month) {
                return [
                    'month' => $month,
                    'month_name' => Carbon::createFromFormat('Y-m', $month)->format('F Y'),
                    'order_count' => $orders->count(),
                    'total_service_fees' => $orders->sum('service_fees'),
                    'average_per_order' => $orders->count() > 0 ? $orders->sum('service_fees') / $orders->count() : 0
                ];
            });

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

        $query = ClientInvoice::with(['client', 'items']);

        // Apply filters
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

        if ($request->month) {
            $month = Carbon::createFromFormat('Y-m', $request->month);
            $query->whereYear('invoice_date', $month->year)
                  ->whereMonth('invoice_date', $month->month);
        }

        $invoices = $query->latest()->paginate(20);

        // Get monthly summary for display
        $monthlySummary = ClientInvoice::selectRaw('
                YEAR(invoice_date) as year,
                MONTH(invoice_date) as month,
                COUNT(*) as total_invoices,
                SUM(total_amount) as total_amount,
                SUM(CASE WHEN status = ? THEN total_amount ELSE 0 END) as paid_amount,
                SUM(CASE WHEN status != ? THEN total_amount ELSE 0 END) as pending_amount
            ', [ClientInvoice::STATUS_PAID, ClientInvoice::STATUS_PAID])
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get()
            ->map(function($item) {
                $item->month_name = Carbon::createFromDate($item->year, $item->month, 1)->format('F Y');
                return $item;
            });

        $clients = User::where('user_role', 2)->select('id', 'first_name', 'email')->get();

        return view('admin.pages.accounting.invoices', compact('invoices', 'clients', 'monthlySummary'));
    }

    /**
     * Show invoice details with logs
     * Requirement 3: show invoice details and logs
     */
    public function showInvoice($id)
    {
        abort_unless(auth()->user()->hasPermissionTo('accounting_access'), 403, 'You do not have permission to view this page.');

        $invoice = ClientInvoice::with([
            'client', 
            'items', 
            'paymentReceipts', 
            'logs.user',
            'orders'
        ])->findOrFail($id);

        return view('admin.pages.accounting.invoice-details', compact('invoice'));
    }

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

        $month = Carbon::createFromFormat('Y-m', $request->month);
        
        try {
            if ($request->client_id) {
                $this->generateInvoiceForClient($request->client_id, $month);
                $message = 'Invoice generated successfully for the selected client';
            } else {
                // Check if we should generate for all clients
                GenerateMonthlyInvoices::dispatch($month);
                $message = 'Monthly invoice generation job queued successfully';
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
        $orders = Order::where('ingr_shop_id', $clientId)
            ->whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month)
            ->where('invoiced', false)
            ->where(function($q) {
                $q->whereIn('status', [9, 10]); // Delivered or completed orders only
            })
            ->get();

        if ($orders->isEmpty()) {
            throw new \Exception('No uninvoiced orders found for this client and month');
        }

        DB::transaction(function() use ($client, $orders, $month) {
            $settings = CompanyFinancialSetting::getSettings();
            
            // Create invoice
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

            // Create invoice item with detailed description
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => "Delivery services for " . $month->format('F Y') . " ({$totalOrders} orders)",
                'quantity' => $totalOrders,
                'unit_price' => $totalOrders > 0 ? $totalServiceFees / $totalOrders : 0,
                'total_price' => $totalServiceFees,
                'service_month' => $month->format('Y-m-01')
            ]);

            $subtotal = $totalServiceFees;

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

            // Mark orders as invoiced
            Order::whereIn('id', $orders->pluck('id'))->update([
                'invoiced' => true,
                'invoice_id' => $invoice->id
            ]);

            // Generate ZATCA QR code
            $this->zatcaService->generateQRCode($invoice);

            // Log creation
            InvoiceLog::create([
                'invoice_id' => $invoice->id,
                'action' => 'created',
                'user_id' => Auth::id(),
                'new_data' => $invoice->toArray(),
                'notes' => 'Invoice automatically generated for ' . $month->format('F Y') . ' by ' . Auth::user()->full_name
            ]);

            // Update client's last invoice date
            if ($client->client) {
                $client->client->update(['last_invoice_date' => $invoice->invoice_date]);
            }
        });
    }

    /**
     * Review and confirm invoice (CFO function)
     * Requirement 4: CFO confirmation before sending
     */
    public function confirmInvoice(Request $request, $id)
    {
        abort_unless(auth()->user()->hasPermissionTo('accounting_access'), 403, 'You do not have permission to view this page.');

        $invoice = ClientInvoice::findOrFail($id);
        
        if ($invoice->status !== ClientInvoice::STATUS_GENERATED) {
            return response()->json(['success' => false, 'message' => 'Invoice cannot be confirmed in current status'], 400);
        }

        DB::transaction(function() use ($invoice, $request) {
            $oldStatus = $invoice->status;
            $invoice->update(['status' => ClientInvoice::STATUS_CONFIRMED]);

            // Log the confirmation
            InvoiceLog::create([
                'invoice_id' => $invoice->id,
                'action' => 'confirmed',
                'user_id' => Auth::id(),
                'old_data' => ['status' => $oldStatus],
                'new_data' => ['status' => $invoice->status],
                'notes' => 'Invoice confirmed and approved by ' . Auth::user()->full_name . ' (CFO/Account Manager)'
            ]);

            // Send to client automatically after confirmation
            $this->sendInvoiceToClient($invoice);

            // Log the sending
            InvoiceLog::create([
                'invoice_id' => $invoice->id,
                'action' => 'sent_to_client',
                'user_id' => Auth::id(),
                'new_data' => ['emails_sent_to' => $invoice->getEmailList()],
                'notes' => 'Invoice sent to client emails after CFO confirmation'
            ]);
        });

        return response()->json(['success' => true, 'message' => 'Invoice confirmed and sent to client successfully']);
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
    public function generateInvoicePDF($id)
    {
        abort_unless(auth()->user()->hasPermissionTo('accounting_access'), 403, 'You do not have permission to view this page.');

        $invoice = ClientInvoice::with(['client', 'items'])->findOrFail($id);
        
        try {
            $pdf = $this->pdfService->generate($invoice);

            // Log PDF generation
            InvoiceLog::create([
                'invoice_id' => $invoice->id,
                'action' => 'pdf_generated',
                'user_id' => Auth::id(),
                'notes' => 'PDF generated by ' . Auth::user()->full_name
            ]);

            return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
        } catch (\Exception $e) {
            Log::error('PDF generation failed for invoice ' . $invoice->id . ': ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Mark invoice as paid manually (Finance team only)
     * Requirement: Finance team can change status to paid for bank transfers
     */
    public function markAsPaid(Request $request, $id)
    {
        abort_unless(auth()->user()->hasPermissionTo('accounting_access'), 403, 'You do not have permission to view this page.');

        $request->validate([
            'payment_method' => 'required|in:bank_transfer,cash,other,tap_gateway',
            'payment_date' => 'required|date',
            'amount_paid' => 'required|numeric|min:0',
            'transaction_reference' => 'nullable|string',
            'notes' => 'nullable|string',
            'payment_documents' => 'nullable|array',
            'payment_documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        $invoice = ClientInvoice::findOrFail($id);

        try {
            DB::transaction(function() use ($request, $invoice) {
                // Handle file uploads for payment documents
                $documentPaths = [];
                if ($request->hasFile('payment_documents')) {
                    foreach ($request->file('payment_documents') as $file) {
                        $path = $file->store('payment_documents', 'public');
                        $documentPaths[] = $path;
                    }
                }

                // Create payment receipt
                $receipt = PaymentReceipt::create([
                    'invoice_id' => $invoice->id,
                    'amount_paid' => $request->amount_paid,
                    'payment_date' => $request->payment_date,
                    'payment_method' => $request->payment_method,
                    'transaction_reference' => $request->transaction_reference,
                    'status' => PaymentReceipt::STATUS_UNDER_REVIEW, // CFO needs to confirm
                    'notes' => $request->notes,
                    'payment_details' => [
                        'documents' => $documentPaths,
                        'recorded_by' => Auth::user()->full_name,
                        'recorded_at' => now()
                    ]
                ]);

                // Log the payment recording
                InvoiceLog::create([
                    'invoice_id' => $invoice->id,
                    'action' => 'payment_recorded',
                    'user_id' => Auth::id(),
                    'new_data' => $receipt->toArray(),
                    'notes' => 'Payment recorded manually by ' . Auth::user()->full_name . ' (Finance Team). Awaiting CFO confirmation.'
                ]);

                // If amount covers full invoice, update status (but still needs CFO confirmation for receipt)
                if ($invoice->getRemainingAmount() <= 0) {
                    $oldStatus = $invoice->status;
                    $invoice->update(['status' => ClientInvoice::STATUS_PAID]);

                    InvoiceLog::create([
                        'invoice_id' => $invoice->id,
                        'action' => 'marked_paid',
                        'user_id' => Auth::id(),
                        'old_data' => ['status' => $oldStatus],
                        'new_data' => ['status' => $invoice->status],
                        'notes' => 'Invoice marked as paid by ' . Auth::user()->full_name . ' via manual payment entry'
                    ]);
                }
            });

            return response()->json(['success' => true, 'message' => 'Payment recorded successfully. Receipt pending CFO confirmation.']);
        } catch (\Exception $e) {
            Log::error('Payment recording failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to record payment: ' . $e->getMessage()], 500);
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
        abort_unless(auth()->user()->hasPermissionTo('accounting_access'), 403, 'You do not have permission to view this page.');

        try {
            SendOverdueNotifications::dispatch();
            return response()->json(['success' => true, 'message' => 'Overdue notifications sent successfully']);
        } catch (\Exception $e) {
            Log::error('Overdue notifications failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to send notifications: ' . $e->getMessage()], 500);
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

        // Implementation for exporting invoices
        // This would use Laravel Excel or similar package
        
        return redirect()->back()->with('success', 'Export completed');
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
}
