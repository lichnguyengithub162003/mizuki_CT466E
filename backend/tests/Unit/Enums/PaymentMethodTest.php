<?php

use App\Enums\PaymentMethod;

test('it defines the supported payment methods with stable database values', function (): void {
    expect(PaymentMethod::cases())->toHaveCount(3)
        ->and(PaymentMethod::Wallet->value)->toBe('wallet')
        ->and(PaymentMethod::VNPay->value)->toBe('vnpay')
        ->and(PaymentMethod::Cash->value)->toBe('cash');
});

test('it provides Vietnamese labels for payment methods', function (): void {
    expect(PaymentMethod::Wallet->label())->toBe('Ví Mizuki')
        ->and(PaymentMethod::VNPay->label())->toBe('VNPay')
        ->and(PaymentMethod::Cash->label())->toBe('Tiền mặt');
});
