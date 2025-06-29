<?php

use App\Http\Controllers\Api\TrybotrController;
use Illuminate\Support\Facades\Route;


//trybotr//
Route::group(['prefix' => '/trybotr/{API_TOKEN}', "middleware" => ['token.auth']], function ()
{
    //order
    Route::group(['prefix' => 'order'], function ()
    {
        Route::post('/add', [TrybotrController::class, 'request_order']);
        Route::post('/update/{id}', [TrybotrController::class, 'update_order']);
        Route::get('/cancel/{id}', [TrybotrController::class, 'cancel_order']);
    });

});
//trybotr//

