<?php

use App\Http\Controllers\Api\OperatorController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\TokenAuthController;
use Illuminate\Support\Facades\Route;

/*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider and all of them will
    | be assigned to the "api" middleware group. Make something great!
    |
    */
require_once __DIR__ . '/deliverect.php'; //done  /   /////         /                          //ok

require_once __DIR__ . '/luluMarket.php'; //done /    /////          branch done /             //ok

require_once __DIR__ . '/lyve.php';         //done /    /////          branch done /                   //ok
require_once __DIR__ . '/dominospizza.php'; //done //            branch done /                  //ok
require_once __DIR__ . '/foodics.php';      // done /   /////          branch done /                 //ok
require_once __DIR__ . '/blink.php';        //done //                      branch done //              //ok
require_once __DIR__ . '/americana.php';    // done //                  branch done                //ok

require_once __DIR__ . '/maestropizza.php'; //done //                    branch done //         //ok
require_once __DIR__ . '/Sols.php';         //done  //                            branch done //        //ok
require_once __DIR__ . '/arabianoud.php';   //done  //                       branch done //       //ok
require_once __DIR__ . '/b1_burger.php';    // done  //                        branch done //      //ok
require_once __DIR__ . '/papaJohns.php';    //done  //                        branch done  //     //ok
require_once __DIR__ . '/althawaqah.php';   // done  //                       branch done  //     //ok
require_once __DIR__ . '/loginextold.php';  // done  //                     branch done          //ok

require_once __DIR__ . '/c4d.php'; //done     //                           branch done         //ok

require_once __DIR__ . '/baraka_app.php';     //done  //                       branch done         //ok
require_once __DIR__ . '/panda.php';          //done    //                         branch done         //ok
require_once __DIR__ . '/sanabelAlsalam.php'; //done   //                  branch done         //ok

require_once __DIR__ . '/oto.php';      // done   //                            branch done         //ok
require_once __DIR__ . '/loginext.php'; //done  //                         branch done         //ok
require_once __DIR__ . '/zyda.php';     // done    //                           branch  done        //ok
require_once __DIR__ . '/golyv.php';    //done   //                           branch done         //ok

require_once __DIR__ . '/afco_app.php';      //done   //                        branch done         //ok
require_once __DIR__ . '/FoodicsOnline.php'; //done   //                   branch done         //ok
require_once __DIR__ . '/wasfty.php';        //done   //                          branch done         //ok
require_once __DIR__ . '/TrustAngel.php';    //done   //                      branch done         //ok
require_once __DIR__ . '/twina.php';         //                                   branch done             //ok
require_once __DIR__ . '/maqlopa.php';       //                                 branch done         //ok
require_once __DIR__ . '/burgAlhammam.php';  //                            branch done         //ok
require_once __DIR__ . '/trybotr.php';       //                                 branch done         //ok
require_once __DIR__ . '/bostani.php';       //                                 branch done         //ok
require_once __DIR__ . '/ordable.php';       //                                  branch done         //ok
require_once __DIR__ . '/xorder.php';        //                                  branch done         //ok
require_once __DIR__ . '/dgtera.php';        //                                  branch done         //ok
require_once __DIR__ . '/moon10.php';        //                                  branch done         //ok
require_once __DIR__ . '/marbleSlab.php';    //                                  branch done         //ok
require_once __DIR__ . '/qud_elite.php';     //                                  branch done      //ok

require_once __DIR__ . '/alkhawarizmi.php'; //                                  branch done    //ok

require_once __DIR__ . '/Integration.php'; //ok

Route::post('client/generate-token', TokenAuthController::class);
Route::group(['prefix' => '/a/{API_TOKEN}', "middleware" => ['token.auth']], function () {
    //order
    Route::group(['prefix' => 'order'], function () {
        Route::post('/add', [OrderController::class, 'request_order']); // done   branch done   //ok
        Route::get('/rate', [OrderController::class, 'get_order_rate']);
        Route::get('/get/{id}', [OrderController::class, 'get_order']);
        Route::post('/update/{id}', [OrderController::class, 'update_order']);
        Route::get('/cancel/{id}', [OrderController::class, 'cancel_order']);
        Route::get('/track/{id}', [OrderController::class, 'track_order']);
    });

//        Route::post('webhooks/add',  [OrderController::class, 'addWebHook']);
//        Route::get('webhooks/list',  [OrderController::class, 'listWebHook']);
//        Route::get('webhook/delete/{id}',  [OrderController::class, 'deleteWebHook']);

    Route::group(['prefix' => 'operators'], function () {

        Route::post('/add', [OperatorController::class, 'add_operator']);

    });
});

Route::group(['prefix' => 'operator/auth'], function () {
    Route::post('/send-otp', [OperatorController::class, 'send_otp']);
    Route::post('/login', [OperatorController::class, 'login']);
});
/* ----------------------------------- with login -----------------------------------------------------------------*/
Route::group(["middleware" => ["auth:api", 'AcceptHeader']], function () {
    //operator
    Route::group(['prefix' => 'operator'], function () {
        Route::group(['prefix' => 'auth'], function () {
            Route::post('/logout', [OperatorController::class, 'logout']);
            Route::post('/change-password', [OperatorController::class, 'change_password']);
            Route::post('/update-profile', [OperatorController::class, 'update_profile']);
            Route::post('/send-otp-update-phone', [OperatorController::class, 'send_otp_to_update_phone']);
            Route::post('/update-phone', [OperatorController::class, 'update_phone']);
            Route::post('/update-status', [OperatorController::class, 'update_status']);
            Route::get('/profile', [OperatorController::class, 'profile']);
            Route::post('/detect_location', [OperatorController::class, 'detect_location']);
        });
        // BANK

        Route::group(['prefix' => 'bank'], function () {
            Route::get('/details', [OperatorController::class, 'bank_details']);
            Route::post('/new', [OperatorController::class, 'new_bank_details']);
        });
        // WALLET
        Route::group(['prefix' => 'wallet'], function () {
            Route::get('/details', [OperatorController::class, 'wallet_details']);
            Route::post('/charge', [OperatorController::class, 'add_wallet_balance']);
            Route::get('/transactions', [OperatorController::class, 'wallet_transactions']);
        });

        //vehicle
        Route::group(['prefix' => 'vehicle'], function () {
            Route::post('/add', [OperatorController::class, 'add_vehicle']);
        });
        //orders
        Route::group(['prefix' => 'order'], function () {
            Route::post('/accept-order', [OperatorController::class, 'accept_order'])
                ->middleware('is_online');
            Route::get('/new-order', [OperatorController::class, 'new_order']);
            Route::get('/driver-orders', [OperatorController::class, 'driver_orders']);
            Route::get('/history-orders', [OperatorController::class, 'driver_orders_history']);
            Route::get('/failed-orders', [OperatorController::class, 'driver_orders_failed']);
            Route::post('/update-order-status', [OperatorController::class, 'update_order_status']);
            Route::post('/send-order-otp', [OperatorController::class, 'send_order_otp']);
            Route::post('report-problem', [OperatorController::class, 'report_problem']);
            Route::post('/cancel-order-request', [OperatorController::class, 'cancel_order_request']);
            Route::patch('/accept-cancel-order-request', [OperatorController::class, 'accept_cancel_order_request']);
        });
    });
});
