<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AccountingService;
use App\Repositories\ClientRepository;
use App\Repositories\InvoiceRepository;
use App\Http\Requests\Accounting\ClientUpdateRequest;
use App\Http\Requests\Accounting\InvoiceGenerateRequest;
use App\Http\Requests\Accounting\MarkAsPaidRequest;
use App\Http\Resources\ClientResource;
use App\Http\Resources\InvoiceResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AccountingController extends Controller
{
    protected $accountingService;
    protected $clientRepository;
    protected $invoiceRepository;

    public function __construct(
        AccountingService $accountingService,
        ClientRepository $clientRepository,
        InvoiceRepository $invoiceRepository
    ) {
        $this->middleware(\App\Http\Middleware\AccountingAccessMiddleware::class);
        $this->accountingService = $accountingService;
        $this->clientRepository = $clientRepository;
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * Display accounting dashboard with cached statistics
     */
    public function index()
    {
        $stats = Cache::remember('accounting_dashboard_stats', 300, function () {
            return $this->accountingService->getDashboardStats();
        });

        $recentInvoices = $this->invoiceRepository->getRecentInvoices(10);
        $overdueAlerts = $this->invoiceRepository->getOverdueInvoices(5);

        return view('admin.pages.accounting.index', compact('stats', 'recentInvoices', 'overdueAlerts'));
    }

    /**
     * Display clients with optimized pagination and filtering
     */
    public function clients(Request $request)
    {
        $filters = $request->only(['search', 'status']);
        $clients = $this->clientRepository->getClientsWithFinancialData($filters);

        return view('admin.pages.accounting.clients', compact('clients'));
    }

    /**
     * Display client edit form with optimized data loading
     */
    public function editClient($id)
    {
        $client = $this->clientRepository->findWithRelations($id, [
            'client',
            'invoices' => function ($q) {
                $q->with('items')->latest()->limit(10);
            }
        ]);

        if (!$client) {
            abort(404, 'Client not found');
        }

        $monthlyOrderSummary = $this->accountingService->getMonthlyOrderSummary($id);

        return view('admin.pages.accounting.client-edit', compact('client', 'monthlyOrderSummary'));
    }

    /**
     * Update client financial information
     */
    public function updateClient(ClientUpdateRequest $request, $id)
    {
        $client = $this->clientRepository->updateFinancialData($id, $request->validated());

        return redirect()->back()->with('success', 'Client financial information updated successfully');
    }

    /**
     * Display invoices with optimized queries and caching
     */
    public function invoices(Request $request)
    {
        $filters = $request->only(['status', 'client_id', 'overdue']);
        
        // Use cursor pagination for better performance with large datasets
        $invoices = $this->invoiceRepository->getInvoicesWithFilters($filters);
        
        $monthlySummary = Cache::remember(
            'monthly_summary_' . md5(serialize($filters)),
            600,
            fn() => $this->invoiceRepository->getMonthlySummary($filters)
        );

        $clients = Cache::remember('clients_dropdown', 1800, function () {
            return $this->clientRepository->getClientsForDropdown();
        });

        return view('admin.pages.accounting.invoices', compact('invoices', 'clients', 'monthlySummary'));
    }

    /**
     * Show invoice details with minimal queries
     */
    public function showInvoice($id)
    {
        $invoice = $this->invoiceRepository->findWithAllRelations($id);

        if (!$invoice) {
            abort(404, 'Invoice not found');
        }

        return view('admin.pages.accounting.invoice-show', compact('invoice'));
    }

    /**
     * Generate monthly invoices with batch processing
     */
    public function generateMonthlyInvoices(InvoiceGenerateRequest $request)
    {
        try {
            $result = $this->accountingService->generateMonthlyInvoices(
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => $result['data']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate invoices: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirm invoice with transaction safety
     */
    public function confirmInvoice($id)
    {
        try {
            $invoice = $this->accountingService->confirmInvoice($id);

            return response()->json([
                'success' => true,
                'message' => 'Invoice confirmed and sent successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Mark invoice as paid with proper validation
     */
    public function markInvoiceAsPaid(MarkAsPaidRequest $request, $id)
    {
        try {
            $this->accountingService->markInvoiceAsPaid($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Export clients with streaming for large datasets
     */
    public function exportClients(Request $request)
    {
        $filters = $request->only(['search', 'status']);
        
        return $this->accountingService->exportClients($filters);
    }

    /**
     * Get client invoice history with pagination
     */
    public function getClientInvoiceHistory($clientId)
    {
        $history = $this->invoiceRepository->getClientInvoiceHistory($clientId);

        return response()->json($history);
    }

    /**
     * Suspend client account
     */
    public function suspendClient($id)
    {
        try {
            $this->accountingService->suspendClient($id);

            return response()->json([
                'success' => true,
                'message' => 'Client account suspended due to overdue payments'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Reactivate client account
     */
    public function reactivateClient($id)
    {
        $this->accountingService->reactivateClient($id);

        return response()->json([
            'success' => true,
            'message' => 'Client account reactivated successfully'
        ]);
    }

    /**
     * Send overdue notifications with batch processing
     */
    public function sendOverdueNotifications()
    {
        try {
            $result = $this->accountingService->sendOverdueNotifications();

            return response()->json([
                'success' => true,
                'message' => "Sent {$result['count']} overdue notifications successfully!"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get accounting settings
     */
    public function settings()
    {
        $settings = $this->accountingService->getSettings();
        
        return view('admin.pages.accounting.settings', compact('settings'));
    }

    /**
     * Update accounting settings
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'tax_id' => 'required|string|max:100',
            'commercial_registration' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'bank_account' => 'nullable|string|max:100',
            'iban' => 'nullable|string|max:50',
            'payment_due_days' => 'required|integer|min:1|max:90',
        ]);

        $this->accountingService->updateSettings($validated);

        return redirect()->back()->with('success', 'Settings updated successfully');
    }

    /**
     * Get dashboard data for AJAX requests
     */
    public function getDashboardData()
    {
        $data = Cache::remember('dashboard_data', 300, function () {
            return $this->accountingService->getDashboardData();
        });

        return response()->json($data);
    }

    /**
     * Get accounting reports
     */
    public function getAccountingReports(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $reports = Cache::remember(
            'accounting_reports_' . md5($startDate . $endDate),
            3600,
            fn() => $this->accountingService->getAccountingReports($startDate, $endDate)
        );

        return response()->json($reports);
    }

    /**
     * Download invoice PDF
     */
    public function downloadInvoicePdf($id)
    {
        try {
            return $this->accountingService->generateInvoicePdf($id);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'PDF generation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resend invoice to client
     */
    public function resendInvoice($id)
    {
        try {
            $this->accountingService->resendInvoice($id);

            return response()->json([
                'success' => true,
                'message' => 'Invoice resent successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get invoice logs
     */
    public function getInvoiceLogs($id)
    {
        $logs = $this->invoiceRepository->getInvoiceLogs($id);

        return response()->json(['logs' => $logs]);
    }

    /**
     * Send reminder to client
     */
    public function sendClientReminder($clientId)
    {
        try {
            $result = $this->accountingService->sendClientReminder($clientId);

            return response()->json([
                'success' => true,
                'message' => $result['message']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
