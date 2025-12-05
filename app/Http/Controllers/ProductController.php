<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Service\CartService;

class ProductController extends Controller
{
    public function index(CartService $cartService)
    {
        $products = Product::withMin('variants', 'price')->get();

        $cart = $cartService;

        return view('product.index', compact('products', 'cart'));
    }

    public function show(Product $product, CartService $cartService)
    {
        $product->load('variants')->load('reviews');

        $cart = $cartService;

        return view('product.show', compact('product', 'cart'));
    }
}
