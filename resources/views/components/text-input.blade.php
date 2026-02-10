@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-500 dark:bg-gray-700 dark:text-gray-100 focus:border-primary dark:focus:border-primary-light focus:ring-primary dark:focus:ring-primary-light rounded-md shadow-sm']) }}>
