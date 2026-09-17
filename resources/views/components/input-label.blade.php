@props(['value', 'required' => false])

<label {{ $attributes->merge(['class' => 'block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5']) }}>
    {{ $value ?? $slot }}
    @if($required)
        <span class="text-rose-500 font-bold ml-0.5">*</span>
    @endif
</label>
