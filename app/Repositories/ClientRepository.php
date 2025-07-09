<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\ClientInvoice;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ClientRepository
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Get clients with financial data using single optimized query
     */
    public function getClientsWithFinancialData(array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->where('user_role', 2) // Client role
            ->with(['client', 'wallet'])
            ->withCount([
                'invoices',
                'invoices as overdue_invoices_count' => function ($q) {
                    $q->where('due_date', '<', now())
                      ->where('status', '!=', ClientInvoice::STATUS_PAID);
                }
            ])
            ->withSum('invoices', 'total_amount')
            ->withSum([
                'invoices as paid_amount' => function ($q) {
                    $q->where('status', ClientInvoice::STATUS_PAID);
                }
            ], 'total_amount')
            ->with([
                'invoices' => function ($q) {
                    $q->latest('invoice_date')->limit(1)->select('id', 'client_id', 'invoice_date');
                }
            ]);

        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($subQuery) use ($search) {
                      $subQuery->where('account_number', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($filters['status'])) {
            switch ($filters['status']) {
                case 'active':
                    $query->where('is_active', true);
                    break;
                case 'suspended':
                    $query->where('is_active', false);
                    break;
                case 'overdue':
                    $query->whereHas('invoices', function ($q) {
                        $q->where('due_date', '<', now())
                          ->where('status', '!=', ClientInvoice::STATUS_PAID);
                    });
                    break;
            }
        }

        return $query->paginate(20);
    }

    /**
     * Find client with specific relations
     */
    public function findWithRelations($id, array $relations = []): ?User
    {
        return $this->model->with($relations)->find($id);
    }

    /**
     * Get clients for dropdown (cached)
     */
    public function getClientsForDropdown(): Collection
    {
        return $this->model->where('user_role', 2)
            ->select('id', 'first_name', 'last_name', 'email')
            ->orderBy('first_name')
            ->get();
    }

    /**
     * Update client financial data
     */
    public function updateFinancialData(int $clientId, array $data): User
    {
        $client = $this->model->findOrFail($clientId);
        
        // Create or update client record
        $clientRecord = $client->client;
        if (!$clientRecord) {
            $clientRecord = $client->client()->create([
                'user_id' => $client->id,
                'account_number' => 'ACC-' . str_pad($client->id, 6, '0', STR_PAD_LEFT),
                'billing_emails' => $data['billing_emails'] ?? [],
                'auto_generate_invoice' => $data['auto_generate_invoice'] ?? false,
                'invoice_template_notes' => $data['invoice_template_notes'] ?? null,
                'payment_terms' => $data['payment_terms'] ?? null
            ]);
        } else {
            $clientRecord->update([
                'billing_emails' => $data['billing_emails'] ?? [],
                'auto_generate_invoice' => $data['auto_generate_invoice'] ?? false,
                'invoice_template_notes' => $data['invoice_template_notes'] ?? null,
                'payment_terms' => $data['payment_terms'] ?? null
            ]);
        }

        return $client;
    }

    /**
     * Get client financial summary using single query
     */
    public function getClientFinancialSummary(int $clientId): array
    {
        $result = DB::table('client_invoices')
            ->where('client_id', $clientId)
            ->selectRaw('
                COUNT(*) as total_invoices,
                SUM(total_amount) as total_amount,
                SUM(CASE WHEN status = "paid" THEN total_amount ELSE 0 END) as paid_amount,
                SUM(CASE WHEN due_date < NOW() AND status != "paid" THEN 1 ELSE 0 END) as overdue_count,
                MAX(invoice_date) as last_invoice_date
            ')
            ->first();

        return [
            'total_invoices' => $result->total_invoices ?? 0,
            'total_amount' => $result->total_amount ?? 0,
            'paid_amount' => $result->paid_amount ?? 0,
            'overdue_count' => $result->overdue_count ?? 0,
            'last_invoice_date' => $result->last_invoice_date,
        ];
    }

    /**
     * Get clients with overdue invoices for bulk operations
     */
    public function getClientsWithOverdueInvoices(): Collection
    {
        return $this->model->where('user_role', 2)
            ->whereHas('invoices', function ($q) {
                $q->where('due_date', '<', now())
                  ->where('status', '!=', ClientInvoice::STATUS_PAID);
            })
            ->with([
                'invoices' => function ($q) {
                    $q->where('due_date', '<', now())
                      ->where('status', '!=', ClientInvoice::STATUS_PAID);
                }
            ])
            ->get();
    }

    /**
     * Batch update client status
     */
    public function batchUpdateStatus(array $clientIds, bool $isActive): int
    {
        return $this->model->whereIn('id', $clientIds)
            ->update(['is_active' => $isActive]);
    }

    /**
     * Get export data with streaming support
     */
    public function getExportData(array $filters = [])
    {
        $query = $this->model->where('user_role', 2)
            ->with(['client', 'wallet'])
            ->withCount('invoices')
            ->withSum('invoices', 'total_amount')
            ->withSum([
                'invoices as paid_amount' => function ($q) {
                    $q->where('status', ClientInvoice::STATUS_PAID);
                }
            ], 'total_amount')
            ->withCount([
                'invoices as overdue_invoices_count' => function ($q) {
                    $q->where('due_date', '<', now())
                      ->where('status', '!=', ClientInvoice::STATUS_PAID);
                }
            ]);

        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            switch ($filters['status']) {
                case 'active':
                    $query->where('is_active', true);
                    break;
                case 'suspended':
                    $query->where('is_active', false);
                    break;
            }
        }

        return $query->cursor(); // Use cursor for memory efficiency
    }
}

namespace App\Repositories;

use App\Models\ClientInvoice;
use App\Models\InvoiceLog;
use App\Models\Order;
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
     * Get uninvoiced orders count for client
     */
    public function getUninvoicedOrdersCount(int $clientId, Carbon $month): int
    {
        return Order::where('ingr_shop_id', $clientId)
            ->whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month)
            ->where('invoiced', false)
            ->where('status', 9) // Only delivered orders
            ->count();
    }

    /**
     * Get uninvoiced orders total service fees
     */
    public function getUninvoicedOrdersTotalFees(int $clientId, Carbon $month): float
    {
        return Order::where('ingr_shop_id', $clientId)
            ->whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month)
            ->where('invoiced', false)
            ->where('status', 9) // Only delivered orders
            ->sum('service_fees');
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