<?php

use App\Http\Controllers\Api\PandaController;
use Illuminate\Support\Facades\Route;


//panda//
Route::group(['prefix' => '/panda/{API_TOKEN}', "middleware" => ['token.auth']], function ()
{
    //order
    Route::group(['prefix' => 'order'], function ()
    {
        Route::post('/add', [PandaController::class, 'request_order']);
        Route::post('/update/{id}', [PandaController::class, 'update_order']);
        Route::get('/cancel/{id}', [PandaController::class, 'cancel_order']);
    });

});
//panda//

