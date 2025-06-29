<?php

namespace App\Repositories;

use App\Http\Resources\Api\Loginext\LoginextOrderResource;
use App\Http\Services\AutoDispatcherService;
use App\Models\City;
use App\Models\OrderLog;
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

class LoginextRepository
{
    use HandleResponse, OrderCreationDateValidation;

    public function __construct(FirebaseRepository $firebaseRepository, AutoDispatcherService $autoDispatcherService)
    {
        $this->firebaseRepository = $firebaseRepository;
        $this->autoDispatcherService = $autoDispatcherService;
    }

    public function formatNumber($number)
    {
        // Check if the number starts with "0"
        if (str_starts_with($number, '0')) {
            return substr($number, 1); // Remove the first character
        }
        return $number; // Return the number as is
    }

    public function save_order(Request $request)
    {
        try {
            $validated = $request->validate([
                'pickup_lat' => 'required:pickup_id|numeric',
                'pickup_lng' => 'required:pickup_id|numeric',
                'pickup_id' => 'required',
                'client_order_id' => 'nullable',
                'value' => 'nullable|numeric',
                'payment_type' => 'required|in:1,2',
                'preparation_time' => 'nullable|integer|min:0',
                'lat' => 'nullable|required_without:address,city|numeric',
                'lng' => 'nullable|required_without:address,city|numeric',
                'address' => 'nullable|required_without:lat,lng|string',
                'city' => 'nullable',
                'customer_phone' => ['required'],
                'customer_name' => 'nullable|string',
                'deliver_at' => 'nullable|date_format:Y-m-d H:i:s',
                'details' => 'nullable|string',
                'pickup_poa' => 'nullable|boolean',
                'dropoff_poa' => 'nullable|boolean',
            ]);

            $user = User::where('integration_token', @$request->route('API_TOKEN'))->first();

            if (!$user) {
                return response()->json(['error' => 'invalid token'], 404);
            }
            $pickup_id = $request->pickup_id;
            if (strpos($pickup_id, '.') !== false) {
                $clean_pickup_id = explode('.', $pickup_id)[0];
            } else {
                $clean_pickup_id = $pickup_id;
            }
            $branch = ClientBranches::where('client_id', $user->id)->where(function ($q) use ($clean_pickup_id) {
                $q->where('id', $clean_pickup_id)
                    ->orWhere('custom_id', $clean_pickup_id);
            })->first();
            //            }
            //            if (isset($validated['pickup_lat'], $validated['pickup_lng'])) {
            //                $branch = ClientBranches::where('client_id', $user->id)->where(function ($q) use ($validated) {
            //                    $q->where('lat', $validated['pickup_lat'])->where('lng', $validated['pickup_lng']);
            //                })->first();
            //            }
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

//            $city = null;
//            if ($validated['city']) {
//                $city = City::where('name', 'LIKE', "%{$validated['city']}%")->first()->id;
//            }

            $order = Order::create([
                'pickup_lat' => $validated['pickup_lat'] ?? null,
                'pickup_lng' => $validated['pickup_lng'] ?? null,
                'pickup_id' => $clean_pickup_id ?? null,
                'client_order_id_string' => $validated['client_order_id'] ?? null,
                'value' => $validated['value'] ?? null,
                'payment_type' => $validated['payment_type'],
                'preparation_time' => $validated['preparation_time'] ?? 0,
                'lat' => $validated['lat'] ?? null,
                'lng' => $validated['lng'] ?? null,
                'address' => $validated['address'] ?? null,
                'city' =>  null,
                'customer_phone' => $this->formatNumber($validated['customer_phone']),
                'customer_name' => $validated['customer_name'] ?? null,
                'deliver_at' => $validated['deliver_at'] ?? null,
                'details' => $validated['details'] ?? null,
                'pickup_poa' => $validated['pickup_poa'] ?? null,
                'dropoff_poa' => $validated['dropoff_poa'] ?? null,
                'status' => 1,
                'ingr_shop_id' => $validated['ingr_shop_id'] ?? null,
                'ingr_branch_id' => $validated['ingr_branch_id'] ?? $clean_pickup_id,
                'ingr_shop_name' => $validated['ingr_shop_name'] ?? null,
                'ingr_branch_name' => $validated['ingr_branch_name'] ?? null,
                'ingr_branch_lat' => $validated['ingr_branch_lat'] ?? null,
                'ingr_branch_lng' => $validated['ingr_branch_lng'] ?? null,
                'ingr_branch_phone' => $validated['ingr_branch_phone'] ?? null,
                'integration_token' => @$request->route('API_TOKEN'),
                'additional_details' => $validated,
                'integration_id'=>@$user->client->integration_id,

            ]);


            $client = $order->shop?->client ?? $order->branchIntegration?->client?->client;

            if ($client?->integration) {
                $webhook = WebHook::where('integration_company_id', $client?->integration?->id)->where('type', 'order_created')->first();
                if ($webhook && $webhook->url) {
                    $orderData = new LoginextOrderResource($order);
                    $this->sendOrderToWebhook($webhook->url, $orderData);
                }
            }


            return response()->json(new LoginextOrderResource($order), 200);
        } catch (ValidationException $e) {

            return response()->json(['errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function sendOrderToWebhook($url, $data)
    {
//        $jsonData = json_encode($data);
//        // dd($jsonData);
//        $ch = curl_init($url);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, [
//            'Content-Type: application/json',
//            'Content-Length: ' . strlen($jsonData)
//        ]);
//        curl_setopt($ch, CURLOPT_POST, true);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
//        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
//
//        $response = curl_exec($ch);
//        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//        curl_close($ch);
//
//
//        if ($httpCode !== 200) {
//            Log::error('Webhook delivery failed', [
//                'url' => $url,
//                'response' => $response,
//                'http_code' => $httpCode,
//                'sent_data' => $jsonData
//
//            ]);
//        } else {
//            Log::info('Webhook delivered successfully', [
//                'url' => $url,
//                'response' => $response,
//                'http_code' => $httpCode,
//                'sent_data' => $jsonData
//            ]);
//        }

        $jsonData = json_encode($data);
        // dd($jsonData);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_TIMEOUT_MS, 700);
        //  curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
               if ($httpCode !== 200) {
           Log::error('Webhook Loginext delivery failed', [
               'url' => $url,
               'response' => $response,
               'http_code' => $httpCode,
               'sent_data' => $jsonData

           ]);
       } else {
           Log::info('Webhook Loginext delivered successfully', [
               'url' => $url,
               'response' => $response,
               'http_code' => $httpCode,
               'sent_data' => $jsonData
           ]);
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
        return response()->json(new OrderResource($order), 200);
    }

    public function cancel_order(Request $request, $id)
    {

        $order = Order::find($id);
        // Check if the order exists
        if ($order) {
            $order->status = OrderStatus::PENDING_CLIENT_ORDER_CANCELLATION;
            $order->save();

            OrderLog::create([
                'order_id' =>  $order->id,
                'status' => OrderStatus::PENDING_CLIENT_ORDER_CANCELLATION,
                'action' => 'Request Cancel Order',
                'user_id' => $order->ingr_shop_id,
                'description' => 'The client has requested to cancel the order',
            ]);
            // Return the order wrapped in a resource
            return response()->json(['message' => 'ok , Order Cancelled'], 200);
        } else {
            // Handle the case where the order was not found
            return response()->json(['error' => 'Order not found'], 404);
        }
    }
}
