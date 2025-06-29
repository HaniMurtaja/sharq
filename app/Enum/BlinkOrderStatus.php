<?php

namespace App\Enum;

enum BlinkOrderStatus: int
{


    public static function values(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }



    public static function GetStatus($status)
    {

        return match ($status) {
            OrderStatus::DRIVER_ACCEPTED => 1,
            OrderStatus::ARRIVED_PICK_UP => 2,
            OrderStatus::ARRIVED_TO_DROPOFF => 2,
            OrderStatus::PICKED_UP => 2,
            OrderStatus::DELIVERED => 3,

        };

    }
}
