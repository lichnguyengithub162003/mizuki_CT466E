<?php

use App\Models\Service;

test('it casts service duration, price, visibility, and sort order', function (): void {
    $service = new Service([
        'duration_minutes' => '60',
        'price' => '450000',
        'is_active' => 1,
        'sort_order' => '2',
    ]);

    expect($service->duration_minutes)->toBeInt()->toBe(60)
        ->and($service->price)->toBeInt()->toBe(450000)
        ->and($service->is_active)->toBeTrue()
        ->and($service->sort_order)->toBeInt()->toBe(2);
});
