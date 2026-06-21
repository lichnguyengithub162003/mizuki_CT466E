<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Wallet = 'wallet';
    case VNPay = 'vnpay';
    case Cash = 'cash';

    public function label(): string
    {
        return match ($this) {
            self::Wallet => 'Ví Mizuki',
            self::VNPay => 'VNPay',
            self::Cash => 'Tiền mặt',
        };
    }
}
