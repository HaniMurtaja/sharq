<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Repositories\OrderRepository;
use App\Settings\GeneralSettings;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Services\OrderService;
use App\Http\Services\DeliverectOrderService;
use Illuminate\Support\Facades\File;

class DeliverectOrderController extends Controller
{


        public $deliverectOrderService;
        public function __construct(DeliverectOrderService $OrderService)
        {

            $this->deliverectOrderService = $OrderService;
        }


        public function createOrder ($token,Request $request) {
            File::append(public_path('createOrder.text'),"-----------------"."\n".json_encode(\request()->all())."\n");

            return $this->deliverectOrderService->createOrder($token,$request);
        }

        public function ValidateOrderold ($token,Request $request) {
            // Prepare the response data
            // dd(9);
            $fees = Client::where('integration_token',$token)->first()->client?->clienGroup?->default_delivery_fee;

            $orderId = $request->deliveryLocations[0]['orderId'];
            File::append(public_path('ValidateOrder.text'),"-----------------"."\n".json_encode(\request()->all())."\n");
            $settings = new GeneralSettings();
            $eta = $settings->eta;

            $responseData = [
                "jobId" => $request->jobId,
                "canDeliver" => true,
                "distance" => 0,
                "pickupTimeETA" => Carbon::now('UTC')->addMinutes((int)$eta['default_arrive_to_pickup_time'])->format('Y-m-d\TH:i:s.u\Z'),
                "deliveryLocations" => [
                    [
                        "deliveryId" => "",
                        "orderId" => $orderId,
                         "deliveryTimeETA" => Carbon::now('UTC')->addMinutes((int)($eta['default_arrive_to_pickup_time']+$eta['default_arrive_to_dropoff_time']))->format('Y-m-d\TH:i:s.u\Z')
                    ]
                ],
                "price" => [
                    "price" => ($fees) ? $fees : 0,
                    "taxRate" => 0
                ]
            ];

            return response()->json($responseData);
        }
        public function ValidateOrder ($token, Request $request) {
            $fees = Client::where('integration_token', $token)->first()->client?->clienGroup?->default_delivery_fee;

            $orderId = $request->deliveryLocations[0]['orderId'];
            File::append(public_path('ValidateOrder.text'), "-----------------\n" . json_encode($request->all()) . "\n");

            $settings = new GeneralSettings();
            $eta = $settings->eta;

            // ✅ تحويل القيم إلى أرقام
            $pickupMinutes = (int) ($eta['default_arrive_to_pickup_time'] ?? 0);
            $dropoffMinutes = (int) ($eta['default_arrive_to_dropoff_time'] ?? 0);

            $responseData = [
                "jobId" => $request->jobId,
                "canDeliver" => true,
                "distance" => 0,
                "pickupTimeETA" => Carbon::now('UTC')->addMinutes($pickupMinutes)->format('Y-m-d\TH:i:s.u\Z'),
                "deliveryLocations" => [
                    [
                        "deliveryId" => "",
                        "orderId" => $orderId,
                        "deliveryTimeETA" => Carbon::now('UTC')->addMinutes($pickupMinutes + $dropoffMinutes)->format('Y-m-d\TH:i:s.u\Z')
                    ]
                ],
                "price" => [
                    "price" => $fees ?: 0,
                    "taxRate" => 0
                ]
            ];

            return response()->json($responseData);
        }


        public function CancelOrder ($token,Request $request) {
            File::append(public_path('cancelOrder.text'),"-----------------"."\n".json_encode(\request()->all())."\n");
            // dd($request->all());
            return $this->deliverectOrderService->cancelOrder($token,$request);

        }

        public function FormatValidateOrder()
        {

        }

    }








