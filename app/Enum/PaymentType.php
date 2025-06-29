<?php

namespace App\Enum;

enum PaymentType: int
{


    case
    CASH = 1;
    case
    SPANMACHINE = 2;
    case
    PAID = 3;

    public
    static function values(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }

    public
    function getLabel(): ?string
    {
        return match ($this) {
            self::CASH => 'Cash',
            self::SPANMACHINE => 'Span Machine',
            self::PAID => 'Paid',
        };
    }
}
