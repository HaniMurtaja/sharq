<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\OrderLog;
use App\Observers\OrderLogObserver;
use App\Observers\OrderObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Keep it simple for now
    }

    public function boot(): void
    {
        OrderLog::observe(OrderLogObserver::class);
        Order::observe(OrderObserver::class);
    }
}
