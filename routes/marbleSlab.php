<?php

use App\Http\Controllers\Api\MarbleSlabController;
use Illuminate\Support\Facades\Route;


//marbleSlab//
Route::group(['prefix' => '/marbleSlab/{API_TOKEN}', "middleware" => ['token.auth']], function ()
{
    //order
    Route::group(['prefix' => 'order'], function ()
    {
        Route::post('/add', [MarbleSlabController::class, 'request_order']);
        Route::post('/update/{id}', [MarbleSlabController::class, 'update_order']);
        Route::get('/cancel/{id}', [MarbleSlabController::class, 'cancel_order']);
    });

});
//marbleSlab//

