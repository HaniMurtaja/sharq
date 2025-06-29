<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginextoldController;


//loginextold//
Route::group(['prefix' => '/loginextold/{API_TOKEN}', "middleware" => ['token.auth']], function ()
{
    //order
    Route::group(['prefix' => 'order'], function ()
    {
        Route::post('/create', [LoginextoldController::class, 'request_order']);
        Route::post('/update', [LoginextoldController::class, 'update_order']);
        Route::get('/cancel', [LoginextoldController::class, 'cancel_order']);
    });

});
//loginextold//

