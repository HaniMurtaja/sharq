<?php

use Livewire\Livewire;
use App\Livewire\Welcome;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
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
