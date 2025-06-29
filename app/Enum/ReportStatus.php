<?php

namespace App\Enum;



enum ReportStatus: int  {
    case SUCCESS = 1;
    case PENDING = 2;
    case FAILED = 3;

    public static function values(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }
    public function getLabel(): ?string {
        return match($this) {
            
            self::SUCCESS => 'Success',
            self::PENDING => 'Pending',
            self::FAILED => 'Failed',

        };
    }
    

}
