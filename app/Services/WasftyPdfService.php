<?php
namespace App\Services;

use App\Models\Order;
use App\Traits\FileHandler;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf;
class WasftyPdfService
{
    use FileHandler;
    public function generateLabel(Order $order): string
    {
        // Configure PDF
        $pdf = PDF::loadView('pdf.wasfty', [
            'order' => $order,
        ]);

        // Set paper size and orientation
        $pdf->setPaper('a4', 'portrait');

        // Generate unique filename
        $filename = 'storage/labels/order_' . $order->id . '_' . time() . '.pdf';

        // Store PDF
        Storage::disk('public')->put($filename, $pdf->output());
        $this->BunnyStorageUpload($filename);
        return $filename;
    }

    public function generateIntegrationLabel(Order $order): string
    {
        // Configure PDF
        $pdf = LaravelMpdf::loadView('pdf.integration', [
            'order' => $order,
        ]);

        // Generate unique filename
        $filename = 'storage/labels/order_' . $order->id . '_' . time() . '.pdf';

        // Store PDF
        Storage::disk('public')->put($filename, $pdf->output());
        $this->BunnyStorageUpload($filename);
        return $filename;
    }
}
