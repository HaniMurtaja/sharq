<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SettingsController;

use App\Http\Controllers\Api\DeliverectOrderController;

//deliverect//
Route::group(['prefix' => 'deliverect/{API_TOKEN}' ,"middleware" => [ 'force.json', 'token.auth']] , function () {
    Route::post('/Validate', [DeliverectOrderController::class, 'ValidateOrder']);
    Route::post('/create_job', [DeliverectOrderController::class, 'createOrder']);
    Route::post('/Cancel', [DeliverectOrderController::class, 'CancelOrder']);

});
//deliverect//

