<?php

use App\Models\Product;
use App\Models\Variant;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('product price returns the minimum price from variants', function () {
    $product = Product::factory()->create();

    $prices = [150, 200, 100, 175, 125, 225, 90, 180];
    $sizes = ['2XS', 'XS', 'S', 'M', 'L', 'XL', '2XL', '3XL'];
    $expectedMinPrice = min($prices);

    foreach ($prices as $index => $price) {
        Variant::factory()->create([
            'product_id' => $product->id,
            'price' => $price,
            'size' => $sizes[$index],
        ]);
    }

    $product->refresh();

    expect($product->price)->toBe($expectedMinPrice);
});
