@props(['disabled' => false, 'currentValue' => '', 'options' => [], 'slot'])

<select {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border border-gray-200 dark:border-gray-700/80 bg-white dark:bg-gray-800/80 text-gray-900 dark:text-gray-100 focus:border-primary dark:focus:border-primary-light focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary-light/20 rounded-xl shadow-xs transition-all duration-200 disabled:opacity-60 disabled:cursor-not-allowed']) !!}>
    @if (!empty($options))
        @foreach ($options as $value => $label)
            <option value="{{ $value }}" {{ $currentValue == $value ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    @else
        {{-- Permite passar options como slot --}}
        {{ $slot }}
    @endif
</select>

{{-- Script para selecionar o valor correto, pois o Blade renderiza antes do Alpine/JS --}}
{{-- Se passar as options via slot, o script abaixo pode ser útil --}}
@if ($currentValue)
<script>
    // Garante que o select reflita o valor passado, especialmente útil com Alpine/slot
    document.addEventListener('livewire:navigated', () => {
        const selectElement = document.getElementById('{{ $attributes->get("id") }}');
        if (selectElement) {
            selectElement.value = '{{ $currentValue }}';
        }
    }, { once: false });
</script>
@endif