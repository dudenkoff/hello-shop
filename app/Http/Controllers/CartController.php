<?php

namespace App\Http\Controllers;

use App\Service\CartService;

class CartController extends Controller
{
    public function index(CartService $cartService)
    {
        return view('cart.index', [
            'count' => $cartService->getCount(),
            'items' => $cartService->getItems(),
            'subtotal' => $cartService->getSubtotal(),
        ]);
    }

    public function store(CartService $cartService)
    {
        $request = request()->validate([
            'variant' => ['required', 'integer', 'min:1'],
        ]);

        $cartService->addVariant($request['variant']);

        return redirect()->route('cart.index')->with('success', 'Product added');
    }

    public function destroy(CartService $cartService, $variantId)
    {
        $cartService->removeVariant($variantId);

        return redirect()->route('cart.index')->with('success', 'Item removed from cart');
    }

    public function update(CartService $cartService, $variantId)
    {
        $request = request()->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cartService->updateQuantity($variantId, $request['quantity']);

        return redirect()->route('cart.index')->with('success', 'Cart updated');
    }
}
