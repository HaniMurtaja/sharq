<?php

namespace App\Services;

use App\Models\ClientInvoice;
use App\Models\CompanyFinancialSetting;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ZatcaQRCodeService
{

    public function generateQRCode(ClientInvoice $invoice): string
    {
        $settings = CompanyFinancialSetting::getSettings();
  
        $tlvData = $this->buildTLVData($invoice, $settings);
 
        $base64Data = base64_encode($tlvData);
 
        $qrCode = QrCode::format('png')
            ->size(200)
            ->margin(1)
            ->generate($base64Data);

        $fileName = 'qr_codes/invoice_' . $invoice->id . '_' . time() . '.png';
        $path = storage_path('app/public/' . $fileName);
 
        $directory = dirname($path);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        file_put_contents($path, $qrCode);

        $invoice->update([
            'qr_code_path' => $fileName,
            'zatca_qr_data' => [
                'seller_name' => $settings->company_name,
                'vat_number' => $settings->tax_id,
                'timestamp' => $invoice->invoice_date->toISOString(),
                'invoice_total' => $invoice->total_amount,
                'vat_total' => $invoice->tax_amount
            ]
        ]);
        
        return $fileName;
    }

    private function buildTLVData(ClientInvoice $invoice, CompanyFinancialSetting $settings): string
    {
        $tlv = '';
        
       
        $sellerName = $settings->company_name ?? 'Al Shrouq Express';
        $tlv .= $this->addTLVField(1, $sellerName);
        
       
        $vatNumber = $settings->tax_id ?? '';
        $tlv .= $this->addTLVField(2, $vatNumber);
        
       
        $timestamp = $invoice->invoice_date->toISOString();
        $tlv .= $this->addTLVField(3, $timestamp);
        
       
        $invoiceTotal = number_format($invoice->total_amount, 2, '.', '');
        $tlv .= $this->addTLVField(4, $invoiceTotal);
        
        
        $vatTotal = number_format($invoice->tax_amount, 2, '.', '');
        $tlv .= $this->addTLVField(5, $vatTotal);
        
        return $tlv;
    }

   
    private function addTLVField(int $tag, string $value): string
    {
        $length = strlen($value);
        return chr($tag) . chr($length) . $value;
    }

   
    public function validateQRCode(string $qrData): bool
    {
        try {
            $decodedData = base64_decode($qrData);
            
            
            $requiredTags = [1, 2, 3, 4, 5];
            $foundTags = [];
            
            $pos = 0;
            $dataLength = strlen($decodedData);
            
            while ($pos < $dataLength) {
                if ($pos + 2 > $dataLength) break;
                
                $tag = ord($decodedData[$pos]);
                $length = ord($decodedData[$pos + 1]);
                
                if ($pos + 2 + $length > $dataLength) break;
                
                $foundTags[] = $tag;
                $pos += 2 + $length;
            }
            
            return count(array_intersect($requiredTags, $foundTags)) === count($requiredTags);
            
        } catch (\Exception $e) {
            return false;
        }
    }

   
    public function getQRCodeUrl(ClientInvoice $invoice): ?string
    {
        if (!$invoice->qr_code_path) {
            return null;
        }
        
        return asset('storage/' . $invoice->qr_code_path);
    }
}
