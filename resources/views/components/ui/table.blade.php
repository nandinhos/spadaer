<div class="overflow-x-auto custom-scrollbar">
    <table {{ $attributes->merge(['class' => 'min-w-full divide-y divide-gray-200 dark:divide-gray-700']) }}>
        <thead class="bg-gray-50/50 dark:bg-gray-800/50">
            {{ $head }}
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-transparent">
            {{ $slot }}
        </tbody>
    </table>
</div>
