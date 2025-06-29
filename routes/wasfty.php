<?php

use App\Http\Controllers\Api\WasftyController;
use Illuminate\Support\Facades\Route;


//wasfty//
Route::group(['prefix' => '/wasfty/{API_TOKEN}', "middleware" => ['token.auth']], function ()
{
    //order
    Route::group(['prefix' => 'order'], function ()
    {
        Route::post('/add', [WasftyController::class, 'request_order']);
        Route::post('/update/{id}', [WasftyController::class, 'update_order']);
        Route::get('/cancel/{id}', [WasftyController::class, 'cancel_order']);
        Route::get('/track/{id}', [WasftyController::class, 'track_order']);
        Route::get('/get/{id}', [WasftyController::class, 'get_order']);

    });

});
//wasfty//

