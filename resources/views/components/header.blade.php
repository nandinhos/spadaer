<header class="bg-primary dark:bg-primary-dark text-white p-4 shadow-md shrink-0"> {{-- shrink-0 --}}
    <div class="flex items-center justify-between">
         {{-- Título pode vir do slot ou ser fixo --}}
        <h1 class="text-xl md:text-2xl font-bold">@yield('header-title', 'Gerenciamento de Documentos')</h1>

        <div class="flex items-center space-x-4">
            {{-- Notificações (exemplo estático, precisaria de lógica real) --}}
            <div class="relative">
                <button class="text-white flex items-center space-x-1 hover:text-gray-200">
                    <i class="fas fa-bell"></i>
                    {{-- Badge de notificação (exemplo) --}}
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center animate-pulse">3</span>
                </button>
                {{-- Dropdown de notificações aqui --}}
            </div>

            {{-- Informações do Usuário e Dark Mode Toggle --}}
            <div class="flex items-center space-x-2">
                <div class="text-right hidden md:block">
                    <p class="font-medium text-sm">{{ Auth::user()->name }}</p>
                     {{-- Cargo/Email --}}
                    <p class="text-xs text-gray-200">{{ Auth::user()->email }}</p>
                </div>
                {{-- Dark Mode Toggle Button --}}
                <button @click="toggleDarkMode()" class="p-2 text-white hover:text-gray-200 rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-primary-dark focus:ring-white" aria-label="Alternar modo escuro">
                    <i x-bind:class="darkMode ? 'fas fa-sun' : 'fas fa-moon'"></i>
                </button>
            </div>
        </div>
    </div>
</header>