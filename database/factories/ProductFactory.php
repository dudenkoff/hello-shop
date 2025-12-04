<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Variant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    private const SIZES = ['2XS', 'XS', 'S', 'M', 'L', 'XL', '2XL', '3XL'];

    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $name = $this->faker->name(),
            'slug' => Str::slug($name),
            'description' => $this->faker->text(1000),
        ];
    }

    public function withAllSizes(): ProductFactory
    {
        return $this->has(
            Variant::factory()
                ->count(count(self::SIZES))
                ->state(
                    new Sequence(
                        ...array_map(fn($s) => ['size' => $s], self::SIZES)
                    )
                )
        );
    }
}
