<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AccountingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
       
        $this->app->bind(ClientRepository::class, function ($app) {
            return new ClientRepository($app->make(\App\Models\User::class));
        });

        $this->app->bind(InvoiceRepository::class, function ($app) {
            return new InvoiceRepository($app->make(\App\Models\ClientInvoice::class));
        });

       
        $this->app->bind(AccountingService::class, function ($app) {
            return new AccountingService(
                $app->make(ClientRepository::class),
                $app->make(InvoiceRepository::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app['router']->aliasMiddleware('accounting.access', \App\Http\Middleware\AccountingAccessMiddleware::class);
        
     
        $this->app->booted(function () {
            $schedule = $this->app->make(\Illuminate\Console\Scheduling\Schedule::class);
            
           
            $schedule->command('accounting:generate-monthly-invoices')
                ->monthlyOn(1, '09:00')
                ->withoutOverlapping()
                ->runInBackground();
            
           
            $schedule->command('accounting:send-overdue-notifications')
                ->dailyAt('10:00')
                ->withoutOverlapping()
                ->runInBackground();
        });
    }
    
}
