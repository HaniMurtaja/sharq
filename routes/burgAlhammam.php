<?php

use App\Http\Controllers\Api\BurgAlhammamController;
use Illuminate\Support\Facades\Route;


//Burg-alhammam//
Route::group(['prefix' => '/burg-alhammam/{API_TOKEN}', "middleware" => ['token.auth']], function ()
{
    //order
    Route::group(['prefix' => 'order'], function ()
    {
        Route::post('/add', [BurgAlhammamController::class, 'request_order']);
        Route::post('/update/{id}', [BurgAlhammamController::class, 'update_order']);
        Route::get('/cancel/{id}', [BurgAlhammamController::class, 'cancel_order']);
    });

});
//Burg-alhammam//

