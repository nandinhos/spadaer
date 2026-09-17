@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border border-gray-200 dark:border-gray-700/80 bg-white dark:bg-gray-800/80 text-gray-900 dark:text-gray-100 focus:border-primary dark:focus:border-primary-light focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary-light/20 rounded-xl shadow-xs transition-all duration-200 placeholder:text-gray-400 dark:placeholder:text-gray-500 disabled:opacity-60 disabled:cursor-not-allowed']) }}>
