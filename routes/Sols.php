<?php

use App\Http\Controllers\Api\SolsController;
use Illuminate\Support\Facades\Route;


//Sols//
Route::group(['prefix' => '/Sols/{API_TOKEN}', "middleware" => ['token.auth']], function ()
{
    //order
    Route::group(['prefix' => 'order'], function ()
    {
        Route::post('/add', [SolsController::class, 'request_order']);
        Route::post('/update/{id}', [SolsController::class, 'update_order']);
        Route::get('/cancel/{id}', [SolsController::class, 'cancel_order']);
    });


});
//Sols//

