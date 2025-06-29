<?php


use App\Http\Controllers\Api\Integration\IntegrationController;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => '/integration/{API_TOKEN}/orders', "middleware" => ['token.auth']], function () {


    Route::post('/create', [IntegrationController::class, 'orderCreate']);
    Route::post('/update', [IntegrationController::class, 'orderUpdate']);
    Route::post('/cancel', [IntegrationController::class, 'orderCancel']);
    Route::post('/get', [IntegrationController::class, 'getOrder']);
    Route::post('/track-order', [IntegrationController::class, 'trackOrder']);
});




