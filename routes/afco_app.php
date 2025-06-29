<?php

use App\Http\Controllers\Api\AfcoAppController;
use Illuminate\Support\Facades\Route;


//afco_app//
Route::group(['prefix' => '/afco_app/{API_TOKEN}', "middleware" => ['token.auth']], function ()
{
    //order
    Route::group(['prefix' => 'order'], function ()
    {
        Route::post('/add', [AfcoAppController::class, 'request_order']);
        Route::post('/update/{id}', [AfcoAppController::class, 'update_order']);
        Route::get('/cancel/{id}', [AfcoAppController::class, 'cancel_order']);
    });

});
//afco_app//

