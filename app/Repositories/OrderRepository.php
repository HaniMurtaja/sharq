<?php
namespace App\Repositories;

use App\Enum\BlinkOrderStatus;
use App\Enum\DeliverectOrderStatus;
use App\Enum\DriverStatus;
use App\Enum\FoodicsOrderStatus;
use App\Enum\LuluMarketOrderStatus;
use App\Enum\LyveOrderStatus;
use App\Enum\OrderStatus;
use App\Http\Resources\Api\AmericanaWebHookRequestResource;
use App\Http\Resources\Api\DriverOrderCollection;
use App\Http\Resources\Api\Integration\IntegrationResource;
use App\Http\Resources\Api\Loginext\LoginextOrderResource;
use App\Http\Resources\Api\OrderResource;
use App\Http\Resources\Api\WebHookResource;
use App\Http\Services\AutoDispatcherService;
use App\Http\Services\NotificationService;
use App\Http\Services\SendSms;
use App\Http\Services\WalletService;
use App\Models\ClientBranches;
use App\Models\Operator;
use App\Models\OperatorDetail;
use App\Models\Order;
use App\Models\OrderLog;
use App\Models\OrderReport;
use App\Models\WebHook;
use App\Models\WebHook as ModelsWebHook;
use App\Repositories\FirebaseRepository;
use App\Repositories\OperatorRepository;
use App\Rules\KSAPhoneRule;
use App\Traits\HandleResponse;
use App\Traits\LocationTrait;
use App\Traits\OrderCreationDateValidation;
use Auth;
use Carbon\Carbon;
use Dotenv\Exception\ValidationException;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Validator;

class OrderRepository
{
    use HandleResponse, OrderCreationDateValidation, LocationTrait;

    public function __construct(
        FirebaseRepository $firebaseRepository,
        OperatorRepository $operatorRepository,
        WalletService $WalletService,
        NotificationService $notificationService,
        AutoDispatcherService $autoDispatcherService
    ) {
        $this->firebaseRepository    = $firebaseRepository;
        $this->operatorRepository    = $operatorRepository;
        $this->WalletService         = $WalletService;
        $this->notificationService   = $notificationService;
        $this->autoDispatcherService = $autoDispatcherService;
    }

