{{-- resources/views/admin/pages/accounting/clients.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Clients Financial Management')

@section('content')
<div class="flex flex-col p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Clients Financial Management</h1>
        <div class="flex gap-2">
            <button onclick="generateBulkInvoices()" class="btn btn-primary">Generate Monthly Invoices</button>
            <a href="{{ route('accounting.clients.export') }}" class="btn btn-secondary">Export Clients</a>
        </div>
    </div>

   
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <form method="GET" class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Client name, email, or account number..." 
                       class="form-control">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="form-control">
                    <option value="">All Clients</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Has Overdue</option>
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('accounting.clients') }}" class="btn btn-secondary">Clear</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Client Info</th>
                        <th>Account Details</th>
                        <th>Financial Summary</th>
                        <th>Invoice Status</th>
                        <th>Last Activity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                    <tr class="{{ !$client->is_active ? 'table-danger' : '' }}">
                        <td>
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div>
                                    <div class="font-semibold">{{ $client->full_name }}</div>
                                    <div class="text-sm text-gray-600">{{ $client->email }}</div>
                                    @if($client->phone)
                                        <div class="text-sm text-gray-500">{{ $client->phone }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm">
                                <div><strong>Account:</strong> {{ $client->client?->account_number ?? 'N/A' }}</div>
                                <div><strong>Status:</strong> 
                                    <span class="badge {{ $client->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $client->is_active ? 'Active' : 'Suspended' }}
                                    </span>
                                </div>
                                <div><strong>Wallet:</strong> {{ number_format($client->financial_summary['wallet_balance'], 2) }} SAR</div>
                                @if($client->client?->billing_emails)
                                    <div><strong>Billing Emails:</strong> {{ count($client->client->billing_emails) }}</div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="text-sm">
                                <div><strong>Total Invoices:</strong> {{ $client->financial_summary['total_invoices'] }}</div>
                                <div><strong>Total Amount:</strong> {{ number_format($client->financial_summary['total_amount'], 2) }} SAR</div>
                                <div><strong>Paid:</strong> <span class="text-green-600">{{ number_format($client->financial_summary['paid_amount'], 2) }} SAR</span></div>
                                <div><strong>Pending:</strong> <span class="text-orange-600">{{ number_format($client->financial_summary['total_amount'] - $client->financial_summary['paid_amount'], 2) }} SAR</span></div>
                            </div>
                        </td>
                        <td>
                            @if($client->financial_summary['overdue_count'] > 0)
                                <span class="badge bg-danger">{{ $client->financial_summary['overdue_count'] }} Overdue</span>
                            @else
                                <span class="badge bg-success">Up to Date</span>
                            @endif
                            
                            @if($client->financial_summary['last_invoice_date'])
                                <div class="text-sm text-gray-600 mt-1">
                                    Last: {{ $client->financial_summary['last_invoice_date']->format('M Y') }}
                                </div>
                            @endif
                        </td>
                        <td class="text-sm text-gray-600">
                            <div>Joined: {{ $client->created_at->format('M d, Y') }}</div>
                            <div>Updated: {{ $client->updated_at->diffForHumans() }}</div>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                    Actions
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('accounting.clients.edit', $client->id) }}">
                                            <i class="fas fa-edit mr-2"></i> Edit Financial Data
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="viewInvoiceHistory({{ $client->id }})">
                                            <i class="fas fa-file-invoice mr-2"></i> View Invoices
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="generateClientInvoice({{ $client->id }})">
                                            <i class="fas fa-plus mr-2"></i> Generate Invoice
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    @if($client->is_active)
                                        @if($client->financial_summary['overdue_count'] > 0)
                                            <li>
                                                <a class="dropdown-item text-warning" href="#" onclick="suspendClient({{ $client->id }})">
                                                    <i class="fas fa-ban mr-2"></i> Suspend Account
                                                </a>
                                            </li>
                                        @endif
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="sendInvoiceReminder({{ $client->id }})">
                                                <i class="fas fa-bell mr-2"></i> Send Reminder
                                            </a>
                                        </li>
                                    @else
                                        <li>
                                            <a class="dropdown-item text-success" href="#" onclick="reactivateClient({{ $client->id }})">
                                                <i class="fas fa-check mr-2"></i> Reactivate Account
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-8">
                            <div class="text-gray-500">
                                <i class="fas fa-users fa-3x mb-3"></i>
                                <p>No clients found matching your criteria</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{ $clients->appends(request()->query())->links() }}
    </div>
