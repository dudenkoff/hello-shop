@props(['cart'])
<button
    class="flex items-center justify-center fixed right-7 top-8 bg-center bg-cover w-10 h-10"
    style="background-image: url({{ asset('images/shopping-bag.svg') }});">
    <div class="text-black font-bold text-sm mt-3">{{ $cart->getCount() }}</div>
</button>

