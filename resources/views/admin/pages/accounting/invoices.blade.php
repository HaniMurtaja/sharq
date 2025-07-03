{{-- resources/views/admin/pages/accounting/invoices.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Invoices')

@section('content')
<div class="flex flex-col p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Invoices</h1>
        <div class="flex gap-2">
            <button onclick="generateInvoices()" class="btn btn-primary">Generate New Invoice</button>
            <a href="{{ route('accounting.invoices.export') }}" class="btn btn-secondary">Export</a>
        </div>
    </div>


    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <form method="GET" class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="generated_under_review" {{ request('status') == 'generated_under_review' ? 'selected' : '' }}>Under Review</option>
                    <option value="confirmed_sent_unpaid" {{ request('status') == 'confirmed_sent_unpaid' ? 'selected' : '' }}>Sent - Unpaid</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Client</label>
                <select name="client_id" class="form-control">
                    <option value="">All Clients</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->first_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Overdue Only</label>
                <input type="checkbox" name="overdue" value="1" {{ request('overdue') ? 'checked' : '' }} class="form-check-input">
            </div>
            <div>
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('accounting.invoices') }}" class="btn btn-secondary">Clear</a>
            </div>
        </form>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white rounded-lg shadow">
        <table class="table" id="invoicesTable">
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
                <tr class="{{ $invoice->isOverdue() ? 'table-danger' : '' }}">
                    <td>
                        <a href="{{ route('accounting.invoices.show', $invoice->id) }}" class="text-blue-600 hover:underline">
                            {{ $invoice->invoice_number }}
                        </a>
                    </td>
                    <td>{{ $invoice->client->full_name }}</td>
                    <td>{{ $invoice->invoice_date->format('Y-m-d') }}</td>
                    <td>
                        {{ $invoice->due_date->format('Y-m-d') }}
                        @if($invoice->isOverdue())
                            <span class="badge bg-danger">{{ $invoice->getDaysOverdue() }} days overdue</span>
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
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('accounting.invoices.show', $invoice->id) }}">View</a></li>
                                <li><a class="dropdown-item" href="{{ route('accounting.invoices.pdf', $invoice->id) }}">Download PDF</a></li>
                                @if($invoice->status == 'generated_under_review')
                                    <li>
                                        <form action="{{ route('accounting.invoices.confirm', $invoice->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="dropdown-item" onclick="return confirm('Confirm and send this invoice?')">
                                                Confirm & Send
                                            </button>
                                        </form>
                                    </li>
                                @endif
                                @if($invoice->status != 'paid')
                                    <li><a class="dropdown-item" href="#" onclick="markAsPaid({{ $invoice->id }})">Mark as Paid</a></li>
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        {{ $invoices->links() }}
    </div>
</div>

<!-- Mark as Paid Modal -->
<div class="modal fade" id="markAsPaidModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark Invoice as Paid</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="markAsPaidForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select class="form-control" id="payment_method" name="payment_method" required>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cash">Cash</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="payment_date" class="form-label">Payment Date</label>
                        <input type="date" class="form-control" id="payment_date" name="payment_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="amount_paid" class="form-label">Amount Paid</label>
                        <input type="number" step="0.01" class="form-control" id="amount_paid" name="amount_paid" required>
                    </div>
                    <div class="mb-3">
                        <label for="transaction_reference" class="form-label">Transaction Reference</label>
                        <input type="text" class="form-control" id="transaction_reference" name="transaction_reference">
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Record Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function markAsPaid(invoiceId) {
    document.getElementById('markAsPaidForm').action = `/admin/accounting/invoices/${invoiceId}/mark-paid`;
    $('#markAsPaidModal').modal('show');
}

function generateInvoices() {
   
    $('#generateInvoicesModal').modal('show');
}
</script>
@endsection