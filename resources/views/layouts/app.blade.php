<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Sistema')</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js']) {{-- Inclui o app.js modificado --}}
    @stack('styles')
</head>

<body
    class="h-full font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 transition-colors duration-200"
    x-data="layout()" {{-- Inicializa o layout Alpine --}}
    {{-- A classe 'dark' será controlada pela função layout() --}}>
    <div class="flex h-full overflow-hidden">
        <x-sidebar /> {{-- Inclui Sidebar --}}
        <div class="flex flex-col flex-grow overflow-hidden">
            <x-header /> {{-- Inclui Header --}}
            <main class="flex-grow overflow-y-auto p-4 md:p-6">
                {{-- Mensagens Flash Globais (opcional) --}}
                <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-4"> {{-- Container para conteúdo principal e mensagens --}}
                    @if (session('success'))
                    <div class="mb-4 px-4 py-3 border rounded relative text-green-800 bg-green-100 border-green-300 dark:bg-green-900 dark:text-green-200 dark:border-green-700" role="alert">
                        {!! session('success') !!}
                    </div>
                    @endif
                    @if (session('warning'))
                    <div class="mb-4 px-4 py-3 border rounded relative text-yellow-800 bg-yellow-100 border-yellow-300 dark:bg-yellow-900 dark:text-yellow-200 dark:border-yellow-700" role="alert">
                        {!! session('warning') !!}
                    </div>
                    @endif
                    {{-- Erro geral pode ser tratado aqui ou na view específica --}}
                    @if (session('error') && !session('import_error_message')) {{-- Só mostra erro geral se não for erro de importação --}}
                    <div class="mb-4 px-4 py-3 border rounded relative text-red-800 bg-red-100 border-red-300 dark:bg-red-900 dark:text-red-200 dark:border-red-700" role="alert">
                        {!! session('error') !!}
                    </div>
                    @endif
                    {{-- Conteúdo da View Específica --}}
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    {{-- Modal Global de Detalhes do Documento --}}
    <x-document-modal />

    {{-- Script Alpine.js é carregado via @vite --}}
    {{-- REMOVIDO SCRIPT INLINE DO ALPINE.STORE --}}

    @stack('scripts') {{-- Para scripts específicos da página --}}
</body>

</html>