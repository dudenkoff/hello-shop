<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ProductFactory extends Factory
{
    private const int NB_DIGITS = 3;

    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'price' => $this->faker->randomNumber(self::NB_DIGITS),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
