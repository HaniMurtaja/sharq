<?php
namespace App\Console\Commands;

use App\Enum\OrderStatus;
use App\Http\Services\AutoDispatcherService;
use App\Models\Order;
use App\Traits\OrderCreationDateValidation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class AssignOrderToDriver extends Command
{
    use OrderCreationDateValidation;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assignOrderToDriver';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assigns an order to a driver';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    public function handle(AutoDispatcherService $autoDispatcherService)
    {
        // if (! Cache::has('assignOrderToDriver')) {
        //     Cache::put('assignOrderToDriver', true, now()->addMinute());

        $getDateTime = $this->getBusinessHoursIfNowWithinRange();

        $orders = Order::
            with('shop', 'branch', 'driver', 'orderLogs')->
            whereBetween('created_at', [
            $getDateTime['start'],
            $getDateTime['end'],
        ])
            ->where('status', OrderStatus::AUTO_DISPATCH->value)
            ->whereNull('driver_id')->cursor();
        foreach ($orders as $order) {

            $autoDispatcherService->autoDispatch($order);
        }

        Cache::forget('assignOrderToDriver');

        // }
    }
}
