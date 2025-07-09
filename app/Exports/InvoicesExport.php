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

class InvoicesExport implements FromGenerator, WithHeadings, WithMapping, WithChunkReading, ShouldAutoSize, WithStyles
{
    protected $filters;
    protected $invoiceRepository;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->invoiceRepository = app(\App\Repositories\InvoiceRepository::class);
    }

    /**
     * Use generator for memory efficiency
     */
    public function generator(): \Generator
    {
        foreach ($this->invoiceRepository->getExportData($this->filters) as $invoice) {
            yield $invoice;
        }
    }

    /**
     * Define column headings
     */
    public function headings(): array
    {
        return [
            'Invoice Number',
            'Client Name',
            'Client Email',
            'Invoice Date',
            'Due Date',
            'Status',
            'Subtotal (SAR)',
            'Tax Amount (SAR)',
            'Total Amount (SAR)',
            'Paid Amount (SAR)',
            'Remaining Amount (SAR)',
            'Currency',
            'Order Count',
            'Service Month',
            'Days Overdue',
            'Created At'
        ];
    }

    /**
     * Map data for each row
     */
    public function map($invoice): array
    {
        $paidAmount = $invoice->paymentReceipts->sum('amount_paid');
        $remainingAmount = $invoice->total_amount - $paidAmount;
        $daysOverdue = $invoice->due_date->isPast() && $invoice->status !== 'paid' 
            ? $invoice->due_date->diffInDays(now()) : 0;

        return [
            $invoice->invoice_number,
            $invoice->client?->full_name ?? 'N/A',
            $invoice->client?->email ?? 'N/A',
            $invoice->invoice_date?->format('Y-m-d') ?? 'N/A',
            $invoice->due_date?->format('Y-m-d') ?? 'N/A',
            ucfirst(str_replace('_', ' ', $invoice->status)),
            number_format($invoice->subtotal ?? 0, 2),
            number_format($invoice->tax_amount ?? 0, 2),
            number_format($invoice->total_amount ?? 0, 2),
            number_format($paidAmount, 2),
            number_format($remainingAmount, 2),
            $invoice->currency ?? 'SAR',
            $invoice->items->sum('quantity') ?? 0,
            $invoice->items->first()?->service_month 
                ? \Carbon\Carbon::parse($invoice->items->first()->service_month)->format('F Y') 
                : 'N/A',
            $daysOverdue > 0 ? $daysOverdue : 'N/A',
            $invoice->created_at->format('Y-m-d H:i:s')
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
            'A1:P1' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E8F5E8']
                ]
            ]
        ];
    }
}