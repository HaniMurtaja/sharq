<?php

use App\Http\Controllers\Api\TwinaController;
use Illuminate\Support\Facades\Route;


//twina//
Route::group(['prefix' => '/twina/{API_TOKEN}', "middleware" => ['token.auth']], function ()
{
    //order
    Route::group(['prefix' => 'order'], function ()
    {
        Route::post('/add', [TwinaController::class, 'request_order']);
        Route::post('/update/{id}', [TwinaController::class, 'update_order']);
        Route::get('/cancel/{id}', [TwinaController::class, 'cancel_order']);
    });

});
//twina//

