<?php

use App\Models\User;
use App\Models\Wallet;

test('it casts wallet balance to an integer', function (): void {
    $wallet = new Wallet(['balance' => '500000']);

    expect($wallet->balance)->toBeInt()->toBe(500000);
});

test('it belongs to a user', function (): void {
    $wallet = new Wallet();

    expect($wallet->user()->getRelated())->toBeInstanceOf(User::class);
});
