{{-- resources/views/admin/pages/accounting/settings.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Accounting Settings')

@section('content')
<div class="flex flex-col p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Accounting Settings</h1>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <form action="{{ route('accounting.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold mb-4">Company Information</h3>
                    
                    <div class="mb-3">
                        <label for="company_name" class="form-label">Company Name</label>
                        <input type="text" class="form-control" id="company_name" name="company_name" 
                               value="{{ $settings->company_name }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="tax_id" class="form-label">Tax ID / VAT Number</label>
                        <input type="text" class="form-control" id="tax_id" name="tax_id" 
                               value="{{ $settings->tax_id }}">
                    </div>

                    <div class="mb-3">
                        <label for="commercial_registration" class="form-label">Commercial Registration</label>
                        <input type="text" class="form-control" id="commercial_registration" name="commercial_registration" 
                               value="{{ $settings->commercial_registration }}">
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3">{{ $settings->address }}</textarea>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact Information</h3>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" 
                               value="{{ $settings->phone }}">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="{{ $settings->email }}">
                    </div>

                    <div class="mb-3">
                        <label for="bank_account" class="form-label">Bank Account</label>
                        <input type="text" class="form-control" id="bank_account" name="bank_account" 
                               value="{{ $settings->bank_account }}">
                    </div>

                    <div class="mb-3">
                        <label for="iban" class="form-label">IBAN</label>
                        <input type="text" class="form-control" id="iban" name="iban" 
                               value="{{ $settings->iban }}">
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <h3 class="text-lg font-semibold mb-4">Invoice Settings</h3>
                
                <div class="mb-3">
                    <label for="payment_due_days" class="form-label">Payment Due Days</label>
                    <input type="number" class="form-control" id="payment_due_days" name="payment_due_days" 
                           value="{{ $settings->payment_due_days }}" min="1" max="90" required>
                    <small class="form-text text-muted">Number of days after invoice date when payment is due</small>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="btn btn-primary">Save Settings</button>
                <a href="{{ route('accounting.dashboard') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection