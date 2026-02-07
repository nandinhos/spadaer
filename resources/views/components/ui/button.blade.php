@props([
    'variant' => 'primary', // primary, secondary, danger, outline, ghost
    'size' => 'md', // sm, md, lg
    'icon' => null,
])

@php
    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base',
    ];

    $variants = [
        'primary' => 'bg-primary hover:bg-primary-dark text-white shadow-sm shadow-primary/20',
        'secondary' => 'bg-gray-100 hover:bg-gray-200 text-gray-900 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white',
        'success' => 'bg-emerald-500 hover:bg-emerald-600 text-white shadow-sm shadow-emerald-500/20',
        'warning' => 'bg-amber-500 hover:bg-amber-600 text-white shadow-sm shadow-amber-500/20',
        'danger' => 'bg-rose-500 hover:bg-rose-600 text-white shadow-sm shadow-rose-500/20',
        'outline' => 'bg-transparent border-2 border-primary text-primary hover:bg-primary hover:text-white font-bold',
        'ghost' => 'bg-transparent hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400',
        'ghost-primary' => 'bg-transparent hover:bg-primary/10 text-primary hover:text-primary-dark',
        'ghost-warning' => 'bg-transparent hover:bg-amber-50 text-amber-500 hover:text-amber-600 dark:hover:bg-amber-900/20',
        'ghost-danger' => 'bg-transparent hover:bg-rose-50 text-rose-500 hover:text-rose-600 dark:hover:bg-rose-900/20',
    ];

    $baseClasses = 'inline-flex items-center justify-center font-bold rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary disabled:opacity-50 disabled:cursor-not-allowed';
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $variantClass = $variants[$variant] ?? $variants['primary'];
@endphp

<button {{ $attributes->merge(['class' => "$baseClasses $sizeClass $variantClass"]) }}>
    @if($icon)
        <i class="{{ $icon }} {{ $slot->isEmpty() ? '' : 'mr-2' }}"></i>
    @endif
    
    {{ $slot }}
</button>
