<?php

namespace App\Enum;

enum VerificationStatuses: int
{
    case
    NOT_VERIFIED = 0;
    case
    WAITING_FOR_VERIFICATION = 1; 
    case
    VERIFIED = 2; 
    

    public static function values(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::NOT_VERIFIED => 'Not Verified',
            self::WAITING_FOR_VERIFICATION => 'Waiting for Verification',
            self::VERIFIED => 'Verified',
           
        };
    }



   

   

}
