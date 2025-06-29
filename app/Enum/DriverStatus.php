<?php

namespace App\Enum;



enum DriverStatus: int  {
    case AVAILABLE = 1;
    case BUSY = 2;
    case AWAY = 3;
    case OFFLINE = 4;


    public static function values(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }
    public function getLabel(): ?string {
        return match($this) {

            self::AVAILABLE => 'Available',
            self::BUSY => 'Busy',
            self::AWAY => 'Away',
            self::OFFLINE => 'Offline',


        };
    }

}
