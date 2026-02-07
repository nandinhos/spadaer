@props([
    'variant' => 'primary', // primary, success, warning, danger, info, gray
])

@php
    $variants = [
        'primary' => 'bg-primary/10 text-primary border-primary/20',
        'success' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
        'warning' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
        'danger' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
        'info' => 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border-sky-500/20',
        'gray' => 'bg-gray-500/10 text-gray-600 dark:text-gray-400 border-gray-500/20',
    ];

    $classes = $variants[$variant] ?? $variants['primary'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border ' . $classes]) }}>
    {{ $slot }}
</span>
