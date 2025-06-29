<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersDataTableExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $data;
    protected $columns;

    public function __construct(array $data, array $columns)
    {
        $this->data = $data;
        $this->columns = $columns;
    }

    // This method will return the collection of data to be exported
    public function collection()
    {
        // Convert the data array to a Laravel Collection
        return collect($this->data);
    }

    // This method will set the headers (column names) in the export file
    public function headings(): array
    {
        return array_values($this->columns);
    }
   

    // public function collection()
    // {
    //     return Order::all();
    // }
}
