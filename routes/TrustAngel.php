<?php

use App\Http\Controllers\Api\TrustAngelController;
use Illuminate\Support\Facades\Route;


//TrustAngel//
Route::group(['prefix' => '/TrustAngel/{API_TOKEN}', "middleware" => ['token.auth']], function ()
{
    //order
    Route::group(['prefix' => 'order'], function ()
    {
        Route::post('/add', [TrustAngelController::class, 'request_order']);
        Route::post('/update/{id}', [TrustAngelController::class, 'update_order']);
        Route::get('/cancel/{id}', [TrustAngelController::class, 'cancel_order']);
    });

});
//TrustAngel//

