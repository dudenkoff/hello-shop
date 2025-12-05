<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    private const PRODUCT_COUNT = 10;

    public function run(): void
    {
        Product::factory()
            ->withAllSizes()
            ->withReviews()
            ->count(self::PRODUCT_COUNT)
            ->create();
    }
}

