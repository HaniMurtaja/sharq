<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class OrdersReportsExport implements FromView
{
    public function __construct($items,$columns){
        $this->items = $items;
        $this->columns = $columns;
    }
    public function view(): View
    {
       // dd($this->items);

        return view('admin.pages.exports.reports.OrdersExport', [
            'items' => $this->items,
            'columns' => $this->columns
        ]);
    }
}
