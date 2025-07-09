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

class ClientsExport implements FromGenerator, WithHeadings, WithMapping, WithChunkReading, ShouldAutoSize, WithStyles
{
    protected $filters;
    protected $clientRepository;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->clientRepository = app(ClientRepository::class);
    }

    /**
     * Use generator for memory efficiency with large datasets
     */
    public function generator(): \Generator
    {
        foreach ($this->clientRepository->getExportData($this->filters) as $client) {
            yield $client;
        }
    }

    /**
     * Define column headings
     */
    public function headings(): array
    {
        return [
            'Client Name',
            'Email',
            'Phone',
            'Account Number',
            'Company Name',
            'Status',
            'Total Invoices',
            'Total Amount (SAR)',
            'Paid Amount (SAR)',
            'Pending Amount (SAR)',
            'Overdue Count',
            'Wallet Balance (SAR)',
            'Last Invoice Date',
            'Created At'
        ];
    }

    /**
     * Map data for each row
     */
    public function map($client): array
    {
        return [
            $client->full_name,
            $client->email,
            $client->phone ?? 'N/A',
            $client->client?->account_number ?? 'N/A',
            $client->client?->company_name ?? 'N/A',
            $client->is_active ? 'Active' : 'Suspended',
            $client->invoices_count ?? 0,
            number_format($client->invoices_sum_total_amount ?? 0, 2),
            number_format($client->paid_amount ?? 0, 2),
            number_format(($client->invoices_sum_total_amount ?? 0) - ($client->paid_amount ?? 0), 2),
            $client->overdue_invoices_count ?? 0,
            number_format($client->wallet?->balance ?? 0, 2),
            $client->invoices->first()?->invoice_date?->format('Y-m-d') ?? 'N/A',
            $client->created_at->format('Y-m-d H:i:s')
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
            'A1:N1' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E3F2FD']
                ]
            ]
        ];
    }
}



