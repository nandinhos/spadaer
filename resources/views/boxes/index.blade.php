<x-app-layout>
    @section('title', 'Caixas')
    @can('boxes.view')
        @section('header-title', 'Gerenciamento de Caixas')
        @push('scripts')
            <script>
                {
                    // Usando bloco de escopo para evitar erro de redeclaração com wire:navigate
                    const batchDeleteRoute = "{{ route('boxes.batch-destroy') }}";

                    document.addEventListener('livewire:navigated', function() {
                        const selectAllCheckbox = document.getElementById('select-all');
                        const checkboxes = document.querySelectorAll('input[name="selected_boxes[]"]');
                        const toggleAllBtn = document.getElementById('toggle-all-button');
                        const deleteBtn = document.getElementById('batch-delete-button');
                        const batchDeleteForm = document.querySelector(`form[action="${batchDeleteRoute}"]`);

                        function updateDeleteButton() {
                            const anyChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
                            if (deleteBtn) deleteBtn.disabled = !anyChecked;
                        }

                        if (toggleAllBtn) {
                            toggleAllBtn.addEventListener('click', function() {
                                const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
                                checkboxes.forEach(checkbox => checkbox.checked = !allChecked);
                                if (selectAllCheckbox) selectAllCheckbox.checked = !allChecked;
                                updateDeleteButton();
                            });
                        }

                        if (selectAllCheckbox) {
                            selectAllCheckbox.addEventListener('change', function() {
                                checkboxes.forEach(checkbox => checkbox.checked = selectAllCheckbox.checked);
                                updateDeleteButton();
                            });
                        }

                        checkboxes.forEach(checkbox => {
                            checkbox.addEventListener('change', function() {
                                if (selectAllCheckbox && !this.checked) selectAllCheckbox.checked = false;
                                updateDeleteButton();
                            });
                        });

                        if (batchDeleteForm) {
                            batchDeleteForm.addEventListener('submit', function(event) {
                                const checkedCount = Array.from(checkboxes).filter(checkbox => checkbox.checked).length;
                                const confirmationMessage = `Tem certeza que deseja processar as ${checkedCount} caixa(s) selecionadas? \n\nCaixas vazias serão excluídas permanentemente.\nCaixas com documentos terão seus documentos desassociados e as caixas NÃO serão excluídas.`;
                                if (!confirm(confirmationMessage)) event.preventDefault();
                            });
                        }

                        updateDeleteButton();
                    }, { once: false });
                }
            </script>
        @endpush


        <div class="p-4 mb-4 bg-white rounded-lg shadow dark:bg-gray-800">
            {{-- Formulário de Filtro/Busca --}}
            <form method="GET" action="{{ route('boxes.index') }}" id="filter-form"> {{-- Adicionado ID ao form --}}
                {{-- Input hidden para manter ordenação atual ao filtrar --}}
                <input type="hidden" name="sort_by" value="{{ request('sort_by', 'boxes.number') }}">
                <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'asc') }}">
                <input type="hidden" name="per_page" value="{{ request('per_page', 15) }}">


                <div class="grid items-end grid-cols-1 gap-4 md:grid-cols-6">
                    {{-- Filtro Busca (agora ocupa 2 colunas) --}}
                    <div class="md:col-span-2">
                        <x-input-label for="search" value="Buscar Nº/Local/Proj./Conf." />
                        <x-text-input id="search" name="search" :value="request('search')" class="w-full mt-1"
                            placeholder="Digite para buscar..." />
                    </div>

                    {{-- >>> INÍCIO DO NOVO FILTRO DE STATUS <<< --}}
                    <div>
                        <x-input-label for="filter_status" value="Status da Caixa" />
                        <x-select-input id="filter_status" name="filter_status" class="w-full mt-1" :currentValue="request('filter_status')">
                            {{-- O controller agora passa a variável $statusOptions --}}
                            @foreach ($statusOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </x-select-input>
                    </div>
                    {{-- >>> FIM DO NOVO FILTRO DE STATUS <<< --}}

                    {{-- Filtro Projeto --}}
                    <div>
                        <x-input-label for="project_id" value="Projeto" />
                        <x-select-input id="project_id" name="project_id" class="w-full mt-1" :currentValue="request('project_id')">
                            <option value="">Todos os Projetos</option>
                            @foreach ($projects as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </x-select-input>
                    </div>

                    {{-- Filtro Conferente (CORRIGIDO) --}}
                    <div>
                        {{-- Label atualizado --}}
                        <x-input-label for="commission_member_id" value="Conferente" />
                        {{-- ID e Name atualizados, usa $activeMembers --}}
                        <x-select-input id="commission_member_id" name="commission_member_id" class="w-full mt-1"
                            :currentValue="request('commission_member_id')">
                            <option value="">Todos os Conferentes</option>
                            {{-- Itera sobre a variável correta passada pelo controller --}}
                            @foreach ($activeMembers as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </x-select-input>
                    </div>

                    {{-- Botões --}}
                    <div class="flex items-center space-x-2">
                        <x-ui.button variant="primary" size="sm" type="submit">Filtrar</x-ui.button>
                        <x-ui.button variant="ghost" size="sm" type="button"
                            onclick="window.location.href='{{ route('boxes.index', ['sort_by' => request('sort_by'), 'sort_dir' => request('sort_dir'), 'per_page' => request('per_page')]) }}'">Limpar</x-ui.button>
                    </div>
                </div>
            </form>
        </div>

        <div class="flex flex-col items-center justify-between gap-4 mb-4 sm:flex-row">
            {{-- Seletor por página --}}
            <form method="GET" action="{{ route('boxes.index') }}" class="flex items-center gap-2">
                {{-- Mantém outros filtros/sort/search --}}
                <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                <input type="hidden" name="sort_dir" value="{{ request('sort_dir') }}">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <input type="hidden" name="project_id" value="{{ request('project_id') }}">
                <input type="hidden" name="checker_member_id" value="{{ request('checker_member_id') }}">

                <label for="per_page" class="text-sm text-gray-700 dark:text-gray-300 whitespace-nowrap">Itens por
                    página:</label>
                <x-select-input id="per_page" name="per_page" class="text-sm !py-1 !px-2" onchange="this.form.submit()"
                    :currentValue="request('per_page', 15)">
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </x-select-input>
            </form>
            {{-- Botão Adicionar --}}
            @can('boxes.create')
                <a href="{{ route('boxes.create') }}" wire:navigate>
                    <x-ui.button variant="primary" icon="fas fa-plus">
                        Adicionar Caixa
                    </x-ui.button>
                </a>
            @endcan
        </div>


        <div class="overflow-hidden bg-white rounded-lg shadow dark:bg-gray-800">
            <div class="overflow-x-auto">
                @can('boxes.delete')
                    <form method="POST" action="{{ route('boxes.batch-destroy') }}">
                        @csrf
                        @method('DELETE')
                        <div class="mb-4 p-4 flex items-center space-x-2">
                            <x-ui.button variant="danger" type="submit" id="batch-delete-button" icon="fas fa-trash-alt" disabled>
                                Excluir Selecionados
                            </x-ui.button>
                            <x-ui.button variant="secondary" type="button" id="toggle-all-button" icon="fas fa-check-square">
                                Selecionar Todos
                            </x-ui.button>
                        </div>

                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                        <input type="checkbox" id="select-all"
                                            class="rounded border-gray-300 text-primary shadow-sm focus:border-primary-light focus:ring focus:ring-primary-light focus:ring-opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:focus:ring-primary">
                                    </th>
                                    {{-- Helper para gerar links de ordenação --}}
                                    @php
                                        $requestParamsForSort = request()->except(['page']); // Mantém filtros/busca ao ordenar
                                        function sortLink($label, $columnKey, $currentSortBy, $currentSortDir, $params)
                                        {
                                            $newSortDir =
                                                $currentSortBy == $columnKey && $currentSortDir == 'asc'
                                                    ? 'desc'
                                                    : 'asc';
                                            $url = route(
                                                'boxes.index',
                                                array_merge($params, [
                                                    'sort_by' => $columnKey,
                                                    'sort_dir' => $newSortDir,
                                                ]),
                                            );
                                            $icon = '';
                                            if ($currentSortBy == $columnKey) {
                                                $icon =
                                                    $currentSortDir == 'asc'
                                                        ? '<i class="ml-1 fas fa-sort-up"></i>'
                                                        : '<i class="ml-1 fas fa-sort-down"></i>';
                                            }
                                            return '<a href="' .
                                                $url .
                                                '" class="flex items-center space-x-1 group">' .
                                                $label .
                                                $icon .
                                                '</a>';
                                        }
                                    @endphp
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                        {!! sortLink(
                                            'Número',
                                            'boxes.number',
                                            request('sort_by', 'boxes.number'),
                                            request('sort_dir', 'asc'),
                                            $requestParamsForSort,
                                        ) !!}
                                    </th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                        {!! sortLink(
                                            'Local Físico',
                                            'boxes.physical_location',
                                            request('sort_by'),
                                            request('sort_dir'),
                                            $requestParamsForSort,
                                        ) !!}
                                    </th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                        {!! sortLink('Projeto', 'projects.name', request('sort_by'), request('sort_dir'), $requestParamsForSort) !!}
                                    </th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                        {!! sortLink('Conferente', 'checker_users.name', request('sort_by'), request('sort_dir'), $requestParamsForSort) !!}
                                    </th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                        {!! sortLink(
                                            'Data Conferência',
                                            'boxes.conference_date',
                                            request('sort_by'),
                                            request('sort_dir'),
                                            $requestParamsForSort,
                                        ) !!}
                                    </th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase dark:text-gray-300">
                                        Documentos
                                    </th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase dark:text-gray-300">
                                        Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                @forelse ($boxes as $box)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-750/50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" name="selected_boxes[]" value="{{ $box->id }}"
                                                class="box-checkbox rounded border-gray-300 text-primary shadow-sm focus:border-primary-light focus:ring focus:ring-primary-light focus:ring-opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:focus:ring-primary">
                                        </td>
                                        {{-- Usa os aliases definidos no select do controller --}}
                                        <td
                                            class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-gray-100">
                                            {{ $box->number }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap dark:text-gray-300">
                                            {{ $box->physical_location ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap dark:text-gray-300">
                                            {{ $box->project->name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap dark:text-gray-300">
                                            {{ $box->checker_name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap dark:text-gray-300">
                                            {{ $box->conference_date?->format('d/m/Y') ?? '-' }}
                                        </td>
                                        <td
                                            class="px-6 py-4 text-sm text-gray-600 text-center whitespace-nowrap dark:text-gray-300">
                                            {{ $box->documents_count ?? '0' }}
                                        </td>
                                        <td class="px-6 py-4 space-x-2 text-sm font-medium text-right whitespace-nowrap">
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
                                                {{-- Botão Excluir com formulário --}}
                                                <form method="POST" action="{{ route('boxes.destroy', $box) }}" class="inline"
                                                    onsubmit="return confirm('Tem certeza que deseja excluir esta caixa e TODOS os documentos contidos nela?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-ui.button variant="ghost-danger" size="sm" icon="fas fa-trash-alt" type="submit" />
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                            Nenhuma caixa encontrada.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                </div>
                {{-- Paginação --}}
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                    {{ $boxes->links() }}
                </div>
            </div>
        @endcan
    @endcan
</x-app-layout>
