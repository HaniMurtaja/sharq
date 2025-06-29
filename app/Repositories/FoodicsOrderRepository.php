<?php

namespace App\Repositories;

use App\Enum\DeliverectOrderStatus;
use App\Enum\LyveOrderStatus;
use App\Http\Requests\Api\Foodics\FoodicsOrderRequest;
use App\Http\Services\NotificationService;
use App\Models\User;
use Auth;
use Validator;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Vehicle;
use App\Models\webHook;
use App\Models\Operator;
use App\Models\OrderLog;
use App\Enum\OrderStatus;
use App\Enum\DriverStatus;
use App\Models\OrderDriver;
use App\Models\OrderReport;
use App\Rules\KSAPhoneRule;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\ClientBranches;
use App\Models\OperatorDetail;
use App\Traits\HandleResponse;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Services\WalletService;
use Illuminate\Support\Facades\Http;
use App\Models\WebHook as ModelsWebHook;
use App\Repositories\FirebaseRepository;
use App\Repositories\OperatorRepository;
use App\Http\Resources\Api\OrderResource;
use App\Http\Resources\Api\WebHookResource;
use App\Models\DriverVehicle;
use App\Traits\OrderCreationDateValidation;
use Dotenv\Exception\ValidationException;
use Exception;
use phpDocumentor\Reflection\Metadata\Hook;

class FoodicsOrderRepository
{
    use HandleResponse, OrderCreationDateValidation;

    public function __construct(FirebaseRepository $firebaseRepository, OperatorRepository $operatorRepository, WalletService $WalletService, NotificationService $notificationService)
    {
        $this->firebaseRepository = $firebaseRepository;
        $this->operatorRepository = $operatorRepository;
        $this->WalletService = $WalletService;
        $this->notificationService = $notificationService;
    }
    public function success(Request $request)
    {
        return $this->getFoodicsToken($request->code);
    }

    public function getFoodicsToken($authorizationCode)
    {

        $url = 'https://api.foodics.com/oauth/token';
        $payload = [
            'grant_type' => 'authorization_code',
            'code' => $authorizationCode,
            'client_id' => env('FoodicsClientId'),
            'client_secret' => env('FoodicsClientSecret'),
            'redirect_uri' => "https://alshrouqdelivery.com/api/foodics/App/success",
        ];

        // Make the POST request
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($url, $payload);

        // Return the response
        if ($response->successful()) {
            $result = $response->json();
            return $this->WhoAim($result['access_token']);
        } else {
            $message = [
                'status' => "error",
                'message' => "Cant get  data from Foodics , please try again"
            ];
            return view('admin.pages.Foodics.pages', compact('message'));
        }
    }


