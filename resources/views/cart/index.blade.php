<x-layout>
    <div class="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
        <div class="flex justify-center mb-15">
            <div
                class="bg-center bg-cover w-20 h-20 relative"
                style="background-image: url({{ asset('images/shopping-bag.svg') }});">
                <span
                    class="absolute -top-1 right-1 flex h-7 w-7 items-center justify-center rounded-full bg-red-600 text-xs font-bold text-white">
                    {{ $count }}
                </span>
            </div>
        </div>
        @if(count($items) > 0)
            <ul role="list" class="mt-8 divide-y divide-gray-200 border-b border-t border-gray-200">
                @foreach($items as $item)
                    <li class="flex py-6">
                        <div class="flex-shrink-0">
                            <img
                                src="https://tailwindcss.com/plus-assets/img/ecommerce-images/category-page-04-image-card-01.jpg"
                                alt="{{ $item['product_name'] }}"
                                class="h-24 w-24 rounded-md object-cover object-center sm:h-32 sm:w-32">
                        </div>

                        <div class="ml-4 flex flex-1 flex-col sm:ml-6">
                            <div>
                                <div class="flex justify-between">
                                    <h3 class="text-sm">
                                        <a href="{{ route('product.show', $item['product_slug']) }}"
                                           class="font-medium text-gray-700 hover:text-gray-800">
                                            {{ $item['product_name'] }}
                                        </a>
                                    </h3>
                                    <p class="ml-4 text-sm font-medium text-gray-900">
                                        ${{ number_format($item['price'] * $item['quantity'], 2) }}
                                    </p>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Size: {{ $item['size'] }}</p>
                            </div>

                            <div class="mt-4 flex flex-1 items-end justify-between">
                                <div class="flex items-center space-x-2">
                                    <label for="quantity-{{ $item['variant_id'] }}" class="text-sm">Qty</label>
                                    <form method="POST"
                                          action="{{ route('cart.update', $item['variant_id']) }}"
                                          class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <select name="quantity"
                                                id="quantity-{{ $item['variant_id'] }}"
                                                onchange="this.form.submit()"
                                                class="rounded-md border border-gray-300 py-2.5 pl-4 text-base leading-6  focus:outline-none sm:text-sm">
                                            @for($i = 1; $i <= 10; $i++)
                                                <option
                                                    value="{{ $i }}" {{ $item['quantity'] == $i ? 'selected' : '' }}>{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </form>
                                </div>

                                <form method="POST"
                                      action="{{ route('cart.destroy', $item['variant_id']) }}"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-sm font-medium text-grey-500 hover:text-red-500 cursor-pointer">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>

            <div class=" pt-10">
                <div class="sm:flex sm:items-center sm:justify-between">
                    <dt class="text-sm font-medium text-gray-900">Subtotal</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:text-base">
                        ${{ number_format($subtotal, 2) }}</dd>
                </div>
                <p class="mt-1 text-sm text-gray-500">Shipping and taxes calculated at checkout.</p>
                <div class="mt-6">
                    <button type="button"
                            class="w-full rounded-md border border-transparent bg-red-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        Checkout
                    </button>
                </div>
                <div class="mt-10 text-center">
                    <a href="{{ route('product.index') }}"
                       class="text-sm font-medium text-gray-600 hover:text-red-500">
                        Continue Shopping
                    </a>
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No items in cart</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by adding some products to your cart.</p>
                <div class="mt-6">
                    <a href="{{ route('product.index') }}"
                       class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-red-600">
                        Continue Shopping
                    </a>
                </div>
            </div>
        @endif
    </div>
</x-layout>
