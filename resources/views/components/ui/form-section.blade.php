@props(['title' => null, 'description' => null, 'icon' => null])

<div {{ $attributes->merge(['class' => 'pt-6 first:pt-0']) }}>
    @if($title)
        <div class="mb-4 pb-3 border-b border-gray-100 dark:border-gray-800 flex items-start justify-between gap-4">
            <div>
                <h4 class="text-sm font-bold uppercase tracking-wider text-gray-800 dark:text-gray-200 flex items-center gap-2">
                    @if($icon)
                        <i class="{{ $icon }} text-primary dark:text-primary-light"></i>
                    @endif
                    <span>{{ $title }}</span>
                </h4>
                @if($description)
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        {{ $description }}
                    </p>
                @endif
            </div>
        </div>
    @endif

    <div>
        {{ $slot }}
    </div>
</div>
