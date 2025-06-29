<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersExport implements FromCollection, WithHeadings
{
    protected $orders;

    // Accept orders data through the constructor
    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    // The collection() method returns the data to be exported
    public function collection()
    {
        $data = [];

        foreach ($this->orders as $order) {
            $driver = $order->drivers()->orderBy('created_at', 'desc')->first()?->driver?->full_name;
            $nestedData['id'] = $order->id;
            $nestedData['customer_name'] = $order->customer_name;
            $nestedData['customer_phone'] = $order->customer_phone;
            $nestedData['driver'] = $driver;
            $nestedData['shop'] =  $order->shop?->first_name ?? $order->branchIntegration?->client?->first_name;
            $nestedData['branch'] = $order->branch?->name ?? $order->branchIntegration?->name;
            $nestedData['status'] = $order->status->getLabel();
            $nestedData['order_value'] = $order->value;
            $nestedData['fees'] = $order->service_fees;
            $nestedData['total'] = $order->service_fees + $order->value;
            $nestedData['created_at'] = $order->created_at->format('Y-m-d');

            $data[] = $nestedData;
        }

        return collect($data);
    }

    // Headings for the Excel file
    public function headings(): array
    {
        return [
            'ID',
            'Customer name',
            'Customer phone',
            'Driver',
            'Shop',
            'Branch',
            'Status',
            'Order value',
            'Fees',
            'Total value',
            'Created at'
        ];
    }
}
