<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SettingsController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::post('save-account', [SettingsController::class, 'saveAccount'])->name('save-account');

Route::post('save-vehicle-types', [SettingsController::class, 'saveVehicleTypes'])->name('save-vehicle-types');

Route::post('save-privacy', [SettingsController::class, 'savePrivacy'])->name('save-privacy');

Route::post('save-api-settings', [SettingsController::class, 'saveApi'])->name('save-api-settings');

Route::post('save-operators', [SettingsController::class, 'saveOperators'])->name('save-operators');


Route::post('save-auto-dispatch', [SettingsController::class, 'saveAutoDispatch'])->name('save-auto-dispatch');

Route::post('save-dishpatcher-page', [SettingsController::class, 'saveoDispatcherPage'])->name('save-dishpatcher-page');

Route::post('save-dashboard-page', [SettingsController::class, 'saveDashboardPage'])->name('save-dashboard-page');

Route::post('save-services', [SettingsController::class, 'saveServices'])->name('save-services');

Route::post('save-eta-settings', [SettingsController::class, 'saveEtaSettinga'])->name('save-eta-settings');

Route::post('save-customer-messages', [SettingsController::class, 'saveCustomerMessages'])->name('save-customer-messages');

Route::post('save-announcements', [SettingsController::class, 'saveAnnouncements'])->name('save-announcements');

Route::post('save-taxes', [SettingsController::class, 'saveTaxes'])->name('save-taxes');


Route::post('save-payment', [SettingsController::class, 'savePayment'])->name('save-payment');


Route::post('save-dispatching', [SettingsController::class, 'saveDispatching'])->name('save-dispatching');


Route::post('save-foodics', [SettingsController::class, 'saveFoodics'])->name('save-foodics');

Route::post('save-business-hours', [SettingsController::class, 'saveBusinessHours'])->name('save-business-hours');

Route::post('save-special-business-hours', [SettingsController::class, 'saveSpecialBusinessHours'])->name('save-special-business-hours');


Route::get('get-special-hours', [SettingsController::class, 'getSopecialHours'])->name('get-special-hours');














