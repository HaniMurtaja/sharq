<?php

namespace App\Enum;



enum FeedType: string  {
    case KM = 'per_km';
    case DELIVERY ='per_delivery_fees';
    case WORKING_HOURS= 'per_working_hours';
    public static function values(): array
    {
        return array_column(self::cases(), 'name', 'value');
        // ["deposit" => "Deposit", "withdraw" => "Withdraw"]
    }
   

    public function getLabel(): ?string {
        return match($this) {
            
            self::KM => 'Per KM',
            self::DELIVERY => 'Per Delivery Fees',
            self::WORKING_HOURS => 'Per Working Hours',
            

        };
    }

}
