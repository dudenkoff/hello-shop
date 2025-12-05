@props(['cart'])
<a href="{{ route('cart.index') }}"
   class="flex items-center justify-center fixed right-7 top-8 bg-center bg-cover w-10 h-10 cursor-pointer"
   style="background-image: url({{ asset('images/shopping-bag.svg') }});">
    @if($cart->getCount() > 0)
        <span
            class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-600 text-xs font-bold text-white">
            {{ $cart->getCount() }}
        </span>
    @endif
</a>

