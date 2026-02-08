<div>
    <x-ui.card>
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
            <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                Listagem de <span class="text-primary">Comissões</span>
            </h2>
            
            <a href="{{ route('commissions.create') }}" wire:navigate>
                <x-ui.button icon="fas fa-plus">
                    Nova Comissão
                </x-ui.button>
            </a>
        </div>

        <!-- Filtros e Busca -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="md:col-span-3 relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400 group-focus-within:text-primary transition-colors"></i>
                </div>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search"
                    placeholder="Buscar por nome, portaria ou descrição..." 
                    class="block w-full pl-11 pr-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl focus:ring-2 focus:ring-primary/20 dark:text-white transition-all duration-200"
                >
            </div>
            
            <select 
                wire:model.live="status"
                class="block w-full py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl focus:ring-2 focus:ring-primary/20 dark:text-white transition-all duration-200"
            >
                <option value="">Todos os Status</option>
                <option value="active">Ativas</option>
                <option value="inactive">Inativas</option>
            </select>
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
                    <x-ui.th sortable wire:click="sortBy('name')" :direction="$sort_by === 'name' ? $sort_dir : null">
                        Comissão
                    </x-ui.th>
                    <x-ui.th sortable wire:click="sortBy('ordinance_number')" :direction="$sort_by === 'ordinance_number' ? $sort_dir : null">
                        Portaria
                    </x-ui.th>
                    <x-ui.th sortable wire:click="sortBy('ordinance_date')" :direction="$sort_by === 'ordinance_date' ? $sort_dir : null">
                        Data
                    </x-ui.th>
                    <x-ui.th>
                        Status
                    </x-ui.th>
                    <x-ui.th align="center">
                        Membros
                    </x-ui.th>
                    <x-ui.th align="right">
                        Ações
                    </x-ui.th>
                </tr>
            </x-slot>

            @forelse ($commissions as $commission)
                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-gray-900 dark:text-white">
                            {{ $commission->name }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-[200px]">
                            {{ $commission->description }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        @if($commission->ordinance_file)
                            <a href="{{ Storage::url($commission->ordinance_file) }}" target="_blank" class="text-primary hover:underline flex items-center">
                                <i class="fas fa-file-pdf mr-2"></i>{{ $commission->ordinance_number }}
                            </a>
                        @else
                            <span class="text-gray-600 dark:text-gray-400">{{ $commission->ordinance_number }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $commission->ordinance_date?->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <x-ui.badge :variant="$commission->status === 'active' ? 'success' : 'danger'">
                            {{ $commission->status === 'active' ? 'Ativa' : 'Inativa' }}
                        </x-ui.badge>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <x-ui.badge variant="info">
                            {{ $commission->members_count }}
                        </x-ui.badge>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('commissions.show', $commission) }}" wire:navigate>
                                <x-ui.button variant="ghost-primary" size="sm" icon="fas fa-eye" />
                            </a>
                            <a href="{{ route('commissions.edit', $commission) }}" wire:navigate>
                                <x-ui.button variant="ghost-warning" size="sm" icon="fas fa-edit" />
                            </a>
                            <x-ui.button
                                variant="ghost-danger"
                                size="sm"
                                icon="fas fa-trash"
                                @click="$store.confirmDelete.open({
                                    title: 'Excluir Comissão',
                                    message: 'Tem certeza que deseja excluir a comissão {{ $commission->name }}?',
                                    onConfirm: () => { $wire.deleteCommission({{ $commission->id }}) }
                                })"
                            />
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        Nenhuma comissão encontrada.
                    </td>
                </tr>
            @endforelse
        </x-ui.table>

        <div class="mt-6">
            {{ $commissions->links() }}
        </div>
    </x-ui.card>
</div>
