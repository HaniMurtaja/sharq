<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FoodicsOrderController;


//foodics//
Route::group(['prefix' => '/foodics', "middleware" => ['force.json']], function ()
{
    //order
    Route::group(['prefix' => 'App','as' => 'Foodics.'], function ()
    {
        Route::get('success', [FoodicsOrderController::class, 'success'])->name('success');
        Route::post('webhook', [FoodicsOrderController::class, 'webhook'])->name('webhook');

    });


});
//foodics//

