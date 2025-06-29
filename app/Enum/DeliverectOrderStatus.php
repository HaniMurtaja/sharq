<?php

namespace App\Enum;

enum DeliverectOrderStatus: int
{
    case
    EN_ROUTE_TO_PICKUP = 83;
    case
    ARRIVED_AT_PICKUP = 85;
    case
    EN_ROUTE_TO_DROPOFF = 87;
    case
    ARRIVED_AT_DROPOFF = 89;
    case
    DELIVERED = 90;


    public static function values(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }

    public
    function getLabel(): ?string
    {
        return match ($this) {
            self::EN_ROUTE_TO_PICKUP => 'Courier approaching the pickup location',
            self::ARRIVED_AT_PICKUP => 'The courier has arrived at the pickup location',
            self::EN_ROUTE_TO_DROPOFF => 'Courier approaching the drop-off location',
            self::ARRIVED_AT_DROPOFF => 'The courier has arrived at the drop-off location',

            self::DELIVERED => 'Courier has delivered the order	',

        };
    }


    public static function GetStatus($status)
    {

        return match ($status) {
            OrderStatus::DRIVER_ACCEPTED => 83,
            OrderStatus::ARRIVED_PICK_UP => 85,
            OrderStatus::PICKED_UP => 87,
            OrderStatus::ARRIVED_TO_DROPOFF => 89,
            OrderStatus::DELIVERED => 90,

        };

    }
}
