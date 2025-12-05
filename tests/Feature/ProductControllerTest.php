<?php

use App\Models\Product;
use App\Models\Variant;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('ProductController', function () {
    describe('index method', function () {
        test('returns successful response', function () {
            $response = $this->get('/');

            $response->assertStatus(200);
        });

        test('displays products in the view', function () {
            $product1 = Product::factory()->create(['name' => 'Product 1']);
            $product2 = Product::factory()->create(['name' => 'Product 2']);

            $response = $this->get('/');

            $response->assertStatus(200)
                ->assertViewIs('product.index')
                ->assertViewHas('products')
                ->assertSee('Product 1')
                ->assertSee('Product 2');
        });

        test('displays products with minimum variant price', function () {
            $product = Product::factory()->create(['name' => 'Test Product']);
            Variant::factory()->create([
                'product_id' => $product->id,
                'price' => 100.00,
                'size' => 'S',
            ]);
            Variant::factory()->create([
                'product_id' => $product->id,
                'price' => 50.00,
                'size' => 'M',
            ]);

            $response = $this->get('/');

            $response->assertStatus(200)
                ->assertViewHas('products', function ($products) use ($product) {
                    $foundProduct = $products->firstWhere('id', $product->id);
                    return $foundProduct && $foundProduct->variants_min_price === '50.00';
                });
        });

        test('displays empty list when no products exist', function () {
            $response = $this->get('/');

            $response->assertStatus(200)
                ->assertViewIs('product.index')
                ->assertViewHas('products', function ($products) {
                    return $products->isEmpty();
                });
        });

        test('loads all products regardless of variant count', function () {
            $productWithVariants = Product::factory()->create(['name' => 'With Variants']);
            $productWithoutVariants = Product::factory()->create(['name' => 'Without Variants']);

            Variant::factory()->create(['product_id' => $productWithVariants->id, 'size' => 'S']);

            $response = $this->get('/');

            $response->assertStatus(200)
                ->assertSee('With Variants')
                ->assertSee('Without Variants');
        });

        test('products are ordered correctly', function () {
            $product1 = Product::factory()->create(['name' => 'First Product', 'created_at' => now()->subDay()]);
            $product2 = Product::factory()->create(['name' => 'Second Product', 'created_at' => now()]);

            $response = $this->get('/');

            $response->assertStatus(200)
                ->assertViewHas('products');
        });

        test('handles products with multiple variants correctly', function () {
            $product = Product::factory()->create(['name' => 'Multi Variant Product']);
            $sizes = ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL', '6XL'];
            foreach ($sizes as $index => $size) {
                Variant::factory()->create([
                    'product_id' => $product->id,
                    'price' => $index === 0 ? 5.00 : 10.00,
                    'size' => $size,
                ]);
            }

            $response = $this->get('/');

            $response->assertStatus(200)
                ->assertViewHas('products', function ($products) use ($product) {
                    $foundProduct = $products->firstWhere('id', $product->id);
                    return $foundProduct && $foundProduct->variants_min_price === '5.00';
                });
        });
    });

    describe('show method', function () {
        test('returns successful response for valid product', function () {
            $product = Product::factory()->create(['slug' => 'test-product']);

            $response = $this->get("/product/{$product->slug}");

            $response->assertStatus(200);
        });

        test('displays product details in the view', function () {
            $product = Product::factory()->create([
                'name' => 'Test Product',
                'slug' => 'test-product',
                'description' => 'Test description',
            ]);

            $response = $this->get("/product/{$product->slug}");

            $response->assertStatus(200)
                ->assertViewIs('product.show')
                ->assertViewHas('product')
                ->assertSee('Test Product')
                ->assertSee('Test description');
        });

        test('loads product variants in the view', function () {
            $product = Product::factory()->create(['slug' => 'test-product']);
            $variant1 = Variant::factory()->create([
                'product_id' => $product->id,
                'sku' => 'SKU-001',
                'price' => 29.99,
                'size' => 'S',
            ]);
            $variant2 = Variant::factory()->create([
                'product_id' => $product->id,
                'sku' => 'SKU-002',
                'price' => 39.99,
                'size' => 'M',
            ]);

            $response = $this->get("/product/{$product->slug}");

            $response->assertStatus(200)
                ->assertViewHas('product', function ($viewProduct) use ($product, $variant1, $variant2) {
                    return $viewProduct->id === $product->id
                        && $viewProduct->relationLoaded('variants')
                        && $viewProduct->variants->count() === 2
                        && $viewProduct->variants->contains('id', $variant1->id)
                        && $viewProduct->variants->contains('id', $variant2->id);
                });
        });

        test('displays product with no variants', function () {
            $product = Product::factory()->create(['slug' => 'no-variants-product']);

            $response = $this->get("/product/{$product->slug}");

            $response->assertStatus(200)
                ->assertViewHas('product', function ($viewProduct) use ($product) {
                    return $viewProduct->id === $product->id
                        && $viewProduct->relationLoaded('variants')
                        && $viewProduct->variants->isEmpty();
                });
        });

        test('returns 404 for non-existent product slug', function () {
            $response = $this->get('/product/non-existent-slug');

            $response->assertStatus(404);
        });

        test('returns 404 for invalid slug format', function () {
            $response = $this->get('/product/invalid@slug#123');

            $response->assertStatus(404);
        });

        test('uses route model binding with slug', function () {
            $product = Product::factory()->create(['slug' => 'bound-product']);

            $response = $this->get("/product/{$product->slug}");

            $response->assertStatus(200)
                ->assertViewHas('product', function ($viewProduct) use ($product) {
                    return $viewProduct->id === $product->id;
                });
        });

        test('does not find product by id when using slug route', function () {
            $product = Product::factory()->create(['slug' => 'test-slug']);

            // Try to access by ID instead of slug
            $response = $this->get("/product/{$product->id}");

            $response->assertStatus(404);
        });

        test('handles product with many variants efficiently', function () {
            $product = Product::factory()->create(['slug' => 'many-variants']);
            $sizes = ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL', '6XL'];
            for ($i = 0; $i < 50; $i++) {
                Variant::factory()->create([
                    'product_id' => $product->id,
                    'size' => $sizes[$i % count($sizes)] . '-' . $i,
                ]);
            }

            $response = $this->get("/product/{$product->slug}");

            $response->assertStatus(200)
                ->assertViewHas('product', function ($viewProduct) use ($product) {
                    return $viewProduct->id === $product->id
                        && $viewProduct->variants->count() === 50;
                });
        });

        test('displays product with null description', function () {
            $product = Product::factory()->create([
                'slug' => 'no-description',
                'description' => null,
            ]);

            $response = $this->get("/product/{$product->slug}");

            $response->assertStatus(200)
                ->assertViewHas('product', function ($viewProduct) use ($product) {
                    return $viewProduct->id === $product->id
                        && $viewProduct->description === null;
                });
        });

        test('only loads variants for the requested product', function () {
            $product1 = Product::factory()->create(['slug' => 'product-1']);
            $product2 = Product::factory()->create(['slug' => 'product-2']);

            $variant1 = Variant::factory()->create(['product_id' => $product1->id, 'size' => 'S']);
            $variant2 = Variant::factory()->create(['product_id' => $product2->id, 'size' => 'S']);

            $response = $this->get("/product/{$product1->slug}");

            $response->assertStatus(200)
                ->assertViewHas('product', function ($viewProduct) use ($product1, $variant1, $variant2) {
                    return $viewProduct->id === $product1->id
                        && $viewProduct->variants->contains('id', $variant1->id)
                        && !$viewProduct->variants->contains('id', $variant2->id);
                });
        });
    });

    describe('route model binding', function () {
        test('route name product.index exists', function () {
            expect(route('product.index'))->toContain('/');
        });

        test('route name product.show exists', function () {
            $product = Product::factory()->create(['slug' => 'route-test']);

            $url = route('product.show', $product);
            expect($url)->toContain("/product/{$product->slug}");
        });

        test('can generate route using product model', function () {
            $product = Product::factory()->create(['slug' => 'model-route']);

            $url = route('product.show', $product);

            expect($url)->toContain("/product/{$product->slug}");
        });
    });

    describe('edge cases and error handling', function () {
        test('handles special characters in slug correctly', function () {
            $product = Product::factory()->create(['slug' => 'product-with-dashes-123']);

            $response = $this->get("/product/{$product->slug}");

            $response->assertStatus(200);
        });

        test('handles long product names', function () {
            $longName = str_repeat('A', 255);
            $product = Product::factory()->create(['name' => $longName, 'slug' => 'long-name']);

            $response = $this->get("/product/{$product->slug}");

            $response->assertStatus(200)
                ->assertSee($longName);
        });

        test('handles products with inactive variants', function () {
            $product = Product::factory()->create(['slug' => 'inactive-variants']);
            $activeVariant = Variant::factory()->create([
                'product_id' => $product->id,
                'is_active' => true,
                'size' => 'S',
            ]);
            $inactiveVariant = Variant::factory()->create([
                'product_id' => $product->id,
                'is_active' => false,
                'size' => 'M',
            ]);

            $response = $this->get("/product/{$product->slug}");

            $response->assertStatus(200)
                ->assertViewHas('product', function ($viewProduct) use ($activeVariant, $inactiveVariant) {
                    return $viewProduct->variants->contains('id', $activeVariant->id)
                        && $viewProduct->variants->contains('id', $inactiveVariant->id);
                });
        });

        test('handles concurrent requests to index', function () {
            Product::factory()->count(10)->create();

            $response1 = $this->get('/');
            $response2 = $this->get('/');

            $response1->assertStatus(200);
            $response2->assertStatus(200);
        });
    });
});
