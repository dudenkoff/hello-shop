<?php

namespace App\Service;

use App\Models\Variant;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function addVariant($variant): void
    {
        $cart = $this->getCart();

        $cart[$variant] = ($cart[$variant] ?? 0) + 1;

        Session::put('cart', $cart);
    }

    public function getCart()
    {
        return Session::get('cart', []);
    }

    public function getCount(): int
    {
        return array_sum($this->getCart());
    }

    public function getSubtotal(): float
    {
        $items = $this->getItems();
        $subtotal = 0;

        foreach ($items as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        return $subtotal;
    }

    public function getItems(): array
    {
        $cart = $this->getCart();
        $items = [];

        foreach ($cart as $variantId => $quantity) {
            $variant = Variant::with('product')->find($variantId);

            if ($variant) {
                $items[] = [
                    'variant_id' => $variant->id,
                    'product_id' => $variant->product_id,
                    'product_name' => $variant->product->name,
                    'product_slug' => $variant->product->slug,
                    'size' => $variant->size,
                    'price' => $variant->price,
                    'quantity' => $quantity,
                ];
            }
        }

        return $items;
    }

    public function removeVariant($variantId): void
    {
        $cart = $this->getCart();
        unset($cart[$variantId]);
        Session::put('cart', $cart);
    }

    public function updateQuantity($variantId, $quantity): void
    {
        $cart = $this->getCart();

        if ($quantity <= 0) {
            unset($cart[$variantId]);
        } else {
            $cart[$variantId] = $quantity;
        }

        Session::put('cart', $cart);
    }
}
