@props(['disabled' => false, 'currentValue' => '', 'options' => [], 'slot'])

<select {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm']) !!}>
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
    document.addEventListener('DOMContentLoaded', () => {
        const selectElement = document.getElementById('{{ $attributes->get("id") }}');
        if (selectElement) {
            selectElement.value = '{{ $currentValue }}';
             // Disparar evento change se necessário para Alpine
             // selectElement.dispatchEvent(new Event('change'));
        }
    });
</script>
@endif