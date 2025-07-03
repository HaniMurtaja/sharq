{{-- resources/views/admin/pages/accounting/invoice-details.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Invoice Details - ' . $invoice->invoice_number)

@section('content')
<div class="flex flex-col p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">Invoice {{ $invoice->invoice_number }}</h1>
            <p class="text-gray-600">{{ $invoice->client->full_name }} - {{ $invoice->invoice_date->format('F d, Y') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('accounting.invoices') }}" class="btn btn-secondary">Back to Invoices</a>
            <a href="{{ route('accounting.invoices.pdf', $invoice->id) }}" class="btn btn-outline-primary">
                <i class="fas fa-download mr-1"></i> Download PDF
            </a>
            @if($invoice->status == 'generated_under_review')
                <button onclick="confirmInvoice()" class="btn btn-success">
                    <i class="fas fa-check mr-1"></i> Confirm & Send
                </button>
            @endif
            @if($invoice->status != 'paid')
                <button onclick="markAsPaid()" class="btn btn-warning">
                    <i class="fas fa-money-bill mr-1"></i> Mark as Paid
                </button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Invoice Summary</h3>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Invoice Number:</span>
                    <span class="font-medium">{{ $invoice->invoice_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Invoice Date:</span>
                    <span class="font-medium">{{ $invoice->invoice_date->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Due Date:</span>
                    <span class="font-medium {{ $invoice->isOverdue() ? 'text-red-600' : '' }}">
                        {{ $invoice->due_date->format('M d, Y') }}
                        @if($invoice->isOverdue())
                            <br><small class="text-red-500">{{ $invoice->getDaysOverdue() }} days overdue</small>
                        @endif
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Status:</span>
                    <span class="badge 
                        @if($invoice->status == 'generated_under_review') bg-warning
                        @elseif($invoice->status == 'confirmed_sent_unpaid') bg-info
                        @else bg-success
                        @endif">
                        {{ ucfirst(str_replace('_', ' ', $invoice->status)) }}
                    </span>
                </div>
                <hr>
                <div class="flex justify-between">
                    <span class="text-gray-600">Subtotal:</span>
                    <span class="font-medium">{{ number_format($invoice->subtotal, 2) }} {{ $invoice->currency }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">VAT (15%):</span>
                    <span class="font-medium">{{ number_format($invoice->tax_amount, 2) }} {{ $invoice->currency }}</span>
                </div>
                <div class="flex justify-between text-lg font-bold">
                    <span>Total Amount:</span>
                    <span class="text-blue-600">{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}</span>
                </div>
                
                @if($invoice->getRemainingAmount() > 0 && $invoice->status == 'paid')
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Amount Paid:</span>
                    <span class="text-green-600">{{ number_format($invoice->getTotalPaidAmount(), 2) }} {{ $invoice->currency }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Remaining:</span>
                    <span class="text-orange-600">{{ number_format($invoice->getRemainingAmount(), 2) }} {{ $invoice->currency }}</span>
                </div>
                @endif
            </div>

            @if($invoice->notes)
            <div class="mt-4 p-3 bg-gray-50 rounded">
                <h4 class="font-medium text-sm text-gray-700 mb-1">Notes:</h4>
                <p class="text-sm text-gray-600">{{ $invoice->notes }}</p>
            </div>
            @endif
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Client Information</h3>
            
            <div class="space-y-3">
                <div>
                    <span class="text-gray-600 block">Client Name:</span>
                    <span class="font-medium">{{ $invoice->client->full_name }}</span>
                </div>
                <div>
                    <span class="text-gray-600 block">Email:</span>
                    <span class="font-medium">{{ $invoice->client->email }}</span>
                </div>
                @if($invoice->client->phone)
                <div>
                    <span class="text-gray-600 block">Phone:</span>
                    <span class="font-medium">{{ $invoice->client->phone }}</span>
                </div>
                @endif
                <div>
                    <span class="text-gray-600 block">Account Number:</span>
                    <span class="font-medium">{{ $invoice->client->client?->account_number ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-gray-600 block">Client Status:</span>
                    <span class="badge {{ $invoice->client->is_active ? 'bg-success' : 'bg-danger' }}">
                        {{ $invoice->client->is_active ? 'Active' : 'Suspended' }}
                    </span>
                </div>
            </div>

            @if($invoice->client_emails)
            <div class="mt-4">
                <h4 class="font-medium text-sm text-gray-700 mb-2">Billing Emails:</h4>
                <div class="space-y-1">
                    @foreach($invoice->getEmailList() as $email)
                        <div class="text-sm text-gray-600 bg-gray-50 px-2 py-1 rounded">{{ $email }}</div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Quick Actions -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
            
            <div class="space-y-2">
                <button onclick="viewInvoicePDF()" class="btn btn-outline-primary w-full">
                    <i class="fas fa-file-pdf mr-2"></i> View PDF
                </button>
                
                @if($invoice->status == 'confirmed_sent_unpaid')
                <button onclick="resendInvoice()" class="btn btn-outline-info w-full">
                    <i class="fas fa-paper-plane mr-2"></i> Resend Invoice
                </button>
                @endif
                
                <button onclick="viewPaymentHistory()" class="btn btn-outline-secondary w-full">
                    <i class="fas fa-history mr-2"></i> Payment History
                </button>
                
                <button onclick="viewInvoiceLogs()" class="btn btn-outline-secondary w-full">
                    <i class="fas fa-list mr-2"></i> View Logs
                </button>
                
                @if($invoice->status != 'paid')
                <button onclick="sendPaymentReminder()" class="btn btn-outline-warning w-full">
                    <i class="fas fa-bell mr-2"></i> Send Reminder
                </button>
                @endif

                @if($invoice->client->is_active && $invoice->isOverdue())
                <button onclick="suspendClientAccount()" class="btn btn-outline-danger w-full">
                    <i class="fas fa-ban mr-2"></i> Suspend Client
                </button>
                @endif
            </div>
        </div>
    </div>

    <div class="mt-6 bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Invoice Items</h3>
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-center">Service Month</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-right">Unit Price</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                    <tr>
                        <td>
                            <div class="font-medium">{{ $item->description }}</div>
                            <div class="text-sm text-gray-600">
                                Delivery services for {{ \Carbon\Carbon::parse($item->service_month)->format('F Y') }}
                            </div>
                        </td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($item->service_month)->format('M Y') }}</td>
                        <td class="text-center">{{ $item->quantity }} orders</td>
                        <td class="text-right">{{ number_format($item->unit_price, 2) }} {{ $invoice->currency }}</td>
                        <td class="text-right font-medium">{{ number_format($item->total_price, 2) }} {{ $invoice->currency }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2">
                        <td colspan="4" class="text-right font-medium">Subtotal:</td>
                        <td class="text-right font-medium">{{ number_format($invoice->subtotal, 2) }} {{ $invoice->currency }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-right font-medium">VAT (15%):</td>
                        <td class="text-right font-medium">{{ number_format($invoice->tax_amount, 2) }} {{ $invoice->currency }}</td>
                    </tr>
                    <tr class="border-t">
                        <td colspan="4" class="text-right text-lg font-bold">Total Amount:</td>
                        <td class="text-right text-lg font-bold text-blue-600">{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Related Orders -->
    @if($invoice->orders->count() > 0)
    <div class="mt-6 bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Related Orders ({{ $invoice->orders->count() }})</h3>
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Service Fee</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->orders->take(10) as $order)
                    <tr>
                        <td>
                            <a href="#" class="text-blue-600 hover:underline">
                                #{{ $order->id }}
                            </a>
                        </td>
                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                        <td>{{ number_format($order->service_fees, 2) }} {{ $invoice->currency }}</td>
                        <td>
                            <span class="badge bg-success">Completed</span>
                        </td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-primary">View Order</a>
                        </td>
                    </tr>
                    @endforeach
                    @if($invoice->orders->count() > 10)
                    <tr>
                        <td colspan="5" class="text-center text-gray-500">
                            ... and {{ $invoice->orders->count() - 10 }} more orders
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Payment History -->
    @if($invoice->paymentReceipts->count() > 0)
    <div class="mt-6 bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Payment History</h3>
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Receipt #</th>
                        <th>Date</th>
                        <th>Method</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Reference</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->paymentReceipts as $receipt)
                    <tr>
                        <td>{{ $receipt->receipt_number }}</td>
                        <td>{{ $receipt->payment_date->format('M d, Y') }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $receipt->payment_method)) }}</td>
                        <td>{{ number_format($receipt->amount_paid, 2) }} {{ $invoice->currency }}</td>
                        <td>
                            <span class="badge {{ $receipt->status == 'confirmed' ? 'bg-success' : 'bg-warning' }}">
                                {{ ucfirst($receipt->status) }}
                            </span>
                        </td>
                        <td>{{ $receipt->transaction_reference ?? 'N/A' }}</td>
                        <td>
                            @if($receipt->status == 'under_review')
                                <button onclick="confirmReceipt({{ $receipt->id }})" class="btn btn-sm btn-success">
                                    Confirm
                                </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<!-- Mark as Paid Modal -->
<div class="modal fade" id="markAsPaidModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark Invoice as Paid</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="markAsPaidForm" action="{{ route('accounting.invoices.mark-paid', $invoice->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select class="form-control" id="payment_method" name="payment_method" required>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cash">Cash</option>
                            <option value="tap_gateway">Tap Gateway</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="payment_date" class="form-label">Payment Date</label>
                        <input type="date" class="form-control" id="payment_date" name="payment_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="amount_paid" class="form-label">Amount Paid</label>
                        <input type="number" step="0.01" class="form-control" id="amount_paid" name="amount_paid" 
                               value="{{ $invoice->getRemainingAmount() }}" required>
                        <small class="form-text text-muted">Remaining amount: {{ number_format($invoice->getRemainingAmount(), 2) }} {{ $invoice->currency }}</small>
                    </div>
                    <div class="mb-3">
                        <label for="transaction_reference" class="form-label">Transaction Reference</label>
                        <input type="text" class="form-control" id="transaction_reference" name="transaction_reference" 
                               placeholder="Bank ref, cheque number, etc.">
                    </div>
                    <div class="mb-3">
                        <label for="payment_documents" class="form-label">Payment Documents</label>
                        <input type="file" class="form-control" id="payment_documents" name="payment_documents[]" 
                               multiple accept=".pdf,.jpg,.jpeg,.png">
                        <small class="form-text text-muted">Upload proof of payment (receipts, bank statements, etc.)</small>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" 
                                  placeholder="Additional notes about this payment..."></textarea>
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

<!-- Logs Modal -->
<div class="modal fade" id="logsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Invoice Activity Logs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="logsContent">
                    <div class="text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p>Loading logs...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmInvoice() {
    if (confirm('Confirm this invoice and send to client?')) {
        fetch(`{{ route('accounting.invoices.confirm', $invoice->id) }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}

function markAsPaid() {
    $('#markAsPaidModal').modal('show');
}

function viewInvoicePDF() {
    window.open('{{ route("accounting.invoices.pdf", $invoice->id) }}', '_blank');
}

function resendInvoice() {
    if (confirm('Resend this invoice to the client?')) {
        fetch(`{{ route('accounting.invoices.resend', $invoice->id) }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(response => response.json())
        .then(data => {
            alert(data.message);
        });
    }
}

function viewInvoiceLogs() {
    $('#logsModal').modal('show');
    
    fetch(`{{ route('accounting.invoices.logs', $invoice->id) }}`)
        .then(response => response.json())
        .then(data => {
            let html = '<div class="timeline">';
            
            data.logs.forEach(log => {
                html += `
                    <div class="timeline-item mb-3">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="bg-primary text-white rounded-circle p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-history fa-sm"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-1">${log.action.replace('_', ' ').toUpperCase()}</h6>
                                    <small class="text-muted">${log.timestamp}</small>
                                </div>
                                <p class="mb-1">${log.notes || 'No additional notes'}</p>
                                <small class="text-muted">by ${log.user}</small>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
            
            document.getElementById('logsContent').innerHTML = html;
        })
        .catch(error => {
            document.getElementById('logsContent').innerHTML = '<div class="alert alert-danger">Failed to load logs</div>';
        });
}

function confirmReceipt(receiptId) {
    if (confirm('Confirm this payment receipt? This will send receipt to client and billing emails.')) {
        fetch(`{{ url('/admin/accounting/receipts') }}/${receiptId}/confirm`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}

function suspendClientAccount() {
    if (confirm('Suspend this client account due to overdue payment? This will prevent them from placing new orders.')) {
        fetch(`{{ route('accounting.clients.suspend', $invoice->client->id) }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                location.reload();
            }
        });
    }
}

document.getElementById('markAsPaidForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    }).then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            $('#markAsPaidModal').modal('hide');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
});
</script>
@endsection