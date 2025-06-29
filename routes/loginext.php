<?php

use App\Http\Controllers\Api\LoginextController;
use Illuminate\Support\Facades\Route;


//loginext//
Route::group(['prefix' => '/loginext/{API_TOKEN}', "middleware" => ['token.auth']], function ()
{
    //order
    Route::group(['prefix' => 'order'], function ()
    {
        Route::post('/add', [LoginextController::class, 'request_order']);
        Route::post('/update/{id}', [LoginextController::class, 'update_order']);
        Route::get('/cancel/{id}', [LoginextController::class, 'cancel_order']);
    });

});
//loginext//

