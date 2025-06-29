<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\OrderLog;
use App\Observers\OrderLogObserver;
use App\Observers\OrderObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
   //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot():void
    {
        OrderLog::observe(OrderLogObserver::class);
        Order::observe(OrderObserver::class);

    }
}
