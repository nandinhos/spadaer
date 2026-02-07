@props([
    'sortable' => false,
    'direction' => null,
    'align' => 'left'
])

@php
    $alignClasses = [
        'left' => 'text-left',
        'center' => 'text-center',
        'right' => 'text-right',
    ][$align] ?? 'text-left';
@endphp

<th {{ $attributes->merge(['class' => "px-6 py-3 $alignClasses text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider"]) }}>
    @if($sortable)
        <button type="button" class="group inline-flex items-center focus:outline-none">
            {{ $slot }}
            <span class="ml-2 flex-none rounded text-gray-400 transition-colors group-hover:text-gray-900 dark:group-hover:text-white">
                @if($direction === 'asc')
                    <i class="fas fa-chevron-up text-[10px]"></i>
                @elseif($direction === 'desc')
                    <i class="fas fa-chevron-down text-[10px]"></i>
                @else
                    <i class="fas fa-sort text-[10px] opacity-0 group-hover:opacity-100"></i>
                @endif
            </span>
        </button>
    @else
        {{ $slot }}
    @endif
</th>
