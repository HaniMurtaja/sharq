<?php

namespace App\Enum;



enum JopType: int  {
    case FULL_TIME = 1;
    case COMMISSION = 2; 
   


    public static function values(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }
    public function getLabel(): ?string {
        return match($this) {

            self::FULL_TIME => 'Full Time',
            self::COMMISSION => 'Commission',
       
        };
    }

}
