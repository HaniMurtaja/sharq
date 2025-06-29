<?php

use App\Http\Controllers\Api\B1BurgerController;
use Illuminate\Support\Facades\Route;


//blink//
Route::group(['prefix' => '/b1Burger/{API_TOKEN}', "middleware" => ['token.auth']], function ()
{
    //order
    Route::group(['prefix' => 'order'], function ()
    {
        Route::post('/add', [B1BurgerController::class, 'request_order']);
        Route::post('/update/{id}', [B1BurgerController::class, 'update_order']);
        Route::get('/cancel/{id}', [B1BurgerController::class, 'cancel_order']);
    });

});
//blink//

