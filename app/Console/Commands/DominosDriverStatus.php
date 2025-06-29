<?php

namespace App\Console\Commands;

use App\Http\Resources\Api\DominosPizzaResource;
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

class DominosDriverStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DominosDriverStatus';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send lat and long to dominos driver';

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
        $orders = Order::with( ['branch','OperatorDetail'])->whereIn('status', [
            17,4,16,6,8
        ])->
        where(function ($q){
            $q->whereDate('created_at', Carbon::yesterday())
                ->orWhereDate('created_at', Carbon::today());
        })
         ->where('integration_id',10)
        ->get();
        $webhook = WebHook::where('integration_company_id', 10)->first();
    foreach ($orders as $order){
    $data = New DominosPizzaResource($order);
      $this->sendOrderToWebhook($webhook->url, $data);

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
                'http_code' => $httpCode
            ]);
        } else {
            Log::info('Webhook delivered successfully', [
                'url' => $url,
                'response' => $response,
                'http_code' => $httpCode,
                'sent_data' => $jsonData
            ]);
        }
    }


}
