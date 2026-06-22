<?php

use App\Models\Category;

test('it casts storefront category attributes to their expected types', function (): void {
    $category = new Category([
        'sort_order' => '10',
        'is_active' => 1,
    ]);

    expect($category->sort_order)->toBeInt()->toBe(10)
        ->and($category->is_active)->toBeTrue();
});

test('it defines parent and child category relationships', function (): void {
    $category = new Category();

    expect($category->parent()->getRelated())->toBeInstanceOf(Category::class)
        ->and($category->children()->getRelated())->toBeInstanceOf(Category::class);
});