    public function WhoAim($token)
    {
        $client = new Client();

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ];
        $response = Http::withHeaders($headers)->get('https://api.foodics.com/v5/whoami');
        if ($response->successful()) {
            $result = $response->json();
            return   $this->SaveUser($result, $token);
        } else {


            $message = [
                'status' => "error",
                'message' => "Cant get  data from Foodics , please try again"
            ];
            return view('admin.pages.Foodics.pages', compact('message'));
        }
    }


    public function SaveUser($data, $token)
    {
        $user = User::where('email', $data['data']['user']['email'])->first();
        if ($user) {
            $user->foodics_user_object = $data;
            $user->foodics_token = $token;
            $user->foodics_reference = $data['data']['business']['reference'];
            $user->save();
            //            return redirect(route('show-login'))->with('success', "User saved With Foodics successfully");
            $message = [
                'status' => "success",
                'message' => "User saved With Foodics successfully"
            ];
            return view('admin.pages.Foodics.pages', compact('message'));
        }
        $message = [
            'status' => "error",
            'message' => "User Not Found"
        ];
        return view('admin.pages.Foodics.pages', compact('message'));
    }

    public function webhook(FoodicsOrderRequest $request)
    {

        $validated = $request->validated();
        $pickup = \App\Models\Client::where('foodics_reference', $validated['business']['reference'])->first();
        if (!$pickup) {
            return response()->json(['error' => 'Cant found business'], 200);
        }

        if ($pickup->is_active != 1) {
            return response()->json(['message' => 'Unactive user'], 200);
        }



        $branches = $pickup->branches->first();

        if ($branches->is_active != 1) {
            return response()->json(['message' => 'Unactive branch'], 200);
        }

        if (!count($validated)) {
            return response()->json(['message' => 'not for us'], 200);
        }
        $searchName = "Alshrouq";
        $tags = $validated['order']['tags'];
        $result = array_filter($tags, function ($tag) use ($searchName) {
            return $tag['name'] === $searchName;
        });

        if (!count($result)) {
            return response()->json(['message' => 'not for us'], 200);
        }
        //            if (!$this->isWithinBusinessHours($request->ingr_shop_id)) {
        //                return response()->json(['error' => 'System Closed'], 200);
        //            }


        $fees = $pickup->client?->clienGroup?->default_delivery_fee;
        if ($validated['event'] == "order.delivery.created") {
            $order = $this->order_Create($validated, $pickup, $branches, $fees);
        } else {
            $order = $this->order_update($validated, $pickup, $branches, $fees);
        }


        if ($order) {
            return  response()->json(new \App\Http\Resources\Api\Lyve\OrderResource($order), 200);
        } else {

            return response()->json(['message' => 'Order not created'], 200);
        }
    }




    public function order_Create($validated, $pickup, $branches, $fees)
    {

        $orderData = [
            'pickup_lat' => $validated['order']['branch']['latitude'] ?? null,
            'pickup_lng' => $validated['order']['branch']['longitude'] ?? null,
            'pickup_id' => $pickup->id,
            'client_order_id_string' => $validated['order']['check_number'] ?? null,
            'value' => $validated['order']['total_price'] ?? null,
            'service_fees' => $fees,
            'lat' => $validated['order']['customer_address']['latitude'] ?? null,
            'lng' => $validated['order']['customer_address']['longitude'] ?? null,
            'address' => $validated['order']['customer_address']['description'] ?? null,
            'customer_phone' => $validated['order']['customer']['phone_number'] ?? '0',
            'customer_name' => $validated['order']['customer']['name'] ?? null,
            'status' => OrderStatus::CREATED,

            'ingr_shop_id' => $pickup->id,
            'ingr_branch_id' => $branches->id,
            'integration_id' => $pickup->client->integration_id,
            'additional_details' => $validated,
            'ingr_shop_name' => $validated['order']['branch']['name'] ?? null,
            'ingr_branch_name' => $validated['order']['branch']['name'] ?? null,
            'ingr_branch_lat' => $validated['order']['branch']['latitude'] ?? null,
            'ingr_branch_lng' => $validated['order']['branch']['longitude'] ?? null,

        ];


        $order = Order::create($orderData);

        return $order;
    }


    public function order_update($validated, $pickup, $branches, $fees)
    {

        $order = Order::where('client_order_id_string', $validated['order']['check_number'])->where('pickup_id', $pickup->id)->first();
        $order->pickup_lat = $validated['order']['branch']['latitude'] ?? NULL;
        $order->pickup_lng = $validated['order']['branch']['longitude'] ?? NULL;
        $order->pickup_id = $pickup->id;
        $order->client_order_id_string = $validated['order']['check_number'] ?? NULL;

        $order->value = $validated['order']['total_price'] ?? NULL;
        $order->service_fees = $fees;
        $order->lat = $validated['order']['customer_address']['latitude'] ?? NULL;
        $order->lng = $validated['order']['customer_address']['longitude'] ?? NULL;
        $order->address = $validated['order']['customer_address']['description'] ?? NULL;
        // $order->city = $validated['pickupLocation']['city'] ?? NULL;
        $order->customer_phone = $validated['order']['customer']['phone_number'] ?? '0';
        $order->customer_name = $validated['order']['customer']['name'] ?? NULL;
        $order->ingr_shop_id = $pickup->id;
        $order->ingr_branch_id = $branches->id;
        $order->integration_id = $pickup->client->integration_id;
        $order->additional_details = $validated;
        $order->ingr_shop_name = $validated['order']['branch']['name'] ?? NULL;
        $order->ingr_branch_name = $validated['order']['branch']['name'] ?? NULL;
        $order->ingr_branch_lat = $validated['order']['branch']['latitude'] ?? NULL;
        $order->ingr_branch_lng = $validated['order']['branch']['latitude'] ?? NULL;
        $order->save();
        $order->refresh();
        return $order;
    }

    private function sendOrderToWebhook($url, $data)
    {
        $jsonData = json_encode($data);
        // dd($jsonData);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);


//        if ($httpCode !== 200) {
//            Log::error('Webhook delivery failed', [
//                'url' => $url,
//                'response' => $response,
//                'http_code' => $httpCode
//            ]);
//        } else {
//            Log::info('Webhook delivered successfully', [
//                'url' => $url,
//                'response' => $response,
//                'http_code' => $httpCode,
//                'sent_data' => $jsonData
//            ]);
//        }
    }




    public function get_order(Request $request, $id)
    {
        $order = Order::with('shop', 'branch', 'driver')->find($id);
        // Check if the order exists
        if ($order) {
            // Return the order wrapped in a resource
            return response()->json(new OrderResource($order), 200);
        } else {
            // Handle the case where the order was not found
            return response()->json(['error' => 'Order not found'], 200);
        }
    }

    public function update_order(Request $request, $id)
    {
        // dd($request->all());
        $validated = $request->validate([
            'details' => 'nullable|string|max:255',
            'instruction' => 'nullable|string|max:255',
            'value' => 'nullable|numeric|min:0',
            'payment_type' => 'nullable|in:1,2', // 1 = CASH, 2 = CREDIT
            'preparation_time' => 'nullable|integer|min:0',
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
        ]);
        // Find the order by ID
        $order = Order::findOrFail($id);
        // Filter out empty fields from the request data
        $filteredData = array_filter($validated, function ($value) {
            return $value !== NULL && $value !== '';
        });
        // Use the fill method to update only the filtered fields
        $order->fill($filteredData);
        // Save the updated order
        $order->save();

        $client = $order->shop?->client ?? $order->branchIntegration?->client?->client;
        if ($client?->integration) {

            $webhook = WebHook::where('integration_company_id', $client?->integration?->id)->first();
            if ($webhook && $webhook->url) {
                $orderData = [
                    "order_id" =>  $order->id,
                    'status' =>  $order->status->value,
                    'status_label' => $order->status->getLabel(),
                    'driver' => $order->driver ? [
                        'id' => $order->driver?->driver?->id,
                        'name' =>  $order->driver?->driver?->full_name,
                        'phone' =>  $order->driver?->driver?->phone,
                        'status' => DriverStatus::tryFrom($order->driver?->driver?->operator?->status)?->getLabel(),
                        'tracking_url' => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",

                    ] : NULL,
                ];
                // dd($webhook->url);

                $this->sendOrderToWebhook($webhook->url, $orderData);
                $this->sendOrderToWebhook('https://webhook.site/329479cd-aea3-4e39-a77d-5384ceeab723', $orderData);
            }
        }
        return response()->json(new OrderResource($order), 201);
    }




    public function cancel_order(Request $request, $id)
    {

        $order = Order::find($id);
        // Check if the order exists
        if ($order) {
            $order->status = OrderStatus::PENDING_CLIENT_ORDER_CANCELLATION;

            $order->save();






            // Return the order wrapped in a resource
            return response()->json(['message' => 'ok , Order Cancelled'], 200);
        } else {
            // Handle the case where the order was not found
            return response()->json(['error' => 'Order not found'], 200);
        }
    }

    public function cancel_order_request(Request $request)
    {
        //check if order belong to user
        // dd($request->all());
        $order = Order::whereNotIn('status', [
            '9',
            '10',
            '20',
        ])->where('id', $request->order_id)->whereHas('driver', function ($query) {
            $query->where('driver_id', auth()->user()->id);
        })->first();
        if (!$order) {
            return $this->send_response(FALSE, 200, 'cant change status', NULL);
        }

        $order->status = OrderStatus::PENDING_ORDER_CANCELLATION;
        $order->save();

        OrderLog::create([
            'order_id' =>  $order->id,
            'status' => OrderStatus::PENDING_ORDER_CANCELLATION,
            'action' => 'Request Cancel Order',

            'user_id' => auth()->id(),
            'description' => auth()->user()->first_name . ' request to cancel order ',
        ]);

        return $this->send_response(TRUE, 200, 'cancel request under processing', NULL);
    }

    public function accept_cancel_order_request(Request $request)
    {

        $order = Order::find($request->order_id);

        if ($order) {
            $this->cancel_order($request, $order->id);

            return response()->json(['message' => 'ok , Order Cancelled'], 200);
        } else {
            // Handle the case where the order was not found
            return response()->json(['error' => 'Order not found'], 200);
        }
    }



    //operators actions
    public function new_order(Request $request)
    {
        $order = Order::with('shop', 'branch', 'driver')->whereIn('status', [
            '2',
            '4',
            '16',
            '6',
            '8',
            '17'
        ])->whereHas('driver', function ($query) {
            $query->where('driver_id', auth()->user()->id);
        })->orderBy('created_at', 'desc')->get();
        if (!$order) {
            return $this->send_response(FALSE, 200, 'no data found', NULL);
        }
        return $this->send_response(TRUE, 200, 'success',  OrderResource::collection($order));
    }

    public function accept_order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'status' => 'required|in:17,18',
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
        ]);
        if ($validator->fails()) {
            return $this->send_response(FALSE, 200, $validator->errors()->first(), NULL);
        }
        //check order can accepted or rejected
        $order = Order::with('shop', 'branch', 'driver')->where('id', $request->order_id)->whereIn('status', [
            '2',
        ])->whereHas('driver', function ($query) {
            $query->where('driver_id', auth()->user()->id);
        })->first();
        if (!$order) {
            return $this->send_response(FALSE, 200, 'no data found', NULL);
        }
        //check vehicle
        $vehicle = Vehicle::where('operator_id', auth()->user()->id)->first() ?? DriverVehicle::where('driver_id', auth()->user()->id)->orderBy('created_at', 'desc')->first();
        if (!$vehicle) {
            return $this->send_response(FALSE, 200, 'you dont have vehicle in our system', NULL);
        }


        $vehicle_id = Vehicle::where('operator_id', auth()->user()->id)->first()?->id ?? DriverVehicle::where('driver_id', auth()->user()->id)->orderBy('created_at', 'desc')->first()?->vehicle_id;
        // dd($vehicle, $vehicle_id);
        DB::beginTransaction();
        $order->status = $request->status;
        if ($request->status == 17) {
            $order->vehicle_id = $vehicle_id;

            $this->operatorRepository->change_status(2);
        }
        $order->save();

        $this->add_order_log($order->id, $request->status, auth()->user()->id, $request->lat, $request->lng);
        DB::commit();

        //firebase actions
        switch ($request->status) {
            case 17:
                $orderResource = new OrderResource($order);
                $orderData = $orderResource->toArray(request());
                //try save firebase
                try {
                    // Attempt to save to Firebase
                    $this->firebaseRepository->save_driver_order(auth()->user()->id, $orderData);
                } catch (\Exception $e) {
                    // Handle the exception (log it, show a message, etc.)
//                    Log::info($e);
                }
                break;
            case 18:
                //try delete firebase reference
                try {
                    $this->firebaseRepository->delete_driver_order(auth()->user()->id, $order->id);
                } catch (\Exception $e) {
                    // Handle the exception (log it, show a message, etc.)
//                    Log::info($e);
                }
                break;
            default:
//                Log::info('not correct status');
        }



        $client = $order->shop?->client ?? $order->branchIntegration?->client?->client;
        if ($client?->integration) {

            $webhook = WebHook::where('integration_company_id', $client?->integration?->id)->first();
            if ($webhook && $webhook->url) {
                $orderData = [
                    "order_id" =>  $order->id,
                    'status' =>  $order->status->value,
                    'status_label' => $order->status->getLabel(),
                    'driver' => $order->driver ? [
                        'id' => $order->driver?->driver?->id,
                        'name' =>  $order->driver?->driver?->full_name,
                        'phone' =>  $order->driver?->driver?->phone,
                        'status' => DriverStatus::tryFrom($order->driver?->driver?->operator?->status)?->getLabel(),
                        'tracking_url' => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",

                    ] : NULL,
                ];
                // dd($webhook->url);

                $this->sendOrderToWebhook($webhook->url, $orderData);
                $this->sendOrderToWebhook('https://webhook.site/329479cd-aea3-4e39-a77d-5384ceeab723', $orderData);
            }
        }

        if ($order->integration_id == 120) {
            $job_id = $order->jop_id;
            $lat = $order->driver?->driver?->operator?->lat;
            $lng = $order->driver?->driver?->operator?->lng;
            $data = [
                "deliveryJobId" => "$job_id",
                "pickupTimeETA" => Carbon::now()->addMinutes(15)->format('Y-m-d\TH:i:s.u\Z'),
                "transportType" => "bicycle",
                "trackingUrl" => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",
                "courier" => [
                    "name" => auth()->user()->full_name,
                    "phone" => auth()->user()->phone,
                    "longitude" => "$lng",
                    "latitude" => "$lat",
                ],
                "locations" => [
                    [
                        "orderId" => "$order->client_order_id_string",
                        "status" => DeliverectOrderStatus::GetStatus($order->status),
                        "deliveryTimeETA" => Carbon::now()->addMinutes(30)->format('Y-m-d\TH:i:s.u\Z')
                    ]
                ]
            ];

            $dss = $this->sendOrderToWebhook("https://api.staging.deliverect.com/fulfillment/generic/events", $data);
        }


        if ($order->integration_id == 121) {
            $driver_id = auth()->user()->id;
            $data = [
                "order_id" => "$order->id",
                "status" => LyveOrderStatus::GetStatus($order->status),

                "driver" => [
                    "id" => "$driver_id",
                    "name" => auth()->user()->full_name,
                    "phone_number" => auth()->user()->phone,
                    "vehicle_type" => "Bike"
                ],
                "timestamp" => Carbon::now()->timestamp,
                "tracking_link" => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",
            ];
            $token = $order->additional_details['callback_token'];
            $this->SendToLyve($token, $data);
        }


        return $this->send_response(TRUE, 200, 'success', NULL);
    }

    public function driver_orders(Request $request)
    {
        $driver = Operator::find(auth()->user()->id);
        $orders = $driver->orders()->with('shop', 'branch', 'driver')->get();
        return $this->send_response(TRUE, 200, 'success', OrderResource::collection($orders));
    }

    public function driver_orders_history(Request $request)
    {
        $driver = Operator::find(auth()->user()->id);
        $orders = $driver->orders()
            ->whereStatus(OrderStatus::DELIVERED)
            ->with(['shop', 'branch', 'driver'])
            ->get();

        return $this->send_response(TRUE, 200, 'success', OrderResource::collection($orders));
    }

    public function driver_orders_failed(Request $request)
    {
        $driver = Operator::find(auth()->user()->id);
        $orders = $driver->orders()
            ->whereStatus(OrderStatus::FAILED)
            ->with(['shop', 'branch', 'driver'])
            ->get();

        return $this->send_response(TRUE, 200, 'success', OrderResource::collection($orders));
    }

    public function update_status(Request $request)
    {
        // dd(99);
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'status' => 'required',
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
        ]);
        if ($validator->fails()) {
            return $this->send_response(FALSE, 200, $validator->errors()->first(), NULL);
        }
        if (!in_array($request->status, ['4', '16', '6', '8', '9'])) {
            return $this->send_response(FALSE, 200, 'cant change status', NULL);
        }
        //check if order belong to user
        $order = Order::with('shop', 'branch', 'driver')->whereNotIn('status', [
            '9',
            '10',
            '20',
        ])->where('id', $request->order_id)->whereHas('driver', function ($query) {
            $query->where('driver_id', auth()->user()->id);
        })->first();
        if (!$order) {
            return $this->send_response(FALSE, 200, 'cant change status', NULL);
        }
        //update order
        $order->status = $request->status;
        $order->save();
        //add order log
        $this->add_order_log($order->id, $request->status, auth()->user()->id, $request->lat, $request->lng);
        //firebase actions
        switch ($request->status) {
            case 4: //pending
            case 16: //Arrived to pickup
            case 6: //Order picked up
            case 8: //Arrived to dropoff
                //change operator status to away
                $this->operatorRepository->change_status(3);
                $orderResource = new OrderResource($order);
                $orderData = $orderResource->toArray(request());
                //try save firebase
                try {
                    // Attempt to save to Firebase
                    $this->firebaseRepository->save_driver_order(auth()->user()->id, $orderData);
                } catch (\Exception $e) {
                    // Handle the exception (log it, show a message, etc.)
//                    Log::info($e);
                }
                break;
            case 9: //delivered
                //change operator status to available again
                $this->operatorRepository->change_status(1);
                //try delete firebase reference
                try {
                    //save wallet transaction
                    $wallet_transaction = [
                        'operator_id' => auth()->user()->id,
                        'type' => 'deposit',
                        'model_id' => $order->id,
                        'model_type' => Order::class,
                        'description' => ' اضافة قيمه طلب رقم' . $order->id,
                    ];
                    $this->WalletService->save($wallet_transaction);
                    $this->firebaseRepository->delete_driver_order(auth()->user()->id,  $order->id);
                } catch (\Exception $e) {
                    // Handle the exception (log it, show a message, etc.)
//                    Log::info($e);
                }
                break;
            default:
//                Log::info('not correct status');
        }

        $client = $order->shop?->client ?? $order->branchIntegration?->client?->client;
        if ($client?->integration) {

            $webhook = WebHook::where('integration_company_id', $client?->integration?->id)->first();
            if ($webhook && $webhook->url) {
                $orderData = [
                    "order_id" =>  $order->id,
                    'status' =>  $order->status->value,
                    'status_label' => $order->status->getLabel(),
                    'driver' => $order->driver ? [
                        'id' => $order->driver?->driver?->id,
                        'name' =>  $order->driver?->driver?->full_name,
                        'phone' =>  $order->driver?->driver?->phone,
                        'status' => DriverStatus::tryFrom($order->driver?->driver?->operator?->status)?->getLabel(),
                        'tracking_url' => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",

                    ] : NULL,
                ];
                // dd($webhook->url);

                $this->sendOrderToWebhook($webhook->url, $orderData);
                $this->sendOrderToWebhook('https://webhook.site/329479cd-aea3-4e39-a77d-5384ceeab723', $orderData);
            }
        }

        if ($order->integration_id == 120) {
            $job_id = $order->jop_id;
            $lat = $order->driver?->driver?->operator?->lat;
            $lng = $order->driver?->driver?->operator?->lng;
            $data = [
                "deliveryJobId" => "$job_id",
                "pickupTimeETA" => Carbon::now()->addMinutes(15)->format('Y-m-d\TH:i:s.u\Z'),
                "transportType" => "bicycle",
                "trackingUrl" => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",
                "courier" => [
                    "name" => auth()->user()->full_name,
                    "phone" => auth()->user()->phone,
                    "longitude" => "$lng",
                    "latitude" => "$lat",
                ],
                "locations" => [
                    [
                        "orderId" => "$order->client_order_id_string",
                        "status" => DeliverectOrderStatus::GetStatus($order->status),
                        "deliveryTimeETA" => Carbon::now()->addMinutes(30)->format('Y-m-d\TH:i:s.u\Z')
                    ]
                ]
            ];
            $this->sendOrderToWebhook("https://api.staging.deliverect.com/fulfillment/generic/events", $data);
        }
        if ($order->integration_id == 121) {
            $driver_id = auth()->user()->id;
            $data = [
                "order_id" => "$order->id",
                "status" => LyveOrderStatus::GetStatus($order->status),

                "driver" => [
                    "id" => "$driver_id",
                    "name" => auth()->user()->full_name,
                    "phone_number" => auth()->user()->phone,
                    "vehicle_type" => "Bike"
                ],
                "timestamp" => Carbon::now()->timestamp,
                "tracking_link" => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",
            ];
            $token = $order->additional_details['callback_token'];
            $this->SendToLyve($token, $data);
        }


        return $this->send_response(TRUE, 200, 'success', new OrderResource($order));
    }

    public function add_order_log($order_id, $status, $driver_id, $lat = null, $lng = null)
    {
        OrderLog::create(
            [
                'order_id' => $order_id,
                'driver_id' => $driver_id,
                'status' => $status,
                'lat' => $lat,
                'lng' => $lng,
                'action' => OrderStatus::tryFrom($status)?->getLabel() ?? 'Unknown Status'
            ]
        );
    }

    public function report_problem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'nullable|string',
            'order_id' => ['required', 'exists:orders,id'],
            'reason' => 'required|string',
        ]);
        if ($validator->fails()) {
            return $this->send_response(FALSE, 200, $validator->errors()->first(), NULL);
        }
        OrderReport::create([
            'order_id' => $request['order_id'],
            'driver_id' => auth()->user()->id,
            'reason' => $request['reason'],
            'description' => $request['description']
        ]);
        return $this->send_response(TRUE, 200, 'Report created successfully', NULL);
    }

    public function addWebHook(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'url' => 'required|url',
            'type' => 'required|in:order_created,order_updated,order_cancelled',
            'format' => 'nullable|in:form-data,JSON',
        ]);

        if ($validator->fails()) {
            return $this->send_response(FALSE, 200, $validator->errors()->first(), NULL);
        }
        $API_TOKEN  = $request->route('API_TOKEN');

        $user = \App\Models\Client::where('integration_token', $API_TOKEN)->first();
        if (!$user) {
            return $this->send_response(FALSE, 200, 'user not found', NULL);
        }
        $webhook = WebHook::create([
            'name' => $request->name,
            'url' => $request->url,
            'type' => $request->type,
            'format' => $request->format,
            'integration_company_id' => $user->client?->integration?->id
        ]);
        return $this->send_response(TRUE, 200, 'Webhook created successfully', NULL);
    }


    public function listWebHook()
    {
        $web_hooks = ModelsWebHook::all();
        return $this->send_response(TRUE, 200, 'success', WebHookResource::collection($web_hooks));
    }

    public function deleteWebHook($id, Request $request)
    {
        $API_TOKEN  = $request->route('API_TOKEN');

        $user = \App\Models\Client::where('integration_token', $API_TOKEN)->first();
        if (!$user) {
            return $this->send_response(FALSE, 200, 'user not found', NULL);
        }
        $web_hook = webHook::find($id);
        if (!$web_hook) {
            return $this->send_response(FALSE, 200, 'web hook not found', NULL);
        } else {
            $web_hook->delete();
            return $this->send_response(TRUE, 200, 'Webhook deleted successfully', NULL);
        }
    }
}
