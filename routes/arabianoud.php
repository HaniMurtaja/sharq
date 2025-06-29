<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ArabianOudController;


//arabianOud//
Route::group(['prefix' => '/arabianOud/{API_TOKEN}', "middleware" => ['token.auth']], function ()
{
    //order
    Route::group(['prefix' => 'order'], function ()
    {
        Route::post('/add', [ArabianOudController::class, 'request_order']);
        Route::post('/update/{id}', [ArabianOudController::class, 'update_order']);
        Route::get('/cancel/{id}', [ArabianOudController::class, 'cancel_order']);
    });

});
//arabianOud//

