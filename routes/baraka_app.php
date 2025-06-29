<?php

use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BarakaAppController;


//baraka//
Route::group(['prefix' => '/baraka_app/{API_TOKEN}', "middleware" => ['token.auth', ForceJsonResponse::class]], function ()
{
    //order
    Route::group(['prefix' => 'order'], function ()
    {
        Route::post('/add', [BarakaAppController::class, 'request_order']);
        Route::post('/update/{id}', [BarakaAppController::class, 'update_order']);
        Route::get('/cancel/{id}', [BarakaAppController::class, 'cancel_order']);
    });

});

Route::group(['prefix' => '/baraka_app/', "middleware" => ['force.json']], function ()
{
    //client
    Route::post('/create-client', [BarakaAppController::class, 'create_client']);
});

//baraka//

