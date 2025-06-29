<?php
namespace App\Helpers;

use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorSVG;

class BarcodeHelper
{
    public static function generateBarcodeSVG($value): string
    {
        $generator = new BarcodeGeneratorSVG();
        return $generator->getBarcode($value, $generator::TYPE_CODE_128, 2, 30);
    }

    public static function generateBarcodePNG($value): string
    {
        $generator = new BarcodeGeneratorPNG();
        $barcode = base64_encode($generator->getBarcode($value, $generator::TYPE_CODE_128, 2, 30));
        return '<img src="data:image/png;base64,' . $barcode . '" alt="Barcode">';
    }
}
