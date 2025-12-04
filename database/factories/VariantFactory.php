<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Variant;
use Illuminate\Database\Eloquent\Factories\Factory;

class VariantFactory extends Factory
{
    private const SKU_PATTERN = 'SKU-????';

    protected $model = Variant::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'sku' => $this->faker->unique()->lexify(self::SKU_PATTERN),
            'price' => $this->faker->numberBetween(100, 200),
        ];
    }
}
