<?php

namespace App\Enum;

enum LyveOrderStatus: int
{
    case
    ASSIGNED = 83;
    case
    PICKED_UP  = 6;
    case
    DONE = 9;
    case
    CANCELLED = 89;
    case NEARBY = 0;
    case AT_STORE = 16;
    case AT_CUSTOMER = 3;


    public static function values(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ASSIGNED => 'Assigned order to driver',
            self::PICKED_UP => 'Order picked up by driver',
            self::DONE => 'Courier approaching the drop-off location',
            self::CANCELLED => 'Order has been cancelled',

            self::NEARBY => 'Driver is almost close to the pick-up location',
            self::AT_STORE  => 'Driver has arrived at the pick-up location',
            self::AT_CUSTOMER   => 'Driver has reached the customer location',

        };
    }


    public static function GetStatus($status)
    {

        return match ($status) {
            OrderStatus::DRIVER_ACCEPTED => "ASSIGNED",
            OrderStatus::PICKED_UP => "PICKED_UP",
            OrderStatus::DELIVERED =>"DONE",
            OrderStatus::CANCELED => "",
            OrderStatus::ARRIVED_TO_DROPOFF => "AT_CUSTOMER",
            OrderStatus::ARRIVED_PICK_UP => "AT_STORE",

        };

    }
}
