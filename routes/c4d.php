<?php

use App\Http\Controllers\Api\C4dController;
use Illuminate\Support\Facades\Route;


//c4d//
Route::group(['prefix' => '/c4d/{API_TOKEN}', "middleware" => ['token.auth']], function () {
    //order
    Route::group(['prefix' => 'order'], function () {
        Route::post('/add', [C4dController::class, 'request_order']);
        Route::post('/update/{id}', [C4dController::class, 'update_order']);
        Route::get('/cancel/{id}', [C4dController::class, 'cancel_order']);


        Route::get('/get/{id}', [C4dController::class, 'get_order']);

        Route::get('/track/{id}', [C4dController::class, 'track_order']);
    });
});
//c4d//
