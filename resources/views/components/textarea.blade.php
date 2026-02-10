@props(['disabled' => false])

<textarea {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary dark:focus:border-primary-light focus:ring-primary dark:focus:ring-primary-light rounded-md shadow-sm']) !!}>{!! $slot !!}</textarea>