{{-- resources/views/admin/pages/accounting/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Accounting Dashboard')

@section('content')
<div class="flex flex-col p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Accounting Dashboard</h1>
        <div class="flex gap-2">
            <a href="{{ route('accounting.invoices.generate') }}" class="btn btn-primary">Generate Monthly Invoices</a>
            <a href="{{ route('accounting.settings') }}" class="btn btn-secondary">Settings</a>
        </div>
    </div>

   
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-file-invoice text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Total Invoices</p>
                    <p class="text-lg font-semibold">{{ $stats['total_invoices'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Pending Review</p>
                    <p class="text-lg font-semibold">{{ $stats['pending_invoices'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Overdue</p>
                    <p class="text-lg font-semibold">{{ $stats['overdue_invoices'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-dollar-sign text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Total Revenue</p>
                    <p class="text-lg font-semibold">{{ number_format($stats['total_revenue'], 2) }} SAR</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-2 bg-orange-100 rounded-lg">
                    <i class="fas fa-hourglass-half text-orange-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Pending Amount</p>
                    <p class="text-lg font-semibold">{{ number_format($stats['pending_amount'], 2) }} SAR</p>
                </div>
            </div>
        </div>
    </div>

 
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
            <div class="space-y-2">
                <a href="{{ route('accounting.invoices') }}" class="block p-3 bg-gray-50 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-list mr-2"></i> View All Invoices
                </a>
                <a href="{{ route('accounting.clients') }}" class="block p-3 bg-gray-50 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-users mr-2"></i> Manage Clients
                </a>
                <button onclick="generateMonthlyInvoices()" class="block w-full text-left p-3 bg-gray-50 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-plus mr-2"></i> Generate Monthly Invoices
                </button>
                <button onclick="sendOverdueNotifications()" class="block w-full text-left p-3 bg-gray-50 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-bell mr-2"></i> Send Overdue Notifications
                </button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Recent Invoices</h3>
            <div class="space-y-2">
               
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Overdue Alerts</h3>
            <div class="space-y-2">
             
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="generateInvoicesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate Monthly Invoices</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('accounting.invoices.generate') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="month" class="form-label">Month</label>
                        <input type="month" class="form-control" id="month" name="month" value="{{ date('Y-m') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="client_id" class="form-label">Client (Optional)</label>
                        <select class="form-control" id="client_id" name="client_id">
                            <option value="">All Clients</option>
                           
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Generate Invoices</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function generateMonthlyInvoices() {
    $('#generateInvoicesModal').modal('show');
}

function sendOverdueNotifications() {
    if (confirm('Are you sure you want to send overdue notifications to all clients with overdue invoices?')) {
        fetch('{{ route("accounting.notifications.overdue") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(response => {
            if (response.ok) {
                alert('Overdue notifications sent successfully');
            } else {
                alert('Error sending notifications');
            }
        });
    }
}
</script>
@endsection
