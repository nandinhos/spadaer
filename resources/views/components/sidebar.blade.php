{{-- Sidebar Component Premium --}}
@auth
<aside
    class="sidebar-width flex flex-col bg-white border-r border-gray-200 transition-all duration-300 ease-in-out shrink-0 z-40 relative shadow-sm"
    @keydown.window.ctrl.b.prevent="toggleSidebar()"
    x-cloak
>
    <!-- Logo & Toggle -->
    <div 
        class="flex items-center justify-between h-20 px-4 border-b border-gray-100 transition-all duration-300"
        :class="sidebarOpen ? '' : 'flex-col justify-center h-auto py-4 gap-4'"
    >
        <div class="flex items-center overflow-hidden group">
            <div class="flex items-center justify-center min-w-[48px] h-12 rounded-xl bg-primary/10 text-primary shadow-inner transition-all shrink-0">
                <i class="fas fa-box-archive text-xl"></i>
            </div>
            <a href="{{ route('dashboard') }}" wire:navigate 
                class="ml-3 transition-all duration-300 transform hover:translate-x-1"
                x-show="sidebarOpen"
                x-transition:enter="delay-100 duration-300"
                x-transition:enter-start="opacity-0 -translate-x-2"
                x-transition:enter-end="opacity-100 translate-x-0"
            >
                <h2 class="font-black text-lg leading-tight tracking-tighter text-gray-900 uppercase">
                    SPADAER
                </h2>
                <span class="text-[10px] font-bold text-primary tracking-widest uppercase opacity-70">GAC-PAC</span>
            </a>
        </div>
        
        <button
            @click="toggleSidebar()"
            class="p-2 rounded-lg text-gray-400 hover:text-primary hover:bg-primary/5 transition-all duration-200 focus:outline-none shrink-0"
            title="Alternar menu (Ctrl+B)"
        >
            <i class="fas" :class="sidebarOpen ? 'fa-outdent' : 'fa-indent'"></i>
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
                    href="{{ route('admin.users.index') }}" 
                    icon="fa-users" 
                    label="Gerenciar Usuários" 
                    :active="request()->routeIs('admin.users.*')"
                />
                <x-sidebar-item 
                    href="{{ route('admin.roles.index') }}" 
                    icon="fa-shield-halved" 
                    label="Gestão de Papéis" 
                    :active="request()->routeIs('admin.roles.*')"
                />
                <x-sidebar-item 
                    href="{{ route('admin.audit') }}" 
                    icon="fa-clock-rotate-left" 
                    label="Logs de Auditoria" 
                    :active="request()->routeIs('admin.audit')"
                />
            </ul>
        </div>
        @endrole
    </nav>
</aside>
@endauth