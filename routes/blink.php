<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BlinkController;


//blink//
Route::group(['prefix' => '/blink/{API_TOKEN}', "middleware" => ['token.auth']], function ()
{
    //order
    Route::group(['prefix' => 'order'], function ()
    {
        Route::post('/add', [BlinkController::class, 'request_order']);
        Route::post('/update/{id}', [BlinkController::class, 'update_order']);
        Route::get('/cancel/{id}', [BlinkController::class, 'cancel_order']);
    });

});
//blink//

