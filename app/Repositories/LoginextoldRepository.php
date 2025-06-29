<?php

namespace App\Repositories;

use App\Http\Services\AutoDispatcherService;
use App\Http\Services\NotificationService;
use App\Models\City;
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

class LoginextoldRepository
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
                'notificationType' => 'required|string',
                'pickupLatitude' => 'required|numeric',
                'pickupLongitude' => 'required|numeric',
                'deliverLatitude' => 'required|numeric',
                'deliverLongitude' => 'required|numeric',
                'orderNo' => 'required|string',
                'referenceId' => 'required|string',
                'awbNumber' => 'required|string',
                'orderState' => 'required|string',
                'endTimeWindow' => 'required|date_format:Y-m-d H:i:s',
                'orderLeg' => 'required|string',
                'pickupAccountCode' => 'required|string',
                'pickupAddressId' => 'required|string',
                'deliverAccountCode' => 'required|string',
                'packageValue' => 'required|numeric',
                'pickupCity' => 'required|string',
                'pickupNotes' => 'nullable|string',
                'deliverNotes' => 'nullable|string',
                'deliverAddressId' => 'required|string',
                'returnAccountCode' => 'required|string',
                'returnAddressId' => 'required|string',
                'clientCode' => 'required|string',
            ]);

            $user = User::where('integration_token', @$request->route('API_TOKEN'))->first();

            if (!$user) {
                return response()->json(['error' => 'invalid token'], 404);
            }

            if (!$this->isWithinBusinessHours($user->id)) {
                return response()->json(['error' => 'System Closed'], 404);
            }
            $client_branch = ClientBranches::where('client_id', $user->id)->where([
                    'lat' => $validated['pickupLatitude'],
                    'lng' => $validated['pickupLongitude'],
                ])->first();
            if (!$client_branch) {
                $city_id = City::where('name', 'like', '%' . $validated['pickupCity'] . '%')->first();
                if (!$city_id) {
                    $city_id = City::create([
                        'name' => $validated['pickupCity'],
                        'country_id' => 1,
                    ]);
                }
                $client_branch =  ClientBranches::create([
                    'client_id' => $user->id,
                    'name' => $validated['pickupAccountCode'],
                    'lat' => $validated['pickupLatitude'],
                    'lng' => $validated['pickupLongitude'],
                    'phone' => '0000000000',
                    'address' => 'Address',
                    'city' => $city_id->id,
                ]);
            }
            $payment_type = 1;

            if (!$client_branch) {
                return response()->json(['error' => 'Branch not found'], 404);
            }


            if ($client_branch->is_active != 1) {
                return response()->json(['message' => 'Unactive branch'], 401);
            }
            $order = Order::create([
                'pickup_lat' => $validated['pickupLatitude'] ?? null,
                'pickup_lng' => $validated['pickupLongitude'] ?? null,
                'pickup_id' => $client_branch->id ?? null,
                'client_order_id_string' => $validated['orderNo'] ?? null, //change
                'value' => $validated['packageValue'] ?? null, //change
                'payment_type' => $payment_type,
                'preparation_time' => $validated['preparation_time'] ?? 0,
                'lat' => $validated['deliverLatitude'] ?? null, //change
                'lng' => $validated['deliverLongitude'] ?? null, //change
                'address' => $validated['deliverNotes'] ?? null, //change
                'city' => $validated['pickupCity'] ?? null,
                'customer_phone' => $validated['customer_phone'] ?? null,
                'customer_name' => $validated['customer_name'] ?? null,
                'details' => $validated['kitchen_notes'] ?? null, //change
                'status' => 1,
                'integration_id' => 22,
                'ingr_shop_id' => $user->id,
                'ingr_branch_id' => $pickup->id ?? null,
                'ingr_shop_name' => $user->fullName ?? null,
                'ingr_branch_name' => $pickup->name ?? null,
                'ingr_branch_lat' => $pickup->lat ?? null,
                'ingr_branch_lng' => $pickup->lng ?? null,
                'ingr_branch_phone' => $pickup->phone ?? null,
                'integration_token' => @$request->route('API_TOKEN'),
                'additional_details' => $validated,
            ]);






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

        // dd(9);
        $order = Order::find($id);
        // Check if the order exists
        if ($order) {
            $order->status = OrderStatus::PENDING_CLIENT_ORDER_CANCELLATION;

            $order->save();


            // $user = User::where('integration_token', @$request->route('API_TOKEN'))->first();

            // OrderLog::create([
            //     'order_id' => $order->id,
            //     'status' => OrderStatus::PENDING_CLIENT_ORDER_CANCELLATION,
            //     'action' => 'Request Cancel Order',
            //     'user_id' => $user->id,
            //     'description' => $user->first_name . ' requested to cancel the order',
            // ]);



            // Return the order wrapped in a resource
            return response()->json(['message' => 'ok , Order Cancelled'], 200);
        } else {
            // Handle the case where the order was not found
            return response()->json(['error' => 'Order not found'], 404);
        }
    }
}
