<!DOCTYPE html>
{{-- Use pt-BR para acessibilidade e SEO --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- Título dinâmico ou padrão --}}
        <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Sistema')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts and Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- Estilos específicos da página (opcional) --}}
        @stack('styles')
    </head>
    {{-- Adicione a classe bg-gray-100 dark:bg-gray-900 aqui ou no body do conteúdo --}}
    <body
        class="h-full font-sans antialiased bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 transition-colors duration-200"
        x-data="layout()" {{-- Alpine data for layout: sidebar, dark mode --}}
    >
        <div class="flex h-full overflow-hidden">
            {{-- Incluir Sidebar Component --}}
            <x-sidebar />

            {{-- Main Content Area --}}
            <div class="flex flex-col flex-grow overflow-hidden">
                {{-- Incluir Header Component --}}
                <x-header />

                <!-- Page Content -->
                <main class="flex-grow overflow-y-auto p-4 md:p-6">
                    {{-- O conteúdo da view específica será injetado aqui --}}
                    {{ $slot }}
                </main>
            </div>
        </div>

         {{-- Incluir o Modal Component aqui, fora do fluxo principal se necessário --}}
         <x-document-modal />

        {{-- Scripts específicos da página (opcional) --}}
        @stack('scripts')
    </body>
</html>