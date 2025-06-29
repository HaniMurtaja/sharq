<?php

use App\Http\Controllers\Api\MaqlopaController;
use Illuminate\Support\Facades\Route;


//Maqlopa//
Route::group(['prefix' => '/maqlopa/{API_TOKEN}', "middleware" => ['token.auth']], function ()
{
    //order
    Route::group(['prefix' => 'order'], function ()
    {
        Route::post('/add', [MaqlopaController::class, 'request_order']);
        Route::post('/update/{id}', [MaqlopaController::class, 'update_order']);
        Route::get('/cancel/{id}', [MaqlopaController::class, 'cancel_order']);
    });

});
//Maqlopa//

