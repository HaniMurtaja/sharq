<?php

namespace App\Enum;



enum CalculationMethod: string  {
    case AREA_TO_AREA = 'admin.pages.calc_methods.area-to-area';
    case PER_AREA = 'admin.pages.calc_methods.per-area';
    case CITY_TO_CITY= 'admin.pages.calc_methods.city-to-city';
    case PER_STOP= 'admin.pages.calc_methods.per-stop';
    case PER_KM= 'admin.pages.calc_methods.per-km';
    case FORMULA= 'admin.pages.calc_methods.formula';
    case FLAT_RATE= 'admin.pages.calc_methods.flat-rate';

    public static function values(): array
    {
        return array_column(self::cases(), 'name', 'value');
       
    }

    public function getLabel(): ?string {
        return $this->value;
    }

}
