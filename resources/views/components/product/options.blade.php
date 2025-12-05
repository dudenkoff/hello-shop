@props(['variant'])
<label aria-label="{{ $variant->size }}"
    {{ $attributes->class(['group relative flex items-center justify-center rounded-md border border-gray-300 bg-white p-3 has-checked:border-red-600 has-checked:bg-red-600 has-focus-visible:outline-2 has-focus-visible:outline-offset-2 has-focus-visible:outline-indigo-600 has-disabled:border-gray-400 has-disabled:bg-gray-200 has-disabled:opacity-25']) }}>
    <input type="radio" name="variant" value="{{ $variant->id }}"
           @if(!$variant->is_active)
               disabled
           @endif
           class="absolute inset-0 appearance-none focus:outline-none disabled:cursor-not-allowed"/>
    <span
        class="text-sm font-medium text-gray-900 uppercase group-has-checked:text-white">{{ $variant->size }}</span>
</label>
