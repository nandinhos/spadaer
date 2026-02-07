@props(['label', 'value', 'subvalue' => null, 'icon', 'color' => 'primary'])

@php
    $colors = [
        'primary' => 'text-primary bg-primary/10 border-primary/20',
        'green'   => 'text-emerald-600 bg-emerald-50 dark:bg-emerald-900/10 border-emerald-100 dark:border-emerald-800',
        'amber'   => 'text-amber-600 bg-amber-50 dark:bg-amber-900/10 border-amber-100 dark:border-amber-800',
        'blue'    => 'text-blue-600 bg-blue-50 dark:bg-blue-900/10 border-blue-100 dark:border-blue-800',
    ];
    $colorClass = $colors[$color] ?? $colors['primary'];
@endphp

<div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-6 transition-all duration-300 hover:shadow-md group">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.15em] mb-1 group-hover:text-gray-500 transition-colors">
                {{ $label }}
            </p>
            <div class="flex items-baseline gap-2">
                <h3 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                    {{ $value }}
                </h3>
                @if($subvalue)
                    <span class="text-xs font-bold text-gray-400 dark:text-gray-500">{{ $subvalue }}</span>
                @endif
            </div>
        </div>
        <div class="w-12 h-12 rounded-xl flex items-center justify-center border transition-transform duration-300 group-hover:scale-110 {{ $colorClass }}">
            <i class="fa-solid {{ $icon }} text-xl"></i>
        </div>
    </div>
</div>
