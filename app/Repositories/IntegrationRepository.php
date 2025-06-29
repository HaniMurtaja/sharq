<?php
namespace App\Repositories;

use App\Enum\OrderStatus;
use App\Http\Resources\Api\Integration\IntegrationResource;
use App\Http\Resources\Api\Integration\IntegrationResourceForWebHook;
use App\Http\Resources\Api\Integration\TrackOrderResource;
use App\Models\ClientBranches;
use App\Models\ClientDetail;
use App\Models\Order;
use App\Models\OrderLog;
use App\Models\User;
use App\Services\WasftyPdfService;
use App\Traits\HandleResponse;
use App\Traits\LocationTrait;
use App\Traits\OrderCreationDateValidation;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Support\Facades\File;

class IntegrationRepository
{
    use HandleResponse, OrderCreationDateValidation, LocationTrait;

    public function orderCreate($request): \Illuminate\Http\JsonResponse
    {
        try {

            $validated = $request->validate([
                'branch_lat'       => 'required_without:branch_id|numeric',
                'branch_lng'       => 'required_without:branch_id|numeric',
                'branch_id'        => 'required_without:branch_lat,branch_lng',
                'client_order_id'  => 'nullable|max:200',
                'value'            => 'nullable|numeric',
                'payment_type'     => 'required|in:1,2',
                'preparation_time' => 'nullable|integer|min:0',
                'customer_lat'     => 'nullable|required_without:address,city|numeric',
                'customer_lng'     => 'nullable|required_without:address,city|numeric',
                'customer_address' => 'nullable|string',
                'customer_phone'   => [
                    'required',
                ],
                'customer_name'    => 'nullable|string',
                'deliver_at'       => 'nullable|date_format:Y-m-d H:i:s|after_or_equal:now',
                'details'          => 'nullable|string',

            ]);
            $user = User::where('integration_token', @$request->route('API_TOKEN'))->first();

            if (! $this->isWithinBusinessHours($user->id)) {
                return response()->json(['error' => 'System Closed'], 503);
            }

            $branch = null;
            if (isset($validated['branch_id'])) {
                $branch = ClientBranches::where('client_id', $user->id)->where(function ($q) use ($validated) {
                    $q->where('id', $validated['branch_id'])
                        ->orWhere('custom_id', $validated['branch_id']);
                })->first();
            }
            if (isset($validated['branch_lat'], $validated['branch_lng'])) {
                $branch = ClientBranches::where('client_id', $user->id)->where(function ($q) use ($validated) {
                    $q->where('lat', $validated['branch_lat'])->where('lng', $validated['branch_lng']);
                })->first();
            }
            if (! $branch) {
                return response()->json(['error' => 'Branch not found'], 422);
            }

            if ($branch->is_active != 1) {
                return response()->json(['error' => 'Unactive branch'], 422);
            }

            $ClientDetail = ClientDetail::where('user_id', $user->id)->first();

            if (! $ClientDetail) {
                return response()->json(['error' => 'Client details not found'], 422);
            }
            if ($ClientDetail->is_integration == 0 || ! $ClientDetail->integration) {
                return response()->json(['error' => 'Client is not integrated'], 422);
            }

            $order = Order::create([
                'pickup_lat'             => $branch->lat,
                'pickup_lng'             => $branch->lng,
                'pickup_id'              => $branch->id,
                'client_order_id_string' => $validated['client_order_id'] ?? null,
                'value'                  => $validated['value'] ?? 0,
                'payment_type'           => $validated['payment_type'],
                'preparation_time'       => $validated['preparation_time'] ?? 0,
                'lat'                    => $validated['customer_lat'] ?? null,
                'lng'                    => $validated['customer_lng'] ?? null,
                'service_fees'           => $ClientDetail->price_order ?? 0,
                'address'                => $validated['customer_address'] ?? null,
                'customer_phone'         => $validated['customer_phone'],
                'customer_name'          => $validated['customer_name'] ?? null,
                'deliver_at'             => $validated['deliver_at'] ?? null,
                'details'                => $validated['details'] ?? null,
                'status'                 => OrderStatus::CREATED->value,
                'ingr_shop_id'           => $branch?->client_id,
                'ingr_branch_id'         => $branch->id,
                'ingr_shop_name'         => $user->full_name,
                'ingr_branch_name'       => $branch->name,
                'ingr_branch_lat'        => $branch->lat,
                'ingr_branch_lng'        => $branch->lng,
                'ingr_branch_phone'      => $branch->phone,
                'integration_token'      => @$request->route('API_TOKEN'),
                'additional_details'     => $validated,
            ]);
            if ($ClientDetail?->integration->otp_awb == 1) {
                // Generate PDF label
                $labelService = new WasftyPdfService();
                $pdfUrl       = $labelService->generateIntegrationLabel($order);

                // Update order with PDF URL
                $order->update([
                    'invoice_url' => $pdfUrl,
                    'otp'         => mt_rand(1000, 9999),
                ]);

            }

            if ($ClientDetail?->integration) {
                $webhook = $ClientDetail->WebhookData()->where('type', 'order_created')->first();
                if ($webhook && $webhook->url) {
                    $orderData = new IntegrationResourceForWebHook($order);
                    $this->sendOrderToWebhook($webhook, $orderData);
                }
            }

            return response()->json(new IntegrationResource($order), 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
            // return response()->json(['error' => "We're unable to create your order right now. Please check your request and try again."], 422);

        }
    }

    public function getOrder($request): \Illuminate\Http\JsonResponse
    {
        $order = Order::where([
            'integration_token' => @$request->route('API_TOKEN'),
            'id'                => $request->order_id,
        ])->first();
        if ($order) {
            return response()->json(['data' => new IntegrationResource($order)]);
        } else {
            return response()->json(['error' => 'Order not found'], 422);
        }
    }
    public function trackOrder($request): \Illuminate\Http\JsonResponse
    {
        $order = Order::where([
            'integration_token' => @$request->route('API_TOKEN'),
            'id'                => $request->order_id,
        ])->first();
        if ($order) {
            return response()->json(['data' => new TrackOrderResource($order)]);
        } else {
            return response()->json(['error' => 'Order not found'], 422);
        }
    }

    public function orderUpdate($request): \Illuminate\Http\JsonResponse
    {
        // dd($request->all());
        $validated = $request->validate([
            'order_id'         => 'required|integer|exists:orders,id',
            'details'          => 'nullable|string|max:255',
            'value'            => 'nullable|numeric|min:0',
            'payment_type'     => 'nullable|in:1,2', // 1 = CASH, 2 = CREDIT
            'preparation_time' => 'nullable|integer|min:0',
            'customer_lat'     => 'nullable|required_without:address,city|numeric',
            'customer_lng'     => 'nullable|required_without:address,city|numeric',
            'customer_address' => 'nullable|string',
            'customer_phone'   => [
                'nullable',
            ],
            'customer_name'    => 'nullable|string',
        ]);
        $user = User::where('integration_token', @$request->route('API_TOKEN'))->first();

        if (! $this->isWithinBusinessHours($user->id)) {
            return response()->json(['error' => 'System Closed'], 503);
        }

        $ClientDetail = ClientDetail::where('user_id', $user->id)->first();

        $order = Order::where([
            'integration_token' => @$request->route('API_TOKEN'),
            'id'                => $request->order_id,
        ])
            ->whereNotIn('status', [
                OrderStatus::DELIVERED,
                OrderStatus::CANCELED,
                OrderStatus::PENDING_ORDER_CANCELLATION,
                OrderStatus::PENDING_CLIENT_ORDER_CANCELLATION,
            ])
            ->first();
        if (! $order) {
            return response()->json(['error' => 'Order not found or its status doesnt allow updates.'], 422);

        }
        $order->update([
            'value'            => $validated['value'] ?? $order->value,
            'payment_type'     => $validated['payment_type'] ?? $order->payment_type,
            'preparation_time' => $validated['preparation_time'] ?? $order->preparation_time,
            'lat'              => $validated['customer_lat'] ?? $order->lat,
            'lng'              => $validated['customer_lng'] ?? $order->lng,
            'address'          => $validated['customer_address'] ?? $order->address,
            'customer_phone'   => $validated['customer_phone'] ?? $order->customer_phone,
            'customer_name'    => $validated['customer_name'] ?? $order->customer_name,
            'details'          => $validated['details'] ?? $order->details,
        ]);

        $order->save();
        OrderLog::create([
            'order_id'    => $order->id,
            'status'      => $order->status->value,
            'action'      => 'Request Cancel Order',
            'user_id'     => $order->ingr_shop_id,
            'description' => 'The client has updated the order details',
        ]);
        if ($ClientDetail?->integration) {
            $webhook = $ClientDetail->WebhookData()->where('type', 'order_created')->first();
            if ($webhook && $webhook->url) {
                $orderData = [
                    'order_id'        => $order->id,
                    'client_order_id' => $order->order_number,
                    'status_id'       => $order->status->value,
                    'status_label'    => $order->status->name,
                    'driver'          => [],
                ];
                $this->sendOrderToWebhook($webhook, $orderData);
            }
        }

        return response()->json(new IntegrationResource($order), 201);
    }

    public function orderCancel($request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
        ]);
        $order = Order::where([
            'integration_token' => @$request->route('API_TOKEN'),
            'id'                => $request->order_id,
        ])
            ->whereNotIn('status', [
                OrderStatus::DELIVERED,
                OrderStatus::CANCELED,
                OrderStatus::PENDING_ORDER_CANCELLATION,
                OrderStatus::PENDING_CLIENT_ORDER_CANCELLATION,
            ])
            ->first();
        if ($order) {
            $order->status = OrderStatus::PENDING_CLIENT_ORDER_CANCELLATION;
            $order->save();
            OrderLog::create([
                'order_id'    => $order->id,
                'status'      => OrderStatus::PENDING_ORDER_CANCELLATION,
                'action'      => 'Request Cancel Order',

                'user_id'     => $order->ingr_shop_id,
                'description' => 'Client request to cancel order ',
            ]);
            return response()->json(['message' => 'ok , Order Cancelled'], 200);
        } else {
            // Handle the case where the order was not found
            return response()->json(['error' => 'Order not found or has already been cancelled.'], 422);
        }
    }

    private function sendOrderToWebhook($webhook, $data)
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
            'message'   => 'Webhook create',
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
