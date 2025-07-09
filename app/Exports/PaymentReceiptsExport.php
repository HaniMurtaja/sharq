<?php

namespace App\Exports;

use App\Repositories\ClientRepository;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class PaymentReceiptsExport implements FromGenerator, WithHeadings, WithMapping, WithChunkReading, ShouldAutoSize, WithStyles
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

   
    public function generator(): \Generator
    {
        $query = \App\Models\PaymentReceipt::with(['invoice.client'])
            ->whereBetween('payment_date', [$this->startDate, $this->endDate])
            ->orderBy('payment_date', 'desc');

        foreach ($query->cursor() as $receipt) {
            yield $receipt;
        }
    }

    /**
     * Define column headings
     */
    public function headings(): array
    {
        return [
            'Receipt Number',
            'Invoice Number',
            'Client Name',
            'Payment Date',
            'Amount Paid (SAR)',
            'Payment Method',
            'Transaction Reference',
            'Status',
            'Notes',
            'Created At'
        ];
    }

    /**
     * Map data for each row
     */
    public function map($receipt): array
    {
        return [
            $receipt->receipt_number ?? 'N/A',
            $receipt->invoice?->invoice_number ?? 'N/A',
            $receipt->invoice?->client?->full_name ?? 'N/A',
            $receipt->payment_date?->format('Y-m-d') ?? 'N/A',
            number_format($receipt->amount_paid ?? 0, 2),
            ucfirst(str_replace('_', ' ', $receipt->payment_method)),
            $receipt->transaction_reference ?? 'N/A',
            ucfirst($receipt->status),
            $receipt->notes ?? 'N/A',
            $receipt->created_at->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Process in chunks for memory efficiency
     */
    public function chunkSize(): int
    {
        return 1000;
    }

    /**
     * Apply styles to the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
            
            // Set background color for header
            'A1:J1' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFF3E0']
                ]
            ]
        ];
    }
}