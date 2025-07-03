<?php

namespace App\Console\Commands;

use App\Jobs\SendOverdueNotifications;
use Illuminate\Console\Command;

class SendOverdueNotificationsCommand extends Command
{
    
    protected $signature = 'invoices:send-overdue-notifications';
    protected $description = 'Send notifications for overdue invoices';

   public function handle()
    {
        $this->info('Sending overdue notifications...');

        SendOverdueNotifications::dispatch();

        $this->info('Overdue notifications job dispatched successfully!');
    }

}
