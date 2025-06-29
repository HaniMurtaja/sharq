<?php

namespace App\Enum;



enum DeleveryFeed: string  {
    case AREA_TO_AREA = 'area_to_area';
    case PER_AREA ='per_area';
    case CITY_TO_CITY= 'city_to_city';
    case PER_STOP= 'per_stop';
    case PER_KM= 'per_km';
    case FORMULA= 'formula';
    case FLAT_RATE= 'flat_rate';

    public static function values(): array
    {
        return array_column(self::cases(), 'name', 'value');
       
    }
    
    
    public function getLabel(): ?string {
        return match($this) {
            
            self::AREA_TO_AREA => 'Area to Area',
            self::PER_AREA => 'Per Area',
            self::CITY_TO_CITY => 'City to City',
            self::PER_STOP => 'Per Stop',
            self::PER_KM => 'Per km',
            self::FORMULA => 'Formula',
            self::FLAT_RATE => 'Flat Rate',
            

        };
    }

    public function getCalculationMethod(): ?string {
        return match($this) {
            self::AREA_TO_AREA => CalculationMethod::AREA_TO_AREA->value,
            self::PER_AREA => CalculationMethod::PER_AREA->value,
            self::CITY_TO_CITY => CalculationMethod::CITY_TO_CITY->value,
            self::PER_STOP => CalculationMethod::PER_STOP->value,
            self::PER_KM => CalculationMethod::PER_KM->value,
            self::FORMULA => CalculationMethod::FORMULA->value,
            self::FLAT_RATE => CalculationMethod::FLAT_RATE->value,
        };
    }
    

}
