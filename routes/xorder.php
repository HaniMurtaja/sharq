<?php

use App\Http\Controllers\Api\XorderController;
use Illuminate\Support\Facades\Route;


//xorder//
Route::group(['prefix' => '/c4d/{API_TOKEN}', "middleware" => ['token.auth']], function ()
{
    //xorder
    Route::group(['prefix' => 'order'], function ()
    {
        Route::post('/add', [XorderController::class, 'request_order']);
        Route::post('/update/{id}', [XorderController::class, 'update_order']);
        Route::get('/cancel/{id}', [XorderController::class, 'cancel_order']);
    });

});
//xorder//

