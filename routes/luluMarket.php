<?php

use App\Http\Controllers\Api\LuluMarket\LuluMarketController;
use Illuminate\Support\Facades\Route;

//LuluMarket//
Route::group(['prefix' => 'LuluMarket', "middleware" => ['force.json', 'LyveMiddleware']], function () {
    Route::post('/update', [LuluMarketController::class, 'UpdateOrder']);
    Route::post('/create', [LuluMarketController::class, 'createOrder']);

});
//LuluMarket//
