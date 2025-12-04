<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();

        return view('product.index', compact('products'));
    }

    public function show(Product $product)
    {
        $variants = $product->variants;

        return view('product.show', compact('product', 'variants'));
    }
}
