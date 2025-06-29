<?php
namespace App\Http\Services;

use App\Enum\LuluPaymentMethodEnum;
use App\Enum\OrderStatus;
use App\Http\Requests\Api\LuluMarket\LuluMarketRequest;
use App\Http\Requests\Api\LuluMarket\LuluMarketUpdateRequest;
use App\Models\Client;
use App\Models\ClientBranches;
use App\Models\Order;
use App\Models\OrderLog;
use App\Traits\OrderCreationDateValidation;
use App\Traits\ResponseHandler;

class LuluMarketService
{

    use ResponseHandler;
    use OrderCreationDateValidation;

    public function CreateOrder(LuluMarketRequest $request): \Illuminate\Http\JsonResponse
    {
        $data      = $request->validated();
        $pickup_id = Client::where('id', auth()->user()->id)->first();

        if (! $pickup_id) {
            return response()->json(['message' => 'Client Not Found Please Check Your Token'], 400);
        }

        if ($pickup_id->is_active != 1) {
            return response()->json(['message' => 'Client Not Active'], 400);
        }

        $branches = ClientBranches::where([
            'client_id' => auth()->user()->id,
            'custom_id' => $data['origin']['name'],
        ])->first();

        if (! $this->isWithinBusinessHours(auth()->user()->id)) {
            return response()->json(['error' => 'System Closed'], 400);
        }

        if (! $branches) {
            return response()->json(['error' => 'Branch Not Found'], 400);
        }

        if ($branches->is_active != 1) {
            return response()->json(['message' => 'Branch Not Active'], 400);
        }

        $validated = $request->validated();
        $fees      = $pickup_id->client?->clienGroup?->default_delivery_fee;

        $orderData = [
            'pickup_lat'             => $validated['destination']['latitude'] ?? null,
            'pickup_lng'             => $validated['destination']['longitude'] ?? null,
            'pickup_id'              => $pickup_id->id,
            'client_order_id_string' => $validated['job_number'] ?? null,
            'value'                  => $validated['payment_info']['prices']['order_value'] ?? null,
            'service_fees'           => $fees,
            'lat'                    => $validated['destination']['latitude'] ?? null,
            'lng'                    => $validated['destination']['longitude'] ?? null,
            'address'                => $validated['destination']['address'] ? $validated['destination']['address'] . " " . $validated['destination']['address_two'] . " " . $validated['destination']['description'] : null,
            // 'city' => $validated['pickupLocation']['city'] ?? NULL,
            'customer_phone'         => $validated['recipient']['phone_number'] ?? '',
            'customer_name'          => $validated['recipient']['name'] ?? null,
            'status'                 => 1,
            'ingr_shop_id'           => auth()->user()->id,
            'ingr_branch_id'         => $branches->id,
            'payment_type'           => LuluPaymentMethodEnum::getLabel($validated['payment_info']['method'] ?? null),
            'integration_id'         => @$pickup_id->client->integration_id,
            'additional_details'     => $validated,
            'ingr_shop_name'         => $validated['origin']['name'] ?? null,
            'ingr_branch_name'       => $validated['origin']['name'] ?? null,
            'ingr_branch_lat'        => $validated['origin']['latitude'] ?? null,
            'ingr_branch_lng'        => $validated['origin']['longitude'] ?? null,

        ];

        $order = Order::create($orderData);

        if ($order) {
            return response()->json([
                "service_id" => "$order->id",
                "message"    => "SUCCESS",
            ], 200);
        } else {

            return response()->json(['message' => 'Order not created'], 400);
        }
    }

    public function UpdateOrder(LuluMarketUpdateRequest $request): \Illuminate\Http\JsonResponse
    {
        $data      = $request->validated();
        $pickup_id = Client::where('id', auth()->user()->id)->first();

        if (! $pickup_id) {
            return response()->json(['message' => 'Client Not Found Please Check Your Token'], 400);
        }

        if ($pickup_id->is_active != 1) {
            return response()->json(['message' => 'Client Not Active'], 400);
        }

        $branches = ClientBranches::where([
            'client_id' => auth()->user()->id,
            'custom_id' => $data['origin']['name'],
        ])->first();

        if (! $this->isWithinBusinessHours(auth()->user()->id)) {
            return response()->json(['error' => 'System Closed'], 400);
        }

        if (! $branches) {
            return response()->json(['error' => 'Branch Not Found'], 400);
        }

        if ($branches->is_active != 1) {
            return response()->json(['message' => 'Branch Not Active'], 400);
        }

        $order = Order::where([
            'client_order_id_string' => $data['job_number'],
            'pickup_id'              => $pickup_id->id,
            'ingr_shop_id'           => auth()->user()->id,
        ])
            ->where('status', '!=', OrderStatus::DELIVERED->value)->first();

        if (! $order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        if ($data['event_type'] == 'CANCELLED' || $data['event_type'] == 'TASK_RESET') {
            $order->status = OrderStatus::PENDING_CLIENT_ORDER_CANCELLATION;
            $order->save();

            OrderLog::create([
                'order_id'    => $order->id,
                'status'      => OrderStatus::PENDING_CLIENT_ORDER_CANCELLATION,
                'action'      => 'Request Cancel Order',
                'user_id'     => $pickup_id?->id,
                'description' => $pickup_id?->full_name . ' request to cancel order ',
            ]);

        } else {
            $orderData = [
                'pickup_lat'             => $data['destination']['latitude'] ?? null,
                'pickup_lng'             => $data['destination']['longitude'] ?? null,
                'pickup_id'              => $pickup_id->id,
                'client_order_id_string' => $data['job_number'] ?? null,
                'value'                  => $data['payment_info']['prices']['order_value'] ?? null,
                'lat'                    => $data['destination']['latitude'] ?? null,
                'lng'                    => $data['destination']['longitude'] ?? null,
                'address'                => $data['destination']['address'] ? $data['destination']['address'] . " " . $data['destination']['address_two'] . " " . $data['destination']['description'] : null,
                // 'city' => $data['pickupLocation']['city'] ?? NULL,
                'customer_phone'         => $data['recipient']['phone_number'] ?? '',
                'customer_name'          => $data['recipient']['name'] ?? null,
                'payment_type'         => LuluPaymentMethodEnum::getLabel($data['payment_info']['method'] ?? null),
            ];
            $order->update($orderData);
            OrderLog::create([
                'order_id'    => $order->id,
                'status'      => $order->status,
                'action'      => 'Updated Order',

                'user_id'     => $pickup_id?->id,
                'description' => $pickup_id?->full_name . ' Update  order ',
            ]);
        }

        $order->save();

        return response()->json([
            "service_id" => "$order->id",
            "message"    => "SUCCESS",
        ], 200);
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
                'branch'         => function ($subQuery) {
                    $subQuery->select('id', 'lat', 'lng');
                },
                'OperatorDetail' => function ($subQuery) {
                    $subQuery->select('id', 'lat', 'lng', 'operator_id');
                },
            ])->
            where('ingr_shop_id', auth()->user()->id)
            ->find($id);

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
                "driver"   => [
                    "lat" => $order->OperatorDetail ? $order->OperatorDetail->lat : "",
                    "lng" => $order->OperatorDetail ? $order->OperatorDetail->lng : "",
                ],
            ]
            , 200
        );

    }

}
