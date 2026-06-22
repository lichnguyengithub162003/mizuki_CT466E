<?php

use App\Models\Branch;
use App\Models\User;

test('it casts branch attributes to their expected types', function (): void {
    $branch = new Branch([
        'code' => 'CT-01',
        'name' => 'Mizuki Cần Thơ',
        'ghn_district_id' => '916',
        'is_active' => 1,
    ]);

    expect($branch->ghn_district_id)->toBeInt()->toBe(916)
        ->and($branch->is_active)->toBeTrue();
});

test('it has many users', function (): void {
    $branch = new Branch();

    expect($branch->users()->getRelated())->toBeInstanceOf(User::class);
});
