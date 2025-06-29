<?php

namespace App\Repositories;

use App\Enum\DeliverectOrderStatus;
use App\Enum\LyveOrderStatus;
use App\Http\Resources\Api\DominosPizzaOrderResource;
use App\Http\Services\AutoDispatcherService;
use App\Http\Services\NotificationService;
use Auth;
use Validator;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Vehicle;
use App\Models\WebHook;
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

class DominosPizzaOrderRepository
{
    use HandleResponse, OrderCreationDateValidation;

    public function __construct(
        FirebaseRepository $firebaseRepository,
        OperatorRepository
        $operatorRepository,
        WalletService $WalletService,
        NotificationService $notificationService,
        AutoDispatcherService $autoDispatcherService
    ) {
        $this->firebaseRepository = $firebaseRepository;
        $this->operatorRepository = $operatorRepository;
        $this->WalletService = $WalletService;
        $this->notificationService = $notificationService;
        $this->autoDispatcherService = $autoDispatcherService;
    }

    public function save_order(Request $request)
    {
        try {
            $validated = $request->validate([
                'pickup_lat' => 'required_without:pickup_id|numeric',
                'pickup_lng' => 'required_without:pickup_id|numeric',
                'pickup_id' => 'required_without:pickup_lat,pickup_lng|integer',
                'client_order_id' => 'nullable',
                'value' => 'nullable|numeric',
                'payment_type' => 'required',
                'preparation_time' => 'nullable|integer|min:0',
                'lat' => 'nullable|required_without:address,city|numeric',
                'lng' => 'nullable|required_without:address,city|numeric',
                'address' => 'nullable|required_without:lat,lng|string',
                'city' => 'nullable|required_without:lat,lng|integer',
                'customer_phone' => [
                    'required'
                ],
                'customer_name' => 'nullable|string',
                'deliver_at' => 'nullable|date_format:Y-m-d H:i:s|after_or_equal:now',
                'details' => 'nullable|string',
                'pickup_poa' => 'nullable|boolean',
                'dropoff_poa' => 'nullable|boolean',
            ]);

            if (is_null($request->input('pickup_id')) && (is_null($request->input('pickup_lat')) || is_null($request->input('pickup_lng')))) {
                $additionalValidation = [
                    'ingr_shop_id' => 'required|integer',
                    'ingr_shop_name' => 'required|string',
                    'ingr_branch_id' => 'required|integer',
                    'ingr_branch_name' => 'required|string',
                    'ingr_branch_lat' => 'required|numeric',
                    'ingr_branch_lng' => 'required|numeric',
                    'ingr_branch_phone' => 'required|string',
                ];
                $validated = array_merge($validated, $request->validate($additionalValidation));
            }
            if ($request->payment_type == "CASH") {
                $payment_type = 1;
            } else {
                $payment_type = 3;
            }
            if (!$this->isWithinBusinessHours($request->ingr_shop_id)) {
                return response()->json(['error' => 'System Closed'], 404);
            }

            $branch_id = $validated['ingr_branch_id'] ?? $validated['pickup_id'];
            $pickup_id = ClientBranches::where('id', $branch_id)->first();
            // dd($pickup_id->is_active);
            if (! $pickup_id) {
                return response()->json(['message' => 'Branch not found'], 404);
            }

            if ($pickup_id->is_active != 1) {
                return response()->json(['message' => 'Unactive branch'], 401);
            }
            $checkOrderNumber = Order::where([
                'client_order_id_string' => $validated['client_order_id'] ?? null,
                'ingr_shop_id' => $validated['ingr_shop_id'] ?? ClientBranches::find($validated['pickup_id'])?->client_id,
                'pickup_id' => $validated['pickup_id'] ?? null,
                'customer_phone' => $validated['customer_phone'],
                'customer_name' => $validated['customer_name'] ?? null,
                'ingr_branch_id' => $validated['ingr_branch_id'] ?? $validated['pickup_id'],
                'integration_id' => 10,
            ])->exists();
            if ($checkOrderNumber) {
                return response()->json(['message' => 'The order already exists'], 404);
            }
            $orderData = [
                'pickup_lat' => $validated['pickup_lat'] ?? null,
                'pickup_lng' => $validated['pickup_lng'] ?? null,
                'pickup_id' => $validated['pickup_id'] ?? null,
                'client_order_id_string' => $validated['client_order_id'] ?? null,
                'value' => $validated['value'] ?? null,
                'payment_type' => $payment_type,
                'preparation_time' => $validated['preparation_time'] ?? 0,
                'lat' => $validated['lat'] ?? null,
                'lng' => $validated['lng'] ?? null,
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
                'customer_phone' => $validated['customer_phone'],
                'customer_name' => $validated['customer_name'] ?? null,
                'deliver_at' => $validated['deliver_at'] ?? null,
                'details' => $validated['details'] ?? null,
                'pickup_poa' => $validated['pickup_poa'] ?? null,
                'dropoff_poa' => $validated['dropoff_poa'] ?? null,
                'status' => 1,
                'integration_id' => 10,
                'ingr_shop_id' => $validated['ingr_shop_id'] ?? ClientBranches::find($validated['pickup_id'])?->client_id,
                'ingr_branch_id' => $validated['ingr_branch_id'] ?? $validated['pickup_id'],
                'ingr_shop_name' => $validated['ingr_shop_name'] ?? null,
                'ingr_branch_name' => $validated['ingr_branch_name'] ?? null,
                'ingr_branch_lat' => $validated['ingr_branch_lat'] ?? null,
                'ingr_branch_lng' => $validated['ingr_branch_lng'] ?? null,
                'ingr_branch_phone' => $validated['ingr_branch_phone'] ?? null,
                'additional_details' => $validated,

            ];

            $order = Order::create($orderData);


            $client = $order->shop?->client ?? $order->branchIntegration?->client?->client;

            if ($client?->integration) {
                $webhook = WebHook::where('integration_company_id', $client?->integration?->id)->where('type', 'order_created')->first();
                if ($webhook && $webhook->url) {
                    $orderData = [
                        'order_id' => $order->id,
                        'status_id' =>  1,
                        'status' => 'Created',
                        'client_order_id' => $order->client_order_id_string,
                        
                        'created_at' => Carbon::parse($order->created_at)->format('Y-m-d H:i:s'),
                        'driver' => $order->driver ? [
                            'id' => $order->driver?->driver?->id,
                            'name' => $order->driver?->driver?->full_name,
                            'phone' => $order->driver?->driver?->phone,
                            'status' => DriverStatus::tryFrom($order->driver?->driver?->operator?->status)?->getLabel(),
                            'tracking_url' => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",
                        ] : null,
                    ];
                    $this->sendOrderToWebhook($webhook->url, $orderData);
                }
            }

            return response()->json(new DominosPizzaOrderResource($order), 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }







    public function SendToLyve($token, $data)
    {

        $client = new Client();
        $request_data = [
            'body' => json_encode($data),
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ],
        ];

//        Log::info('Lyve api', [
//            $request_data,
//            $token,
//            $data
//        ]);
        $response = $client->request('POST', 'https://delivery-partner.webhook.manage.lyve.global/v1/feedbacks', $request_data);

        $result = $response->getBody();
//        Log::info('Lyve delivered successfully', [
//            $result
//        ]);
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
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

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
            return response()->json(['error' => 'Order not found'], 404);
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
            return response()->json(['error' => 'Order not found'], 404);
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
            return $this->send_response(FALSE, 400, 'cant change status', NULL);
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
            return response()->json(['error' => 'Order not found'], 404);
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
            return $this->send_response(FALSE, 400, 'no data found', NULL);
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
            return $this->send_response(FALSE, 400, $validator->errors()->first(), NULL);
        }
        //check order can accepted or rejected
        $order = Order::with('shop', 'branch', 'driver')->where('id', $request->order_id)->whereIn('status', [
            '2',
        ])->whereHas('driver', function ($query) {
            $query->where('driver_id', auth()->user()->id);
        })->first();
        if (!$order) {
            return $this->send_response(FALSE, 400, 'no data found', NULL);
        }
        //check vehicle
        $vehicle = Vehicle::where('operator_id', auth()->user()->id)->first() ?? DriverVehicle::where('driver_id', auth()->user()->id)->orderBy('created_at', 'desc')->first();
        if (!$vehicle) {
            return $this->send_response(FALSE, 400, 'you dont have vehicle in our system', NULL);
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
            return $this->send_response(FALSE, 400, $validator->errors()->first(), NULL);
        }
        if (!in_array($request->status, ['4', '16', '6', '8', '9'])) {
            return $this->send_response(FALSE, 400, 'cant change status', NULL);
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
            return $this->send_response(FALSE, 400, 'cant change status', NULL);
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
            return $this->send_response(FALSE, 400, $validator->errors()->first(), NULL);
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
            return $this->send_response(FALSE, 400, $validator->errors()->first(), NULL);
        }
        $API_TOKEN  = $request->route('API_TOKEN');

        $user = \App\Models\Client::where('integration_token', $API_TOKEN)->first();
        if (!$user) {
            return $this->send_response(FALSE, 400, 'user not found', NULL);
        }
        $webhook = WebHook::create([
            'name' => $request->name,
            'url' => $request->url,
            'type' => $request->type,
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
            return $this->send_response(FALSE, 400, 'user not found', NULL);
        }
        $web_hook = webHook::find($id);
        if (!$web_hook) {
            return $this->send_response(FALSE, 400, 'web hook not found', NULL);
        } else {
            $web_hook->delete();
            return $this->send_response(TRUE, 200, 'Webhook deleted successfully', NULL);
        }
    }
}
