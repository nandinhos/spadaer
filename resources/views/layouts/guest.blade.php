<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts Premium -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- CSRF Token Config para Axios -->
        <script>
            window.Laravel = {!! json_encode(['csrfToken' => csrf_token()]) !!};
        </script>
    </head>
    <body class="font-['Outfit'] text-gray-900 min-h-screen overflow-hidden">
        <!-- Background Premium Animado -->
        <div class="fixed inset-0 bg-gradient-to-br from-gray-50 via-white to-blue-50 dark:from-gray-900 dark:via-gray-950 dark:to-blue-950">
            <!-- Gradientes Animados -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-1/2 -left-1/2 w-full h-full bg-gradient-to-br from-primary-light/40 via-blue-200/30 to-sky-200/40 dark:from-primary-dark/30 dark:via-blue-900/20 dark:to-sky-900/20 rounded-full blur-3xl animate-blob"></div>
                <div class="absolute -bottom-1/2 -right-1/2 w-full h-full bg-gradient-to-tl from-blue-200/40 via-primary-light/30 to-cyan-200/40 dark:from-blue-900/30 dark:via-primary-dark/20 dark:to-cyan-900/20 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-3/4 h-3/4 bg-gradient-to-r from-blue-100/20 via-primary-light/20 to-sky-100/20 dark:from-blue-900/20 dark:via-primary-dark/20 dark:to-sky-900/20 rounded-full blur-3xl animate-blob animation-delay-4000"></div>
            </div>
            
            <!-- Grid Pattern Sutil -->
            <div class="absolute inset-0 bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-[size:24px_24px] dark:bg-[linear-gradient(to_right,#ffffff08_1px,transparent_1px),linear-gradient(to_bottom,#ffffff08_1px,transparent_1px)]"></div>
        </div>

        <!-- Conteúdo -->
        <div class="relative z-10 min-h-screen flex flex-col sm:justify-center items-center p-4 sm:p-0">
            {{ $slot }}
        </div>
        
        @livewireScriptConfig
    </body>
</html>
