@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-xs text-rose-500 dark:text-rose-400 font-medium space-y-1 mt-1.5']) }}>
        @foreach ((array) $messages as $message)
            <li class="flex items-center gap-1.5">
                <i class="fas fa-circle-exclamation text-[11px] shrink-0"></i>
                <span>{{ $message }}</span>
            </li>
        @endforeach
    </ul>
@endif
