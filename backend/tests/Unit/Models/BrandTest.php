<?php

use App\Models\Brand;

test('it casts the catalog visibility state to a boolean', function (): void {
    $brand = new Brand(['is_active' => 1]);

    expect($brand->is_active)->toBeTrue();
});

test('it permits its catalog fields to be assigned', function (): void {
    $brand = new Brand();

    expect($brand->isFillable('name'))->toBeTrue()
        ->and($brand->isFillable('slug'))->toBeTrue()
        ->and($brand->isFillable('is_active'))->toBeTrue();
});
