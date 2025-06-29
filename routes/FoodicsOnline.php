<?php

use App\Http\Controllers\Api\FoodicsOnlineController;
use Illuminate\Support\Facades\Route;


//FoodicsOnline//
Route::group(['prefix' => '/FoodicsOnline/{API_TOKEN}', "middleware" => ['token.auth']], function ()
{
    //order
    Route::group(['prefix' => 'order'], function ()
    {
        Route::post('/add', [FoodicsOnlineController::class, 'request_order']);
        Route::post('/update/{id}', [FoodicsOnlineController::class, 'update_order']);
        Route::get('/cancel/{id}', [FoodicsOnlineController::class, 'cancel_order']);
        Route::get('/get/{id}', [FoodicsOnlineController::class, 'get_order']);
    });

    Route::post('webhooks/add',  [FoodicsOnlineController::class, 'addWebHook']);
    Route::get('webhooks/list',  [FoodicsOnlineController::class, 'listWebHook']);
    Route::get('webhook/delete/{id}',  [FoodicsOnlineController::class, 'deleteWebHook']);

});
//FoodicsOnline//

