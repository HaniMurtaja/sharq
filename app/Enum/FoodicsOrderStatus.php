<?php

namespace App\Enum;

enum FoodicsOrderStatus: int
{


    public static function values(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }



    public static function GetStatus($status)
    {

        return match ($status) {
            OrderStatus::DRIVER_ACCEPTED => 3,
            OrderStatus::PICKED_UP => 4,
            OrderStatus::DELIVERED => 5,

        };

    }
}
