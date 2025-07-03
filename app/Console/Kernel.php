<?php
namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        //$schedule->command('orderDriverTimeout')->everyTwoMinutes();
        //$schedule->command('changeOrderDriver')->everyMinute();
        // $schedule->command('queue:work --timeout=0 --memory=-1')->everyMinute()->withoutOverlapping();
        // $schedule->command('changeOrderDriver')->everyMinute();
        // $schedule->command('orderDriverTimeout')->everyMinute();
        $schedule->command('orders:export')->dailyAt('05:00');

        $schedule->command('invoices:generate-monthly')
        ->monthlyOn(1, '02:00');

       
        $schedule->command('invoices:send-overdue-notifications')
        ->dailyAt('09:00');

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
