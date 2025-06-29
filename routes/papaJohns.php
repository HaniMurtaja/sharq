<?php

use App\Http\Controllers\Api\PapaJohnsController;
use Illuminate\Support\Facades\Route;


//papaJohns//
Route::group(['prefix' => '/papaJohns/{API_TOKEN}', "middleware" => ['token.auth']], function ()
{
    //order
    Route::group(['prefix' => 'order'], function ()
    {
        Route::post('/add', [PapaJohnsController::class, 'request_order']);
        Route::post('/update/{id}', [PapaJohnsController::class, 'update_order']);
        Route::get('/cancel/{id}', [PapaJohnsController::class, 'cancel_order']);
    });

});
//papaJohns//

