<?php

use App\Http\Controllers\Api\AmericanaController;
use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;


//americana//
Route::group(['prefix' => '/americana/{API_TOKEN}', "middleware" => ['token.auth', ForceJsonResponse::class]], function ()
{
    //order
    Route::group(['prefix' => 'order'], function ()
    {
        Route::post('/add', [AmericanaController::class, 'request_order']);
        Route::get('/get/{id}', [AmericanaController::class, 'get_order']);
        Route::post('/update/{id}', [AmericanaController::class, 'update_order']);
        Route::get('/cancel/{id}', [AmericanaController::class, 'cancel_order']);
        Route::get('/track/{id}', [AmericanaController::class, 'track_order']);
    });

//    Route::post('webhooks/add',  [AmericanaController::class, 'addWebHook']);
//    Route::get('webhooks/list',  [AmericanaController::class, 'listWebHook']);
//    Route::get('webhook/delete/{id}',  [AmericanaController::class, 'deleteWebHook']);

});

//
////Route::group(['prefix' => 'americana/'], function ()
////{
////    Route::post('user/auth/login', [AmericanaController::class, 'auth']);
//   Route::get('sse/{user_id}', [AmericanaController::class, 'sse']);
////
////});



//americana//

