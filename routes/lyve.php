<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\lyve\LyveOrderController;

//lyve//
Route::group(['prefix' => 'lyve' ,"middleware" => [ 'force.json','LyveMiddleware']] , function () {
    Route::put('/create/orders/{order_id}', [LyveOrderController::class, 'UpdateOrder']);
    Route::post('create/orders', [LyveOrderController::class, 'createOrder']);
    Route::delete('/create/orders/{order_id}', [LyveOrderController::class, 'CancelOrder']);
    Route::get('/track/{id}', [LyveOrderController::class, 'track_order']);
    Route::get('/orders/{order_id}/track', [LyveOrderController::class, 'track_order']);

});
//lyve//

