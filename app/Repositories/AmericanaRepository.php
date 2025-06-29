<?php
namespace App\Repositories;

use App\Enum\OrderStatus;
use App\Http\Resources\Api\AmericanaWebHookRequestResource;
use App\Http\Resources\Api\americana\AuthResource;
use App\Http\Resources\Api\americana\OrdersResource;
use App\Http\Resources\Api\OrderResource;
use App\Http\Resources\Api\WebHookResource;
use App\Http\Services\NotificationService;
use App\Http\Services\WalletService;
use App\Models\ClientBranches;
use App\Models\Order;
use App\Models\OrderLog;
use App\Models\User;
use App\Models\WebHook;
use App\Traits\HandleResponse;
use App\Traits\OrderCreationDateValidation;
use Carbon\Carbon;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Validator;

class AmericanaRepository
{
    use HandleResponse, OrderCreationDateValidation;

    public function __construct(FirebaseRepository $firebaseRepository, OperatorRepository $operatorRepository, WalletService $WalletService, NotificationService $notificationService)
    {
        $this->firebaseRepository  = $firebaseRepository;
        $this->operatorRepository  = $operatorRepository;
        $this->WalletService       = $WalletService;
        $this->notificationService = $notificationService;
    }

    public function save_order(Request $request)
    {
        try {
            $validated = $request->validate([
                'pickup_lat'       => 'required_without:pickup_id|numeric',
                'pickup_lng'       => 'required_without:pickup_id|numeric',
                'pickup_id'        => 'required_without:pickup_lat,pickup_lng|integer',
                'client_order_id'  => 'nullable',
                'value'            => 'nullable|numeric',
                'payment_type'     => 'required|in:1,2',
                'preparation_time' => 'nullable|integer|min:0',
                'lat'              => 'nullable|required_without:address,city|numeric',
                'lng'              => 'nullable|required_without:address,city|numeric',
                'address'          => 'nullable|required_without:lat,lng|string',
                'city'             => 'nullable|required_without:lat,lng|integer',
                'customer_phone'   => [
                    'required',
                ],
                'customer_name'    => 'nullable|string',
                'deliver_at'       => 'nullable|date_format:Y-m-d H:i:s|after_or_equal:now',
                'details'          => 'nullable|string',
                'pickup_poa'       => 'nullable|boolean',
                'dropoff_poa'      => 'nullable|boolean',
            ]);
            $user = User::where('integration_token', @$request->route('API_TOKEN'))->first();

            if (! $user) {
                return response()->json(['error' => 'invalid token'], 404);
            }
            $branch = null;
            if (isset($validated['pickup_id'])) {
                $branch = ClientBranches::where('client_id', $user->id)->where(function ($q) use ($validated) {
                    $q->where('id', $validated['pickup_id'])
                        ->orWhere('custom_id', $validated['pickup_id']);
                })->first();
            }
            if (isset($validated['pickup_lat'], $validated['pickup_lng'])) {
                $branch = ClientBranches::where('client_id', $user->id)->where(function ($q) use ($validated) {
                    $q->where('lat', $validated['pickup_lat'])->where('lng', $validated['pickup_lng']);
                })->first();
            }
            if (! $branch) {
                return response()->json(['error' => 'Branch not found'], 404);
            }
            if ($branch->is_active != 1) {
                return response()->json(['message' => 'Unactive branch'], 401);
            }
            $validated['ingr_shop_id']      = $branch->client_id;
            $validated['ingr_branch_id']    = $branch->id;
            $validated['ingr_shop_name']    = $user->fullName ?? null;
            $validated['ingr_branch_name']  = $branch->name;
            $validated['ingr_branch_lat']   = $branch->lat;
            $validated['ingr_branch_lng']   = $branch->lng;
            $validated['ingr_branch_phone'] = $branch->phone;

            if (! $this->isWithinBusinessHours($user->id)) {
                return response()->json(['error' => 'System Closed'], 404);
            }

            $order = Order::create([
                'pickup_lat'             => $validated['pickup_lat'] ?? null,
                'pickup_lng'             => $validated['pickup_lng'] ?? null,
                'pickup_id'              => $validated['pickup_id'] ?? null,
                'client_order_id_string' => $validated['client_order_id'] ?? null,
                'value'                  => $validated['value'] ?? null,
                'payment_type'           => $validated['payment_type'],
                'preparation_time'       => $validated['preparation_time'] ?? 0,
                'lat'                    => $validated['lat'] ?? null,
                'lng'                    => $validated['lng'] ?? null,
                'address'                => $validated['address'] ?? null,
                'city'                   => $validated['city'] ?? null,
                'customer_phone'         => $validated['customer_phone'],
                'customer_name'          => $validated['customer_name'] ?? null,
                'deliver_at'             => $validated['deliver_at'] ?? null,
                'details'                => $validated['details'] ?? null,
                'pickup_poa'             => $validated['pickup_poa'] ?? null,
                'dropoff_poa'            => $validated['dropoff_poa'] ?? null,
                'status'                 => 1,
                'additional_details'     => $validated,

                'ingr_shop_id'           => $validated['ingr_shop_id'] ?? null,
                'ingr_branch_id'         => $validated['ingr_branch_id'] ?? $validated['pickup_id'],
                'ingr_shop_name'         => $validated['ingr_shop_name'] ?? null,
                'ingr_branch_name'       => $validated['ingr_branch_name'] ?? null,
                'ingr_branch_lat'        => $validated['ingr_branch_lat'] ?? null,
                'ingr_branch_lng'        => $validated['ingr_branch_lng'] ?? null,
                'ingr_branch_phone'      => $validated['ingr_branch_phone'] ?? null,
                'integration_token'      => @$request->route('API_TOKEN'),
                'integration_id'         => @$user->client->integration_id,

            ]);

            $client    = $order->shop?->client ?? $order->branchIntegration?->client?->client;
            $orderData = new \App\Http\Resources\Api\americana\OrderResourceForWebHook($order);

            if ($client?->integration) {
                $webhook = WebHook::where('integration_company_id', $client?->integration?->id)->where('type', 'order_created')->first();
                if ($webhook && $webhook->url) {
//                    $orderData = [
//                        'order_id' => $order->id,
//                        'status' => 3,
//                        'client_order_id' => $order->client_order_id_string,
//
//                        'status_label' => 'The order has been accepted',
//                        'driver' => $order->driver ? [
//                            'id' => $order->driver?->driver?->id,
//                            'name' => $order->driver?->driver?->full_name,
//                            'phone' => $order->driver?->driver?->phone,
//                            'status' => DriverStatus::tryFrom($order->driver?->driver?->operator?->status)?->getLabel(),
//                            'tracking_url' => "https://www.google.com/maps?q={$order->driver?->driver?->operator?->lat},{$order->driver?->driver?->operator?->lng}",
//                        ] : null,
//                    ];

                    $this->sendOrderToWebhook($webhook->url, $orderData);
                }
            }

            return response()->json($orderData, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
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
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            Log::error('Webhook delivery failed', [
                'url'       => $url,
                'response'  => $response,
                'http_code' => $httpCode,
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

            $webhook = WebHook::where('integration_company_id', $client?->integration?->id)->where('type', 'order_updated')->first();
            if ($webhook && $webhook->url) {
                $orderData = new AmericanaWebHookRequestResource($order);
                // dd($webhook->url);

                $this->sendOrderToWebhook($webhook->url, $orderData);
            }
        }
        return response()->json(new OrderResource($order), 201);
    }

    public function cancel_order(Request $request, $id)
    {

        $order = Order::whereNotIn('status', [OrderStatus::PENDING_CLIENT_ORDER_CANCELLATION, OrderStatus::DELIVERED, OrderStatus::CANCELED])->find($id);

        // Check if the order exists
        if ($order) {
            $order->status = OrderStatus::PENDING_CLIENT_ORDER_CANCELLATION;

            $order->save();
            OrderLog::create([
                'order_id'    => $order->id,
                'status'      => OrderStatus::PENDING_CLIENT_ORDER_CANCELLATION,
                'action'      => 'Request Cancel Order',
                'user_id'     => $order->ingr_shop_id,
                'description' => 'The client has requested to cancel the order',
            ]);
            // $client = $order->shop?->client ?? $order->branchIntegration?->client?->client;
            // if ($client?->integration) {

            //     $webhook = WebHook::where('integration_company_id', $client?->integration?->id)->where('type', 'order_cancelled')->first();
            //     if ($webhook && $webhook->url) {
            //         $orderData = new AmericanaWebHookRequestResource($order);
            //         // dd($webhook->url);

            //         $this->sendOrderToWebhook($webhook->url, $orderData);
            //     }
            // }
            // Return the order wrapped in a resource
            return response()->json(['message' => 'ok , Order PENDING  CANCELLATION'], 200);
        } else {
            // Handle the case where the order was not found
            return response()->json(['error' => 'Order not found or Order PENDING  CANCELLATION or already cancelled or delivered'], 404);
        }
    }

    public function track_order(Request $request, $id)
    {

        $order = Order::select('id as order_id', 'lat', 'lng', 'ingr_branch_id', 'driver_id')
            ->with([
                'branch'         => function ($subQuery) {
                    $subQuery->select('id', 'lat', 'lng');
                },
                'OperatorDetail' => function ($subQuery) {
                    $subQuery->select('id', 'lat', 'lng', 'operator_id');
                },
            ])->find($id);

        if (! $order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        return response()->json(

            [
                "order_id" => $order->order_id,
                "shop"     => [
                    "lat" => $order->branch->lat,
                    "lng" => $order->branch->lng,
                ],
                "customer" => [
                    "lat" => $order->lat,
                    "lng" => $order->lng,
                ],
                // , requted from americana (saeed) 03-06-2025
                // "driver" => [
                //     "lat" => $order->OperatorDetail ? $order->OperatorDetail->lat : "",
                //     "lng" => $order->OperatorDetail ? $order->OperatorDetail->lng : ""
                // ]
            ]
            , 200
        );

    }

    public function addWebHook($request)
    {

        $API_TOKEN = $request->route('API_TOKEN');

        $user = \App\Models\Client::where('integration_token', $API_TOKEN)->first();
        if (! $user) {
            return response()->json(['message' => 'user not found'], 400);
        }
        if (! $user) {
            return response()->json(['message' => 'user not found'], 400);
        }
        if (! $user->client) {
            return response()->json(['message' => 'user not found'], 400);
        }
        if (! $user->client->integration_id || $user->client->is_integration != 1) {
            return response()->json(['message' => 'user not found'], 400);
        }

        $webhook = WebHook::where('integration_company_id', $user->client->integration_id)->where('type', $request->type)->firstOrCreate([
            'name'                   => $request->name,
            'url'                    => $request->url,
            'type'                   => $request->type,
            'format'                 => $request->format,
            'integration_company_id' => $user->client->integration_id,
        ]);
        return response()->json(['message' => 'webhook created successfully'], 200);
    }

    public function listWebHook($request)
    {
        $API_TOKEN = $request->route('API_TOKEN');

        $user = \App\Models\Client::where('integration_token', $API_TOKEN)->first();
        if (! $user) {
            return response()->json(['message' => 'user not found'], 400);
        }
        if (! $user->client) {
            return response()->json(['message' => 'user not found'], 400);
        }
        if (! $user->client->integration_id || $user->client->is_integration != 1) {
            return response()->json(['message' => 'user not found'], 400);
        }
        $web_hooks = WebHook::where('integration_company_id', $user->client->integration_id)->get();
        return response()->json(WebHookResource::collection($web_hooks));
    }

    public function deleteWebHook($id, $request)
    {
        $API_TOKEN = $request->route('API_TOKEN');

        $user = \App\Models\Client::where('integration_token', $API_TOKEN)->first();
        if (! $user) {
            return response()->json(['message' => 'user not found'], 400);
        }
        if (! $user->client) {
            return response()->json(['message' => 'user not found'], 400);
        }
        if (! $user->client->integration_id || $user->client->is_integration != 1) {
            return response()->json(['message' => 'user not found'], 400);
        }
        $web_hook = WebHook::where('id', $id)->where('integration_company_id', $user->client->integration_id)->first();
        if (! $web_hook) {
            return response()->json(['message' => 'web hook not found'], 400);
        } else {
            $web_hook->delete();
            return response()->json(['message' => 'webhook deleted successfully'], 200);
        }
    }

    public function auth($request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email|exists:users,email',
            'password' => 'required',
        ],

            [
                'email'    => 'البريد الالكتروني',
                'password' => 'كلمه السر',
            ]

            , []);
        if ($validator->fails()) {
            return response()->json(['message' => 'email or password is required', 'success' => false, 'code' => 406], 406);

        }

        $credentials = [
            'email'    => $request['email'],
            'password' => $request['password'],
        ];
        if (\auth()->attempt($credentials)) {
            $user            = \auth()->user();
            $token           = $user->createToken('auth-token')->plainTextToken;
            $user->jwt_token = $token;
            return response()->json(['data' => [
                'user' => new AuthResource($user),
            ], 'success' => true, 'code' => 200], 200);

        }
        return response()->json(['message' => 'email or password is Invalid', 'success' => false, 'code' => 406], 406);

    }
//    public function sse($id, Request $request)
//    {
//        // Validate token (unchanged)
//        $token = $request->token;
//        if (!$token || !User::where('integration_token', $token)->exists()) {
//            return response()->json(['message' => 'Invalid token', 'success' => false], 406);
//        }
//
//        // Create SSE stream
//        $response = new StreamedResponse(function() use ($id) {
//            $lastDriverStates = []; // Track last known state of each driver
//
//            while (true) {
//                // Fetch orders with driver locations
//                $orders = Order::where('ingr_shop_id', $id)
//                    ->whereNotNull('driver_id')
//                    ->whereDate('created_at', '>=', Carbon::yesterday())
//                    ->get();
//
//                $updatedDrivers = [];
//
//                foreach ($orders as $order) {
//                    $driverId = $order->driver_id;
//                    $currentState = [
//                        'lat' => $order->lat,
//                        'lng' => $order->lng,
//                        'driver_status_id' => $order->driver_status_id,
//                    ];
//
//                    // Check if driver data has changed
//                    if (!isset($lastDriverStates[$driverId]) || $lastDriverStates[$driverId] != $currentState) {
//                        $updatedDrivers[] = [
//                            'parent_id' => $order->parent_id,
//                            'driver_id' => $driverId,
//                            'lat' => $order->lat,
//                            'lng' => $order->lng,
//                            'driver_status_id' => $order->driver_status_id,
//                        ];
//                        $lastDriverStates[$driverId] = $currentState; // Update stored state
//                    }
//                }
//
//                // Send update if there are changes
//                if (!empty($updatedDrivers)) {
//                    $data = [
//                        'event' => 'UPDATE_DRIVER_LOCATION',
//                        'data' => $updatedDrivers,
//                    ];
//
//                    // SSE format: "event: ...\ndata: {...}\n\n"
//                    echo "event: UPDATE_DRIVER_LOCATION\n";
//                    echo 'data: ' . json_encode($data) . "\n\n";
//                    ob_flush();
//                    flush();
//                }
//
//                sleep(5); // Adjust interval as needed
//            }
//        });
//
//        // Set SSE headers
//        $response->headers->set('Content-Type', 'text/event-stream');
//        $response->headers->set('Cache-Control', 'no-cache');
//        $response->headers->set('Connection', 'keep-alive');
//        $response->headers->set('X-Accel-Buffering', 'no'); // Disable buffering for nginx
//
//        return $response;
//    }

    public function sse_old($id, Request $request)
    {
        header("Content-Type: text/event-stream");
        header("Cache-Control: no-cache");
        header("Connection: keep-alive");
        header("X-Accel-Buffering: no");
        \Log::info('SSE Started');

        $response = new StreamedResponse(function () use ($id) {

            // Turn off output buffering completely
            if (function_exists('apache_setenv')) {
                apache_setenv('no-gzip', '1');
            }
            ini_set('output_buffering', 'off');
            ini_set('zlib.output_compression', 'off');
            ini_set('implicit_flush', '1');
            for ($i = 0; $i < ob_get_level(); $i++) {
                ob_end_flush();
            }
            ob_implicit_flush(true);

            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('Connection: keep-alive');
            header('X-Accel-Buffering: no');
            while (true) {
                // Log start of loop

                // Fetch data
                $orders = Order::where(function ($q) {
                    $q->whereDate('created_at', Carbon::yesterday())
                        ->orWhereDate('created_at', Carbon::today());
                })->whereNotNull('driver_id')->where('ingr_shop_id', $id)->get();

                $data   = OrdersResource::collection($orders);
                $result = [
                    "event" => "UPDATE_DRIVER_LOCATION",
                    "data"  => OrdersResource::collection($orders),
                ];
                // Send SSE message
                echo "event: UPDATE_DRIVER_LOCATION\n";
                echo "data: " . json_encode($result) . "\n\n";
                echo str_repeat("event: UPDATE_DRIVER_LOCATION", 1024); // Prevent buffering

                // Flush output
                @ob_end_flush();
                @ob_flush();
                flush();

                // Log that data was sent

                // Check if the client has disconnected
                if (connection_aborted()) {
                    \Log::info('SSE Connection Closed');
                    break;
                }

                sleep(50);
            }

        });

        return $response;
    }
    public function sse($id, Request $request)
    {
        // Validate token (unchanged)
        $token = $request->token;
        if (! $token || ! User::where('id', $id)->where('integration_token', $token)->exists()) {
            return response()->json(['message' => 'Invalid token', 'success' => false], 406);
        }
        header("Content-Type: text/event-stream");
        header("Cache-Control: no-cache");
        header("Connection: keep-alive");
        header("X-Accel-Buffering: no");

        $response = new StreamedResponse(function () use ($id) {

            // Turn off output buffering completely
            if (function_exists('apache_setenv')) {
                apache_setenv('no-gzip', '1');
            }
            ini_set('output_buffering', 'off');
            ini_set('zlib.output_compression', 'off');
            ini_set('implicit_flush', '1');
            for ($i = 0; $i < ob_get_level(); $i++) {
                ob_end_flush();
            }
            ob_implicit_flush(true);

            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('Connection: keep-alive');
            header('X-Accel-Buffering: no');
            while (true) {
                // Log start of loop

                // Fetch data
                $orders = Order::where(function ($q) {
                    $q->whereDate('created_at', Carbon::yesterday())
                        ->orWhereDate('created_at', Carbon::today());
                })->whereNotNull('driver_id')->where('ingr_shop_id', $id)->first();

                $data   = (isset($orders)) ? new OrdersResource($orders) : (object) [];
                $result = [
                    "event" => "UPDATE_DRIVER_LOCATION",
                    "data"  => $data,
                ];
                // Send SSE message
                echo "event: UPDATE_DRIVER_LOCATION\n";
                echo "data: " . json_encode($result) . "\n\n";
                echo str_repeat("event: UPDATE_DRIVER_LOCATION", 1024); // Prevent buffering

                // Flush output
                @ob_end_flush();
                @ob_flush();
                flush();

                // Log that data was sent

                // Check if the client has disconnected
                if (connection_aborted()) {
                    break;
                }

                sleep(50);
            }

        });

        return $response;
    }

}
