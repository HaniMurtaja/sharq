@extends('admin.layouts.app')

@section('title', 'Invoices - Al Shrouq Express')

@section('content')

@include('admin.includes.content-header', [
    'header' => 'Invoices', 
    'title' => 'Accounting'
])

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Invoices</h1>
                <div>
                    <button type="button" class="btn btn-primary mr-2" onclick="generateNewInvoice()">
                        <i class="fas fa-plus"></i> Generate New Invoice
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="exportInvoices()">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="generated_under_review" {{ request('status') == 'generated_under_review' ? 'selected' : '' }}>Under Review</option>
                                <option value="confirmed_sent_unpaid" {{ request('status') == 'confirmed_sent_unpaid' ? 'selected' : '' }}>Sent - Unpaid</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Client</label>
                            <select name="client_id" class="form-select">
                                <option value="">All Clients</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->first_name }} {{ $client->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Overdue Only</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="overdue" value="1" {{ request('overdue') ? 'checked' : '' }}>
                                <label class="form-check-label">Show overdue only</label>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary mr-2">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="{{ url('/admin/accounting/invoices') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Monthly Summary -->
            @if($monthlySummary->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Monthly Summary</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Invoices</th>
                                    <th>Total Amount</th>
                                    <th>Paid Amount</th>
                                    <th>Pending Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthlySummary as $summary)
                                <tr>
                                    <td>{{ $summary->month_name }}</td>
                                    <td>{{ $summary->invoice_count }}</td>
                                    <td>{{ number_format($summary->total_amount, 2) }} SAR</td>
                                    <td>{{ number_format($summary->paid_amount, 2) }} SAR</td>
                                    <td>{{ number_format($summary->pending_amount, 2) }} SAR</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Invoices Table -->
            <div class="card">
                <div class="card-body">
                    @if($invoices->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Client</th>
                                        <th>Date</th>
                                        <th>Due Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoices as $invoice)
                                    <tr class="{{ $invoice->isOverdue() ? 'table-warning' : '' }}">
                                        <td>
                                            <strong>{{ $invoice->invoice_number }}</strong>
                                            @if($invoice->isOverdue())
                                                <span class="badge badge-danger ml-1">Overdue</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $invoice->client->first_name }} {{ $invoice->client->last_name }}
                                            <br><small class="text-muted">{{ $invoice->client->email }}</small>
                                        </td>
                                        <td>{{ $invoice->invoice_date->format('M d, Y') }}</td>
                                        <td>{{ $invoice->due_date->format('M d, Y') }}</td>
                                        <td>
                                            <strong>{{ number_format($invoice->total_amount, 2) }}</strong> {{ $invoice->currency }}
                                            @if($invoice->getRemainingAmount() > 0 && $invoice->status === 'paid')
                                                <br><small class="text-success">Paid: {{ number_format($invoice->getTotalPaidAmount(), 2) }}</small>
                                            @elseif($invoice->getTotalPaidAmount() > 0)
                                                <br><small class="text-info">Paid: {{ number_format($invoice->getTotalPaidAmount(), 2) }}</small>
                                                <br><small class="text-warning">Remaining: {{ number_format($invoice->getRemainingAmount(), 2) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($invoice->status === 'generated_under_review')
                                                <span class="badge badge-warning">Under Review</span>
                                            @elseif($invoice->status === 'confirmed_sent_unpaid')
                                                <span class="badge badge-info">Sent - Unpaid</span>
                                            @elseif($invoice->status === 'paid')
                                                <span class="badge badge-success">Paid</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ url('/admin/accounting/invoices/' . $invoice->id) }}" class="btn btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ url('/admin/accounting/invoices/' . $invoice->id . '/pdf') }}" class="btn btn-outline-secondary">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                                @if($invoice->status === 'generated_under_review')
                                                    <button type="button" class="btn btn-outline-success" onclick="confirmInvoice({{ $invoice->id }})">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif
                                                @if($invoice->status === 'confirmed_sent_unpaid')
                                                    <button type="button" class="btn btn-outline-warning" onclick="markAsPaid({{ $invoice->id }})">
                                                        <i class="fas fa-dollar-sign"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Showing {{ $invoices->firstItem() }} to {{ $invoices->lastItem() }} of {{ $invoices->total() }} results
                            </div>
                            <div>
                                {{ $invoices->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                            <h5>No invoices found yet.</h5>
                            <p class="text-muted">Once you create the invoice tables and models, invoices will appear here.</p>
                            <button type="button" class="btn btn-primary" onclick="generateNewInvoice()">
                                <i class="fas fa-plus"></i> Generate Your First Invoice
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Generate Invoice Modal -->
<div class="modal fade" id="generateInvoiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate New Invoice</h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <form id="generateInvoiceForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Month</label>
                        <input type="month" name="month" class="form-control" value="{{ now()->format('Y-m') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Client (Optional)</label>
                        <select name="client_id" class="form-select">
                            <option value="">All Clients</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->first_name }} {{ $client->last_name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Leave empty to generate for all clients</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Generate Invoice</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Mark as Paid Modal -->
<div class="modal fade" id="markAsPaidModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark as Paid</h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <form id="markAsPaidForm">
                <div class="modal-body">
                    <input type="hidden" name="invoice_id" id="paymentInvoiceId">
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cash">Cash</option>
                            <option value="tap_gateway">Tap Gateway</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Date</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount Paid</label>
                        <input type="number" name="amount_paid" class="form-control" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Transaction Reference</label>
                        <input type="text" name="transaction_reference" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Mark as Paid</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function generateNewInvoice() {
    $('#generateInvoiceModal').modal('show');
}

function confirmInvoice(invoiceId) {
    if (confirm('Are you sure you want to confirm and send this invoice?')) {
        fetch(`{{ url('/admin/accounting/invoices') }}/${invoiceId}/confirm`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Invoice confirmed and sent successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while confirming the invoice.');
        });
    }
}

function markAsPaid(invoiceId) {
    document.getElementById('paymentInvoiceId').value = invoiceId;
    $('#markAsPaidModal').modal('show');
}

function exportInvoices() {
    window.location.href = '{{ url("/admin/accounting/invoices/export") }}';
}

// Generate Invoice Form
document.getElementById('generateInvoiceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    fetch('{{ url("/admin/accounting/invoices/generate") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Invoice generated successfully!');
            $('#generateInvoiceModal').modal('hide');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while generating the invoice.');
    });
});

// Mark as Paid Form
document.getElementById('markAsPaidForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const invoiceId = formData.get('invoice_id');
    const data = Object.fromEntries(formData.entries());
    
    fetch(`{{ url('/admin/accounting/invoices') }}/${invoiceId}/mark-paid`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Payment recorded successfully!');
            $('#markAsPaidModal').modal('hide');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while recording the payment.');
    });
});
</script>
@endpush