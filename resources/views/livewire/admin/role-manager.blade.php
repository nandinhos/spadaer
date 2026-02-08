<div>
    <x-ui.card>
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-8">
            <div>
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight flex items-center">
                    Gestão de <span class="text-primary ml-2 italic text-2xl">Papéis e Permissões</span>
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Defina as competências de cada Função (Role) do sistema para automatizar o controle de acesso.</p>
            </div>
            
            <x-ui.button icon="fas fa-plus" wire:click="openCreateModal">
                Novo Papel
            </x-ui.button>
        </div>

        @if (session()->has('success'))
            <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-xl text-sm font-bold flex items-center shadow-sm">
                <i class="fas fa-check-circle mr-3 text-lg opacity-80"></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 rounded-xl text-sm font-bold flex items-center shadow-sm">
                <i class="fas fa-exclamation-triangle mr-3 text-lg opacity-80"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($roles as $role)
                <div class="bg-white dark:bg-gray-800/50 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-6 flex flex-col h-full group hover:shadow-xl hover:shadow-primary/5 hover:border-primary/20 transition-all duration-300 relative overflow-hidden">
                    <!-- Decorador -->
                    <div class="absolute top-0 right-0 w-24 h-24 bg-primary/5 -mr-12 -mt-12 rounded-full group-hover:bg-primary/20 transition-all"></div>
                    
                    <div class="flex items-center justify-between mb-5 relative">
                        <div class="p-3 rounded-xl bg-primary/10 text-primary group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="flex gap-1">
                            <a href="{{ route('admin.roles.edit', $role->id) }}" wire:navigate class="p-2 text-gray-400 hover:text-amber-500 transition-colors" title="Editar Permissões">
                                <i class="fas fa-pen text-xs"></i>
                            </a>
                            @if(!in_array($role->name, ['admin', 'user']))
                                <button 
                                    @click="$store.confirmDelete.open({
                                        title: 'Excluir Papel',
                                        message: 'Tem certeza que deseja excluir o papel {{ $role->name }}?',
                                        onConfirm: () => { $wire.deleteRole({{ $role->id }}) }
                                    })"
                                    class="p-2 text-gray-400 hover:text-rose-500 transition-colors" 
                                    title="Excluir Papel"
                                >
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    <h4 class="text-lg font-black text-gray-900 dark:text-white mb-2 uppercase tracking-tight flex items-center">
                        {{ match($role->name) {
                            'admin' => 'Administrador',
                            'commission_president' => 'Presidente de Comissão',
                            'commission_member' => 'Membro de Comissão',
                            default => $role->name
                        } }}
                    </h4>
                    
                    <div class="flex-grow space-y-2 mt-4 max-h-56 overflow-y-auto pr-2 mb-6 custom-scrollbar">
                        @forelse($role->permissions->take(6) as $permission)
                            <div class="flex items-center px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-900/50 border border-transparent hover:border-gray-100 dark:hover:border-gray-700/50 transition-all">
                                <div class="w-1.5 h-1.5 rounded-full bg-{{ explode('.', $permission->name)[0] === 'documents' ? 'blue' : (explode('.', $permission->name)[0] === 'commissions' ? 'emerald' : 'amber') }}-500 mr-3"></div>
                                <span class="text-[11px] font-bold text-gray-600 dark:text-gray-400">{{ $permission->name }}</span>
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center py-8 text-gray-400">
                                <i class="fas fa-key text-xl opacity-20 mb-2"></i>
                                <span class="text-[10px] font-bold uppercase tracking-widest opacity-50">Sem permissões</span>
                            </div>
                        @endforelse
                        @if($role->permissions->count() > 6)
                            <div class="text-center pt-2">
                                <a href="{{ route('admin.roles.edit', $role->id) }}" wire:navigate class="text-[10px] font-black uppercase text-primary hover:underline">
                                    + {{ $role->permissions->count() - 6 }} permissões...
                                </a>
                            </div>
                        @endif
                    </div>
                    
                    <div class="pt-4 border-t border-gray-100 dark:border-gray-700 mt-auto flex items-center justify-between">
                        <div class="text-[10px] text-gray-400 uppercase font-black tracking-widest">Ativas</div>
                        <div class="h-6 px-3 flex items-center justify-center rounded-full bg-primary/10 text-primary text-[10px] font-black group-hover:bg-primary group-hover:text-white transition-all">
                            {{ $role->permissions->count() }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </x-ui.card>

    <!-- Modal de Criação (Simples) -->
    <div
        x-show="$wire.showCreateModal"
        class="fixed inset-0 z-[60] overflow-y-auto"
        x-cloak
    >
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:items-center sm:block sm:p-0">
            <div 
                x-show="$wire.showCreateModal" 
                x-transition:enter="ease-out duration-300" 
                class="fixed inset-0 transition-opacity bg-gray-900/75 backdrop-blur-sm"
                @click="$wire.showCreateModal = false"
            ></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                x-show="$wire.showCreateModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                class="inline-block w-full overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-2xl dark:bg-gray-800 sm:my-8 sm:align-middle sm:max-w-md relative z-10"
            >
                <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/50">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tighter">Novo Papel</h3>
                    <button @click="$wire.showCreateModal = false" class="text-gray-400 hover:text-gray-500 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="px-8 py-8 space-y-4">
                    <div class="space-y-2">
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-400">Nome do Papel</label>
                        <input type="text" wire:model="roleName" class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary/20 dark:text-white font-bold px-4 py-3" placeholder="ex: auditor">
                        @error('roleName') <span class="text-xs text-rose-500 font-bold mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="px-8 py-6 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3 bg-gray-50/50 dark:bg-gray-800/50">
                    <button @click="$wire.showCreateModal = false" class="px-6 py-2.5 text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors">
                        Cancelar
                    </button>
                    <x-ui.button wire:click="saveNewRole">
                        Criar e Configurar
                    </x-ui.button>
                </div>
            </div>
        </div>
    </div>
</div>
