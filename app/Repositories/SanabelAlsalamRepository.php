<?php

namespace App\Repositories;

use App\Http\Services\AutoDispatcherService;
use App\Models\User;
use App\Models\WebHook;
use App\Repositories\FirebaseRepository;
use Auth;
use Validator;

use App\Models\Order;

use App\Enum\OrderStatus;
use App\Enum\DriverStatus;

use App\Rules\KSAPhoneRule;
use Illuminate\Http\Request;
use App\Models\ClientBranches;
use App\Traits\HandleResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\Api\OrderResource;
use App\Traits\OrderCreationDateValidation;
use Dotenv\Exception\ValidationException;
use Exception;

class SanabelAlsalamRepository
{
    use HandleResponse, OrderCreationDateValidation;

    public function __construct(FirebaseRepository $firebaseRepository, AutoDispatcherService $autoDispatcherService)
    {
        $this->firebaseRepository = $firebaseRepository;
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
                'payment_type' => 'required|in:1,2',
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

            $user = User::where('integration_token', @$request->route('API_TOKEN'))->first();

            if (!$user) {
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
            if (!$branch) {
                return response()->json(['error' => 'Branch not found'], 404);
            }

            if ($branch->is_active != 1) {
                return response()->json(['message' => 'Unactive branch'], 401);
            }

            $validated['ingr_shop_id'] = $branch->client_id;
            $validated['ingr_branch_id'] = $branch->id;
            $validated['ingr_shop_name'] = $user->fullName ?? null;
            $validated['ingr_branch_name'] = $branch->name;
            $validated['ingr_branch_lat'] = $branch->lat;
            $validated['ingr_branch_lng'] = $branch->lng;
            $validated['ingr_branch_phone'] = $branch->phone;

            if (!$this->isWithinBusinessHours($user->id)) {
                return response()->json(['error' => 'System Closed'], 404);
            }

            $order = Order::create([
                'pickup_lat' => $validated['pickup_lat'] ?? null,
                'pickup_lng' => $validated['pickup_lng'] ?? null,
                'pickup_id' => $validated['pickup_id'] ?? null,
                'client_order_id_string' => $validated['client_order_id'] ?? null,
                'value' => $validated['value'] ?? null,
                'payment_type' => $validated['payment_type'],
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
                'additional_details' => $validated,
                'integration_id'=>@$user->client->integration_id,

                'ingr_shop_id' => $validated['ingr_shop_id'] ?? null,
                'ingr_branch_id' => $validated['ingr_branch_id'] ?? $validated['pickup_id'],
                'ingr_shop_name' => $validated['ingr_shop_name'] ?? null,
                'ingr_branch_name' => $validated['ingr_branch_name'] ?? null,
                'ingr_branch_lat' => $validated['ingr_branch_lat'] ?? null,
                'ingr_branch_lng' => $validated['ingr_branch_lng'] ?? null,
                'ingr_branch_phone' => $validated['ingr_branch_phone'] ?? null,
                'integration_token' => @$request->route('API_TOKEN'),
            ]);


            $client = $order->shop?->client ?? $order->branchIntegration?->client?->client;

            if ($client?->integration) {
                $webhook = WebHook::where('integration_company_id', $client?->integration?->id)->where('type', 'order_created')->first();
                if ($webhook && $webhook->url) {
                    $orderData = [
                        'order_id' => $order->id,
                        'status' => 3,
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

            $webhook = WebHook::where('integration_company_id', $client?->integration?->id)->where('type', 'order_updated')->first();
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
}
