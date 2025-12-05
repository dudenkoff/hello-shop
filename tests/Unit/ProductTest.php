<?php

use App\Models\Product;
use App\Models\Variant;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

describe('Product Model', function () {
    test('can create a product', function () {
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'description' => 'Test description',
        ]);

        expect($product->name)->toBe('Test Product')
            ->and($product->slug)->toBe('test-product')
            ->and($product->description)->toBe('Test description')
            ->and($product->id)->toBeInt()
            ->and($product->created_at)->not->toBeNull()
            ->and($product->updated_at)->not->toBeNull();
    });

    test('product can have nullable description', function () {
        $product = Product::factory()->create([
            'name' => 'Product Without Description',
            'slug' => 'product-without-description',
            'description' => null,
        ]);

        expect($product->description)->toBeNull();
    });

    test('product slug must be unique', function () {
        Product::factory()->create(['slug' => 'duplicate-slug']);

        expect(fn() => Product::factory()->create(['slug' => 'duplicate-slug']))
            ->toThrow(Illuminate\Database\QueryException::class);
    });

    test('product has variants relationship', function () {
        $product = Product::factory()->create();
        $variant1 = Variant::factory()->create(['product_id' => $product->id, 'size' => 'S']);
        $variant2 = Variant::factory()->create(['product_id' => $product->id, 'size' => 'M']);

        expect($product->variants)->toHaveCount(2)
            ->and($product->variants->pluck('id')->toArray())->toContain($variant1->id)
            ->and($product->variants->pluck('id')->toArray())->toContain($variant2->id);
    });

    test('product variants relationship returns correct type', function () {
        $product = Product::factory()->create();

        expect($product->variants())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\HasMany::class);
    });

    test('product can have no variants', function () {
        $product = Product::factory()->create();

        expect($product->variants)->toHaveCount(0)
            ->and($product->variants->isEmpty())->toBeTrue();
    });

    test('product can be deleted and variants are cascade deleted', function () {
        $product = Product::factory()->create();
        $variant1 = Variant::factory()->create(['product_id' => $product->id, 'size' => 'S']);
        $variant2 = Variant::factory()->create(['product_id' => $product->id, 'size' => 'M']);

        $variant1Id = $variant1->id;
        $variant2Id = $variant2->id;

        $product->delete();

        expect(Variant::find($variant1Id))->toBeNull()
            ->and(Variant::find($variant2Id))->toBeNull()
            ->and(Product::find($product->id))->toBeNull();
    });

    test('product can be queried by slug', function () {
        $product = Product::factory()->create(['slug' => 'unique-slug-123']);

        $foundProduct = Product::where('slug', 'unique-slug-123')->first();

        expect($foundProduct->id)->toBe($product->id)
            ->and($foundProduct->slug)->toBe('unique-slug-123');
    });

    test('product has fillable attributes', function () {
        $product = new Product();
        $product->fill([
            'name' => 'Fillable Product',
            'slug' => 'fillable-product',
            'description' => 'Fillable description',
        ]);

        expect($product->name)->toBe('Fillable Product')
            ->and($product->slug)->toBe('fillable-product')
            ->and($product->description)->toBe('Fillable description');
    });

    test('product timestamps are automatically managed', function () {
        $product = Product::factory()->create();
        $originalUpdatedAt = $product->updated_at;

        // Wait a moment to ensure timestamp difference
        sleep(1);

        $product->name = 'Updated Name';
        $product->save();

        expect($product->updated_at->gt($originalUpdatedAt))->toBeTrue();
    });

    test('can get product with minimum variant price using withMin', function () {
        $product = Product::factory()->create();
        Variant::factory()->create([
            'product_id' => $product->id,
            'price' => 50.00,
            'size' => 'S',
        ]);
        Variant::factory()->create([
            'product_id' => $product->id,
            'price' => 30.00,
            'size' => 'M',
        ]);
        Variant::factory()->create([
            'product_id' => $product->id,
            'price' => 40.00,
            'size' => 'L',
        ]);

        $productWithMinPrice = Product::withMin('variants', 'price')->find($product->id);

        expect($productWithMinPrice->variants_min_price)->toBe('30.00');
    });

    test('product with no variants has null min price', function () {
        $product = Product::factory()->create();

        $productWithMinPrice = Product::withMin('variants', 'price')->find($product->id);

        expect($productWithMinPrice->variants_min_price)->toBeNull();
    });
});
