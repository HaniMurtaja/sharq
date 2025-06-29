<?php

use App\Http\Controllers\Api\MaestropizzaController;
use Illuminate\Support\Facades\Route;


//maestropizza//
Route::group(['prefix' => '/maestropizza/{API_TOKEN}', "middleware" => ['token.auth']], function ()
{
    //order
    Route::group(['prefix' => 'order'], function ()
    {
        Route::post('/add', [MaestropizzaController::class, 'request_order']);
        Route::post('/update/{id}', [MaestropizzaController::class, 'update_order']);
        Route::get('/cancel/{id}', [MaestropizzaController::class, 'cancel_order']);
        Route::get('/track/{id}', [MaestropizzaController::class, 'track_order']);

    });
});
//maestropizza//

