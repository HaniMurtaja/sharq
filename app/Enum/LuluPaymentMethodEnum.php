<?php
namespace App\Enum;

enum LuluPaymentMethodEnum: string{
    case CASH             = 'CASH';             // Cash payment
    case PREPAID          = 'PREPAID';          // Payment by credit card
    case PAYMENT_TERMINAL = 'PAYMENT_TERMINAL'; // Payment with card machine
    case LOYALTY_CARD     = 'LOYALTY_CARD';     // Points or loyalty card
    case PAYMENT_LINK     = 'PAYMENT_LINK';     // Payment by payment link
    case TRANSFER         = 'TRANSFER';         // Payment by transfer


    public static function getLabel($id): int
    {
        return match ($id) {
            self::CASH => 1,
            self::PAYMENT_TERMINAL => 3,
            default => 2,
        };
    }
}
