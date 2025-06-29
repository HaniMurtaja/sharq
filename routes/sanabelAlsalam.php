<?php

use App\Http\Controllers\Api\SanabelAlsalamController;
use Illuminate\Support\Facades\Route;


//sanabelAlsalam//
Route::group(['prefix' => '/sanabelAlsalam/{API_TOKEN}', "middleware" => ['token.auth']], function ()
{
    //order
    Route::group(['prefix' => 'order'], function ()
    {
        Route::post('/add', [SanabelAlsalamController::class, 'request_order']);
        Route::post('/update/{id}', [SanabelAlsalamController::class, 'update_order']);
        Route::get('/cancel/{id}', [SanabelAlsalamController::class, 'cancel_order']);
        Route::get('/get/{id}', [SanabelAlsalamController::class, 'get_order']);
    });

});
//sanabelAlsalam//

