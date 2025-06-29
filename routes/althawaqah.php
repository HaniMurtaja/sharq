<?php

use App\Http\Controllers\Api\AlthawaqahController;
use Illuminate\Support\Facades\Route;


//althawaqah//
Route::group(['prefix' => '/althawaqah/{API_TOKEN}', "middleware" => ['token.auth']], function ()
{
    //order
    Route::group(['prefix' => 'order'], function ()
    {
        Route::post('/add', [AlthawaqahController::class, 'request_order']);
        Route::post('/update/{id}', [AlthawaqahController::class, 'update_order']);
        Route::get('/cancel/{id}', [AlthawaqahController::class, 'cancel_order']);
    });

});
//althawaqah//

