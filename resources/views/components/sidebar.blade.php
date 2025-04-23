{{-- Sidebar Component --}}
@auth
<div
    class="flex flex-col bg-gray-100 dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-all duration-300 ease-in-out shrink-0 shadow-lg"
    :class="sidebarOpen ? 'w-64' : 'w-16'"
    @keydown.window.ctrl.b.prevent="toggleSidebar()">
    <!-- Logo -->
    <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 dark:border-gray-700">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 text-decoration-none">
            <i class="fas fa-box text-primary text-xl"></i>
            <h2
                class="font-bold whitespace-nowrap overflow-hidden transition-opacity duration-300 text-gray-800 dark:text-gray-200"
                :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">
                SPADAER GAC-PAC
            </h2>
        </a>
        <button
            @click="toggleSidebar()"
            class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary-light hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200"
            title="Alternar menu (Ctrl+B)">
            <i
                class="fas transition-transform duration-300"
                :class="sidebarOpen ? 'fa-chevron-left' : 'fa-chevron-right'"></i>
        </button>
    </div>

    <!-- Menu Items -->
    <nav class="flex-grow py-4 px-2 overflow-y-auto">
        <ul class="space-y-2">

            <li :class="sidebarOpen ? '' : 'justify-center'">
                <a href="{{ route('documents.index') }}"
                    class="flex items-center space-x-2 p-2 rounded-lg {{ request()->routeIs('documents.*') ? 'bg-primary bg-opacity-10 text-primary dark:text-primary-light font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                    <i class="fas fa-file-alt w-5 text-center"></i>
                    <span class="whitespace-nowrap overflow-hidden transition-opacity duration-300"
                        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">
                        Documentos
                    </span>
                </a>
            </li>
            <li>
                <a href="{{ route('boxes.index') }}" {{-- Link para a nova rota --}}
                    class="flex items-center space-x-2 p-2 rounded-lg {{ request()->routeIs('boxes.*') ? 'bg-primary bg-opacity-10 text-primary dark:text-primary-light font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                    :class="{ 'justify-center': !sidebarOpen }">
                    <i class="fas fa-box w-5 text-center shrink-0"></i>
                    <span class="whitespace-nowrap overflow-hidden transition-opacity duration-300"
                        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">
                        Caixas {{-- Label PT --}}
                    </span>
                </a>
            </li>
            <li :class="sidebarOpen ? '' : 'justify-center'">
                <a href="#"
                    class="flex items-center space-x-2 p-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700">
                    <i class="fas fa-project-diagram w-5 text-center"></i>
                    <span class="whitespace-nowrap overflow-hidden transition-opacity duration-300"
                        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">
                        Projetos
                    </span>
                </a>
            </li>
            <li :class="sidebarOpen ? '' : 'justify-center'">
                <a href="{{ route('commissions.index') }}"
                    class="flex items-center space-x-2 p-2 rounded-lg {{ request()->routeIs('commissions.*') ? 'bg-primary bg-opacity-10 text-primary dark:text-primary-light font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                    <i class="fas fa-users w-5 text-center"></i>
                    <span class="whitespace-nowrap overflow-hidden transition-opacity duration-300"
                        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">
                        Comissão
                    </span>
                </a>
            </li>
            <li :class="sidebarOpen ? '' : 'justify-center'">
                <a href="{{ route('profile.edit') }}"
                    class="flex items-center space-x-2 p-2 rounded-lg {{ request()->routeIs('profile.edit') ? 'bg-primary bg-opacity-10 text-primary dark:text-primary-light font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                    <i class="fas fa-cog w-5 text-center"></i>
                    <span class="whitespace-nowrap overflow-hidden transition-opacity duration-300"
                        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">
                        Configurações
                    </span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- User Profile -->
    <div class="p-2 border-t border-gray-200 dark:border-gray-700 mt-auto">
        <a href="{{ route('profile.edit') }}"
            class="flex items-center space-x-2 p-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700"
            :class="sidebarOpen ? '' : 'justify-center'">
            <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white shrink-0">
                <i class="fas fa-user"></i>
            </div>
            <div class="whitespace-nowrap overflow-hidden transition-opacity duration-300"
                :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">
                <p class="font-medium text-sm">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
            </div>
        </a>
        <form method="POST" action="{{ route('logout') }}" class="mt-2">
            @csrf
            <button type="submit"
                class="w-full flex items-center space-x-2 p-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-red-100 dark:hover:bg-red-800 hover:text-red-600 dark:hover:text-red-300"
                :class="sidebarOpen ? '' : 'justify-center'"
                title="Sair">
                <i class="fas fa-sign-out-alt w-5 text-center"></i>
                <span class="whitespace-nowrap overflow-hidden transition-opacity duration-300"
                    :class="open ? 'opacity-100' : 'opacity-0 w-0'">
                    Sair
                </span>
            </button>
        </form>
    </div>
</div>
@else
<div class="flex flex-col items-center justify-center min-h-screen bg-gray-100 dark:bg-gray-800">
    <div class="text-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4">Acesso Restrito</h1>
        <p class="text-gray-600 dark:text-gray-400 mb-6">Por favor, faça login para acessar o sistema.</p>
        <a href="{{ route('login') }}" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary-dark transition-colors duration-200">
            Entrar
        </a>
    </div>
</div>
@endauth