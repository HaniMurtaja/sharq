<?php

namespace App\Enum;

enum AmericanaOrderStatus: int
{
    case ORDER_CREATION = 1;
    case DRIVER_ALLOCATION = 4;
    case ORDER_SCANNED = 16;
    case ORDER_PICKED = 6;
    case DELIVERED = 9;
    case CANCELLED = 10;

    public static function mainStatus($id): AmericanaOrderStatus
    {
        return match($id) {
            OrderStatus::CREATED->value => self::ORDER_CREATION,
            OrderStatus::DRIVER_ACCEPTED->value => self::DRIVER_ALLOCATION,
            OrderStatus::ARRIVED_PICK_UP->value => self::ORDER_SCANNED,
            OrderStatus::PICKED_UP->value => self::ORDER_PICKED,
            OrderStatus::ARRIVED_TO_DROPOFF->value => OrderStatus::ARRIVED_TO_DROPOFF->value,
            OrderStatus::DELIVERED->value => self::DELIVERED,
            OrderStatus::CANCELED->value => self::CANCELLED,
        };
    }

}
