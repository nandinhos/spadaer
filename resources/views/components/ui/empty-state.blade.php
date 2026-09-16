@props([
    'icon' => 'fa-folder-open',
    'colspan' => 4,
])

<tr>
    <td colspan="{{ $colspan }}" class="px-6 py-12 text-center">
        <div class="flex flex-col items-center">
            <i class="fas {{ $icon }} text-4xl text-gray-200 dark:text-gray-700 mb-4"></i>
            <p class="text-gray-500 dark:text-gray-400 font-medium">{{ $slot }}</p>
        </div>
    </td>
</tr>
