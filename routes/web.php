<?php

use Livewire\Livewire;
use App\Livewire\Welcome;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\Admin\AccountingController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::prefix('accounting')->name('accounting.')->group(function () {
        // Dashboard
        Route::get('/', [AccountingController::class, 'index'])->name('dashboard');
        
        // Clients Management
        Route::get('/clients', [AccountingController::class, 'clients'])->name('clients');
        Route::get('/clients/{id}/edit', [AccountingController::class, 'editClient'])->name('clients.edit');
        Route::put('/clients/{id}', [AccountingController::class, 'updateClient'])->name('clients.update');
        Route::post('/clients/{id}/suspend', [AccountingController::class, 'suspendClient'])->name('clients.suspend');
        Route::post('/clients/{id}/reactivate', [AccountingController::class, 'reactivateClient'])->name('clients.reactivate');
        Route::get('/clients/{clientId}/invoice-history', [AccountingController::class, 'getClientInvoiceHistory'])->name('clients.invoice-history');
        Route::get('/clients/export', [AccountingController::class, 'exportClients'])->name('clients.export');
        
        // Invoices Management
        Route::get('/invoices', [AccountingController::class, 'invoices'])->name('invoices');
        Route::get('/invoices/data', [AccountingController::class, 'getInvoicesData'])->name('invoices.data');
        Route::post('/invoices/generate', [AccountingController::class, 'generateMonthlyInvoices'])->name('invoices.generate');
        Route::get('/invoices/export', [AccountingController::class, 'exportInvoices'])->name('invoices.export');
        Route::get('/invoices/{invoice}', [AccountingController::class, 'showInvoice'])->name('invoices.show');
        Route::get('/invoices/{invoice}/pdf', [AccountingController::class, 'downloadInvoicePdf'])->name('invoices.pdf');
        Route::post('/invoices/{invoice}/confirm', [AccountingController::class, 'confirmInvoice'])->name('invoices.confirm');
        Route::post('/invoices/{invoice}/mark-paid', [AccountingController::class, 'markInvoiceAsPaid'])->name('invoices.mark-paid');
        Route::post('/invoices/{id}/resend', [AccountingController::class, 'resendInvoice'])->name('invoices.resend');
        Route::get('/invoices/{id}/logs', [AccountingController::class, 'getInvoiceLogs'])->name('invoices.logs');
        Route::post('/invoices/bulk-actions', [AccountingController::class, 'bulkInvoiceActions'])->name('invoices.bulk-actions');
        
        // Payment Receipts
        Route::get('/invoices/{id}/receipts', [AccountingController::class, 'getPaymentReceipts'])->name('receipts.get');
        Route::post('/receipts/{id}/confirm', [AccountingController::class, 'confirmPaymentReceipt'])->name('receipts.confirm');
        
        // Notifications
        Route::post('/notifications/overdue', [AccountingController::class, 'sendOverdueNotifications'])->name('notifications.overdue');
        
        // Settings
        Route::get('/settings', [AccountingController::class, 'settings'])->name('settings');
        Route::put('/settings', [AccountingController::class, 'updateSettings'])->name('settings.update');
        
        // Reports
        Route::get('/reports', [AccountingController::class, 'getAccountingReports'])->name('reports');
        Route::get('/dashboard-data', [AccountingController::class, 'getDashboardData'])->name('dashboard.data');
    });
});
// Route::get('/', Welcome::class);

Route::redirect('/', 'admin/login');
// Livewire::setUpdateRoute(function ($handle) {
//     return Route::post('/livewire/update', $handle);
// });
// Livewire::setScriptRoute(function ($handle) {
//     return Route::get('/livewire/livewire.js', $handle);
// });
// Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => 'web'], function () {
//     // Your other localized routes...
//     Livewire::setUpdateRoute(function ($handle) {
//         return Route::post('/public/vendor/livewire/update', $handle);
//     });
// });

// ...
// Livewire::setScriptRoute(function ($handle) {
//     return Route::get('/Al_Shrouq_Express_system/livewire/livewire.js', $handle);
// });

// Livewire::setUpdateRoute(function ($handle) {
//     return Route::get('/Al_Shrouq_Express_system/en/admin/livewire/update', $handle);
// });
// ..



Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'web']
], function () {
    Route::get('track_order/{id}', [\App\Http\Controllers\Admin\HomeController::class, 'track_order'])->name('track_order');

    require_once __DIR__ . '/admin.php';

});
