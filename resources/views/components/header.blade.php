<header class="bg-primary p-4 shadow-md shrink-0 z-30">
    <div class="flex items-center justify-between max-w-7xl mx-auto w-full">
         {{-- Título --}}
        <div>
            <h1 class="text-lg font-black text-white uppercase tracking-tighter">
                @yield('header-title', 'Gerenciamento de Documentos')
            </h1>
        </div>

        <div class="flex items-center space-x-6">
            {{-- Notificações Dinâmicas --}}
            <livewire:admin.header-notifications />

            {{-- Perfil do Usuário com Dropdown --}}
            <div 
                x-data="{ open: false }" 
                class="relative"
            >
                <button 
                    @click="open = !open"
                    class="flex items-center gap-3 p-1.5 rounded-2xl hover:bg-white/10 transition-all duration-200 group"
                >
                    <div class="relative">
                        <div class="w-10 h-10 rounded-xl bg-white text-primary flex items-center justify-center font-black text-sm shadow-md group-hover:scale-105 transition-transform duration-300">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-primary rounded-lg shadow-sm"></div>
                    </div>
                    
                    <div class="text-left hidden lg:block">
                        <p class="text-sm font-black text-white leading-tight group-hover:text-white/90 transition-colors">
                            {{ Auth::user()->name }}
                        </p>
                        <p class="text-[10px] text-white/70 font-bold uppercase tracking-tighter">
                            {{ Auth::user()->rank ?? Auth::user()->email }}
                        </p>
                    </div>

                    <i class="fas fa-chevron-down text-[10px] text-white/50 group-hover:text-white transition-all ml-1" :class="open ? 'rotate-180' : ''"></i>
                </button>

                {{-- Dropdown de Perfil --}}
                <div 
                    x-show="open" 
                    @click.away="open = false"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    class="absolute right-0 mt-3 w-64 bg-white rounded-2xl shadow-2xl border border-gray-100 z-50 overflow-hidden"
                    x-cloak
                >
                    <div class="p-4 border-b border-gray-50 bg-gray-50/50 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-black">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-xs font-black text-gray-700 truncate">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-gray-400 font-bold truncate">{{ Auth::user()->email }}</p>
                        </div>
                    </div>

                    <div class="p-2">
                        <a href="{{ route('profile.edit') }}" wire:navigate class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-gray-600 hover:bg-primary/5 hover:text-primary transition-all">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 group-hover:text-primary transition-colors">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            Meu Perfil
                        </a>
                        
                        @role('admin')
                        <a href="{{ route('admin.users.index') }}" wire:navigate class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-gray-600 hover:bg-primary/5 hover:text-primary transition-all">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400">
                                <i class="fas fa-users-cog"></i>
                            </div>
                            Administração
                        </a>
                        @endrole

                        <div class="h-px bg-gray-50 my-2 mx-2"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-rose-600 hover:bg-rose-50 transition-all">
                                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                                    <i class="fas fa-power-off"></i>
                                </div>
                                Sair do Sistema
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>