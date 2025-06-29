<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DominosPizzaOrderController;


//blink//
Route::group(['prefix' => '/dominos_pizza/{API_TOKEN}', "middleware" => ['token.auth']], function ()
{
    //order
    Route::group(['prefix' => 'order'], function ()
    {
        Route::post('/add', [DominosPizzaOrderController::class, 'request_order']);
        Route::get('/rate', [DominosPizzaOrderController::class, 'get_order_rate']);
        Route::get('/get/{id}', [DominosPizzaOrderController::class, 'get_order']);
        Route::post('/update/{id}', [DominosPizzaOrderController::class, 'update_order']);
        Route::get('/cancel/{id}', [DominosPizzaOrderController::class, 'cancel_order']);
        Route::get('/track/{id}', [DominosPizzaOrderController::class, 'track_order']);
    });

//    Route::post('webhooks/add',  [DominosPizzaOrderController::class, 'addWebHook']);
//    Route::get('webhooks/list',  [DominosPizzaOrderController::class, 'listWebHook']);
//    Route::get('webhook/delete/{id}',  [DominosPizzaOrderController::class, 'deleteWebHook']);

    Route::group(['prefix' => 'operators'], function ()
    {

        Route::post('/add', [\App\Http\Controllers\Api\OperatorController::class, 'add_operator']);

    });
});
//blink//

