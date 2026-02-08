@section('header-title', 'Editar Permissões')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
        <!-- Header / Breadcrumbs -->
        <div class="flex items-center justify-between px-2">
            <div>
                <nav class="flex mb-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('admin.roles.index') }}" wire:navigate class="text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-primary transition-colors">
                                Gestão de Papéis
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-[8px] text-gray-300 mx-1"></i>
                                <span class="text-[10px] font-black uppercase tracking-widest text-primary">{{ $role->name }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">
                    Editar <span class="text-primary italic">Permissões</span>
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Configure o nível de acesso para o papel: <strong class="text-gray-900 dark:text-white">{{ $role->name }}</strong></p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.roles.index') }}" wire:navigate class="px-6 py-2.5 text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors">
                    Cancelar
                </a>
                <x-ui.button wire:click="save" icon="fas fa-save">
                    Salvar Alterações
                </x-ui.button>
            </div>
        </div>

        <x-ui.card>
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-12">
                <!-- Sidebar: Role Name e Info -->
                <div class="lg:col-span-1 space-y-8">
                    <div class="space-y-4">
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-400">Identificador do Papel</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-primary transition-colors">
                                <i class="fas fa-id-badge"></i>
                            </div>
                            <input 
                                type="text" 
                                wire:model="roleName" 
                                class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-primary/20 dark:text-white text-lg font-bold transition-all pl-12 py-3"
                                {{ in_array($role->name, ['admin', 'user']) ? 'disabled' : '' }}
                            >
                        </div>
                        @error('roleName') <span class="text-xs text-rose-500 font-bold mt-1">{{ $message }}</span> @enderror
                        @if(in_array($role->name, ['admin', 'user'])) 
                            <p class="p-4 rounded-xl bg-amber-500/5 border border-amber-500/10 text-[11px] text-amber-600 dark:text-amber-400 font-bold leading-relaxed">
                                <i class="fas fa-info-circle mr-1 opacity-70"></i> 
                                Este é um papel reservado do sistema e não pode ser renomeado.
                            </p> 
                        @endif
                    </div>

                    <div class="p-6 rounded-2xl bg-primary/5 border border-primary/10">
                        <h4 class="text-[10px] font-black uppercase tracking-widest text-primary mb-4 flex items-center">
                            <i class="fas fa-chart-pie mr-2"></i> Resumo de Acesso
                        </h4>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500 font-bold">Total de Permissões</span>
                                <span class="text-sm font-black text-primary">{{ count($rolePermissions) }}</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 h-2 rounded-full overflow-hidden">
                                @php 
                                    $allCount = \Spatie\Permission\Models\Permission::count();
                                    $percent = $allCount > 0 ? (count($rolePermissions) / $allCount) * 100 : 0;
                                @endphp
                                <div class="bg-primary h-full transition-all duration-1000" style="width: {{ $percent }}%"></div>
                            </div>
                            <p class="text-[10px] text-gray-400 font-medium italic">Este papel possui acesso a {{ round($percent) }}% das funcionalidades do sistema.</p>
                        </div>
                    </div>
                </div>

                <!-- Main: Matriz de Permissões Organizadíssima -->
                <div class="lg:col-span-3 space-y-12">
                    <div class="flex items-center space-x-2 pb-4 border-b border-gray-100 dark:border-gray-700">
                        <div class="p-2 rounded-lg bg-emerald-500/10 text-emerald-500">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <h4 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tighter">Matriz de Competências</h4>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-12">
                        @foreach($permissionsByCategory as $category => $perms)
                            <div class="space-y-6">
                                <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-900/40 px-4 py-3 rounded-xl border-l-4 border-{{ $category === 'documents' ? 'blue' : ($category === 'commissions' ? 'emerald' : 'amber') }}-500 shadow-sm">
                                    <div class="flex items-center">
                                        <i class="fas {{ $category === 'documents' ? 'fa-file-alt text-blue-500' : ($category === 'commissions' ? 'fa-users text-emerald-500' : 'fa-cogs text-amber-500') }} mr-3 opacity-70"></i>
                                        <span class="text-xs font-black uppercase tracking-widest text-gray-700 dark:text-gray-300">Módulo: {{ $category }}</span>
                                    </div>
                                    <span class="text-[10px] font-black text-gray-400 bg-white dark:bg-gray-800 px-2 py-1 rounded-lg border border-gray-100 dark:border-gray-700">{{ $perms->count() }}</span>
                                </div>
                                
                                <div class="grid grid-cols-1 gap-3 pl-2">
                                    @foreach($perms as $permission)
                                        <label class="relative flex items-center p-3 rounded-xl border border-transparent hover:border-gray-100 dark:hover:border-gray-800 hover:bg-white dark:hover:bg-gray-900 hover:shadow-xl hover:shadow-gray-200/20 transition-all cursor-pointer group">
                                            <div class="relative flex items-center">
                                                <input 
                                                    type="checkbox" 
                                                    wire:model="rolePermissions" 
                                                    value="{{ $permission->name }}" 
                                                    class="w-6 h-6 rounded-lg border-gray-200 text-primary focus:ring-primary/20 transition-all cursor-pointer"
                                                >
                                            </div>
                                            <div class="ml-4 flex-grow">
                                                <div class="flex items-center justify-between">
                                                    <span class="block text-sm font-bold text-gray-700 dark:text-gray-300 group-hover:text-primary transition-colors">
                                                        {{ str_replace($category . '.', '', $permission->name) }}
                                                    </span>
                                                    @if(str_contains($permission->name, 'delete'))
                                                        <i class="fas fa-trash-alt text-[10px] text-rose-300 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                                    @endif
                                                </div>
                                                <span class="text-[10px] text-gray-400 font-medium uppercase tracking-tighter opacity-50">{{ $permission->name }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </x-ui.card>
    </div>
</div>