    public function save_order(Request $request)
    {
        try {

            $validated = $request->validate([
                'pickup_lat'       => 'required_without:pickup_id|numeric',
                'pickup_lng'       => 'required_without:pickup_id|numeric',
                'pickup_id'        => 'required_without:pickup_lat,pickup_lng|integer',
                'client_order_id'  => 'nullable|integer',
                'value'            => 'nullable|numeric',
                'payment_type'     => 'required|in:1,2',
                'preparation_time' => 'nullable|integer|min:0',
                'lat'              => 'nullable|required_without:address,city|numeric',
                'lng'              => 'nullable|required_without:address,city|numeric',
                'address'          => 'nullable|required_without:lat,lng|string',
                'city'             => 'nullable|required_without:lat,lng|integer',
                'customer_phone'   => [
                    'required',
                    new KSAPhoneRule(),
                ],
                'customer_name'    => 'nullable|string',
                'deliver_at'       => 'nullable|date_format:Y-m-d H:i:s|after_or_equal:now',
                'details'          => 'nullable|string',
                'pickup_poa'       => 'nullable|boolean',
                'dropoff_poa'      => 'nullable|boolean',
            ]);

            if (is_null($request->input('pickup_id')) && (is_null($request->input('pickup_lat')) || is_null($request->input('pickup_lng')))) {
                $additionalValidation = [
                    'ingr_shop_id'      => 'required|integer',
                    'ingr_shop_name'    => 'required|string',
                    'ingr_branch_id'    => 'required|integer',
                    'ingr_branch_name'  => 'required|string',
                    'ingr_branch_lat'   => 'required|numeric',
                    'ingr_branch_lng'   => 'required|numeric',
                    'ingr_branch_phone' => 'required|string',
                ];
                $validated = array_merge($validated, $request->validate($additionalValidation));
            }

            if (! $this->isWithinBusinessHours($request->ingr_shop_id)) {
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

            $order = Order::create([
                'pickup_lat'        => $validated['pickup_lat'] ?? null,
                'pickup_lng'        => $validated['pickup_lng'] ?? null,
                'pickup_id'         => $validated['pickup_id'] ?? null,
                'client_order_id'   => $validated['client_order_id'] ?? null,
                'value'             => $validated['value'] ?? null,
                'payment_type'      => $validated['payment_type'],
                'preparation_time'  => $validated['preparation_time'] ?? 0,
                'lat'               => $validated['lat'] ?? null,
                'lng'               => $validated['lng'] ?? null,
                'address'           => $validated['address'] ?? null,
                'city'              => $validated['city'] ?? null,
                'customer_phone'    => $validated['customer_phone'],
                'customer_name'     => $validated['customer_name'] ?? null,
                'deliver_at'        => $validated['deliver_at'] ?? null,
                'details'           => $validated['details'] ?? null,
                'pickup_poa'        => $validated['pickup_poa'] ?? null,
                'dropoff_poa'       => $validated['dropoff_poa'] ?? null,
                'status'            => 1,
                'ingr_shop_id'      => $validated['ingr_shop_id'] ?? ClientBranches::find($validated['pickup_id'])?->client_id,
                'ingr_branch_id'    => $validated['ingr_branch_id'] ?? $validated['pickup_id'],
                'ingr_shop_name'    => $validated['ingr_shop_name'] ?? null,
                'ingr_branch_name'  => $validated['ingr_branch_name'] ?? null,
                'ingr_branch_lat'   => $validated['ingr_branch_lat'] ?? null,
                'ingr_branch_lng'   => $validated['ingr_branch_lng'] ?? null,
                'ingr_branch_phone' => $validated['ingr_branch_phone'] ?? null,
                'integration_token' => @$request->route('API_TOKEN'),
            ]);

            $client = $order->shop?->client ?? $order->branchIntegration?->client?->client;

            if ($client?->integration) {
                $webhook = WebHook::where('integration_company_id', $client?->integration?->id)->first();
                if ($webhook && $webhook->url) {
                    $orderData = [
                        'order_id'     => $order->id,
                        'status'       => 3,
                        'status_label' => 'The order has been accepted',
                        'driver'       => $order->driver ? [
                            'id'           => $order->driver?->driver?->id,
                            'name'         => $order->driver?->driver?->full_name,
                            'phone'        => $order->driver?->driver?->phone,
                            'status'       => DriverStatus::tryFrom($order->driver?->driver?->operator?->status)?->getLabel(),
                            'tracking_url' => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",
                        ] : null,
                    ];
                    $this->sendOrderToWebhooktry($webhook->url, $orderData);
                    //                    $this->sendOrderToWebhook('https://webhook.site/329479cd-aea3-4e39-a77d-5384ceeab723', $orderData);
                }
            }

            return response()->json(new OrderResource($order), 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    //    public function auto_dispatch($order, $client)
    //    {
    //        if ($client) {
    //            if ($client->auto_dispatch == 1) {
    //                $this->autoDispatcherService->autoDispatch($order);
    //            }
    //        }
    //    }

    function FoodicsWebhook($orderId, $token, $payload)
    {
        $url = "https://api-sandbox.foodics.com/v5/orders/{$orderId}";

        $payload = [
            "driver_assigned_at" => "2019-12-19 11:15:30",
            "dispatched_at"      => "2019-12-19 11:14:30",
            "delivered_at"       => "2019-12-19 11:45:30",
            "driver_id"          => "8f12f639",
            "delivery_status"    => 2,
        ];

        $response = Http::withHeaders([
            'Accept'        => 'application/json',
            'Authorization' => "Bearer {$token}",
        ])

            ->timeout(10)
            ->put($url, $payload);

        if ($response->successful()) {
            return $response->json();
        } else {
            // Handle errors
            return [
                'status' => $response->status(),
                'error'  => $response->body(),
            ];
        }
    }

    public function SendToLyve($token, $data)
    {

        $client       = new Client();
        $request_data = [
            'body'    => json_encode($data),
            'headers' => [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json',
            ],
        ];

        //        Log::info('Lyve api', [
        //            $request_data,
        //            $token,
        //            $data
        //        ]);
        $response = $client->request('POST', 'https://delivery-partner.webhook.manage.lyve.global/v1/feedbacks', $request_data);

        $result = $response->getBody();
        Log::info('Lyve delivered Lyve api', [
            $result,
            $request_data,
            $token,
            $data,
        ]);
    }

    public function blinkHook($url, $data)
    {
        $token        = env('BLINK_TOKEN');
        $client       = new Client();
        $request_data = [
            'body'    => json_encode($data),
            'headers' => [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json',
            ],
            'timeout' => 10,
        ];

        Log::info('BLINK api', [
            $request_data,
            $token,
            $data,
        ]);
        $response = $client->request('POST', $url, $request_data);

        $result = $response->getBody();
        Log::info('BLINK delivered successfully', [
            $result,
        ]);
    }

    private function sendOrderToWebhook($url, $data)
    {
        $jsonData = json_encode($data);
        // dd($jsonData);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            Log::error('Webhook delivery failed', [
                'url'       => $url,
                'response'  => $response,
                'http_code' => $httpCode,
                'sent_data' => $jsonData,
            ]);
        } else {
            Log::info('Webhook delivered successfully', [
                'url'       => $url,
                'response'  => $response,
                'http_code' => $httpCode,
                'sent_data' => $jsonData,
            ]);
        }
    }
    private function sendOrderToWebhookamericana($url, $data)
    {
        $jsonData = json_encode($data);
        // dd($jsonData);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            Log::error('Webhook delivery failed', [
                'url'       => $url,
                'response'  => $response,
                'http_code' => $httpCode,
                'sent_data' => $jsonData,
            ]);
        } else {
            Log::info('Webhook delivered successfully', [
                'url'       => $url,
                'response'  => $response,
                'http_code' => $httpCode,
                'sent_data' => $jsonData,
            ]);
        }
    }
    private function sendOrderToWebhooktry($url, $data)
    {
        $jsonData = json_encode($data);
        // dd($jsonData);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    }
    private function sendOrderToWebhookLoginNext($url, $data)
    {
        $jsonData = json_encode($data);
        // dd($jsonData);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 700);
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode !== 200) {
            Log::error('Webhook delivery failed', [
                'url'       => $url,
                'response'  => $response,
                'http_code' => $httpCode,
                'sent_data' => $jsonData,
            ]);
        } else {
            Log::info('Webhook delivered successfully', [
                'url'       => $url,
                'response'  => $response,
                'http_code' => $httpCode,
                'sent_data' => $jsonData,
            ]);
        }
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
            'details'          => 'nullable|string|max:255',
            'instruction'      => 'nullable|string|max:255',
            'value'            => 'nullable|numeric|min:0',
            'payment_type'     => 'nullable|in:1,2', // 1 = CASH, 2 = CREDIT
            'preparation_time' => 'nullable|integer|min:0',
            'lat'              => 'nullable|numeric|between:-90,90',
            'lng'              => 'nullable|numeric|between:-180,180',
        ]);
        // Find the order by ID
        $order = Order::findOrFail($id);
        // Filter out empty fields from the request data
        $filteredData = array_filter($validated, function ($value) {
            return $value !== null && $value !== '';
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
                    "order_id"     => $order->id,
                    'status'       => $order->status->value,
                    'status_label' => $order->status->getLabel(),
                    'driver'       => $order->driver ? [
                        'id'           => $order->driver?->driver?->id,
                        'name'         => $order->driver?->driver?->full_name,
                        'phone'        => $order->driver?->driver?->phone,
                        'status'       => DriverStatus::tryFrom($order->driver?->driver?->operator?->status)?->getLabel(),
                        'tracking_url' => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",

                    ] : null,
                ];
                // dd($webhook->url);

                $this->sendOrderToWebhook($webhook->url, $orderData);
                //                $this->sendOrderToWebhook('https://webhook.site/329479cd-aea3-4e39-a77d-5384ceeab723', $orderData);
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
        ])->where('id', $request->order_id)->where('driver_id', auth()->user()->id)->first();
        if (! $order) {
            return $this->send_response(false, 400, 'cant change status', null);
        }

        $order->status = OrderStatus::PENDING_ORDER_CANCELLATION;
        $order->save();

        OrderLog::create([
            'order_id'    => $order->id,
            'status'      => OrderStatus::PENDING_ORDER_CANCELLATION,
            'action'      => 'Request Cancel Order',

            'user_id'     => auth()->id(),
            'description' => auth()->user()->first_name . ' request to cancel order ',
        ]);

        return $this->send_response(true, 200, 'cancel request under processing', null);
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

    private function sendToWasftyWebhook($url, $data, $token)
    {
        $jsonData = json_encode($data);
        // dd($jsonData);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
            'api-key: ' . $token,
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            Log::error('Webhook Wasfty  failed', [
                'url'       => $url,
                'response'  => $response,
                'http_code' => $httpCode,
            ]);
        } else {
            Log::info('Webhook Wasfty successfully', [
                'url'       => $url,
                'response'  => $response,
                'http_code' => $httpCode,
                'sent_data' => $jsonData,
            ]);
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
            '17',
        ])->where('driver_id', auth()->user()->id)->orderBy('created_at', 'desc')->get();
        if (! $order) {
            return $this->send_response(false, 400, 'no data found', null);
        }
        return $this->send_response(true, 200, 'success', OrderResource::collection($order));
    }

    public function accept_order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'status'   => 'required|in:17,18',
            'lat'      => ['nullable', 'numeric'],
            'lng'      => ['nullable', 'numeric'],
        ]);
        if ($validator->fails()) {
            return $this->send_response(false, 400, $validator->errors()->first(), null);
        }
        //check order can accepted or rejected
        $order = Order::with('shop', 'branch', 'driver')->where('id', $request->order_id)->whereIn('status', [
            2, 13,23
        ])->where('driver_id', auth()->user()->id)->first();
        if (! $order) {
            return $this->send_response(false, 400, 'no data found', null);
        }
        //check vehicle
        //        $vehicle = Vehicle::where('operator_id', auth()->user()->id)->first() ?? DriverVehicle::where('driver_id', auth()->user()->id)->orderBy('created_at', 'desc')->first();
        //        if (!$vehicle) {
        //            return $this->send_response(FALSE, 400, 'you dont have vehicle in our system', NULL);
        //        }

        //        $vehicle_id = Vehicle::where('operator_id', auth()->user()->id)->first()?->id ?? DriverVehicle::where('driver_id', auth()->user()->id)->orderBy('created_at', 'desc')->first()?->vehicle_id;
        // dd($vehicle, $vehicle_id);
        // DB::beginTransaction();

        $order->status               = $request->status;
        $order->driver_accepted_time = Carbon::now('Asia/Riyadh');
        try {
            if ($order->lat && $order->lng) {
                $branchLatitude         = @$order->branch->lat;
                $branchLongitude        = @$order->branch->lng;
                $location               = $this->getTravelTime($branchLatitude, $branchLongitude, auth()->user()->operator->lat, auth()->user()->operator->lng);
                $order->pickup_duration = $location['duration'];
                $order->pickup_distance = $location['distance'];
            }
        } catch (\Exception $exception) {
        }
        if ($request->status == 17) {
            //            $order->vehicle_id = $vehicle_id;

            $this->operatorRepository->change_status(2);
        }
        $order->save();

        try {
            $firebase = new \App\Repositories\FirebaseRepositoryTest();
            $firebase->saveBranches(collect([@$order->branch]));
            $firebase->saveMapData($order->id);
        } catch (\Throwable $e) {
        }

        $order->refresh();
        $this->add_order_log($order->id, $request->status, auth()->user()->id, $request->lat, $request->lng);
        // DB::commit();

        //firebase actions
        switch ($request->status) {
            case 17:
                $orderResource = new OrderResource($order);
                $orderData     = $orderResource->toArray(request());
                //try save firebase
                try {
                    // Attempt to save to Firebase
                    $this->firebaseRepository->save_driver_order(auth()->user()->id, $orderData);
                } catch (\Exception $e) {
                    // Handle the exception (log it, show a message, etc.)
                    Log::info($e);
                }
                break;
            case 18:
                //try delete firebase reference
                try {
                    $this->firebaseRepository->delete_driver_order(auth()->user()->id, $order->id);
                } catch (\Exception $e) {
                    // Handle the exception (log it, show a message, etc.)
                    Log::info($e);
                }
                break;
            default:
                Log::info('not correct status');
        }

        $client = $order->shop?->client ?? $order->branchIntegration?->client?->client;

        if ($client?->integration) {

            $webhook = WebHook::where('integration_company_id', $client?->integration?->id)->where('type', 'order_updated')->first();

            if ($webhook && $webhook->url) {

                if ($webhook->integration_company_id == 10) {
                    $orderData = [
                        "order_id"        => $order->id,
                        'status_id'       => 4,
                        'client_order_id' => $order->client_order_id_string,
                        'status'          => "Pending order preparation",
                        'created_at'      => Carbon::parse($order->created_at)->format('Y-m-d H:i:s'),
                        'driver'          => $order->driver ? [
                            'id'           => $order->driver?->driver?->id,
                            'name'         => $order->driver?->driver?->full_name,
                            'phone'        => $order->driver?->driver?->phone,
                            'status'       => DriverStatus::tryFrom($order->driver?->driver?->operator?->status)?->getLabel(),
                            'tracking_url' => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",
                            'location'     => [
                                'lat' => $order->driver?->driver?->operator?->lat,
                                'lng' => $order->driver?->driver?->operator?->lng,
                            ],
                        ] : null,
                    ];
                    $this->sendOrderToWebhook($webhook->url, $orderData);
                } elseif ($order->integration_id == 13) {
                    //blink
                    $data = [
                        "blink_order_id" => "$order->client_order_id_string",
                        "status"         => BlinkOrderStatus::GetStatus($order->status),
                    ];
                    $this->blinkHook($webhook->url, $data);
                    //blink

                } elseif ($client?->integration?->id == 21) {

                    $webhook = WebHook::where('integration_company_id', $client?->integration?->id)->where('type', 'order_updated')->first();

                    $orderData = [
                        "order_id"        => $order->id,
                        'status_id'       => $order->status->value,
                        'client_order_id' => $order->client_order_id_string,
                        'status'          => $order->status->getLabel(),
                        'created_at'      => Carbon::parse($order->created_at)->format('Y-m-d H:i:s'),
                        'driver'          => $order->driver ? [
                            'id'           => $order->driver?->driver?->id,
                            'name'         => $order->driver?->driver?->full_name,
                            'phone'        => $order->driver?->driver?->phone,
                            'status'       => DriverStatus::tryFrom($order->driver?->driver?->operator?->status)?->getLabel(),
                            'tracking_url' => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",

                        ] : null,
                    ];
                    $this->sendOrderToWebhook($webhook->url, $orderData);
                } elseif ($client?->integration?->id == 22) {

                    $webhook       = WebHook::where('integration_company_id', $client?->integration?->id)->where('type', 'order_updated')->first();
                    $order->status = OrderStatus::PENDING_ORDER_PREPARATION;
                    $orderData     = new LoginextOrderResource($order);
                    $this->sendOrderToWebhookLoginNext($webhook->url, $orderData);

                    //                    $orderData = [
                    //                        "order_id" => $order->id,
                    //                        'status_id' => 4,
                    //                        'client_order_id' => $order->client_order_id_string,
                    //                        'status' => "Driver accepted the order",
                    //                        'created_at' => Carbon::parse($order->created_at)->format('Y-m-d H:i:s'),
                    //                        'driver' => $order->driver ? [
                    //                            'id' => $order->driver?->driver?->id,
                    //                            'name' => $order->driver?->driver?->full_name,
                    //                            'phone' => $order->driver?->driver?->phone,
                    //                            'status' => DriverStatus::tryFrom($order->driver?->driver?->operator?->status)?->getLabel(),
                    //                            'tracking_url' => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",
                    //
                    //                        ] : NULL,
                    //                    ];
                } elseif ($client?->integration?->id == 120) {
                    $job_id  = $order->jop_id;
                    $lat     = $order->driver?->driver?->operator?->lat;
                    $lng     = $order->driver?->driver?->operator?->lng;
                    $ksaTime = Carbon::parse($order->driver_accepted_time, 'Asia/Riyadh');
                    $utcTime = $ksaTime->setTimezone('UTC');
                    $data    = [
                        "deliveryJobId" => "$job_id",
                        "pickupTimeETA" => $utcTime->addSeconds($order->pickup_duration)->format('Y-m-d\TH:i:s.u\Z'),
                        "transportType" => "bicycle",
                        "trackingUrl"   => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",
                        "courier"       => [
                            "name"      => auth()->user()->full_name,
                            "phone"     => auth()->user()->phone,
                            "longitude" => "$lng",
                            "latitude"  => "$lat",
                        ],
                        "locations"     => [
                            [
                                "orderId"         => "$order->client_order_id_string",
                                "status"          => DeliverectOrderStatus::GetStatus($order->status),
                                "deliveryTimeETA" => $utcTime->addSeconds($order->pickup_duration + $order->delivery_duration)->format('Y-m-d\TH:i:s.u\Z'),
                            ],
                        ],
                    ];

                    $this->sendOrderToWebhook("https://api.deliverect.com/fulfillment/generic/events", $data);
                } elseif ($client?->integration?->id == 15) {
                    //                    $orderData = new \App\Http\Resources\Api\americana\OrderResource($order);
                    $orderData = new AmericanaWebHookRequestResource($order);

                    $this->sendOrderToWebhookamericana($webhook->url, $orderData);
                } elseif ($client?->integration?->id == 14) {
                    $this->FoodicsWebhook($order->client_order_id_string, $order->shop?->client->foodics_token, [
                        "driver_assigned_at" => Carbon::now()->format('Y-m-d H:i:s'),
                        "dispatched_at"      => Carbon::now()->format('Y-m-d H:i:s'),
                        "delivered_at"       => Carbon::now()->addMinutes(30)->format('Y-m-d H:i:s'),
                        "driver_id"          => auth()->user()->id,
                        "delivery_status"    => 3,
                    ]);
                } elseif ($client?->integration?->id == 121) {
                    $driver_id = auth()->user()->id;
                    $data      = [
                        "order_id"        => "$order->id",
                        "status"          => LyveOrderStatus::GetStatus(OrderStatus::DRIVER_ACCEPTED),
                        'client_order_id' => $order->client_order_id_string,

                        "driver"          => [
                            "id"           => "$driver_id",
                            "name"         => auth()->user()->full_name,
                            "phone_number" => auth()->user()->phone,
                            "vehicle_type" => "Bike",
                        ],
                        'eta'             => [
                            'pickup'   => Carbon::parse($order->driver_accepted_time)->addSeconds($order->pickup_duration)->timestamp,
                            'delivery' => Carbon::parse($order->driver_accepted_time)->addSeconds($order->pickup_duration + $order->delivery_duration)->timestamp,
                        ],

                        "timestamp"       => Carbon::now()->timestamp,
                        "tracking_link"   => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",
                    ];
                    $token = $order->additional_details['callback_token'];
                    $this->SendToLyve($token, $data);
                } elseif ($client?->integration?->id == 140) {
                    $driver_id = auth()->user()->id;
                    $data      = [
                        "order_id"        => "$order->id",
                        "status"          => LyveOrderStatus::GetStatus($order->status),
                        'client_order_id' => $order->client_order_id_string,

                        "driver"          => [
                            "id"           => "$driver_id",
                            "name"         => auth()->user()->full_name,
                            "phone_number" => auth()->user()->phone,
                            "vehicle_type" => "Bike",
                        ],
                        'eta'             => [
                            'pickup'   => Carbon::parse($order->driver_accepted_time)->addSeconds($order->pickup_duration)->timestamp,
                            'delivery' => Carbon::parse($order->driver_accepted_time)->addSeconds($order->pickup_duration + $order->delivery_duration)->timestamp,
                        ],

                        "timestamp"       => Carbon::now()->timestamp,
                        "tracking_link"   => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",
                    ];
                    $token = $order->additional_details['callback_token'];
                    $this->SendToLyve($token, $data);
                } elseif ($client?->integration?->id == 125 || $client?->integration?->id == 126 || $client?->integration?->id == 127) {
                    $orderData = [
                        'order_id'        => $order->id,
                        'status_id'       => $order->status->value,
                        'client_order_id' => $order->client_order_id_string,

                        'status_label'    => $order->status->getLabel(),
                        'driver'          => $order->driver ? [
                            'id'           => $order->driver?->driver?->id,
                            'name'         => $order->driver?->driver?->full_name,
                            'phone'        => $order->driver?->driver?->phone,
                            'status'       => DriverStatus::tryFrom($order->driver?->driver?->operator?->status)?->getLabel(),
                            'tracking_url' => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",
                        ] : null,
                    ];
                    $this->sendOrderToWebhook($webhook->url, $orderData);
                } elseif ($client?->integration?->id == 153) {
                    $token = $order->additional_details['task_id'];

                    $orderData = [
                        'task_id'       => $token,
                        'status'        => LuluMarketOrderStatus::GetStatus($order->status),
                        'delivery_info' => $order->driver ? [
                            'name'      => $order->driver?->driver?->full_name,
                            'phone'     => $order->driver?->driver?->phone,
                            'photo_url' => $order->DriverData2?->ImageUrl,
                        ] : null,
                    ];
                    $this->sendOrderToWebhook($webhook->url, $orderData);
                } elseif ($client?->integration?->id == 128 || $client?->integration?->id == 146) {

                    $orderData = [
                        "order_id"        => $order->id,
                        'status_id'       => $order->status->value,
                        'client_order_id' => $order->client_order_id_string,
                        'status'          => $order->status->getLabel(),
                        'created_at'      => $order->created_at,
                        'invoice_url'     => $order->PdfUrl,
                        'tracking_url'    => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",
                        'driver'          => $order->driver ? [
                            'id'           => $order->driver?->driver?->id,
                            'name'         => $order->driver?->driver?->full_name,
                            'phone'        => $order->driver?->driver?->phone,
                            'status'       => DriverStatus::tryFrom($order->driver?->driver?->operator?->status)?->getLabel(),
                            'tracking_url' => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",

                        ] : null,
                    ];
                    $this->sendToWasftyWebhook($webhook->url, $orderData, $webhook->token);
                } else {

                    if ($webhook->client_type == 1) {

                        $orderData = new IntegrationResource($order);
                        $this->sendOrderIntegrationToWebhook($webhook, $orderData);
                    } else {
                        if ($client?->integration?->id == 17) {
                            $webhook = WebHook::where('integration_company_id', $client?->integration?->id)->where('type', 'order_updated')->first();
                        }
                        $orderData = [
                            "order_id"        => $order->id,
                            'status'          => $order->status->value,
                            'client_order_id' => $order->client_order_id_string ?? $order->client_order_id ?? $order->id,
                            'status_label'    => $order->status->getLabel(),
                            'created_at'      => Carbon::parse($order->created_at)->format('Y-m-d H:i:s'),
                            'driver'          => $order->driver ? [
                                'id'           => $order->driver?->driver?->id,
                                'name'         => $order->driver?->driver?->full_name,
                                'phone'        => $order->driver?->driver?->phone,
                                'status'       => DriverStatus::tryFrom($order->driver?->driver?->operator?->status)?->getLabel(),
                                'tracking_url' => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",

                            ] : null,
                        ];
                        $this->sendOrderToWebhook($webhook->url, $orderData);
                    }
                }

                // dd($webhook->url);

                //                $this->sendOrderToWebhook('https://webhook.site/329479cd-aea3-4e39-a77d-5384ceeab723', $orderData);
            }
        }

        return $this->send_response(true, 200, 'success', null);
    }

    public function driver_orders(Request $request)
    {
        $driver = Operator::find(auth()->user()->id);
        $orders = $driver->orders()->with('shop', 'branch', 'driver')->get();
        return $this->send_response(true, 200, 'success', OrderResource::collection($orders));
    }

    public function driver_orders_history(Request $request)
    {
        $driver = Operator::find(auth()->user()->id);
        $orders = $driver->orders()
            ->whereStatus(OrderStatus::DELIVERED)
            ->with(['shop', 'branch', 'driver'])
            ->paginate(10);

        return $this->send_response(true, 200, 'success', new DriverOrderCollection($orders));
    }

    public function driver_orders_failed(Request $request)
    {
        $driver = Operator::find(auth()->user()->id);
        $orders = $driver->orders()
            ->whereStatus(OrderStatus::FAILED)
            ->with(['shop', 'branch', 'driver'])
            ->paginate(10);

        return $this->send_response(true, 200, 'success', new DriverOrderCollection($orders));
    }

    public function update_status(Request $request)
    {
        // dd(99);
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'status'   => 'required',
            'lat'      => ['nullable', 'numeric'],
            'lng'      => ['nullable', 'numeric'],
            'code'     => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->send_response(false, 400, $validator->errors()->first(), null);
        }
        if (! in_array($request->status, ['4', '16', '6', '8', '9'])) {
            return $this->send_response(false, 400, 'cant change status', null);
        }
        //check if order belong to user

        $order = Order::with('shop', 'branch', 'driver')->whereNotIn('status', [
            '9',
            '10',
            '20',
        ])->where('id', $request->order_id)->where('driver_id', auth()->user()->id)->first();
        if (! $order) {
            return $this->send_response(false, 400, 'cant change status', null);
        }
        //update order
        $order->status = $request->status;
        if ($order->status == OrderStatus::DELIVERED) {
            //check if order need verify otp or not
            // Check if order needs OTP verification
            $response = $this->checkOrderOtp($request, $order);

            // Halt execution if OTP verification fails
            if ($response !== true) {
                return $response; // Return the error response
            }

            //set delivered at
        }

        switch ($request->status) {
            case 16: //Arrived to pickup
                $order->arrived_to_pickup_time = Carbon::now('Asia/Riyadh');
                break;
            case 6: //Order picked up
                $order->picked_up_time = Carbon::now('Asia/Riyadh');
                break;
            case 8: //Arrived to dropoff
                $order->arrived_to_dropoff_time = Carbon::now('Asia/Riyadh');
                break;
            case 9: //delivered
                $order->delivered_at = Carbon::now('Asia/Riyadh');
                break;
            default:
        }
        $order->save();

        // dd($order->delivered_at->format('Y:M:D'));
        //add order log
        $this->add_order_log($order->id, $request->status, auth()->user()->id, $request->lat, $request->lng);
        //firebase actions
        switch ($request->status) {
            case 4:  //pending
            case 16: //Arrived to pickup
            case 6:  //Order picked up

            case 8: //Arrived to dropoff
                        //change operator status to away
                $this->operatorRepository->change_status(2);
                $orderResource = new OrderResource($order);
                $orderData     = $orderResource->toArray(request());
                //try save firebase
                try {
                    // Attempt to save to Firebase
                    $this->firebaseRepository->save_driver_order(auth()->user()->id, $orderData);
                } catch (\Exception $e) {
                    // Handle the exception (log it, show a message, etc.)
                    Log::info($e);
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
                        'type'        => 'deposit',
                        'model_id'    => $order->id,
                        'model_type'  => Order::class,
                        'description' => ' اضافة قيمه طلب رقم' . $order->id,
                    ];
                    $this->WalletService->save($wallet_transaction);
                    $this->firebaseRepository->delete_driver_order(auth()->user()->id, $order->id);
                } catch (\Exception $e) {
                    // Handle the exception (log it, show a message, etc.)
                    Log::info($e);
                }
                break;
            default:
                Log::info('not correct status');
        }

        $client = $order->shop?->client ?? $order->branchIntegration?->client?->client;
        if ($client?->integration) {
            $webhook = WebHook::where('integration_company_id', $client?->integration?->id)->where('type', 'order_updated')->first();
            if ($webhook && $webhook->url) {

                if ($webhook->integration_company_id == 10) {
                    if ($order->status == OrderStatus::ARRIVED_PICK_UP || $order->status == OrderStatus::PICKED_UP) {
                        $lat = $order->branch->lat;
                        $lng = $order->branch->lng;
                    } elseif ($order->status == OrderStatus::ARRIVED_TO_DROPOFF || $order->status == OrderStatus::DELIVERED) {
                        $lat = $order->lat;
                        $lng = $order->lng;
                    } else {
                        $lat = $order->OperatorDetail->lat;
                        $lng = $order->OperatorDetail->lng;
                    }
                    $orderData = [
                        "order_id"        => $order->id,
                        'status_id'       => $order->status->value,
                        'client_order_id' => $order->client_order_id_string,
                        'status'          => $order->status->getLabel(),
                        'created_at'      => Carbon::parse($order->created_at)->format('Y-m-d H:i:s'),
                        'driver'          => $order->driver ? [
                            'id'           => $order->driver?->driver?->id,
                            'name'         => $order->driver?->driver?->full_name,
                            'phone'        => $order->driver?->driver?->phone,
                            'status'       => DriverStatus::tryFrom($order->driver?->driver?->operator?->status)?->getLabel(),
                            'tracking_url' => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",
                            'location'     => [
                                'lat' => $lat,
                                'lng' => $lng,
                            ],
                        ] : null,
                    ];
                    $this->sendOrderToWebhook($webhook->url, $orderData);
                } elseif ($client?->integration?->id == 128 || $client?->integration?->id == 146) {

                    $orderData = [
                        "order_id"        => $order->id,
                        'status_id'       => $order->status->value,
                        'client_order_id' => $order->client_order_id_string,
                        'status'          => $order->status->getLabel(),
                        'created_at'      => $order->created_at,
                        'invoice_url'     => $order->PdfUrl,
                        'tracking_url'    => "https://www.google.com/maps?q={$order->DriverData2?->operator?->lat},{$order->DriverData2?->operator?->lng}",
                        'driver'          => $order->DriverData2 ? [
                            'id'           => $order->DriverData2?->id,
                            'name'         => $order->DriverData2?->full_name,
                            'phone'        => $order->DriverData2?->phone,
                            'status'       => DriverStatus::tryFrom($order->DriverData2?->operator?->status)?->getLabel(),
                            'tracking_url' => "https://www.google.com/maps?q={$order->DriverData2?->operator?->lat},{$order->DriverData2?->operator?->lng}",

                        ] : null,
                    ];
                    $this->sendToWasftyWebhook($webhook->url, $orderData, $webhook->token);
                } elseif ($client?->integration?->id == 15) {
                    //                    $orderData = new \App\Http\Resources\Api\americana\OrderResource($order);
                    $orderData = new AmericanaWebHookRequestResource($order);

                    $this->sendOrderToWebhookamericana($webhook->url, $orderData);
                } elseif ($client?->integration?->id == 153) {
                    $token = $order->additional_details['task_id'];

                    $orderData = [
                        'task_id'       => $token,
                        'status'        => LuluMarketOrderStatus::GetStatus($order->status),
                        'delivery_info' => $order->driver ? [
                            'name'      => $order->driver?->driver?->full_name,
                            'phone'     => $order->driver?->driver?->phone,
                            'photo_url' => $order->DriverData2?->ImageUrl,
                        ] : null,
                    ];
                    $this->sendOrderToWebhook($webhook->url, $orderData);
                } elseif ($client?->integration?->id == 120) {
                    $job_id  = $order->jop_id;
                    $lat     = $order->driver?->driver?->operator?->lat;
                    $lng     = $order->driver?->driver?->operator?->lng;
                    $ksaTime = Carbon::parse($order->driver_accepted_time, 'Asia/Riyadh');
                    $utcTime = $ksaTime->setTimezone('UTC');
                    $data    = [

                        "deliveryJobId"   => "$job_id",
                        "deliveryTimeETA" => $utcTime->addSeconds($order->pickup_duration)->format('Y-m-d\TH:i:s.u\Z'),
                        "transportType"   => "bicycle",
                        "trackingUrl"     => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",
                        "courier"         => [
                            "name"      => auth()->user()->full_name,
                            "phone"     => auth()->user()->phone,
                            "longitude" => "$lng",
                            "latitude"  => "$lat",
                        ],
                        "locations"       => [
                            [
                                "orderId"         => "$order->client_order_id_string",
                                "status"          => DeliverectOrderStatus::GetStatus($order->status),
                                "deliveryTimeETA" => $utcTime->addSeconds($order->pickup_duration + $order->delivery_duration)->format('Y-m-d\TH:i:s.u\Z'),
                            ],
                        ],
                    ];
                    $this->sendOrderToWebhook("https://api.deliverect.com/fulfillment/generic/events", $data);
                } elseif ($client?->integration?->id == 121 || $client?->integration?->id == 140) {
                    $driver_id = auth()->user()->id;
                    $data      = [
                        "order_id"      => "$order->id",
                        "status"        => LyveOrderStatus::GetStatus($order->status),
                        "driver"        => [
                            "id"           => "$driver_id",
                            "name"         => auth()->user()->full_name,
                            "phone_number" => auth()->user()->phone,
                            "vehicle_type" => "Bike",
                        ],
                        'eta'           => [
                            'pickup'   => Carbon::parse($order->driver_accepted_time)->addSeconds($order->pickup_duration)->timestamp,
                            'delivery' => Carbon::parse($order->driver_accepted_time)->addSeconds($order->pickup_duration + $order->delivery_duration)->timestamp,
                        ],
                        "timestamp"     => Carbon::now()->timestamp,
                        "tracking_link" => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",
                    ];
                    $token = $order->additional_details['callback_token'];
                    $this->SendToLyve($token, $data);
                } elseif ($client?->integration?->id == 14) {
                    $this->FoodicsWebhook($order->client_order_id_string, $order->shop?->client->foodics_token, [
                        "driver_assigned_at" => Carbon::now()->format('Y-m-d H:i:s'),
                        "dispatched_at"      => Carbon::parse($order->updated_at)->format('Y-m-d H:i:s'),
                        "delivered_at"       => Carbon::parse($order->created_at)->addMinutes(30)->format('Y-m-d H:i:s'),
                        "driver_id"          => auth()->user()->id,
                        "delivery_status"    => FoodicsOrderStatus::GetStatus($order->status),
                    ]);
                } elseif ($client?->integration?->id == 13) {
                    //blink
                    $data = [
                        "blink_order_id" => "$order->client_order_id_string",
                        "status"         => BlinkOrderStatus::GetStatus($order->status),
                    ];
                    $this->blinkHook($webhook->url, $data);
                    //blink
                    $this->sendOrderToWebhook($webhook->url, $orderData);
                } elseif ($client?->integration?->id == 125 || $client?->integration?->id == 126 || $client?->integration?->id == 127) {
                    $orderData = [
                        'order_id'        => $order->id,
                        'status_id'       => $order->status->value,
                        'client_order_id' => $order->client_order_id_string,
                        'status_label'    => $order->status->getLabel(),
                        'driver'          => $order->driver ? [
                            'id'           => $order->driver?->driver?->id,
                            'name'         => $order->driver?->driver?->full_name,
                            'phone'        => $order->driver?->driver?->phone,
                            'status'       => DriverStatus::tryFrom($order->driver?->driver?->operator?->status)?->getLabel(),
                            'tracking_url' => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",
                        ] : null,
                    ];
                    $this->sendOrderToWebhook($webhook->url, $orderData);
                } elseif ($webhook->integration_company_id == 21) {
                    $webhook = WebHook::where('integration_company_id', $client?->integration?->id)->where('type', 'order_updated')->first();

                    $orderData = [
                        "order_id"        => $order->id,
                        'status_id'       => $order->status->value,
                        'client_order_id' => $order->client_order_id_string,
                        'status'          => $order->status->getLabel(),
                        'created_at'      => Carbon::parse($order->created_at)->format('Y-m-d H:i:s'),
                        'driver'          => $order->driver ? [
                            'id'           => $order->driver?->driver?->id,
                            'name'         => $order->driver?->driver?->full_name,
                            'phone'        => $order->driver?->driver?->phone,
                            'status'       => DriverStatus::tryFrom($order->driver?->driver?->operator?->status)?->getLabel(),
                            'tracking_url' => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",

                        ] : null,
                    ];
                    $this->sendOrderToWebhook($webhook->url, $orderData);
                } elseif ($webhook->integration_company_id == 22) {
                    $webhook = WebHook::where('integration_company_id', $client?->integration?->id)->where('type', 'order_updated')->first();

                    $orderData = new LoginextOrderResource($order);

                    //                    if ($order->status != OrderStatus::ARRIVED_PICK_UP) {
                    $this->sendOrderToWebhookLoginNext($webhook->url, $orderData);
                    //                    }

                } else {
                    if ($webhook->client_type == 1) {

                        $orderData = new IntegrationResource($order);
                        $this->sendOrderIntegrationToWebhook($webhook, $orderData, $webhook->token);
                    } else {
                        $orderData = [
                            "order_id"        => $order->id,
                            'status'          => $order->status->value,
                            'client_order_id' => $order->client_order_id_string ?? $order->client_order_id ?? $order->id,
                            'status_label'    => $order->status->getLabel(),
                            'driver'          => $order->driver ? [
                                'id'           => $order->driver?->driver?->id,
                                'name'         => $order->driver?->driver?->full_name,
                                'phone'        => $order->driver?->driver?->phone,
                                'status'       => DriverStatus::tryFrom($order->driver?->driver?->operator?->status)?->getLabel(),
                                'tracking_url' => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",
                                'latitude'     => $order->DriverData2?->operator?->lat,
                                'longitude'    => $order->DriverData2?->operator?->lng,
                            ] : null,
                        ];
                        $this->sendOrderToWebhook($webhook->url, $orderData);
                    }
                }
                // dd($webhook->url);

                //               $this->sendOrderToWebhook('https://webhook.site/329479cd-aea3-4e39-a77d-5384ceeab723', $orderData);
            }
        }

        return $this->send_response(true, 200, 'success', new OrderResource($order));
    }

    public function add_order_log($order_id, $status, $driver_id, $lat = null, $lng = null)
    {
        OrderLog::create(
            [
                'order_id'  => $order_id,
                'driver_id' => $driver_id,
                'status'    => $status,
                'lat'       => $lat,
                'lng'       => $lng,
                'action'    => OrderStatus::tryFrom($status)?->getLabel() ?? 'Unknown Status',
            ]
        );
        //        Log::info('latandlong',[
        //            'lat' => $lat,
        //            'lng' => $lng,
        //        ]);

    }

    public function report_problem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'nullable|string',
            'order_id'    => ['required', 'exists:orders,id'],
            'reason'      => 'required|string',
        ]);
        if ($validator->fails()) {
            return $this->send_response(false, 400, $validator->errors()->first(), null);
        }
        OrderReport::create([
            'order_id'    => $request['order_id'],
            'driver_id'   => auth()->user()->id,
            'reason'      => $request['reason'],
            'description' => $request['description'],
        ]);
        return $this->send_response(true, 200, 'Report created successfully', null);
    }

    public function addWebHook(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name'   => 'required',
            'url'    => 'required|url',
            'type'   => 'required|in:order_created,order_updated,order_cancelled',
            'format' => 'nullable|in:form-data,JSON',
        ]);

        if ($validator->fails()) {
            return $this->send_response(false, 400, $validator->errors()->first(), null);
        }
        $API_TOKEN = $request->route('API_TOKEN');

        $user = \App\Models\Client::where('integration_token', $API_TOKEN)->first();
        if (! $user) {
            return $this->send_response(false, 400, 'user not found', null);
        }
        $webhook = WebHook::create([
            'name'                   => $request->name,
            'url'                    => $request->url,
            'type'                   => $request->type,
            'integration_company_id' => $user->client?->integration?->id,
        ]);
        return $this->send_response(true, 200, 'Webhook created successfully', null);
    }

    public function listWebHook()
    {
        $web_hooks = ModelsWebHook::all();
        return $this->send_response(true, 200, 'success', WebHookResource::collection($web_hooks));
    }

    public function deleteWebHook($id, Request $request)
    {
        $API_TOKEN = $request->route('API_TOKEN');

        $user = \App\Models\Client::where('integration_token', $API_TOKEN)->first();
        if (! $user) {
            return $this->send_response(false, 400, 'user not found', null);
        }
        $web_hook = webHook::find($id);
        if (! $web_hook) {
            return $this->send_response(false, 400, 'web hook not found', null);
        } else {
            $web_hook->delete();
            return $this->send_response(true, 200, 'Webhook deleted successfully', null);
        }
    }

    public function send_order_otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
        ]);
        if ($validator->fails()) {
            return $this->send_response(false, 403, $validator->errors()->first(), null);
        }
        //check if order belong to user and status is arrived to dropoff

        $order = Order::with('shop', 'branch', 'driver')->whereIn('status', [
            '8',
        ])->where('id', $request->order_id)->where('driver_id', auth()->user()->id)->first();
        if (! $order) {
            return $this->send_response(false, 400, 'order not found', null);
        }
        if (! $order->otp) {
            return $this->send_response(false, 400, 'this order not have otp', null);
        }
        // send otp and return status
        $otp_status = $this->send_otp_to_customer($order);
        if (! $otp_status) {
            return $this->send_response(false, 400, 'code not sent', null);
        } else {
            return $this->send_response(true, 200, 'success', null);
        }
    }
    private function send_otp_to_customer($order)
    {
        $otpState = false;
        $phone    = $order->customer_phone;
        $code     = $order->otp;
        try {
            // send sms code
            if (App::environment('production')) {
                if ($phone == '566278832' || $phone == '566238294') {
                    $otpState = true;
                } else {
                    $message_sending = ' رمز تفعيل الطلب : ' . $code;
                    //send sms and get response
                    $notify_res = SendSms::toSms($phone, $message_sending);
                    $notify_res = $notify_res->getData();
                    $otpState   = $notify_res->status;
                }
            } else {
                $otpState = true;
            }
            // store data in db
            if ($otpState) {

                // DB::transaction(function () use ($order) {
                $order->otp_sent_at = Carbon::now()->format('Y-m-d H:i:s');
                $order->save();
                //  });
            }
        } catch (\Exception $exception) {
            Log::info('SendSms::toSms error', [
                'getMessage' => $exception->getMessage(),
            ]);
            $otpState = false;
        }
        return $otpState;
    }

    private function checkOrderOtp($request, $order)
    {
        if ($order->otp) {
            if (! $request->code) {
                return $this->send_response(false, 400, 'you should enter customer code to deliver this order', null);
            }
            $is_verified = $this->verify_otp($request->code, $order);
            if (! $is_verified) {
                return $this->send_response(false, 400, 'code not verified', null);
            }
        }
        return true;
    }

    private function verify_otp($code, $order)
    {
        $is_verified = false;
        try {
            if (! $order->otp_sent_at) {
                return $is_verified = false;
            }
            $created = new Carbon($order->otp_sent_at);
            $now     = Carbon::now();
            //check if code = requested code and not extend 2 minutes
            $is_verified = ($order->otp == $code) ? 1 : 0;
            if ($is_verified) {
                $is_verified = true;
            } else {
                return $is_verified = false;
            }
        } catch (\Exception $exception) {
            $is_verified = false;
        }
        return $is_verified;
    }

    private function sendOrderIntegrationToWebhook($webhook, $data): void
    {
        $jsonData = json_encode($data);
        $header   = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
        ];
        if ($webhook->token) {
            $header[] = 'Authorization:' . $webhook->token;
        }
        $ch = curl_init($webhook->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = [
            'message'   => 'Webhook update',
            'url'       => $webhook->url,
            'response'  => $response,
            'http_code' => $httpCode,
            'sent_data' => $jsonData,
        ];
        $this->logWebhookData($webhook->id, $data);
    }

    function logWebhookData($webhookId, $data): void
    {
        $folderPath = public_path('logs/webhooks');
        $fileName   = 'callback-' . now()->format('Y-m-d') . "-{$webhookId}.txt";
        $filePath   = $folderPath . '/' . $fileName;

        try {
            if (! File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
            }

            if (! File::exists($filePath)) {
                File::put($filePath, '');
            }

            // Append data to the file
            $formattedData = json_encode($data, JSON_PRETTY_PRINT);
            File::append($filePath, $formattedData . PHP_EOL);
        } catch (Exception $e) {
        }
    }
}
