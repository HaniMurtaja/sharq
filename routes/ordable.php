<?php

use App\Http\Controllers\Api\OrdableController;
use Illuminate\Support\Facades\Route;


//ordable//
Route::group(['prefix' => '/ordable/{API_TOKEN}', "middleware" => ['token.auth']], function ()
{
    //order
    Route::group(['prefix' => 'order'], function ()
    {
        Route::post('/add', [OrdableController::class, 'request_order']);
        Route::post('/update/{id}', [OrdableController::class, 'update_order']);
        Route::get('/cancel/{id}', [OrdableController::class, 'cancel_order']);
    });

});
//ordable//

