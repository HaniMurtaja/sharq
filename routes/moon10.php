<?php

use App\Http\Controllers\Api\Moon10Controller;
use Illuminate\Support\Facades\Route;


//moon10//
Route::group(['prefix' => '/moon10/{API_TOKEN}', "middleware" => ['token.auth']], function ()
{
    //order
    Route::group(['prefix' => 'order'], function ()
    {
        Route::post('/add', [Moon10Controller::class, 'request_order']);
        Route::post('/update/{id}', [Moon10Controller::class, 'update_order']);
        Route::get('/cancel/{id}', [Moon10Controller::class, 'cancel_order']);
    });

});
//moon10//

