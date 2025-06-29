<?php

namespace App\Jobs;

use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ExportCustomerInvoicesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filters;
    protected $exportPath;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($filters, $exportPath)
    {
        $this->filters = $filters;
        $this->exportPath = $exportPath;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
           $query = \App\Models\ExportedOrders::orderByDesc('order_created_at');

    // طبق نفس دالة search المستخدمة عندك
    app('\App\Http\Controllers\Admin\ExportController')->search($query, $this->filters);

    (new FastExcel($query->cursor()->getIterator()))
        ->export($this->exportPath);
    }
}
