<?php

use App\Models\Order;
use App\Models\User;
use App\Models\UserAddress;

test('it casts delivery address attributes to their expected types', function (): void {
    $address = new UserAddress([
        'ghn_district_id' => '916',
        'is_default' => 1,
    ]);

    expect($address->ghn_district_id)->toBeInt()->toBe(916)
        ->and($address->is_default)->toBeTrue();
});

test('it defines delivery address relationships', function (): void {
    $address = new UserAddress();

    expect($address->user()->getRelated())->toBeInstanceOf(User::class)
        ->and($address->orders()->getRelated())->toBeInstanceOf(Order::class);
});
