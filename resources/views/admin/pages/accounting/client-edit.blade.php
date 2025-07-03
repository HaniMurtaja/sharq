{{-- resources/views/admin/pages/accounting/client-edit.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Edit Client Financial Data')

@section('content')
<div class="flex flex-col p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">Edit Client Financial Data</h1>
            <p class="text-gray-600">{{ $client->full_name }} - {{ $client->email }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('accounting.clients') }}" class="btn btn-secondary">Back to Clients</a>
            <button onclick="generateInvoiceForClient()" class="btn btn-primary">Generate Invoice</button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
     
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Client Information</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Name:</span>
                    <span class="font-medium">{{ $client->full_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Email:</span>
                    <span class="font-medium">{{ $client->email }}</span>
                </div>
                @if($client->phone)
                <div class="flex justify-between">
                    <span class="text-gray-600">Phone:</span>
                    <span class="font-medium">{{ $client->phone }}</span>
                </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-gray-600">Account Number:</span>
                    <span class="font-medium">{{ $client->client?->account_number ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Status:</span>
                    <span class="badge {{ $client->is_active ? 'bg-success' : 'bg-danger' }}">
                        {{ $client->is_active ? 'Active' : 'Suspended' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Wallet Balance:</span>
                    <span class="font-medium">{{ number_format($client->wallet?->balance ?? 0, 2) }} SAR</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Joined:</span>
                    <span class="font-medium">{{ $client->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Financial Settings & Invoice Template</h3>
            
            <form action="{{ route('accounting.clients.update', $client->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="billing_emails" class="form-label">Billing Email Addresses</label>
                        <div id="billing-emails-container">
                            @if($client->client?->billing_emails)
                                @foreach($client->client->billing_emails as $index => $email)
                                <div class="flex gap-2 mb-2 billing-email-row">
                                    <input type="email" name="billing_emails[]" value="{{ $email }}" 
                                           class="form-control" placeholder="billing@example.com">
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeEmailRow(this)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                @endforeach
                            @else
                                <div class="flex gap-2 mb-2 billing-email-row">
                                    <input type="email" name="billing_emails[]" value="{{ $client->email }}" 
                                           class="form-control" placeholder="billing@example.com">
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeEmailRow(this)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addEmailRow()">
                            <i class="fas fa-plus mr-1"></i> Add Email
                        </button>
                        <small class="form-text text-muted">These emails will receive invoice notifications</small>
                    </div>

                    <div>
                        <label for="auto_generate_invoice" class="form-label">Auto Generate Monthly Invoices</label>
                        <div class="form-check">
                            <input type="checkbox" name="auto_generate_invoice" value="1" 
                                   {{ $client->client?->auto_generate_invoice ? 'checked' : '' }}
                                   class="form-check-input" id="auto_generate_invoice">
                            <label class="form-check-label" for="auto_generate_invoice">
                                Generate invoices automatically at month end
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="payment_terms" class="form-label">Payment Terms</label>
                        <select name="payment_terms" class="form-control">
                            <option value="">Default (As per settings)</option>
                            <option value="Net 7" {{ $client->client?->payment_terms == 'Net 7' ? 'selected' : '' }}>Net 7 Days</option>
                            <option value="Net 15" {{ $client->client?->payment_terms == 'Net 15' ? 'selected' : '' }}>Net 15 Days</option>
                            <option value="Net 30" {{ $client->client?->payment_terms == 'Net 30' ? 'selected' : '' }}>Net 30 Days</option>
                            <option value="Due on Receipt" {{ $client->client?->payment_terms == 'Due on Receipt' ? 'selected' : '' }}>Due on Receipt</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="invoice_template_notes" class="form-label">Invoice Template Notes</label>
                        <textarea name="invoice_template_notes" class="form-control" rows="4" 
                                  placeholder="Special notes or terms to include on invoices for this client...">{{ $client->client?->invoice_template_notes }}</textarea>
                        <small class="form-text text-muted">These notes will appear on all invoices for this client</small>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="btn btn-primary">Update Financial Settings</button>
                    <a href="{{ route('accounting.clients') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

   
    <div class="mt-6 bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Recent Invoices</h3>
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Date</th>
                        <th>Due Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($client->invoices->take(10) as $invoice)
                    <tr class="{{ $invoice->isOverdue() ? 'table-warning' : '' }}">
                        <td>
                            <a href="{{ route('accounting.invoices.show', $invoice->id) }}" class="text-blue-600 hover:underline">
                                {{ $invoice->invoice_number }}
                            </a>
                        </td>
                        <td>{{ $invoice->invoice_date->format('M d, Y') }}</td>
                        <td>
                            {{ $invoice->due_date->format('M d, Y') }}
                            @if($invoice->isOverdue())
                                <br><small class="text-danger">{{ $invoice->getDaysOverdue() }} days overdue</small>
                            @endif
                        </td>
                        <td>{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}</td>
                        <td>
                            <span class="badge 
                                @if($invoice->status == 'generated_under_review') bg-warning
                                @elseif($invoice->status == 'confirmed_sent_unpaid') bg-info
                                @else bg-success
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $invoice->status)) }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('accounting.invoices.show', $invoice->id) }}" class="btn btn-outline-primary btn-sm">View</a>
                                <a href="{{ route('accounting.invoices.pdf', $invoice->id) }}" class="btn btn-outline-secondary btn-sm">PDF</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-gray-500">No invoices found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($monthlyOrderSummary->count() > 0)
    <div class="mt-6 bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Uninvoiced Orders Summary</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($monthlyOrderSummary as $summary)
            <div class="border rounded-lg p-4">
                <h4 class="font-semibold text-blue-600">{{ $summary['month_name'] }}</h4>
                <div class="mt-2 space-y-1 text-sm">
                    <div class="flex justify-between">
                        <span>Orders:</span>
                        <span class="font-medium">{{ $summary['order_count'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Total Fees:</span>
                        <span class="font-medium">{{ number_format($summary['total_service_fees'], 2) }} SAR</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Average/Order:</span>
                        <span class="font-medium">{{ number_format($summary['average_per_order'], 2) }} SAR</span>
                    </div>
                </div>
                <button onclick="generateInvoiceForMonth('{{ $summary['month'] }}')" 
                        class="btn btn-primary btn-sm w-full mt-3">
                    Generate Invoice
                </button>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>


<div class="modal fade" id="generateInvoiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('accounting.invoices.generate') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="month" class="form-label">Month</label>
                        <input type="month" class="form-control" id="month" name="month" value="{{ date('Y-m') }}" required>
                    </div>
                    <input type="hidden" name="client_id" value="{{ $client->id }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Generate Invoice</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function addEmailRow() {
    const container = document.getElementById('billing-emails-container');
    const newRow = document.createElement('div');
    newRow.className = 'flex gap-2 mb-2 billing-email-row';
    newRow.innerHTML = `
        <input type="email" name="billing_emails[]" class="form-control" placeholder="billing@example.com">
        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeEmailRow(this)">
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(newRow);
}

function removeEmailRow(button) {
    const rows = document.querySelectorAll('.billing-email-row');
    if (rows.length > 1) {
        button.closest('.billing-email-row').remove();
    } else {
        alert('At least one email address is required');
    }
}

function generateInvoiceForClient() {
    $('#generateInvoiceModal').modal('show');
}

function generateInvoiceForMonth(month) {
    document.getElementById('month').value = month;
    $('#generateInvoiceModal').modal('show');
}
</script>
@endsection