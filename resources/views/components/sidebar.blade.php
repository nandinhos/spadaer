{{-- Sidebar Component Premium --}}
@auth
<aside
    class="sidebar-width flex flex-col bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 transition-all duration-300 ease-in-out shrink-0 z-40 relative shadow-sm"
    @keydown.window.ctrl.b.prevent="toggleSidebar()"
    x-cloak
>
    <!-- Logo & Toggle -->
    <div class="flex items-center justify-between h-20 px-4 border-b border-gray-100 dark:border-gray-800">
        <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center overflow-hidden group">
            <div class="flex items-center justify-center min-w-[48px] h-12 rounded-xl bg-primary/10 text-primary shadow-inner group-hover:bg-primary group-hover:text-white transition-all">
                <i class="fas fa-box-archive text-xl"></i>
            </div>
            <div 
                class="ml-3 transition-all duration-300 transform"
                x-show="sidebarOpen"
                x-transition:enter="delay-100 duration-300"
                x-transition:enter-start="opacity-0 -translate-x-2"
                x-transition:enter-end="opacity-100 translate-x-0"
            >
                <h2 class="font-black text-lg leading-tight tracking-tighter text-gray-900 dark:text-white uppercase">
                    SPADAER
                </h2>
                <span class="text-[10px] font-bold text-primary tracking-widest uppercase opacity-70">GAC-PAC</span>
            </div>
        </a>
        
        <button
            @click="toggleSidebar(); document.documentElement.classList.toggle('sidebar-collapsed')"
            class="p-2 rounded-lg text-gray-400 hover:text-primary hover:bg-primary/5 transition-all duration-200 focus:outline-none"
            title="Alternar menu (Ctrl+B)"
        >
            <i class="fas" :class="sidebarOpen ? 'fa-indent' : 'fa-outdent'"></i>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-grow py-6 px-3 space-y-8 overflow-y-auto overflow-x-hidden custom-scrollbar">
        
        {{-- Grupo: Principal --}}
        <div>
            <p x-show="sidebarOpen" x-transition class="px-4 mb-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Principal</p>
            <ul class="space-y-1.5">
                <x-sidebar-item 
                    href="{{ route('documents.index') }}" 
                    icon="fa-file-lines" 
                    label="Documentos" 
                    :active="request()->routeIs('documents.*')"
                />
                <x-sidebar-item 
                    href="{{ route('boxes.index') }}" 
                    icon="fa-boxes-stacked" 
                    label="Acervo / Caixas" 
                    :active="request()->routeIs('boxes.*')"
                />
            </ul>
        </div>

        {{-- Grupo: Gestão --}}
        <div>
            <p x-show="sidebarOpen" x-transition class="px-4 mb-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Gestão</p>
            <ul class="space-y-1.5">
                <x-sidebar-item 
                    href="{{ route('commissions.index') }}" 
                    icon="fa-users-gear" 
                    label="Comissões" 
                    :active="request()->routeIs('commissions.*')"
                />
                @role('admin')
                <x-sidebar-item 
                    href="{{ route('projects.index') }}" 
                    icon="fa-diagram-project" 
                    label="Projetos" 
                    :active="request()->routeIs('projects.*')"
                />
                @endrole
            </ul>
        </div>

        {{-- Grupo: Sistema (Admin Only) --}}
        @role('admin')
        <div>
            <p x-show="sidebarOpen" x-transition class="px-4 mb-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Configurações</p>
            <ul class="space-y-1.5">
                <x-sidebar-item 
                    href="{{ route('admin.permissions') }}" 
                    icon="fa-shield-halved" 
                    label="Permissões & Segurança" 
                    :active="request()->routeIs('admin.*')"
                />
            </ul>
        </div>
        @endrole
    </nav>

    <!-- Footer: User Profile -->
    <div class="p-4 border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/50">
        <div 
            class="flex items-center p-2 rounded-xl transition-all duration-200"
            :class="sidebarOpen ? 'bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700' : 'justify-center'"
        >
            <div class="relative shrink-0">
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-primary to-primary-light flex items-center justify-center text-white font-bold shadow-md">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full"></div>
            </div>
            
            <div 
                class="ml-3 overflow-hidden transition-all duration-300"
                x-show="sidebarOpen"
                x-transition
            >
                <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ Auth::user()->name }}</p>
                <p class="text-[10px] text-gray-500 dark:text-gray-400 truncate uppercase tracking-tighter">{{ Auth::user()->rank }}</p>
            </div>
        </div>

        <div class="mt-4 flex items-center gap-2" :class="sidebarOpen ? 'px-2' : 'flex-col items-center'">
            <a href="{{ route('profile.edit') }}" class="p-2 text-gray-400 hover:text-primary transition-colors" title="Perfil">
                <i class="fas fa-cog"></i>
            </a>
            <button @click="toggleDarkMode()" class="p-2 text-gray-400 hover:text-yellow-500 transition-colors" title="Alternar Tema">
                <i class="fas" :class="darkMode ? 'fa-sun' : 'fa-moon'"></i>
            </button>
            <div class="flex-grow"></div>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="p-2 text-gray-400 hover:text-red-500 transition-colors" title="Sair">
                    <i class="fas fa-power-off"></i>
                </button>
            </form>
        </div>
    </div>
</aside>
@endauth