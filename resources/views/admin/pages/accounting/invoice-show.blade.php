@extends('admin.layouts.app')

@section('title', 'Invoice ' . $invoice->invoice_number . ' - Al Shrouq Express')

@section('content')

@include('admin.includes.content-header', [
    'header' => 'Invoice ' . $invoice->invoice_number, 
    'title' => 'Accounting'
])

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="card-title">Invoice Details</h3>
                        </div>
                        <div class="col-sm-6">
                            <div class="float-right">
                                <a href="{{ url('/admin/accounting/invoices') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Invoices
                                </a>
                                <a href="{{ url('/admin/accounting/invoices/' . $invoice->id . '/pdf') }}" class="btn btn-primary">
                                    <i class="fas fa-download"></i> Download PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Invoice Header -->
                    <div class="row">
                        <div class="col-12">
                            <h2 class="page-header">
                                <i class="fas fa-file-invoice"></i> Invoice {{ $invoice->invoice_number }}
                                <small class="float-right">
                                    @if($invoice->status === 'generated_under_review')
                                        <span class="badge badge-warning">Under Review</span>
                                    @elseif($invoice->status === 'confirmed_sent_unpaid')
                                        <span class="badge badge-info">Sent - Unpaid</span>
                                    @elseif($invoice->status === 'paid')
                                        <span class="badge badge-success">Paid</span>
                                    @endif
                                </small>
                            </h2>
                        </div>
                    </div>

                    <!-- Invoice Info -->
                    <div class="row invoice-info">
                        <div class="col-sm-4 invoice-col">
                            <strong>From</strong>
                            <address>
                                <strong>Al Shrouq Express</strong><br>
                                Riyadh, Saudi Arabia<br>
                                Phone: +966 11 123 4567<br>
                                Email: billing@alshrouqexpress.com
                            </address>
                        </div>

                        <div class="col-sm-4 invoice-col">
                            <strong>To</strong>
                            <address>
                                @if($invoice->client)
                                    <strong>{{ $invoice->client->first_name ?? 'N/A' }} {{ $invoice->client->last_name ?? '' }}</strong><br>
                                    @if($invoice->client->client)
                                        {{ $invoice->client->client->company_name }}<br>
                                    @endif
                                    Phone: {{ $invoice->client->phone ?? 'N/A' }}<br>
                                    Email: {{ $invoice->client->email ?? 'N/A' }}
                                @else
                                    <strong>Client #{{ $invoice->client_id }}</strong><br>
                                    <em>Client information not available</em>
                                @endif
                            </address>
                        </div>

                        <div class="col-sm-4 invoice-col">
                            <b>Invoice #{{ $invoice->invoice_number }}</b><br>
                            <br>
                            <b>Invoice Date:</b> {{ $invoice->invoice_date->format('M d, Y') }}<br>
                            <b>Due Date:</b> {{ $invoice->due_date->format('M d, Y') }}<br>
                            <b>Payment Terms:</b> {{ $invoice->client->client?->payment_terms ?? 'Net 30' }}
                        </div>
                    </div>

                    <!-- Table row -->
                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th>Service Month</th>
                                        <th>Qty</th>
                                        <th>Unit Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoice->items as $item)
                                    <tr>
                                        <td>{{ $item->description }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->service_month)->format('F Y') }}</td>
                                        <td>{{ number_format($item->quantity) }}</td>
                                        <td>{{ number_format($item->unit_price, 2) }} {{ $invoice->currency }}</td>
                                        <td>{{ number_format($item->total_price, 2) }} {{ $invoice->currency }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Payments -->
                        <div class="col-6">
                            @if($invoice->paymentReceipts->count() > 0)
                                <h4>Payment History</h4>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Method</th>
                                                <th>Amount</th>
                                                <th>Reference</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($invoice->paymentReceipts as $payment)
                                            <tr>
                                                <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                                <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                                <td>{{ number_format($payment->amount_paid, 2) }} {{ $invoice->currency }}</td>
                                                <td>{{ $payment->transaction_reference ?? '-' }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                        <!-- Totals -->
                        <div class="col-6">
                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <th style="width:50%">Subtotal:</th>
                                        <td>{{ number_format($invoice->subtotal, 2) }} {{ $invoice->currency }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tax (15%):</th>
                                        <td>{{ number_format($invoice->tax_amount, 2) }} {{ $invoice->currency }}</td>
                                    </tr>
                                    <tr>
                                        <th>Total:</th>
                                        <td><strong>{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}</strong></td>
                                    </tr>
                                    @if($invoice->paymentReceipts->count() > 0)
                                    <tr>
                                        <th>Total Paid:</th>
                                        <td class="text-success">{{ number_format($invoice->paymentReceipts->sum('amount_paid'), 2) }} {{ $invoice->currency }}</td>
                                    </tr>
                                    <tr>
                                        <th>Balance Due:</th>
                                        <td class="text-danger">
                                            {{ number_format($invoice->total_amount - $invoice->paymentReceipts->sum('amount_paid'), 2) }} {{ $invoice->currency }}
                                        </td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    @if($invoice->notes)
                    <div class="row">
                        <div class="col-12">
                            <h4>Notes</h4>
                            <p>{{ $invoice->notes }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="row no-print">
                        <div class="col-12">
                            @if($invoice->status === 'generated_under_review')
                                <button type="button" class="btn btn-success" onclick="confirmInvoice({{ $invoice->id }})">
                                    <i class="fas fa-check"></i> Confirm & Send Invoice
                                </button>
                            @endif

                            @if($invoice->status === 'confirmed_sent_unpaid')
                                <button type="button" class="btn btn-warning" onclick="markAsPaid({{ $invoice->id }}, {{ $invoice->total_amount }})">
                                    <i class="fas fa-dollar-sign"></i> Mark as Paid
                                </button>
                            @endif

                            <button type="button" class="btn btn-primary" onclick="window.print()">
                                <i class="fas fa-print"></i> Print
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Log -->
            @if($invoice->logs->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Activity Log</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Action</th>
                                    <th>User</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->logs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</span>
                                    </td>
                                    <td>{{ $log->user?->full_name ?? 'System' }}</td>
                                    <td>{{ $log->notes }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Mark as Paid Modal -->
<div class="modal fade" id="markAsPaidModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Mark as Paid</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="markAsPaidForm">
                <div class="modal-body">
                    <input type="hidden" name="invoice_id" id="paymentInvoiceId">
                    <div class="form-group">
                        <label>Payment Method</label>
                        <select name="payment_method" class="form-control" required>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cash">Cash</option>
                            <option value="tap_gateway">Tap Gateway</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Payment Date</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Amount Paid</label>
                        <input type="number" name="amount_paid" class="form-control" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Transaction Reference</label>
                        <input type="text" name="transaction_reference" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
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

function markAsPaid(invoiceId, totalAmount) {
    document.getElementById('paymentInvoiceId').value = invoiceId;
    document.querySelector('input[name="amount_paid"]').value = totalAmount;
    $('#markAsPaidModal').modal('show');
}

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