<?php

namespace App\Http\Services;

use App\Enum\OrderStatus;
use App\Http\Requests\Api\Lyve\LyveOrderRequest;
use App\Http\Resources\Api\Lyve\OrderResource;
use App\Models\Client;

use App\Models\ClientBranches;
use App\Models\Order;
use App\Models\OrderLog;

use App\Traits\ResponseHandler;
use App\Traits\OrderCreationDateValidation;
use App\Traits\isWithinBusinessHours;
use Illuminate\Http\Request;


class LyveOrderService
{

    use ResponseHandler;
    use OrderCreationDateValidation;

    public function CreateOrder(LyveOrderRequest $request): \Illuminate\Http\JsonResponse
    {
        $pickup_id = Client::where('id', auth()->user()->id)->first();

        if (! $pickup_id) {
            return response()->json(['message' => 'Location not found'], 404);
        }

        if ($pickup_id->is_active != 1) {
            return response()->json(['message' => 'Unactive user'], 401);
        }

        $pickup = ClientBranches::where([
            'client_id' => auth()->user()->id,
            'custom_id' => $request->sender['name'],
        ]);

        if (!$this->isWithinBusinessHours(auth()->user()->id)) {
            return response()->json(['error' => 'System Closed'], 404);
        }






        $branches = $pickup->first();
        if (! $branches) {
            return response()->json(['error' => 'Location not found'], 400);
        }

        if ($branches->is_active != 1) {
            return response()->json(['message' => 'Unactive branch'], 401);
        }


        $validated = $request->validated();
        $fees = $pickup_id->client?->clienGroup?->default_delivery_fee;

        $orderData = [
            'pickup_lat' => $validated['sender']['location']['latitude'] ?? NULL,
            'pickup_lng' => $validated['sender']['location']['longitude'] ?? NULL,
            'pickup_id' => $pickup_id->id,
            'client_order_id' => (int) $validated['client_order_id'] ?? NULL,
            'client_order_id_string' => $validated['order_number'] ?? NULL,
            'value' => $validated['recipient']['amount'] ?? NULL,
            'service_fees' => $fees,
            'lat' => $validated['recipient']['location']['latitude'] ?? NULL,
            'lng' => $validated['recipient']['location']['longitude'] ?? NULL,
            'address' => $validated['recipient']['location']['address'] ?? NULL,
            // 'city' => $validated['pickupLocation']['city'] ?? NULL,
            'customer_phone' => $validated['recipient']['phone_number'] ?? '566378232',
            'customer_name' => $validated['recipient']['name'] ?? NULL,
            'status' => 1,
            'ingr_shop_id' => auth()->user()->id,
            'ingr_branch_id' => $branches->id,
            'integration_id' => @$pickup_id->client->integration_id,
            'additional_details' => $validated,
            'ingr_shop_name' => $validated['sender']['name'] ?? NULL,
            'ingr_branch_name' => $validated['sender']['name'] ?? NULL,
            'ingr_branch_lat' => $validated['sender']['location']['latitude'] ?? NULL,
            'ingr_branch_lng' => $validated['sender']['location']['longitude'] ?? NULL,

        ];

        $order = Order::create($orderData);


        if ($order) {
            return  response()->json(new OrderResource($order), 201);
        } else {

            return response()->json(['message' => 'Order not created'], 400);
        }
    }


    public function cancelOrder($id): \Illuminate\Http\JsonResponse
    {
        $pickup_id = Client::where('id', auth()->user()->id)->first();
        if (! $pickup_id) {
            return response()->json(['message' => 'Location not found'], 404);
        }
        if ($pickup_id->is_active != 1) {
            return response()->json(['message' => 'Unactive user'], 401);
        }
        $order = Order::where('id', $id)->first();
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if (in_array($order->status, [OrderStatus::CREATED, OrderStatus::PENDINE_DRIVER_ACCEPTANCE])) {

            $order->status = OrderStatus::PENDING_CLIENT_ORDER_CANCELLATION;
            $order->save();

            OrderLog::create([
                'order_id' => $order->id,
                'status' => OrderStatus::PENDING_CLIENT_ORDER_CANCELLATION,
                'action' => 'Request Cancel Order',
                'user_id' => $pickup_id?->id,
                'description' => $pickup_id?->full_name . ' request to cancel order ',
            ]);

            return response()->json(['message' => 'Success'], 204);
        } else {
            return response()->json(['message' => 'The order cannot be cancelled.'], 401);
        }
    }


    public function UpdateOrder($id, Request $request): \Illuminate\Http\JsonResponse
    {
        $pickup_id = Client::where('id', auth()->user()->id)->first();
        if (! $pickup_id) {
            return response()->json(['message' => 'Location not found'], 404);
        }

        if ($pickup_id->is_active != 1) {
            return response()->json(['message' => 'Unactive user'], 401);
        }
        $order = Order::where('id', $id)->first();
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->value = $request->amount;

        OrderLog::create([
            'order_id' =>  $order->id,
            'status' => $order->status,
            'action' => 'Updated Order',

            'user_id' => $pickup_id?->id,
            'description' => $pickup_id?->full_name . ' Update  order ',
        ]);
        $order->save();

        return response()->json(['message' => 'Success'], 204);
    }

    public function track_order($id): \Illuminate\Http\JsonResponse
    {


        $pickup_id = Client::where('id', auth()->user()->id)->first();
        if (! $pickup_id) {
            return response()->json(['message' => 'Location not found'], 404);
        }

        if ($pickup_id->is_active != 1) {
            return response()->json(['message' => 'Unactive user'], 401);
        }


        $order = Order::select('id as order_id', 'lat', 'lng', 'ingr_branch_id', 'driver_id')
            ->with([
                'branch' => function ($subQuery) {
                    $subQuery->select('id', 'lat', 'lng');
                },
                'OperatorDetail' => function ($subQuery) {
                    $subQuery->select('id', 'lat', 'lng', 'operator_id');
                }
            ])->where('ingr_shop_id', auth()->user()->id)
            ->find($id);

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        return response()->json(

            [
                "order_id" => $order->order_id,
                "shop" => [
                    "lat" => $order->branch->lat,
                    "lng" => $order->branch->lng
                ],
                "customer" => [
                    "lat" => $order->lat,
                    "lng" => $order->lng
                ],
                "driver" => [
                    "lat" => $order->OperatorDetail ? $order->OperatorDetail->lat : "",
                    "lng" => $order->OperatorDetail ? $order->OperatorDetail->lng : ""
                ]
            ],
            200
        );
    }
}
