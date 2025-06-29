<?php

namespace App\Console\Commands;

use App\Enum\OrderStatus;
use App\Http\Resources\Api\DominosPizzaResource;
use App\Http\Resources\Api\Loginext\LoginextOrderResource;
use App\Http\Resources\Api\OrderResource;
use App\Http\Services\AutoDispatcherService;
use App\Http\Services\NotificationService;
use App\Models\ClientBranches;
use App\Models\Operator;
use App\Models\OperatorDetail;
use App\Models\Order;
use App\Models\OrderDriver;
use App\Models\OrderLog;
use App\Models\WebHook;
use App\Repositories\FirebaseRepository;
use App\Settings\GeneralSettings;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoginextStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'LoginextStatus';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send time out status to Loginext';

    /**
     * Create a new command instance.
     *
     * @return void
     */


    /**
     * Execute the console command.
     *
     * @return string
     */
    public function handle():string
    {
        $threeMinutesAgo = Carbon::now('Asia/Riyadh')->subMinutes(3);

        $orders = Order::
            with(['cityData','shop','branch','DriverData2'])
            ->whereIn('status', [
                OrderStatus::CREATED->value,
                OrderStatus::PENDINE_DRIVER_ACCEPTANCE->value
            ])
            ->where(function ($q) {
                $q->whereDate('created_at', Carbon::yesterday())
                    ->orWhereDate('created_at', Carbon::today());
            })
            ->where('integration_id', 22)
           ->where('created_at', '<=', $threeMinutesAgo)
            ->get();
        $webhook = WebHook::where('integration_company_id', 22)->where('type','order_updated')->first();
     foreach ($orders as $order){
         $order->status = OrderStatus::DRIVER_ACCEPTANCE_TIMEOUT;
         $order->save();
     $data =  new LoginextOrderResource($order->refresh());

         $this->sendOrderToWebhook($webhook->url, $data);
      OrderLog::create([
                 'order_id' => $order->id,
                 'driver_id' => $order->driver_id ? $order->driver_id : null,
                 'status' => 1,
                 'action' => 'driver not make action on the order',
                 'description' => 'the driver not accept or reject the order within 4 min',
             ]);
     }

    return 'success';
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


        if ($httpCode !== 200) {
            Log::error('Webhook delivery failed', [
                'url' => $url,
                'response' => $response,
                'http_code' => $httpCode,
                'sent_data' => $jsonData,
                'from'=>"LoginextStatus"
            ]);
        } else {
            Log::info('Webhook delivered successfully LoginextStatus', [
                'url' => $url,
                'response' => $response,
                'http_code' => $httpCode,
                'sent_data' => $jsonData,
                'from'=>"LoginextStatus"
            ]);
        }
    }


}
