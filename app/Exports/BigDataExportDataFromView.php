<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class BigDataExportDataFromView implements FromView
{
    public function __construct($items){
        $this->items = $items;
    }
    public function view(): View {


        return view('admin.pages.exports.reports.BigDataExportDataFromView', [
            'items' => $this->items
        ]);
    }
}
