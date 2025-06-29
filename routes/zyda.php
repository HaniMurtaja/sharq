<?php

use App\Http\Controllers\Api\ZydaController;
use Illuminate\Support\Facades\Route;


//zyda//
Route::group(['prefix' => '/zyda/{API_TOKEN}', "middleware" => ['token.auth']], function ()
{
    //order
    Route::group(['prefix' => 'order'], function ()
    {
        Route::post('/add', [ZydaController::class, 'request_order']);
        Route::post('/update/{id}', [ZydaController::class, 'update_order']);
        Route::get('/cancel/{id}', [ZydaController::class, 'cancel_order']);
    });

});
//zyda//

