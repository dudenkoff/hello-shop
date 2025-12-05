<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::withMin('variants', 'price')->get();

        return view('product.index', compact('products'));
    }

    public function show(Product $product)
    {
        $product->load('variants');

        return view('product.show', compact('product'));
    }
}
