<x-app-layout>
    @section('title', 'Caixas')
    @section('header-title', 'Gerenciamento de Caixas')

    <div class="p-4 mb-4 bg-white rounded-lg shadow dark:bg-gray-800">
        {{-- Formulário de Filtro/Busca --}}
        <form method="GET" action="{{ route('boxes.index') }}" id="filter-form"> {{-- Adicionado ID ao form --}}
            {{-- Input hidden para manter ordenação atual ao filtrar --}}
            <input type="hidden" name="sort_by" value="{{ request('sort_by', 'boxes.number') }}">
            <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'asc') }}">
            <input type="hidden" name="per_page" value="{{ request('per_page', 15) }}">


            <div class="grid items-end grid-cols-1 gap-4 md:grid-cols-4">
                {{-- Filtro Busca --}}
                <div>
                    <x-input-label for="search" value="Buscar Nº/Local/Proj./Conf." />
                    <x-text-input id="search" name="search" :value="request('search')" class="w-full mt-1"
                        placeholder="Digite para buscar..." />
                </div>
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
                    <x-input-label for="checker_member_id" value="Conferente" />
                    {{-- ID e Name atualizados, usa $activeMembers --}}
                    <x-select-input id="checker_member_id" name="checker_member_id" class="w-full mt-1"
                        :currentValue="request('checker_member_id')">
                        <option value="">Todos os Conferentes</option>
                        {{-- Itera sobre a variável correta passada pelo controller --}}
                        @foreach ($activeMembers as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </x-select-input>
                </div>
                {{-- Botões --}}
                <div class="flex items-center space-x-2">
                    <x-primary-button type="submit">Filtrar</x-primary-button>
                    {{-- Botão Limpar agora mantém sort/per_page --}}
                    <x-secondary-button type="button"
                        onclick="window.location.href='{{ route('boxes.index', ['sort_by' => request('sort_by'), 'sort_dir' => request('sort_dir'), 'per_page' => request('per_page')]) }}'">Limpar</x-secondary-button>
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
        <x-primary-button onclick="window.location.href='{{ route('boxes.create') }}'">
            <i class="mr-2 fas fa-plus"></i> Adicionar Caixa
        </x-primary-button>
    </div>


    <div class="overflow-hidden bg-white rounded-lg shadow dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        {{-- Helper para gerar links de ordenação --}}
                        @php
                            $requestParamsForSort = request()->except(['page']); // Mantém filtros/busca ao ordenar
                            function sortLink($label, $columnKey, $currentSortBy, $currentSortDir, $params)
                            {
                                $newSortDir = $currentSortBy == $columnKey && $currentSortDir == 'asc' ? 'desc' : 'asc';
                                $url = route(
                                    'boxes.index',
                                    array_merge($params, ['sort_by' => $columnKey, 'sort_dir' => $newSortDir]),
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
                            class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase dark:text-gray-300">
                            Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    @forelse ($boxes as $box)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-750/50">
                            {{-- Usa os aliases definidos no select do controller --}}
                            <td
                                class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-gray-100">
                                {{ $box->number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap dark:text-gray-300">
                                {{ $box->physical_location ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap dark:text-gray-300">
                                {{ $box->project_name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap dark:text-gray-300">
                                {{ $box->checker_name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap dark:text-gray-300">
                                {{ $box->conference_date?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-6 py-4 space-x-2 text-sm font-medium text-right whitespace-nowrap">
                                <a href="{{ route('boxes.show', $box) }}"
                                    class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-200">Ver</a>
                                <a href="{{ route('boxes.edit', $box) }}"
                                    class="text-primary dark:text-primary-light hover:text-primary-dark dark:hover:text-white">Editar</a>
                                {{-- Botão Excluir com formulário --}}
                                <form method="POST" action="{{ route('boxes.destroy', $box) }}" class="inline"
                                    onsubmit="return confirm('Tem certeza que deseja excluir esta caixa e TODOS os documentos contidos nela?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-200">Excluir</button>
                                </form>
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
</x-app-layout>
