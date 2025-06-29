<?php

use App\Http\Controllers\Api\DgteraController;
use Illuminate\Support\Facades\Route;


//dgtera//
Route::group(['prefix' => '/dgtera/{API_TOKEN}', "middleware" => ['token.auth']], function ()
{
    //order
    Route::group(['prefix' => 'order'], function ()
    {
        Route::post('/add', [DgteraController::class, 'request_order']);
        Route::post('/update/{id}', [DgteraController::class, 'update_order']);
        Route::get('/cancel/{id}', [DgteraController::class, 'cancel_order']);
    });

});
//dgtera//

