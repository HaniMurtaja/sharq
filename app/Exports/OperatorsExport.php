<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
class OperatorsExport implements FromCollection, WithHeadings
{
    protected $operators;

    public function __construct($operators)
    {
        $this->operators = $operators;
    }

    public function collection()
    {
        // Transform the operators into the desired format
        return $this->operators->map(function ($operator) {
            return [
                'id' => $operator->id,
                'full_name' => $operator->full_name,
                'phone' => $operator->phone,
                'created_at' => $operator->created_at->format('Y-m-d H:i:s'), // Format the date if needed
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Phone',
            'Creation Date',
        ];
    }
}
