<?php
namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderLog;
use App\Repositories\FirebaseRepository;
use App\Traits\OrderCreationDateValidation;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OrderDriverTimeout extends Command
{
    use OrderCreationDateValidation;

    /**
     * The name and signature of the console command.
     *
     * @var string
     *
     */

    protected $signature = 'orderDriverTimeout';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'make order status timeout after driver not accept the order ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->firebaseRepository = App::make(FirebaseRepository::class);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (! Cache::has('orderDriverTimeout')) {
            Cache::put('orderDriverTimeout', true, now()->addMinute());

            $getDateTime = $this->getBusinessHoursIfNowWithinRange();

            $MinutesAgo = Carbon::now('Asia/Riyadh')->subMinute();
            $orders     = Order::
                whereBetween('created_at', [
                $getDateTime['start'],
                $getDateTime['end'],
            ])->
                whereIn('status', [
                '23',
            ]);
            $withoutDriver           = clone $orders;
            $withDriver              = clone $orders;
            $withoutDriverCollection = $withoutDriver->where('created_at', '<=', $MinutesAgo)->whereNull('driver_id')->get();
            $withDriverCollection    = $withDriver->where('driver_assigned_at', '<=', $MinutesAgo)->get();
            $order_data              = $withoutDriverCollection->merge($withDriverCollection);
            foreach ($order_data as $order) {

                $order->status = 1;

                try {
                    // Attempt to save to Firebase
                    $this->firebaseRepository->delete_driver_order($order->driver_id, $order->id);
                } catch (\Exception $e) {
                    // Handle the exception (log it, show a message, etc.)
                    Log::info($e);
                }

                //add log
                OrderLog::create([
                    'order_id'    => $order->id,
                    'driver_id'   => $order->driver_id ? $order->driver_id : null,
                    'status'      => 1,
                    'action'      => 'driver not make action on the order',
                    'description' => 'the driver not accept or reject the order',
                ]);

                $order->driver_id = null;
                $order->save();

            }
            Cache::forget('orderDriverTimeout');

        }
    }
}
