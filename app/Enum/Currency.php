<?php

namespace App\Enum;



enum Currency: string  {
    case SAR = 'SAR';
    
    public static function values(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }
    public function getLabel(): ?string {
        return $this->value;
    }

}
