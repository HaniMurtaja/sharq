<?php

namespace App\Http\Services;

use App\Enum\OrderStatus;
use App\Http\Resources\Api\DeliverectOrderResource;
use App\Http\Resources\Api\OrderResource;
use App\Http\Services\MapDistanceCalculator;
use App\Models\Client;
use App\Models\ClientBranches;
use App\Models\ClientDetail;
use App\Models\Order;
use App\Models\OrderLog;
use App\Rules\KSAPhoneRule;
use App\Traits\HandleResponse;
use App\Traits\OrderCreationDateValidation;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Null_;

class DeliverectOrderService
{

    use HandleResponse;
    use OrderCreationDateValidation;


    public function createOrder($token, $request)
    {
        // dd($token);
        try {
            $validated = $request->validate([
                'jobId' => 'nullable|string',
                'account' => 'nullable|string',
                //                'pickupTime' => 'nullable|date_format:Y-m-d\TH:i:s\Z',
                'pickupTime' => 'nullable',
                'transportType' => 'nullable|string',
                'driverTip' => 'nullable|integer|min:0',

                'pickupLocation' => 'nullable|array',
                'pickupLocation.location' => 'nullable|string',
                'pickupLocation.name' => 'nullable|string',
                'pickupLocation.remarks' => 'nullable|string',
                'pickupLocation.street' => 'nullable|string',
                'pickupLocation.streetNumber' => 'nullable|string',
                'pickupLocation.postalCode' => 'nullable|string',
                'pickupLocation.city' => 'nullable|string',
                'pickupLocation.latitude' => 'nullable|numeric',
                'pickupLocation.longitude' => 'nullable|numeric',

                'deliveryLocations' => 'nullable|array',
                'deliveryLocations.*.orderId' => 'nullable|string',
                'deliveryLocations.*.channelOrderDisplayId' => 'nullable|string',
                //                'deliveryLocations.*.deliveryTime' => 'nullable|date_format:Y-m-d\TH:i:s\Z',
                'deliveryLocations.*.deliveryTime' => 'nullable',
                'deliveryLocations.*.packageSize' => 'nullable|string',
                'deliveryLocations.*.orderDescription' => 'nullable|string',
                'deliveryLocations.*.company' => 'nullable|string',
                'deliveryLocations.*.name' => 'nullable|string',
                'deliveryLocations.*.street' => 'nullable|string',
                'deliveryLocations.*.streetNumber' => 'nullable|string',
                'deliveryLocations.*.postalCode' => 'nullable|string',
                'deliveryLocations.*.city' => 'nullable|string',
                'deliveryLocations.*.phone' => [
                    'nullable',
                    // new KSAPhoneRule()
                ],
                'deliveryLocations.*.phoneAccessCode' => 'nullable|string',
                'deliveryLocations.*.deliveryRemarks' => 'nullable|string',
                'deliveryLocations.*.latitude' => 'nullable|numeric',
                'deliveryLocations.*.longitude' => 'nullable|numeric',

                'deliveryLocations.*.payment' => 'nullable|array',
                'deliveryLocations.*.payment.orderIsAlreadyPaid' => 'nullable|boolean',
                'deliveryLocations.*.payment.amount' => 'nullable|integer|min:0',
                'deliveryLocations.*.payment.paymentType' => 'nullable|integer',

                'ageCheck' => 'nullable|boolean',
            ]);

            // dd(Client::find(auth()->id())->branches);
            // dd(Client::where('firebase_token',$token)->first()?->branches);
            $pickup_id = Client::where('integration_token', $token)->first()?->id;
            if (! $pickup_id) {
                return response()->json(['error' => 'Client not found'], 404);
            }
            // dd($pickup_id);
            $pickup = ClientBranches::where([
                'client_id' => $pickup_id,
                'custom_id' => $validated['pickupLocation']['location'],
            ]);
            $branches = $pickup->first();
            if (! $branches) {
                return response()->json(['error' => 'Location not found'], 400);
            }
            if ($branches->is_active != 1) {
                return response()->json(['message' => 'Unactive branch'], 401);
            }
            if (!$this->isWithinBusinessHours($pickup_id)) {
                return response()->json(['error' => 'System Closed'], 404);
            }
            //            $ClientDetail = ClientDetail::where('user_id', auth()->id())->first();
            $orderData = [
                'jop_id' => $validated['jobId'] ?? NULL,
                'pickup_lat' => $validated['pickupLocation']['latitude'] ?? NULL,
                'pickup_lng' => $validated['pickupLocation']['longitude'] ?? NULL,
                'pickup_id' => $pickup_id,
                'client_order_id' => (int) $validated['deliveryLocations'][0]['orderId'] ?? NULL,
                'client_order_id_string' => $validated['deliveryLocations'][0]['orderId'] ?? NULL,
                'value' => $validated['deliveryLocations'][0]['payment']['amount'] ?? NULL,
                'service_fees' => $validated['driverTip'],
                'lat' => $validated['deliveryLocations'][0]['latitude'] ?? NULL,
                'lng' => $validated['deliveryLocations'][0]['longitude'] ?? NULL,
                'address' => $validated['deliveryLocations'][0]['street'] ?? NULL,
                // 'city' => $validated['pickupLocation']['city'] ?? NULL,
                'customer_phone' => $validated['deliveryLocations'][0]['phone'] ?? '566378232',
                'customer_name' => $validated['deliveryLocations'][0]['name'] ?? NULL,
                'status' => 1,
                'ingr_shop_id' => $pickup_id,
                'ingr_branch_id' => $branches->id,
                'integration_id' => 120,
                'ingr_shop_name' => $validated['ingr_shop_name'] ?? NULL,
                'ingr_branch_name' => $validated['pickupLocation']['name'] ?? NULL,
                'ingr_branch_lat' => $validated['pickupLocation']['latitude'] ?? NULL,
                'ingr_branch_lng' => $validated['pickupLocation']['longitude'] ?? NULL,
                'integration_token' => $token,
                'additional_details' => $validated,

            ];

            $order = Order::create($orderData);


            return response()->json(new DeliverectOrderResource($order), 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }


    public function cancelOrder($token, $request)
    {
        // dd($request);
        $validated = $request->validate([
            'jobId' => 'nullable|string',
            'account' => 'nullable|string',


            'pickupLocation' => 'nullable|array',
            'pickupLocation.location' => 'nullable|string',


            'deliveryLocations' => 'nullable|array',
            'deliveryLocations.*.orderId' => 'nullable|string',
            'deliveryLocations.*.channelOrderDisplayId' => 'nullable|string',
            'deliveryLocations.*.deliveryId' => 'nullable|string',

            'courier' => 'nullable|array',
            'courier.*.courierId' => 'nullable|string',
        ]);

        $order = Order::where('jop_id', $validated['jobId'])->first();
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 400);
        }

        $order->status = OrderStatus::PENDING_CLIENT_ORDER_CANCELLATION;
        $order->save();

        $client = Client::where('firebase_token', $token)->first();

        OrderLog::create([
            'order_id' =>  $order->id,
            'status' => OrderStatus::PENDING_CLIENT_ORDER_CANCELLATION,
            'action' => 'Request Cancel Order',

            'user_id' => $client?->id,
            'description' => $client?->full_name . ' request to cancel order ',
        ]);

        return $this->send_response(
            TRUE,
            200,
            '',
            [
                'status' => 'confirmed',
                'reason' => '',
                'price' => $order->value + $order->service_fees
            ]
        );
    }

    public function updateStatus($url, $status, $order) {}
}
