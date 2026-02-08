<div>
    <x-ui.card>
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
            <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                Listagem de <span class="text-primary">Projetos</span>
            </h2>
            
            <a href="{{ route('projects.create') }}" wire:navigate>
                <x-ui.button icon="fas fa-plus">
                    Novo Projeto
                </x-ui.button>
            </a>
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
                    placeholder="Buscar por nome, código ou descrição..." 
                    class="block w-full pl-11 pr-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl focus:ring-2 focus:ring-primary/20 dark:text-white transition-all duration-200"
                >
            </div>
        </div>

        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-xl text-sm font-bold">
                {{ session('message') }}
            </div>
        @endif

        <!-- Tabela -->
        <x-ui.table>
            <x-slot name="head">
                <tr>
                    <x-ui.th sortable wire:click="sortBy('code')" :direction="$sort_by === 'code' ? $sort_dir : null">
                        Código
                    </x-ui.th>
                    <x-ui.th sortable wire:click="sortBy('name')" :direction="$sort_by === 'name' ? $sort_dir : null">
                        Nome
                    </x-ui.th>
                    <x-ui.th>
                        Descrição
                    </x-ui.th>
                    <x-ui.th align="right">
                        Ações
                    </x-ui.th>
                </tr>
            </x-slot>

            @forelse ($projects as $project)
                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors group">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <x-ui.badge variant="gray">
                            {{ $project->code }}
                        </x-ui.badge>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                        {{ $project->name }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">
                        {{ $project->description }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('projects.show', $project) }}" wire:navigate>
                                <x-ui.button variant="ghost-primary" size="sm" icon="fas fa-eye" title="Visualizar" />
                            </a>
                            <a href="{{ route('projects.edit', $project) }}" wire:navigate>
                                <x-ui.button variant="ghost-warning" size="sm" icon="fas fa-edit" title="Editar" />
                            </a>
                            <x-ui.button
                                variant="ghost-danger"
                                size="sm"
                                icon="fas fa-trash"
                                @click="$store.confirmDelete.open({
                                    title: 'Excluir Projeto',
                                    message: 'Tem certeza que deseja excluir o projeto {{ $project->name }}?',
                                    onConfirm: () => { $wire.deleteProject({{ $project->id }}) }
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
                            <i class="fas fa-folder-open text-4xl text-gray-200 dark:text-gray-700 mb-4"></i>
                            <p class="text-gray-500 dark:text-gray-400 font-medium">Nenhum projeto encontrado.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-ui.table>

        <div class="mt-6">
            {{ $projects->links() }}
        </div>
    </x-ui.card>
</div>
