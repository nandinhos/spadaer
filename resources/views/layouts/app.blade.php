<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            <div x-data="{ sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false' }" class="flex h-screen overflow-hidden">
                <!-- Sidebar Component -->
                <x-sidebar />

                <!-- Main Content Area -->
                <div class="flex flex-col flex-grow overflow-hidden">
                    <!-- Page Heading -->
                    @if (isset($header))
                        <header class="bg-white dark:bg-gray-800 shadow">
                            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endif

                    <!-- Page Content -->
                    <main class="flex-grow overflow-y-auto p-4 md:p-6">
                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>
    </body>
</html>
