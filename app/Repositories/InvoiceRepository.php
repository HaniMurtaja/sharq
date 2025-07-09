<?php

namespace App\Repositories;

use App\Models\ClientInvoice;
use App\Models\InvoiceLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoiceRepository
{
    protected $model;

    public function __construct(ClientInvoice $model)
    {
        $this->model = $model;
    }

    /**
     * Get recent invoices with minimal relations
     */
    public function getRecentInvoices(int $limit = 10): Collection
    {
        return $this->model->with(['client:id,first_name,last_name,email'])
            ->latest('invoice_date')
            ->limit($limit)
            ->get();
    }

    /**
     * Get overdue invoices with client data
     */
    public function getOverdueInvoices(int $limit = 5): Collection
    {
        return $this->model->with(['client:id,first_name,last_name,email'])
            ->where('due_date', '<', now())
            ->where('status', '!=', ClientInvoice::STATUS_PAID)
            ->orderBy('due_date')
            ->limit($limit)
            ->get();
    }

    /**
     * Get invoices with filters and optimized pagination
     */
    public function getInvoicesWithFilters(array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with([
            'client:id,first_name,last_name,email',
            'items:id,invoice_id,quantity,total_price',
            'paymentReceipts:id,invoice_id,amount_paid,status'
        ]);

        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }

        if (!empty($filters['overdue'])) {
            $query->where('due_date', '<', now())
                  ->where('status', '!=', ClientInvoice::STATUS_PAID);
        }

        return $query->latest('invoice_date')->paginate(20);
    }

    /**
     * Find invoice with all relations for detail view
     */
    public function findWithAllRelations($id): ?ClientInvoice
    {
        return $this->model->with([
            'client',
            'items',
            'paymentReceipts' => function ($q) {
                $q->orderBy('payment_date', 'desc');
            },
            'logs' => function ($q) {
                $q->with('user:id,first_name,last_name')
                  ->orderBy('created_at', 'desc');
            }
        ])->find($id);
    }

    /**
     * Get monthly summary with single query
     */
    public function getMonthlySummary(array $filters = []): Collection
    {
        $query = $this->model->selectRaw('
            YEAR(invoice_date) as year,
            MONTH(invoice_date) as month,
            COUNT(*) as invoice_count,
            SUM(total_amount) as total_amount,
            SUM(CASE WHEN status = "paid" THEN total_amount ELSE 0 END) as paid_amount,
            SUM(CASE WHEN status != "paid" THEN total_amount ELSE 0 END) as pending_amount
        ');

        // Apply same filters as main query
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }

        return $query->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function ($item) {
                $item->month_name = Carbon::createFromDate($item->year, $item->month, 1)->format('F Y');
                return $item;
            });
    }

    /**
     * Get client invoice history with pagination
     */
    public function getClientInvoiceHistory(int $clientId): array
    {
        $client = \App\Models\User::findOrFail($clientId);
        
        // Get invoices grouped by month
        $invoices = $this->model->where('client_id', $clientId)
            ->with(['items', 'paymentReceipts'])
            ->orderBy('invoice_date', 'desc')
            ->get()
            ->groupBy(function ($invoice) {
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
                'order_count' => $monthInvoices->sum(function ($invoice) {
                    return $invoice->items->sum('quantity');
                }),
                'invoices' => $monthInvoices->values()
            ];
        }

        // Get summary using single query
        $totalSummary = $this->model->where('client_id', $clientId)
            ->selectRaw('
                COUNT(*) as total_invoices,
                SUM(total_amount) as total_amount,
                SUM(CASE WHEN status = "paid" THEN total_amount ELSE 0 END) as total_paid,
                SUM(CASE WHEN status != "paid" THEN total_amount ELSE 0 END) as total_pending
            ')
            ->first();

        return [
            'client' => $client,
            'monthly_breakdown' => $monthlyBreakdown,
            'total_summary' => [
                'total_invoices' => $totalSummary->total_invoices ?? 0,
                'total_amount' => $totalSummary->total_amount ?? 0,
                'total_paid' => $totalSummary->total_paid ?? 0,
                'total_pending' => $totalSummary->total_pending ?? 0,
            ]
        ];
    }

    /**
     * Get invoice logs efficiently
     */
    public function getInvoiceLogs(int $invoiceId): Collection
    {
        return InvoiceLog::where('invoice_id', $invoiceId)
            ->with(['user:id,first_name,last_name'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($log) {
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
    }

    /**
     * Get dashboard statistics using single query
     */
    public function getDashboardStats(): array
    {
        $stats = $this->model->selectRaw('
            COUNT(*) as total_invoices,
            SUM(CASE WHEN status = "generated_under_review" THEN 1 ELSE 0 END) as pending_review,
            SUM(CASE WHEN due_date < NOW() AND status != "paid" THEN 1 ELSE 0 END) as overdue_invoices,
            SUM(CASE WHEN status = "paid" THEN total_amount ELSE 0 END) as total_revenue
        ')->first();

        return [
            'total_invoices' => $stats->total_invoices ?? 0,
            'pending_review' => $stats->pending_review ?? 0,
            'overdue_invoices' => $stats->overdue_invoices ?? 0,
            'total_revenue' => $stats->total_revenue ?? 0,
        ];
    }

    /**
     * Get invoices for bulk operations
     */
    public function getInvoicesForBulkOperation(array $invoiceIds): Collection
    {
        return $this->model->whereIn('id', $invoiceIds)
            ->with(['client:id,first_name,last_name,email'])
            ->get();
    }

    /**
     * Batch update invoice status
     */
    public function batchUpdateStatus(array $invoiceIds, string $status): int
    {
        return $this->model->whereIn('id', $invoiceIds)
            ->update(['status' => $status]);
    }

    /**
     * Get export data with cursor for memory efficiency
     */
    public function getExportData(array $filters = [])
    {
        $query = $this->model->with(['client:id,first_name,last_name,email', 'items'])
            ->orderBy('invoice_date', 'desc');

        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }

        if (!empty($filters['overdue'])) {
            $query->where('due_date', '<', now())
                  ->where('status', '!=', ClientInvoice::STATUS_PAID);
        }

        return $query->cursor();
    }

    /**
     * Get revenue by month for reports
     */
    public function getRevenueByMonth(Carbon $startDate, Carbon $endDate): Collection
    {
        return $this->model->whereBetween('invoice_date', [$startDate, $endDate])
            ->where('status', ClientInvoice::STATUS_PAID)
            ->selectRaw('
                YEAR(invoice_date) as year,
                MONTH(invoice_date) as month,
                SUM(total_amount) as revenue
            ')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
    }

    /**
     * Get client performance data
     */
    public function getClientPerformance(Carbon $startDate, Carbon $endDate): Collection
    {
        return DB::table('client_invoices')
            ->join('users', 'client_invoices.client_id', '=', 'users.id')
            ->whereBetween('client_invoices.invoice_date', [$startDate, $endDate])
            ->select([
                'users.id as client_id',
                'users.first_name',
                'users.last_name',
                DB::raw('COUNT(*) as invoice_count'),
                DB::raw('SUM(client_invoices.total_amount) as total_amount'),
                DB::raw('SUM(CASE WHEN client_invoices.status = "paid" THEN client_invoices.total_amount ELSE 0 END) as paid_amount'),
                DB::raw('SUM(CASE WHEN client_invoices.due_date < NOW() AND client_invoices.status != "paid" THEN 1 ELSE 0 END) as overdue_count')
            ])
            ->groupBy('users.id', 'users.first_name', 'users.last_name')
            ->get()
            ->map(function ($item) {
                return [
                    'client_name' => trim($item->first_name . ' ' . $item->last_name),
                    'invoice_count' => $item->invoice_count,
                    'total_amount' => $item->total_amount,
                    'paid_amount' => $item->paid_amount,
                    'overdue_count' => $item->overdue_count
                ];
            });
    }

    /**
     * Get payment methods summary
     */
    public function getPaymentMethodsSummary(Carbon $startDate, Carbon $endDate): Collection
    {
        return DB::table('payment_receipts')
            ->join('client_invoices', 'payment_receipts.invoice_id', '=', 'client_invoices.id')
            ->whereBetween('payment_receipts.payment_date', [$startDate, $endDate])
            ->where('payment_receipts.status', 'confirmed')
            ->selectRaw('
                payment_receipts.payment_method,
                COUNT(*) as count,
                SUM(payment_receipts.amount_paid) as total_amount
            ')
            ->groupBy('payment_receipts.payment_method')
            ->get();
    }

    /**
     * Create invoice with items in single transaction
     */
    public function createInvoiceWithItems(array $invoiceData, array $items): ClientInvoice
    {
        return DB::transaction(function () use ($invoiceData, $items) {
            $invoice = $this->model->create($invoiceData);
            
            if (!empty($items)) {
                $invoice->items()->createMany($items);
            }
            
            return $invoice->load(['items', 'client']);
        });
    }

    /**
     * Get uninvoiced orders count for client
     */
    public function getUninvoicedOrdersCount(int $clientId, Carbon $month): int
    {
        // This would be implemented based on your Order model
        return DB::table('orders')
            ->where('client_id', $clientId)
            ->whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month)
            ->where('invoiced', false)
            ->count();
    }

    /**
     * Check if invoice exists for client and month
     */
    public function invoiceExistsForMonth(int $clientId, Carbon $month): bool
    {
        return $this->model->where('client_id', $clientId)
            ->whereYear('invoice_date', $month->year)
            ->whereMonth('invoice_date', $month->month)
            ->exists();
    }

    /**
     * Get invoices requiring attention (overdue, pending review)
     */
    public function getInvoicesRequiringAttention(): Collection
    {
        return $this->model->with(['client:id,first_name,last_name,email'])
            ->where(function ($query) {
                $query->where('status', ClientInvoice::STATUS_GENERATED)
                      ->orWhere(function ($subQuery) {
                          $subQuery->where('due_date', '<', now())
                                   ->where('status', '!=', ClientInvoice::STATUS_PAID);
                      });
            })
            ->orderBy('due_date')
            ->get();
    }
}