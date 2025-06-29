<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class OperatorAssignReportExport implements FromView
{
    public function __construct($items){
        $this->items = $items;
      
    }
    public function view(): View
    {
       // dd($this->items);

        return view('admin.pages.exports.reports.OperatorAssignReportExport', [
            'items' => $this->items,
        ]);
    }
}
