<?php
namespace App\Enum;

enum OrderStatus: int {
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

    case
    AUTO_DISPATCH = 23;

    public static function values(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }

    public function getLabel(): ?string
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
            self::AUTO_DISPATCH => 'Pending Auto Dispatch',
        };
    }

    public function mapToDeliverectOrderStatus(): ?DeliverectOrderStatus
    {
        return match ($this) {

            self::DRIVER_ACCEPTED => DeliverectOrderStatus::EN_ROUTE_TO_PICKUP,
            self::PICKED_UP => DeliverectOrderStatus::ARRIVED_AT_PICKUP,
            self::ARRIVED_TO_DROPOFF => DeliverectOrderStatus::ARRIVED_AT_DROPOFF,
            self::DELIVERED => DeliverectOrderStatus::DELIVERED,
        };
    }

    public static function GetStatus(): array
    {
        return [
            'All-orders'                   => 'bg-danger',
            'auto-dispatch'                => 'bg-gray1',
            'pending-order'                => 'bg-gray1',
            'time-out-order'               => 'bg-info',
            'accepted-order'               => 'bg-success',
            'driver-at-pickup-order'       => 'bg_accepted',
            'picked-order'                 => 'bg-danger',
            'driver-at-dropoff-order'      => 'bg-success',
            'completed-order'              => 'bg-success',
            'cancellation-requests'        => 'bg-danger',
            'client-cancellation-requests' => 'bg-gray1',
            'cancelled-order'              => 'cancelled_order',
            'failed-order'                 => 'bg-warning',
            'driver_rejected'              => 'bg-warning',
        ];
    }

    public static function GetStatusLabel($Status): array
    {
        $array = [];
        if (in_array($Status, [self::DRIVER_ACCEPTED, self::PICKED_UP, self::ARRIVED_TO_DROPOFF, self::ARRIVED_PICK_UP, self::CREATED])) {
            $array[] = 'All-orders';
        }

        if ($Status == self::DRIVER_ACCEPTANCE_TIMEOUT) {
            $array[] = 'time-out-order';
        }
        if ($Status == self::DRIVER_ACCEPTED) {
            $array[] = 'accepted-order';
        }
        if ($Status == self::PICKED_UP) {
            $array[] = 'picked-order';
        }
        if ($Status == self::ARRIVED_TO_DROPOFF) {
            $array[] = 'driver-at-dropoff-order';
        }
        if ($Status == self::ARRIVED_PICK_UP) {
            $array[] = 'driver-at-pickup-order';
        }
        if ($Status == self::DELIVERED) {
            $array[] = 'completed-order';
        }
        if ($Status == self::CANCELED) {
            $array[] = 'cancelled-order';
        }
        if ($Status == self::FAILED) {
            $array[] = 'failed-order';
        }
        if ($Status == self::PENDING_ORDER_CANCELLATION) {
            $array[] = 'cancellation-requests';
        }
        if ($Status == self::PENDING_CLIENT_ORDER_CANCELLATION) {
            $array[] = 'client-cancellation-requests';
        }
        if ($Status == self::PENDINE_DRIVER_ACCEPTANCE) {
            $array[] = 'pending-order';
        }

        if ($Status == self::CREATED) {
            $array[] = 'pending-order';
        }

        if ($Status == self::AUTO_DISPATCH) {
            $array[] = 'auto-dispatch';
        }

        if ($Status == self::DRIVER_REJECTED) {
            $array[] = 'driver_rejected';
        }
        return $array;
    }
}
