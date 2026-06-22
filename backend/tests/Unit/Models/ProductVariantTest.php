<?php

use App\Models\Product;
use App\Models\ProductVariant;

test('it casts SKU attributes, money, and storefront fields to their expected types', function (): void {
    $variant = new ProductVariant([
        'attributes' => ['size' => '30ml'],
        'price' => '320000',
        'sale_price' => '299000',
        'weight' => '180',
        'sort_order' => '2',
        'is_active' => 1,
    ]);

    expect($variant->attributes)->toBe(['size' => '30ml'])
        ->and($variant->price)->toBeInt()->toBe(320000)
        ->and($variant->sale_price)->toBeInt()->toBe(299000)
        ->and($variant->weight)->toBeInt()->toBe(180)
        ->and($variant->sort_order)->toBeInt()->toBe(2)
        ->and($variant->is_active)->toBeTrue();
});

test('it belongs to a product', function (): void {
    $variant = new ProductVariant();

    expect($variant->product()->getRelated())->toBeInstanceOf(Product::class);
});
