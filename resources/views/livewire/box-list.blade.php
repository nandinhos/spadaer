<div>
    <!-- Filtros -->
    <div class="p-4 mb-4 bg-white rounded-lg shadow dark:bg-gray-800">
        <div class="grid items-end grid-cols-1 gap-4 md:grid-cols-6">
            <div class="md:col-span-2">
                <x-input-label for="search" value="Buscar Nº/Local/Proj./Conf." />
                <x-text-input wire:model.live.debounce.300ms="search" id="search" class="w-full mt-1"
                    placeholder="Digite para buscar..." />
            </div>

            <div>
                <x-input-label value="Status" />
                <x-select-input wire:model.live="filter_status" class="w-full mt-1">
                    <option value="">Todos</option>
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </x-select-input>
            </div>

            <div>
                <x-input-label value="Projeto" />
                <x-select-input wire:model.live="project_id" class="w-full mt-1">
                    <option value="">Todos</option>
                    @foreach($projects as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </x-select-input>
            </div>

            <div>
                <x-input-label value="Conferente" />
                <x-select-input wire:model.live="commission_member_id" class="w-full mt-1">
                    <option value="">Todos</option>
                    @foreach($activeMembers as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </x-select-input>
            </div>

            <div>
                <x-ui.button wire:click="$set('search', '')" variant="secondary" size="sm">
                    Limpar
                </x-ui.button>
            </div>
        </div>
    </div>

    <!-- Seletor por página -->
    <div class="flex flex-col items-center justify-between gap-4 mb-4 sm:flex-row">
        <div class="flex items-center gap-2">
            <label class="text-sm text-gray-700 dark:text-gray-300">Itens por página:</label>
            <x-select-input wire:model.live="per_page" class="text-sm">
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </x-select-input>
        </div>

        @can('boxes.create')
            <a href="{{ route('boxes.create') }}" wire:navigate>
                <x-ui.button variant="primary" icon="fas fa-plus">
                    Adicionar Caixa
                </x-ui.button>
            </a>
        @endcan
    </div>

    <!-- Mensagens -->
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('warning'))
        <div class="mb-4 p-4 bg-yellow-500/10 border border-yellow-500/20 text-yellow-600 rounded-xl">
            {{ session('warning') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-500/10 border border-red-500/20 text-red-600 rounded-xl">
            {{ session('error') }}
        </div>
    @endif

    <!-- Tabela com Alpine.js para seleção -->
    <div x-data="{
        selected: @entangle('selectedBoxes').live,
        allIds: {{ json_encode($boxes->pluck('id')->toArray()) }},
        get isAllSelected() {
            return this.selected.length > 0 && this.selected.length === this.allIds.length;
        },
        toggleAll() {
            this.selected = this.isAllSelected ? [] : [...this.allIds];
        }
    }">

        <!-- Botões de ação em massa -->
        @can('boxes.delete')
            <div x-show="selected.length > 0" x-cloak class="mb-4 p-4 bg-gray-50 rounded-xl">
                <div class="flex items-center space-x-2">
                    <x-ui.button
                        variant="danger"
                        size="sm"
                        icon="fas fa-trash-alt"
                        x-bind:disabled="selected.length === 0"
                        @click="$store.confirmDelete.open({
                            title: 'Excluir Selecionados',
                            message: 'Tem certeza que deseja processar ' + selected.length + ' caixa(s)?',
                            onConfirm: () => { $wire.batchDelete() }
                        })"
                    >
                        Excluir (<span x-text="selected.length"></span>)
                    </x-ui.button>

                    <x-ui.button
                        variant="secondary"
                        size="sm"
                        icon="fas fa-check-square"
                        @click="toggleAll()"
                    >
                        <span x-text="isAllSelected ? 'Desmarcar Todos' : 'Selecionar Todos'"></span>
                    </x-ui.button>
                </div>
            </div>
        @endcan

        <!-- Tabela -->
        <div class="overflow-hidden bg-white rounded-lg shadow dark:bg-gray-800">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            @can('boxes.delete')
                                <th class="w-12 px-6 py-3">
                                    <input type="checkbox" @click="toggleAll()" :checked="isAllSelected" class="rounded border-gray-300">
                                </th>
                            @endcan
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('boxes.number')">
                                Número
                                @if($sort_by === 'boxes.number')
                                    <i class="fas fa-sort-{{ $sort_dir === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Local
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Projeto
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Conferente
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($boxes as $box)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750/50">
                                @can('boxes.delete')
                                    <td class="px-6 py-4">
                                        <input type="checkbox" value="{{ $box->id }}" x-model="selected" class="rounded border-gray-300">
                                    </td>
                                @endcan
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $box->number }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $box->physical_location ?? '--' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $box->project?->name ?? '--' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $box->commissionMember?->user?->name ?? '--' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        @can('boxes.view')
                                            <a href="{{ route('boxes.show', $box) }}" wire:navigate>
                                                <x-ui.button variant="ghost-primary" size="sm" icon="fas fa-eye" />
                                            </a>
                                        @endcan

                                        @can('boxes.edit')
                                            <a href="{{ route('boxes.edit', $box) }}" wire:navigate>
                                                <x-ui.button variant="ghost-warning" size="sm" icon="fas fa-edit" />
                                            </a>
                                        @endcan

                                        @can('boxes.delete')
                                            <x-ui.button
                                                variant="ghost-danger"
                                                size="sm"
                                                icon="fas fa-trash-alt"
                                                @click="$store.confirmDelete.open({
                                                    title: 'Excluir Caixa',
                                                    message: 'Tem certeza que deseja excluir {{ $box->number }}?',
                                                    onConfirm: () => { $wire.deleteBox({{ $box->id }}) }
                                                })"
                                            />
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                    Nenhuma caixa encontrada.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $boxes->links() }}
            </div>
        </div>
    </div>
</div>
