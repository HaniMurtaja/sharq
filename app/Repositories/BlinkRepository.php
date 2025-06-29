<?php

namespace App\Repositories;

use App\Http\Services\AutoDispatcherService;
use App\Http\Services\NotificationService;
use App\Models\ClientBranches;
use App\Models\User;
use App\Models\WebHook;
use Auth;
use Validator;

use App\Models\Order;

use App\Enum\OrderStatus;
use App\Enum\DriverStatus;


use Illuminate\Http\Request;

use App\Traits\HandleResponse;

use Illuminate\Support\Facades\Log;
use App\Http\Services\WalletService;

use App\Http\Resources\Api\OrderResource;
use App\Models\OrderLog;
use App\Traits\OrderCreationDateValidation;
use Dotenv\Exception\ValidationException;
use Exception;

class BlinkRepository
{
    use HandleResponse, OrderCreationDateValidation;

    public function __construct(
        FirebaseRepository $firebaseRepository,
        OperatorRepository $operatorRepository,
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
                'branch_latitude' => 'required_without:branch_id|numeric',
                'branch_longitude' => 'required_without:branch_id|numeric',
                'branch_id' => 'required_without:branch_latitude,branch_longitude',
                'blink_order_id' => 'nullable',
                'value' => 'nullable|numeric',
                'payment_method' => 'required',
                'preparation_time' => 'nullable|integer|min:0',
                'customer_address_latitude' => 'nullable|required_without:customer_address_description|numeric',
                'customer_address_longitude' => 'nullable|required_without:customer_address_description|numeric',
                'customer_address_description' => 'nullable|required_without:customer_address_latitude,customer_address_longitude|string',
                //'city' => 'nullable|required_without:customer_address_latitude,customer_address_longitude|integer',
                'customer_phone' => [
                    'required',
                    //new KSAPhoneRule()
                ],
                'customer_name' => 'nullable|string',
                'due_at' => 'nullable',
                'details' => 'nullable|string',
                'pickup_poa' => 'nullable|boolean',
                'dropoff_poa' => 'nullable|boolean',
                'order_key' => 'nullable|string',
                'type' => 'nullable',
                'channel' => 'nullable',
                'branch_address' => 'nullable',
                'brand_id' => 'nullable',
                'total_discount_amount' => 'nullable',
                'promo_code' => 'nullable',
                'promo_discount' => 'nullable',
                'loyalty_amount' => 'nullable',
                'wallet_amount' => 'nullable',
                'fbr_pos_charge' => 'nullable',
                'tax' => 'nullable',
                'is_pre_order' => 'nullable',
                'payment' => 'nullable',
                'products' => 'nullable|array',
                'products.*.product_id' => 'nullable',
                'products.*.quantity' => 'nullable',
                'products.*.name' => 'nullable',
                'products.*.unit_price' => 'nullable',
                'products.*.total_price' => 'nullable',
                'products.*.options' => 'nullable|array',
                'products.*.options.*.modifier_option_id' => 'nullable',
                'products.*.options.*.unit_price' => 'nullable',
                'products.*.options.*.total_price' => 'nullable',
                'products.*.options.*.name' => 'nullable',
                'products.*.options.*.quantity' => 'nullable',
            ]);
            if ($validated['type'] != "DELIVERY") {
                return response()->json(['error' => 'order type not equal DELIVERY'], 404);
            }
            if (is_null($request->input('branch_id')) && (is_null($request->input('branch_latitude')) || is_null($request->input('branch_longitude')))) {
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

            if (!$this->isWithinBusinessHours($request->ingr_shop_id)) {
                return response()->json(['error' => 'System Closed'], 404);
            }

            if ($request->payment_method == "Cash") {
                $payment_type = 1;
            } else {
                $payment_type = 3;
            }

            $user = User::where('integration_token', @$request->route('API_TOKEN'))->first();

            if (!$user) {
                return response()->json(['error' => 'invalid token'], 404);
            }
            $pickup = ClientBranches::where('client_id', $user->id)->where(function ($q) use ($validated) {
                $q->where('id', $validated['branch_id'])
                    ->orWhere('custom_id', $validated['branch_id']);
            })->first();

            if (!$pickup) {
                return response()->json(['error' => 'pickup not  found'], 404);
            }

            if ($pickup->is_active != 1) {
                return response()->json(['message' => 'Unactive branch'], 401);
            }
            $order = Order::create([
                'pickup_lat' => $validated['branch_latitude'] ?? null,
                'pickup_lng' => $validated['branch_longitude'] ?? null,
                'pickup_id' => $pickup->id ?? null,
                'client_order_id_string' => $validated['blink_order_id'] ?? null, // change
                'value' => $validated['total_price'] ?? null, // change
                'payment_type' => $payment_type,
                'preparation_time' => $validated['preparation_time'] ?? 0,
                'lat' => $validated['customer_address_latitude'] ?? null, // change
                'lng' => $validated['customer_address_longitude'] ?? null, // change
                'address' => $validated['customer_address_description'] ?? null, // change
                'city' => $validated['city'] ?? null,
                'customer_phone' => $validated['customer_phone'],
                'customer_name' => $validated['customer_name'] ?? null,
                'details' => $validated['kitchen_notes'] ?? null, // change
                'status' => 1,
                'integration_id' => 13,
                'ingr_shop_id' => $validated['ingr_shop_id'] ?? $user->id,
                'ingr_branch_id' => $pickup->id ?? null,
                'ingr_shop_name' => $user->fullName ?? null,
                'ingr_branch_name' => $pickup->name ?? null,
                'ingr_branch_lat' => $pickup->lat ?? null,
                'ingr_branch_lng' => $pickup->lng ?? null,
                'ingr_branch_phone' => $pickup->phone ?? null,
                'integration_token' => $request->route('API_TOKEN') ?? null,
                'additional_details' => $validated,

            ]);


            $client = $order->shop?->client ?? $order->branchIntegration?->client?->client;

            if ($client?->integration) {
                $webhook = WebHook::where('integration_company_id', $client?->integration?->id)->where('type', 'order_created')->first();
                if ($webhook && $webhook->url) {
                    $orderData = [
                        'order_id' => $order->id,
                        'status' => 1,
                        'client_order_id' => $order->client_order_id_string,

                        'status_label' => 'The order has been accepted',
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


            return response()->json(new OrderResource($order), 201);
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
                    "order_id" => $order->id,
                    'status' => $order->status->value,
                    'status_label' => $order->status->getLabel(),
                    'driver' => $order->driver ? [
                        'id' => $order->driver?->driver?->id,
                        'name' => $order->driver?->driver?->full_name,
                        'phone' => $order->driver?->driver?->phone,
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

        $user = User::where('integration_token', @$request->route('API_TOKEN'))->first();

        if ($order) {
            $order->status = OrderStatus::PENDING_CLIENT_ORDER_CANCELLATION;

            $order->save();

            OrderLog::create([
                'order_id' => $order->id,
                'status' => OrderStatus::PENDING_CLIENT_ORDER_CANCELLATION,
                'action' => 'Request Cancel Order',
                'user_id' => $user->id,
                'description' => $user->first_name . ' requested to cancel the order',
            ]);
            // Return the order wrapped in a resource
            return response()->json(['message' => 'ok , Order Cancelled'], 200);
        } else {
            // Handle the case where the order was not found
            return response()->json(['error' => 'Order not found'], 404);
        }
    }
}
