<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('variants', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class)->constrained('products')->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->decimal('price');
            $table->string('size');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['product_id', 'size']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variants');
    }
};
