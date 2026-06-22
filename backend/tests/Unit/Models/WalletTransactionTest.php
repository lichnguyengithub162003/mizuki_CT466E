<?php

use App\Models\Order;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;

test('it is immutable and casts wallet ledger amounts to integers', function (): void {
    $transaction = new WalletTransaction([
        'amount' => '100000',
        'balance_after' => '600000',
    ]);

    expect($transaction->usesTimestamps())->toBeFalse()
        ->and($transaction->amount)->toBeInt()->toBe(100000)
        ->and($transaction->balance_after)->toBeInt()->toBe(600000);
});

test('it belongs to a wallet, optional order, and optional creator', function (): void {
    $transaction = new WalletTransaction();

    expect($transaction->wallet()->getRelated())->toBeInstanceOf(Wallet::class)
        ->and($transaction->order()->getRelated())->toBeInstanceOf(Order::class)
        ->and($transaction->createdBy()->getRelated())->toBeInstanceOf(User::class);
});