</div>


<div class="modal fade" id="generateInvoiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate Invoice for Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="generateInvoiceForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="invoice_month" class="form-label">Invoice Month</label>
                        <input type="month" class="form-control" id="invoice_month" name="month" value="{{ date('Y-m') }}" required>
                    </div>
                    <input type="hidden" id="selected_client_id" name="client_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Generate Invoice</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="invoiceHistoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Invoice History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="invoiceHistoryContent">
                    <div class="text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p>Loading invoice history...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function generateClientInvoice(clientId) {
    document.getElementById('selected_client_id').value = clientId;
    document.getElementById('generateInvoiceForm').action = '{{ route("accounting.invoices.generate") }}';
    $('#generateInvoiceModal').modal('show');
}

function viewInvoiceHistory(clientId) {
    $('#invoiceHistoryModal').modal('show');
    
    fetch(`/admin/accounting/clients/${clientId}/invoice-history`)
        .then(response => response.json())
        .then(data => {
            let html = '<div class="row">';
            
         
            html += '<div class="col-md-12 mb-4">';
            html += '<div class="row">';
            html += `<div class="col-md-3"><div class="bg-primary text-white p-3 rounded text-center"><h4>${data.total_summary.total_invoices}</h4><small>Total Invoices</small></div></div>`;
            html += `<div class="col-md-3"><div class="bg-success text-white p-3 rounded text-center"><h4>${(data.total_summary.total_paid/1000).toFixed(1)}K</h4><small>Total Paid (SAR)</small></div></div>`;
            html += `<div class="col-md-3"><div class="bg-warning text-white p-3 rounded text-center"><h4>${(data.total_summary.total_pending/1000).toFixed(1)}K</h4><small>Pending (SAR)</small></div></div>`;
            html += `<div class="col-md-3"><div class="bg-info text-white p-3 rounded text-center"><h4>${((data.total_summary.total_paid/data.total_summary.total_amount)*100).toFixed(1)}%</h4><small>Payment Rate</small></div></div>`;
            html += '</div></div>';
            
           
            html += '<div class="col-md-12">';
            html += '<h6>Monthly Breakdown</h6>';
            html += '<div class="table-responsive">';
            html += '<table class="table table-sm">';
            html += '<thead><tr><th>Month</th><th>Invoices</th><th>Orders</th><th>Amount</th><th>Status</th></tr></thead><tbody>';
            
            data.monthly_breakdown.forEach(month => {
                const paymentRate = (month.paid_amount / month.total_amount * 100).toFixed(1);
                html += `<tr>
                    <td>${month.month}</td>
                    <td>${month.invoice_count}</td>
                    <td>${month.order_count}</td>
                    <td>${month.total_amount.toLocaleString()} SAR</td>
                    <td><span class="badge ${month.pending_amount > 0 ? 'bg-warning' : 'bg-success'}">${paymentRate}% Paid</span></td>
                </tr>`;
            });
            
            html += '</tbody></table></div></div></div>';
            
            document.getElementById('invoiceHistoryContent').innerHTML = html;
        })
        .catch(error => {
            document.getElementById('invoiceHistoryContent').innerHTML = '<div class="alert alert-danger">Failed to load invoice history</div>';
        });
}

function suspendClient(clientId) {
    if (confirm('Are you sure you want to suspend this client account? This will prevent them from placing new orders.')) {
        fetch(`/admin/accounting/clients/${clientId}/suspend`, {
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

function reactivateClient(clientId) {
    if (confirm('Are you sure you want to reactivate this client account?')) {
        fetch(`/admin/accounting/clients/${clientId}/reactivate`, {
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

function sendInvoiceReminder(clientId) {
    if (confirm('Send payment reminder to this client for all unpaid invoices?')) {
        fetch(`/admin/accounting/clients/${clientId}/send-reminder`, {
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


document.getElementById('generateInvoiceForm').addEventListener('submit', function(e) {
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
            $('#generateInvoiceModal').modal('hide');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
});
</script>
@endsection