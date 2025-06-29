<?php
namespace App\Enum;

enum LuluMarketOrderStatus: string {
    case ASSIGNED               = 'ASSIGNED';               // The driver was assigned and heads to the store.
    case ARRIVED_TO_STORE       = 'ARRIVED_TO_STORE';       // The driver arrives at the store.
    case GOING_TO_DESTINATION   = 'GOING_TO_DESTINATION';   // The driver has received all the order packages and is now heading towards the destination.
    case ARRIVED_TO_DESTINATION = 'ARRIVED_TO_DESTINATION'; // The driver arrives at the destination.
    case DELIVERED              = 'DELIVERED';              // The order was delivered successfully. Order finalized.
    case CANCELLED              = 'CANCELLED';              // The 3PL does not have the capacity to fulfill this order.
    case DELIVERY_CODE_UPDATED  = 'DELIVERY_CODE_UPDATED';  // When it is necessary to update the delivery code that the client will see from the LSW of Instaleap.
    case CONTACT_INFO_UPDATED   = 'CONTACT_INFO_UPDATED';   // This status does not change the progress of the task, it is a status that can be sent to update the information about the driver (delivery_info).

    public static function values(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ASSIGNED => 'The driver was assigned and heads to the store.',
            self::ARRIVED_TO_STORE => 'The driver arrives at the store.',
            self::GOING_TO_DESTINATION => 'The driver has received all the order packages and is now heading towards the destination.',
            self::ARRIVED_TO_DESTINATION => 'The driver arrives at the destination.',
            self::DELIVERED => 'The order was delivered successfully. Order finalized.',
            self::CANCELLED => 'The 3PL does not have the capacity to fulfill this order.',
            self::DELIVERY_CODE_UPDATED => 'When it is necessary to update the delivery code that the client will see from the LSW of Instaleap.',
            self::CONTACT_INFO_UPDATED => 'This status does not change the progress of the task, it is a status that can be sent to update the information about the driver (delivery_info).',
        };
    }

    public static function GetStatus($status)
    {

        return match ($status) {
            OrderStatus::DRIVER_ACCEPTED => self::ASSIGNED,
            OrderStatus::ARRIVED_PICK_UP => self::ARRIVED_TO_STORE,
            OrderStatus::PICKED_UP => self::GOING_TO_DESTINATION,
            OrderStatus::DELIVERED => self::DELIVERED,
            OrderStatus::CANCELED => self::CANCELLED,
            OrderStatus::ARRIVED_TO_DROPOFF => self::ARRIVED_TO_DESTINATION,

        };

    }
}
