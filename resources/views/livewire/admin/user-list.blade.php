<div>
    <x-ui.card>
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                    Gerenciamento de <span class="text-primary">Usuários</span>
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Administre os colaboradores, seus dados e níveis de acesso.</p>
            </div>
            
            <x-ui.button icon="fas fa-plus" wire:click="openCreateModal">
                Novo Usuário
            </x-ui.button>
        </div>

        <!-- Filtros e Busca -->
        <div class="mb-6">
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400 group-focus-within:text-primary transition-colors"></i>
                </div>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search"
                    placeholder="Buscar por nome, email, posto ou nome completo..." 
                    class="block w-full pl-11 pr-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl focus:ring-2 focus:ring-primary/20 dark:text-white transition-all duration-200"
                >
            </div>
        </div>

        @if (session()->has('success'))
            <div class="mb-4 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-xl text-sm font-bold flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 p-4 bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 rounded-xl text-sm font-bold flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
        @endif

        <!-- Tabela -->
        <x-ui.table>
            <x-slot name="head">
                <tr>
                    <x-ui.th sortable wire:click="sortBy('name')" :direction="$sort_by === 'name' ? $sort_dir : null">
                        Usuário
                    </x-ui.th>
                    <x-ui.th sortable wire:click="sortBy('rank')" :direction="$sort_by === 'rank' ? $sort_dir : null">
                        Posto / Graduação
                    </x-ui.th>
                    <x-ui.th>
                        Papeis (Roles)
                    </x-ui.th>
                    <x-ui.th align="right">
                        Ações
                    </x-ui.th>
                </tr>
            </x-slot>

            @forelse ($users as $user)
                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors group">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="h-10 w-10 flex-shrink-0 rounded-full bg-gradient-to-br from-primary/10 to-primary-light/10 flex items-center justify-center border border-primary/10">
                                <span class="text-primary font-bold text-sm">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $user->full_name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }} ({{ $user->name }})</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <x-ui.badge variant="primary">
                            {{ $user->rank }}
                        </x-ui.badge>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-wrap gap-2">
                            @foreach($user->roles as $role)
                                @php
                                    $roleConfig = match($role->name) {
                                        'admin' => ['variant' => 'danger', 'icon' => 'fa-shield-alt', 'label' => 'Administrador'],
                                        'commission_president' => ['variant' => 'primary', 'icon' => 'fa-star', 'label' => 'Pres. Comissão'],
                                        'commission_member' => ['variant' => 'success', 'icon' => 'fa-user-gear', 'label' => 'Membro'],
                                        default => ['variant' => 'gray', 'icon' => 'fa-user', 'label' => $role->name]
                                    };
                                @endphp
                                <x-ui.badge :variant="$roleConfig['variant']" class="text-[10px] py-1 px-2.5 shadow-sm border border-black/5 dark:border-white/5">
                                    <i class="fas {{ $roleConfig['icon'] }} mr-1.5 opacity-70"></i>
                                    {{ $roleConfig['label'] }}
                                </x-ui.badge>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end gap-2">
                            <x-ui.button variant="ghost-warning" size="sm" icon="fas fa-edit" title="Editar" wire:click="openEditModal({{ $user->id }})" />
                            <x-ui.button
                                variant="ghost-danger"
                                size="sm"
                                icon="fas fa-trash"
                                @click="$store.confirmDelete.open({
                                    title: 'Excluir Usuário',
                                    message: 'Tem certeza que deseja excluir o usuário {{ $user->full_name }}?',
                                    onConfirm: () => { $wire.deleteUser({{ $user->id }}) }
                                })"
                                title="Excluir"
                            />
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-users-slash text-4xl text-gray-200 dark:text-gray-700 mb-4"></i>
                            <p class="text-gray-500 dark:text-gray-400 font-medium">Nenhum usuário encontrado.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-ui.table>

        <div class="mt-6">
            {{ $users->links() }}
        </div>
    </x-ui.card>

    <!-- Modal de Usuário -->
    <div
        x-show="$wire.showUserModal"
        class="fixed inset-0 z-[60] overflow-y-auto"
        x-cloak
    >
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:items-center sm:block sm:p-0">
            <div 
                x-show="$wire.showUserModal" 
                x-transition:enter="ease-out duration-300" 
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" 
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" 
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 transition-opacity bg-gray-900/75 backdrop-blur-sm"
                @click="$wire.showUserModal = false"
            ></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                x-show="$wire.showUserModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block w-full overflow-hidden text-left align-bottom transition-all transform bg-white rounded-xl shadow-2xl dark:bg-gray-800 sm:my-8 sm:align-middle sm:max-w-2xl relative z-10"
            >
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/50">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ $isEditMode ? 'Editar Usuário' : 'Novo Usuário' }}
                    </h3>
                    <button @click="$wire.showUserModal = false" class="text-gray-400 hover:text-gray-500 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="px-6 py-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-1.5">Nome Completo</label>
                            <input type="text" wire:model="full_name" class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary/20 dark:text-white transition-all">
                            @error('full_name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-1.5">Nome de Guerra</label>
                            <input type="text" wire:model="name" class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary/20 dark:text-white transition-all">
                            @error('name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-1.5">Posto / Graduação</label>
                            <input type="text" wire:model="rank" class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary/20 dark:text-white transition-all">
                            @error('rank') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-1.5">Número de Ordem</label>
                            <input type="text" wire:model="order_number" class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary/20 dark:text-white transition-all">
                            @error('order_number') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-1.5">E-mail (Login)</label>
                            <input type="email" wire:model="email" class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary/20 dark:text-white transition-all">
                            @error('email') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-1.5">Senha {{ $isEditMode ? '(deixe em branco para manter)' : '' }}</label>
                            <input type="password" wire:model="password" class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary/20 dark:text-white transition-all">
                            @error('password') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Papeis do Sistema (Roles) -->
                        <div class="col-span-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-3 flex items-center">
                                <i class="fas fa-user-tag mr-2 text-primary opacity-70"></i>
                                Funções do Sistema (Roles)
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($roles as $role)
                                    @php
                                        $label = match($role->name) {
                                            'admin' => 'Administrador',
                                            'commission_president' => 'Presidente de Comissão',
                                            'commission_member' => 'Membro de Comissão',
                                            default => $role->name
                                        };
                                        $variant = match($role->name) {
                                            'admin' => 'danger',
                                            'commission_president' => 'primary',
                                            'commission_member' => 'success',
                                            default => 'gray'
                                        };
                                    @endphp
                                    <label class="relative flex items-center p-3 rounded-xl border border-gray-100 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-900/50 cursor-pointer transition-all group overflow-hidden">
                                        <div class="absolute inset-y-0 left-0 w-1 bg-{{ $variant === 'primary' ? 'primary' : ($variant === 'danger' ? 'rose-500' : ($variant === 'success' ? 'emerald-500' : 'gray-400')) }} opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                        <input type="checkbox" wire:model="selectedRoles" value="{{ $role->name }}" class="w-5 h-5 rounded border-gray-300 text-primary focus:ring-primary/20 transition-all">
                                        <div class="ml-3">
                                            <span class="block text-sm font-bold text-gray-700 dark:text-gray-200 group-hover:text-primary transition-colors">
                                                {{ $label }}
                                            </span>
                                            <span class="text-[10px] text-gray-400 uppercase font-medium">{{ $role->name }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('selectedRoles') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Acesso Granular (Permissões Diretas) -->
                        <div class="col-span-2 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-4">
                                <label class="text-xs font-black uppercase tracking-widest text-gray-400 flex items-center">
                                    <i class="fas fa-fingerprint mr-2 text-emerald-500 opacity-70"></i>
                                    Acesso Granular (Personalizado)
                                </label>
                                <span class="text-[9px] uppercase tracking-wider font-bold text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-full">Opcional</span>
                            </div>

                            <div class="space-y-6">
                                @foreach($permissionsByCategory as $category => $perms)
                                    <div class="space-y-2">
                                        <h4 class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500 flex items-center pl-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-primary/40 mr-2"></span>
                                            Módulo: {{ $category }}
                                        </h4>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                                            @foreach($perms as $permission)
                                                <label class="flex items-center p-2 rounded-lg border border-gray-50 dark:border-gray-800/50 hover:bg-gray-50 dark:hover:bg-gray-900/50 hover:shadow-sm cursor-pointer transition-all group">
                                                    <input type="checkbox" wire:model="selectedPermissions" value="{{ $permission->name }}" class="w-4 h-4 rounded border-gray-300 text-emerald-500 focus:ring-emerald-500/20 transition-all">
                                                    <span class="ml-2.5 text-[11px] font-medium text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-gray-200 transition-colors">
                                                        {{ str_replace($category . '.', '', $permission->name) }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3 bg-gray-50/50 dark:bg-gray-800/50">
                    <x-ui.button variant="ghost-primary" @click="$wire.showUserModal = false">
                        Cancelar
                    </x-ui.button>
                    <x-ui.button wire:click="saveUser">
                        {{ $isEditMode ? 'Salvar Alterações' : 'Criar Usuário' }}
                    </x-ui.button>
                </div>
            </div>
        </div>
    </div>
</div>
