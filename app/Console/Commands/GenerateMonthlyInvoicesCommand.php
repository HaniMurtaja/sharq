<?php

namespace App\Console\Commands;

use App\Jobs\GenerateMonthlyInvoices;
use App\Jobs\SendOverdueNotifications;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateMonthlyInvoicesCommand extends Command
{
    
    protected $signature = 'invoices:generate-monthly {--month=} {--client=}';
    protected $description = 'Generate monthly invoices for all clients or specific client';

    public function handle()
    {
        $month = $this->option('month') 
            ? Carbon::createFromFormat('Y-m', $this->option('month'))
            : Carbon::now()->subMonth(); 

        $clientId = $this->option('client');

        $this->info("Generating invoices for {$month->format('F Y')}...");

        GenerateMonthlyInvoices::dispatch($month, $clientId);

        $this->info('Invoice generation job dispatched successfully!');
    }
}
