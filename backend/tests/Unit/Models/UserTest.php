<?php

use App\Enums\UserRole;
use App\Models\User;

test('it casts its role to the user role enum', function (): void {
    $user = new User([
        'name' => 'Mizuki Cashier',
        'email' => 'cashier@example.com',
        'password' => 'password',
        'role' => UserRole::Cashier->value,
    ]);

    expect($user->role)->toBe(UserRole::Cashier);
});

test('it permits internal role and branch assignment while hiding sensitive attributes', function (): void {
    $user = new User();

    expect($user->isFillable('role'))->toBeTrue()
        ->and($user->isFillable('branch_id'))->toBeTrue()
        ->and($user->getHidden())->toContain('password', 'remember_token');
});
