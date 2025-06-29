<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\QudEliteController;


//arabianOud//
Route::group(['prefix' => '/qudElite/{API_TOKEN}', "middleware" => ['token.auth']], function ()
{
    //order
    Route::group(['prefix' => 'order'], function ()
    {
        Route::post('/add', [QudEliteController::class, 'request_order']);
        Route::post('/update/{id}', [QudEliteController::class, 'update_order']);
        Route::get('/cancel/{id}', [QudEliteController::class, 'cancel_order']);
    });

});
//arabianOud//

