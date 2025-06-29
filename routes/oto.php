<?php

use App\Http\Controllers\Api\OtoController;
use Illuminate\Support\Facades\Route;


//oto//
Route::group(['prefix' => '/oto/{API_TOKEN}', "middleware" => ['token.auth']], function ()
{
    //order
    Route::group(['prefix' => 'order'], function ()
    {
        Route::post('/add', [OtoController::class, 'request_order']);
        Route::post('/update/{id}', [OtoController::class, 'update_order']);
        Route::get('/cancel/{id}', [OtoController::class, 'cancel_order']);
    });

});
//oto//

