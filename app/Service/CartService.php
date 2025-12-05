<?php

namespace App\Service;

use Illuminate\Support\Facades\Session;

class CartService
{
    public function addVariantToCart($variant): void
    {
        $cart = $this->getCart();

        $cart[$variant] = 1;

        Session::put('cart', $cart);
    }

    public function getCart()
    {
        return Session::get('cart', []);
    }
}
