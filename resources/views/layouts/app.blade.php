<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Sistema')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">

    <!-- Alpine Layout State (Global) -->
    <script>
        window.layout = function() {
            return {
                sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false',

                init() {
                    this.$watch('sidebarOpen', value => {
                        localStorage.setItem('sidebarOpen', value);
                        document.documentElement.classList.toggle('sidebar-collapsed', !value);
                    });
                },

                toggleSidebar() { this.sidebarOpen = !this.sidebarOpen; }
            };
        }
    </script>

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <style>
        /* Regras Críticas para Fluidez */
        [x-cloak] { display: none !important; }
        
        /* Estabilização da Sidebar via CSS puro antes do Alpine */
        .sidebar-width { transition: width 0.3s ease-in-out; }
        html:not(.sidebar-collapsed) .sidebar-width { width: 18rem; /* 72px * 4 */ }
        html.sidebar-collapsed .sidebar-width { width: 5rem; /* 20px * 4 */ }
    </style>
</head>

<body
    class="h-full font-['Outfit'] antialiased bg-gray-50 text-gray-800 transition-colors duration-200"
    x-data="layout()"
>
    <div class="flex h-full overflow-hidden">
        <x-sidebar />
        
        <div class="flex flex-col flex-grow overflow-hidden">
            <x-header />
            
            <main class="flex-grow overflow-y-auto p-4 md:p-8 custom-scrollbar">
                <div class="container mx-auto max-w-7xl">
                    {{-- Mensagens Flash --}}
                    @if (session('success'))
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                             class="mb-6 p-4 rounded-xl border border-emerald-100 bg-emerald-50 text-emerald-800 flex justify-between items-center shadow-sm">
                            <span class="text-sm font-bold"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
                            <button @click="show = false"><i class="fas fa-times"></i></button>
                        </div>
                    @endif

                    {{-- Conteúdo --}}
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    {{-- Modais Globais --}}
    <x-document-modal />
    <x-ui.confirmation-modal />

    @livewireScriptConfig
    @stack('scripts')
</body>

</html>
