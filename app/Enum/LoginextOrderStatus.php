<?php

namespace App\Enum;

enum LoginextOrderStatus: int
{
    case
    CREATED = 1;
    case
    PENDINE_DRIVER_ACCEPTANCE = 2;
    case
    PENDING_ORDER_PREPARATION = 4;
    case
    ARRIVED_PICK_UP = 16;
    case
    PICKED_UP = 6;
    case
    ARRIVED_TO_DROPOFF = 8;
    case
    DELIVERED = 9;
    case
    CANCELED = 10;
    case
    DRIVER_ACCEPTANCE_TIMEOUT = 13;
    case
    DRIVER_ACCEPTED = 17;
    case
    DRIVER_REJECTED = 18;
    case
    UNASSIGNED = 19;
    case
    FAILED = 20;
    case
    PENDING_ORDER_CANCELLATION = 21;

    case
    PENDING_CLIENT_ORDER_CANCELLATION = 22;


    public static function values(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }

    public
    function getLabel(): ?string
    {
        return match ($this) {
            self::CREATED => 'Order created',
            self::PENDINE_DRIVER_ACCEPTANCE => 'Pending driver acceptance',
            self::PENDING_ORDER_PREPARATION => 'Pending order preparation',
            self::ARRIVED_PICK_UP => 'Arrived to pickup',
            self::PICKED_UP => 'Order picked up',
            self::ARRIVED_TO_DROPOFF => 'Arrived to dropoff',
            self::DELIVERED => 'Order delivered',
            self::CANCELED => 'Order cancelled',
            self::DRIVER_ACCEPTANCE_TIMEOUT => 'Driver acceptance timeout',
            self::DRIVER_ACCEPTED => 'Driver accepted the order',
            self::DRIVER_REJECTED => 'Driver rejected the order',
            self::UNASSIGNED => 'Order Unassigned',
            self::FAILED => 'Order failed',
            self::PENDING_ORDER_CANCELLATION => 'Order Cancellation is being Proccessed',
            self::PENDING_CLIENT_ORDER_CANCELLATION => 'Client Cancellation Request is being Proccessed',
        };
    }
    public static function getLabelByStatus($status): ?string
    {
        return match ($status) {
            OrderStatus::CREATED => 'Order created',
            OrderStatus::PENDINE_DRIVER_ACCEPTANCE => 'Pending driver acceptance',
            OrderStatus::PENDING_ORDER_PREPARATION => 'Pending order preparation',
            OrderStatus::ARRIVED_PICK_UP => 'Arrived to pickup',
            OrderStatus::PICKED_UP => 'Order picked up',
            OrderStatus::ARRIVED_TO_DROPOFF => 'Arrived to dropoff',
            OrderStatus::DELIVERED => 'Order delivered',
            OrderStatus::CANCELED => 'Order cancelled',
            OrderStatus::DRIVER_ACCEPTANCE_TIMEOUT => 'Driver acceptance timeout',
            OrderStatus::DRIVER_ACCEPTED => 'Driver accepted the order',
            OrderStatus::DRIVER_REJECTED => 'Driver rejected the order',
            OrderStatus::UNASSIGNED => 'Order Unassigned',
            OrderStatus::FAILED => 'Order failed',
            OrderStatus::PENDING_ORDER_CANCELLATION => 'Order Cancellation is being Proccessed',
            OrderStatus::PENDING_CLIENT_ORDER_CANCELLATION => 'Client Cancellation Request is being Proccessed',
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
