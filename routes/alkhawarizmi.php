<?php


use App\Http\Controllers\Api\AlkhawarizmiController;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => '/alkhawarizmi/{API_TOKEN}/order', "middleware" => ['token.auth']], function () {


    Route::post('/add', [AlkhawarizmiController::class, 'request_order']);
    Route::get('/rate', [AlkhawarizmiController::class, 'get_order_rate']);
    Route::get('/get/{id}', [AlkhawarizmiController::class, 'get_order']);
    Route::post('/update/{id}', [AlkhawarizmiController::class, 'update_order']);
    Route::get('/cancel/{id}', [AlkhawarizmiController::class, 'cancel_order']);
    Route::get('/track/{id}', [AlkhawarizmiController::class, 'track_order']);
});
