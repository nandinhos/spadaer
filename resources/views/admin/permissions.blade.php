@role('admin')
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl bg-clip-text text-transparent bg-gradient-to-r from-primary to-primary-light dark:from-primary-light dark:to-white leading-tight">
                {{ __('Permissões e Segurança') }}
            </h2>
            <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                <i class="fas fa-shield-alt"></i>
                <span>Controle de Acesso</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- Seção: Usuários e Suas Funções --}}
            <x-ui.card>
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Usuários e suas Funções</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gerencie as atribuições de papéis e visualize as permissões efetivas de cada colaborador.</p>
                </div>

                <x-ui.table>
                    <x-slot name="head">
                        <tr>
                            <x-ui.th>Usuário</x-ui.th>
                            <x-ui.th>Função Principal</x-ui.th>
                            <x-ui.th>Permissões Efetivas</x-ui.th>
                            <x-ui.th align="right">Ações</x-ui.th>
                        </tr>
                    </x-slot>

                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors duration-200 group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0 rounded-full bg-gradient-to-br from-primary/10 to-primary-light/10 flex items-center justify-center border border-primary/10">
                                        <span class="text-primary font-bold text-sm">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($user->roles as $role)
                                        <x-ui.badge :variant="$role->name === 'admin' ? 'danger' : 'primary'" class="uppercase text-[10px]">
                                            <i class="fas fa-user-tag mr-1.5 opacity-70"></i>
                                            {{ $role->display_name ?? $role->name }}
                                        </x-ui.badge>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @php
                                        $permissions = $user->roles->flatMap->permissions->unique();
                                        $categories = [
                                            'documents' => ['color' => 'blue', 'icon' => 'fa-file-alt'],
                                            'boxes' => ['color' => 'amber', 'icon' => 'fa-box'],
                                            'commissions' => ['color' => 'emerald', 'icon' => 'fa-users'],
                                            'users' => ['color' => 'purple', 'icon' => 'fa-user-shield'],
                                        ];
                                    @endphp
                                    
                                    @foreach($permissions as $permission)
                                        @php
                                            $name = $permission->name;
                                            $categoryKey = explode('.', $name)[0] ?? 'default';
                                            $cat = $categories[$categoryKey] ?? ['color' => 'gray', 'icon' => 'fa-key'];
                                            
                                            $actionIcon = 'fa-check';
                                            if (str_contains($name, '.view')) $actionIcon = 'fa-eye';
                                            if (str_contains($name, '.create')) $actionIcon = 'fa-plus';
                                            if (str_contains($name, '.edit')) $actionIcon = 'fa-pen';
                                            if (str_contains($name, '.delete')) $actionIcon = 'fa-trash-alt';
                                        @endphp
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-{{ $cat['color'] }}-500/10 text-{{ $cat['color'] }}-600 dark:text-{{ $cat['color'] }}-400 text-[9px] font-bold border border-{{ $cat['color'] }}-500/20" title="{{ $permission->display_name ?? $name }}">
                                            <i class="fas {{ $cat['icon'] }} mr-1 opacity-50"></i>
                                            <i class="fas {{ $actionIcon }} opacity-80"></i>
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <x-ui.button
                                    variant="ghost-primary"
                                    size="sm"
                                    icon="fas fa-edit"
                                    x-data
                                    x-on:click="$dispatch('open-modal', 'edit-user-roles-{{ $user->id }}')"
                                >
                                    Editar
                                </x-ui.button>
                                @include('admin.edit-user-roles', ['user' => $user, 'roles' => $roles])
                            </td>
                        </tr>
                    @endforeach
                </x-ui.table>
            </x-ui.card>

            {{-- Seção: Funções do Sistema (Cards Premium) --}}
            <div class="space-y-6">
                <div class="flex items-center justify-between px-2">
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight flex items-center">
                        Funções do <span class="text-primary ml-2 italic">Sistema</span>
                    </h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($roles as $role)
                        <x-ui.card class="group">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-bold text-gray-900 dark:text-white capitalize group-hover:text-primary transition-colors">
                                    {{ $role->display_name ?? $role->name }}
                                </h4>
                                <div class="p-2 rounded-lg bg-primary/5 text-primary group-hover:bg-primary group-hover:text-white transition-all">
                                    <i class="fas fa-shield-alt text-sm"></i>
                                </div>
                            </div>
                            
                            <div class="space-y-1.5 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                                @foreach($role->permissions as $permission)
                                    <div class="flex items-center p-2 rounded-lg bg-gray-50/50 dark:bg-gray-900/30 border border-transparent hover:border-gray-100 dark:hover:border-gray-700/50 transition-all">
                                        <div class="w-1.5 h-1.5 rounded-full bg-primary/40 mr-3"></div>
                                        <span class="text-[11px] font-bold text-gray-600 dark:text-gray-300">{{ $permission->display_name ?? $permission->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between text-[10px] text-gray-400 uppercase font-black tracking-widest">
                                <span>Permissões Ativas</span>
                                <span class="text-primary">{{ $role->permissions->count() }}</span>
                            </div>
                        </x-ui.card>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
@endrole