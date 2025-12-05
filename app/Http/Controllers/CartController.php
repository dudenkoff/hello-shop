<?php

namespace App\Http\Controllers;

use App\Service\CartService;

class CartController extends Controller
{
    public function store(CartService $cartService)
    {
        $request = request()->validate([
            'variant' => ['required', 'integer', 'min:1'],
        ]);

        $cartService->addVariantToCart($request['variant']);

        return back()->with('addedToBag', 'Added');
    }
}
