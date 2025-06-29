<?php

use App\Http\Controllers\Api\BostaniController;
use Illuminate\Support\Facades\Route;


//bostani//
Route::group(['prefix' => '/bostani/{API_TOKEN}', "middleware" => ['token.auth']], function ()
{
    //order
    Route::group(['prefix' => 'order'], function ()
    {
        Route::post('/add', [BostaniController::class, 'request_order']);
        Route::post('/update/{id}', [BostaniController::class, 'update_order']);
        Route::get('/cancel/{id}', [BostaniController::class, 'cancel_order']);
    });

});
//bostani//

