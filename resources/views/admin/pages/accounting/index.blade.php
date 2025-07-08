@extends('admin.layouts.app')

@section('title', 'Accounting Dashboard - Al Shrouq Express')

@section('content')

@include('admin.includes.content-header', [
    'header' => 'Accounting Dashboard', 
    'title' => 'Accounting'
])

<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total_invoices'] }}</h3>
                    <p>Total Invoices</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <a href="{{ url('/admin/accounting/invoices') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['pending_review'] }}</h3>
                    <p>Pending Review</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="{{ url('/admin/accounting/invoices?status=generated_under_review') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['overdue_invoices'] }}</h3>
                    <p>Overdue Invoices</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <a href="{{ url('/admin/accounting/invoices?overdue=1') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($stats['total_revenue'], 0) }}</h3>
                    <p>Total Revenue (SAR)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <a href="{{ url('/admin/accounting/invoices?status=paid') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Invoices and Overdue Alerts -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Invoices</h3>
                    <div class="card-tools">
                        <a href="{{ url('/admin/accounting/invoices') }}" class="btn btn-tool">
                            <i class="fas fa-eye"></i> View All
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($recentInvoices->count() > 0)
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Client</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentInvoices as $invoice)
                                <tr>
                                    <td>
                                        <a href="{{ url('/admin/accounting/invoices/' . $invoice->id) }}">
                                            {{ $invoice->invoice_number }}
                                        </a>
                                    </td>
                                    <td>{{ $invoice->client->first_name }} {{ $invoice->client->last_name }}</td>
                                    <td>{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}</td>
                                    <td>
                                        @if($invoice->status === 'generated_under_review')
                                            <span class="badge badge-warning">Under Review</span>
                                        @elseif($invoice->status === 'confirmed_sent_unpaid')
                                            <span class="badge badge-info">Sent - Unpaid</span>
                                        @elseif($invoice->status === 'paid')
                                            <span class="badge badge-success">Paid</span>
                                        @endif
                                    </td>
                                    <td>{{ $invoice->invoice_date->format('M d, Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center p-4">
                            <p class="text-muted">No recent invoices found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Overdue Alerts</h3>
                </div>
                <div class="card-body p-0">
                    @if($overdueAlerts->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($overdueAlerts as $alert)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>{{ $alert->invoice_number }}</strong><br>
                                        <small>{{ $alert->client->first_name }} {{ $alert->client->last_name }}</small>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-danger">{{ number_format($alert->total_amount, 2) }} {{ $alert->currency }}</span><br>
                                        <small class="text-muted">{{ $alert->due_date->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                        <div class="card-footer">
                            <a href="{{ url('/admin/accounting/invoices?overdue=1') }}" class="btn btn-danger btn-sm">
                                View All Overdue
                            </a>
                        </div>
                    @else
                        <div class="text-center p-4">
                            <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
                            <p class="text-muted mb-0">No overdue invoices!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <button class="btn btn-primary btn-block" onclick="generateInvoice()">
                                <i class="fas fa-plus"></i> Generate Invoice
                            </button>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ url('/admin/accounting/clients') }}" class="btn btn-info btn-block">
                                <i class="fas fa-users"></i> Manage Clients
                            </a>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-warning btn-block" onclick="sendOverdueNotifications()">
                                <i class="fas fa-bell"></i> Send Overdue Notifications
                            </button>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ url('/admin/accounting/settings') }}" class="btn btn-secondary btn-block">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                        </div>
                    </div>
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
                <h4 class="modal-title">Generate Invoice</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="generateInvoiceForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Month</label>
                        <input type="month" name="month" class="form-control" value="{{ now()->format('Y-m') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Client (Optional)</label>
                        <select name="client_id" class="form-control">
                            <option value="">All Clients</option>
                            <!-- Client options will be loaded here -->
                        </select>
                        <small class="form-text text-muted">Leave empty to generate for all clients</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="generateInvoiceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Generate Invoice</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="generateInvoiceForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="month">Month</label>
                        <input type="month" name="month" class="form-control" value="{{ now()->format('Y-m') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="client_id">Client (Optional)</label>
                        <select name="client_id" class="form-control">
                            <option value="">All Clients</option>
                            {{-- Client options will be loaded dynamically --}}
                        </select>
                        <small class="form-text text-muted">Leave empty to generate for all clients</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
$(document).ready(function() {
    console.log('Accounting dashboard DOM ready...');

    // Generate invoice function
    window.generateInvoice = function() {
        console.log('Opening generate invoice modal...');
        $('#generateInvoiceModal').modal('show');
    };

    // Send overdue notifications function
    window.sendOverdueNotifications = function() {
        if (confirm('Send overdue notifications to all clients with overdue invoices?')) {
            fetch('/admin/accounting/notifications/overdue', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Overdue notifications sent successfully!');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while sending notifications.');
            });
        }
    };

    // Handle generate invoice form submission
    $('#generateInvoiceForm').on('submit', function(e) {
        e.preventDefault();
        console.log('Submitting generate invoice form...');
        
        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        // Show loading state
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Generating...').prop('disabled', true);
        
        $.ajax({
            url: '/admin/accounting/invoices/generate',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Generate invoice response:', response);
                if (response.success) {
                    alert('Invoice generated successfully!');
                    $('#generateInvoiceModal').modal('hide');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Generate invoice error:', error);
                console.error('Response:', xhr.responseText);
                
                let errorMessage = 'An error occurred while generating the invoice.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert('Error: ' + errorMessage);
            },
            complete: function() {
                // Restore button state
                submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });

    // Load clients for the dropdown when modal is opened
    $('#generateInvoiceModal').on('show.bs.modal', function() {
        // Load clients dynamically if needed
        console.log('Modal opened, clients could be loaded here');
    });
});
</script>

@endsection
