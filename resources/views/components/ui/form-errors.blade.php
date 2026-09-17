@props(['title' => 'Ops! Foram encontrados alguns problemas'])

@if ($errors->any())
    <div {{ $attributes->merge(['class' => 'p-4 mb-6 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-800 dark:text-rose-300 shadow-xs']) }} role="alert">
        <div class="flex items-start gap-3">
            <div class="p-2 rounded-xl bg-rose-500/15 text-rose-600 dark:text-rose-400 shrink-0">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
            <div class="flex-1">
                <strong class="block text-sm font-bold">{{ __($title) }}</strong>
                <ul class="mt-2 text-xs space-y-1 font-medium list-disc list-inside text-rose-700 dark:text-rose-400">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif
