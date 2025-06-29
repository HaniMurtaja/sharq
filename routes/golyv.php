<?php

use App\Http\Controllers\Api\GolyvController;
use Illuminate\Support\Facades\Route;


//golyv//
Route::group(['prefix' => '/golyv/{API_TOKEN}', "middleware" => ['token.auth']], function ()
{
    //order
    Route::group(['prefix' => 'order'], function ()
    {
        Route::post('/add', [GolyvController::class, 'request_order']);
        Route::post('/update/{id}', [GolyvController::class, 'update_order']);
        Route::get('/cancel/{id}', [GolyvController::class, 'cancel_order']);
    });

});
//golyv//

