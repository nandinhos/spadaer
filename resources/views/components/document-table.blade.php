@props([
    'documents', // Coleção paginada
    'requestParams' => [], // Parâmetros da request atual
])

@php
    // --- Definição das Colunas e Chaves de Ordenação ---
    // Usamos as chaves que o controller espera para ordenação (com nome da tabela)
    // Usamos as keys do modelo para o switch de exibição quando apropriado
    $columns = [
        ['key' => 'boxes.number', 'label' => 'Caixa', 'model_key' => 'box'], // 'key' para sort, 'model_key' para acesso ao dado relacionado
        ['key' => 'projects.name', 'label' => 'Projeto', 'model_key' => 'project'],
        ['key' => 'documents.item_number', 'label' => 'Item', 'model_key' => 'item_number'],
        ['key' => 'documents.code', 'label' => 'Código', 'model_key' => 'code'],
        ['key' => 'documents.descriptor', 'label' => 'Descritor', 'model_key' => 'descriptor'],
        ['key' => 'documents.document_number', 'label' => 'Número', 'model_key' => 'document_number'],
        ['key' => 'documents.title', 'label' => 'Título', 'model_key' => 'title'],
        ['key' => 'documents.document_date', 'label' => 'Data', 'model_key' => 'document_date'],
        ['key' => 'documents.confidentiality', 'label' => 'Sigilo', 'model_key' => 'confidentiality'],
        ['key' => 'documents.version', 'label' => 'Versão', 'model_key' => 'version'],
        ['key' => 'documents.is_copy', 'label' => 'Cópia', 'model_key' => 'is_copy'],
    ];
    // Default sort deve corresponder a uma chave válida em $columns
    $currentSortBy = $requestParams['sort_by'] ?? 'documents.document_date';
    $currentSortDir = $requestParams['sort_dir'] ?? 'desc';

    // Helper para gerar links de ordenação (mantido da view index de caixas)
    $requestParamsForSort = request()->except(['page']);
    function sortLink($label, $columnKey, $currentSortBy, $currentSortDir, $params)
    {
        $newSortDir = $currentSortBy == $columnKey && $currentSortDir == 'asc' ? 'desc' : 'asc';
        $url = route('documents.index', array_merge($params, ['sort_by' => $columnKey, 'sort_dir' => $newSortDir]));
        $icon = '';
        if ($currentSortBy == $columnKey) {
            $icon =
                $currentSortDir == 'asc'
                    ? '<i class="ml-1 fas fa-sort-up"></i>'
                    : '<i class="ml-1 fas fa-sort-down"></i>';
        }
        return '<a href="' .
            $url .
            '" class="flex items-center space-x-1 group hover:text-gray-900 dark:hover:text-gray-100">' .
            $label .
            $icon .
            '</a>';
    }
@endphp

{{-- Cabeçalho da Tabela (Busca, Itens por página, Ações) --}}
<div class="p-4 border-b border-gray-200 dark:border-gray-700">
    {{-- Formulário para busca e itens por página --}}
    {{-- É melhor ter formulários separados para busca e itens por página --}}
    <div class="flex flex-col items-center justify-between gap-4 mb-4 md:flex-row">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 shrink-0">
            Documentos
            <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                ({{ $documents->total() > 0 ? $documents->firstItem() . '-' . $documents->lastItem() : 0 }} de
                {{ $documents->total() }})
            </span>
        </h2>

        {{-- Formulário de Busca --}}
        <form action="{{ route('documents.index') }}" method="GET" class="flex-grow md:max-w-md">
            {{-- Manter filtros/sort/per_page ao buscar --}}
            <input type="hidden" name="filter_box_number" value="{{ $requestParams['filter_box_number'] ?? '' }}">
            <input type="hidden" name="filter_project_id" value="{{ $requestParams['filter_project_id'] ?? '' }}">
            <input type="hidden" name="filter_year" value="{{ $requestParams['filter_year'] ?? '' }}">
            <input type="hidden" name="sort_by" value="{{ $currentSortBy }}">
            <input type="hidden" name="sort_dir" value="{{ $currentSortDir }}">
            <input type="hidden" name="per_page" value="{{ $requestParams['per_page'] ?? 15 }}">

            <div class="relative">
                <x-text-input type="text" name="search" placeholder="Pesquisar..."
                    class="w-full px-4 py-2 pr-10 text-base" :value="$requestParams['search'] ?? ''" aria-label="Pesquisar documentos" />
                @if (!empty($requestParams['search']))
                    {{-- Link para limpar busca, mantendo outros params --}}
                    <a href="{{ route('documents.index', array_merge($requestParams, ['search' => '', 'page' => 1])) }}"
                        class="absolute text-gray-500 transform -translate-y-1/2 right-10 top-1/2 dark:text-gray-400 hover:text-primary dark:hover:text-primary-light"
                        title="Limpar pesquisa">
                        <i class="fas fa-times-circle"></i>
                    </a>
                @endif
                <button type="submit"
                    class="absolute text-gray-500 transform -translate-y-1/2 right-3 top-1/2 dark:text-gray-400"
                    aria-label="Pesquisar">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>

        {{-- Formulário Itens por Página --}}
        <form action="{{ route('documents.index') }}" method="GET">
            {{-- Manter filtros/sort/search ao mudar página --}}
            <input type="hidden" name="filter_box_number" value="{{ $requestParams['filter_box_number'] ?? '' }}">
            <input type="hidden" name="filter_project_id" value="{{ $requestParams['filter_project_id'] ?? '' }}">
            <input type="hidden" name="filter_year" value="{{ $requestParams['filter_year'] ?? '' }}">
            <input type="hidden" name="sort_by" value="{{ $currentSortBy }}">
            <input type="hidden" name="sort_dir" value="{{ $currentSortDir }}">
            <input type="hidden" name="search" value="{{ $requestParams['search'] ?? '' }}">

            <div class="flex items-center gap-2 shrink-0">
                <label for="per_page"
                    class="text-sm text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ __('Por página:') }}</label>
                <x-select-input id="per_page" name="per_page" class="p-1 text-sm !py-1 !px-2"
                    onchange="this.form.submit()" :currentValue="$requestParams['per_page'] ?? 15">
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </x-select-input>
            </div>
        </form>
    </div>

    <!-- Botões de Ações Rápidas -->
    <div class="flex flex-wrap gap-2">
        {{-- Botão Adicionar (Indigo) - Visível apenas para usuários com permissão de criação --}}
        @can('documents.create')
            <a href="{{ route('documents.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <i class="fas fa-plus-circle mr-1.5"></i> Adicionar
            </a>
        @endcan

        {{-- Botão Exportar Excel (Verde) - Visível apenas para usuários com permissão de exportação Excel --}}
        @can('documents.export.excel')
            <a href="{{ route('documents.export', request()->query()) }}"
                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <i class="fas fa-file-excel mr-1.5"></i> Exportar (Excel)
            </a>
        @endcan

        {{-- Botão Exportar PDF (Vermelho) - Visível apenas para usuários com permissão de exportação PDF --}}
        @can('documents.export.pdf')
            <a href="{{ route('documents.export.pdf', request()->query()) }}" target="_blank"
                class="inline-flex items-center px-4 py-2 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-600 focus:bg-red-600 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <i class="fas fa-file-pdf mr-1.5"></i> Exportar (PDF)
            </a>
        @endcan
    </div>

    {{-- Exibir erros de importação, se houver --}}
    @if (session('import_errors'))
        {{-- ... (código para exibir erros de importação) ... --}}
    @endif
</div>

{{-- Tabela de Dados --}}
<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                {{-- Gera cabeçalhos com links de ordenação --}}
                @foreach ($columns as $column)
                    <th scope="col"
                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                        {!! sortLink($column['label'], $column['key'], $currentSortBy, $currentSortDir, $requestParamsForSort) !!}
                    </th>
                @endforeach
                <th scope="col"
                    class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase dark:text-gray-300">
                    Ações
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
            @forelse ($documents as $document)
                @can('documents.view')
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-750/50">
                    {{-- Itera sobre as colunas definidas para exibir os dados corretos --}}
                    @foreach ($columns as $column)
                        <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap dark:text-gray-300">
                            @switch($column['model_key'])
                                {{-- Usa model_key para acessar o dado --}}
                                @case('box')
                                    {{ $document->box?->number ?? '--' }}
                                @break

                                @case('project')
                                    {{ $document->project?->name ?? '--' }}
                                @break

                                @case('document_date')
                                    {{ $document->document_date }}
                                @break

                                @case('is_copy')
                                    {{-- Exibe a string armazenada ou um traço se for nulo/vazio --}}
                                    {{ $document->is_copy ?: '--' }}
                                @break

                                @case('confidentiality')
                                    @php
                                        // Normaliza o valor para minúsculas para comparação case-insensitive
                                        $level = strtolower($document->confidentiality ?? '');
                                        $confidentialityClass = '';
                                        // Mantém o valor original para exibição (ou '--' se nulo/vazio)
                                        $confidentialityLabel = $document->confidentiality ?: '--';

                                        // Define as classes CSS baseadas no nível de sigilo normalizado
                                        switch ($level) {
                                            case 'unclassified': // Azul
                                                $confidentialityClass =
                                                    'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
                                                break;

                                            case 'ostensivo': // Verde
                                            case 'público': // Verde
                                            case 'public': // Verde
                                                $confidentialityClass =
                                                    'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                                                break;

                                            case 'confidencial': // Amarelo
                                            case 'confidential': // Amarelo
                                                $confidentialityClass =
                                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
                                                break;

                                            case 'restrito': // Vermelho
                                            case 'secreto': // Vermelho
                                            case 'restricted': // Vermelho
                                                $confidentialityClass =
                                                    'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
                                                break;

                                            default:
                                                // Cor padrão cinza para valores vazios ou não reconhecidos
                                                $confidentialityClass =
                                                    'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                                                // Garante que o label seja '--' se for um valor não reconhecido ou vazio
                                                $confidentialityLabel = '--';
                                                break;
                                        }
                                    @endphp
                                    {{-- Aplica a classe e exibe o label original (capitalizado) ou '--' --}}
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize {{ $confidentialityClass }}">
                                        {{-- Usar ucfirst() para capitalizar a primeira letra do label exibido --}}
                                        {{ $confidentialityLabel !== '--' ? ucfirst(strtolower($document->confidentiality)) : '--' }}
                                    </span>
                                @break

                                @case('title')
                                    <span title="{{ $document->title }}">{{ Str::limit($document->title, 50) }}</span>
                                @break

                                @default
                                    {{ $document->{$column['model_key']} ?? '--' }}
                            @endswitch
                        </td>
                    @endforeach
                    {{-- Coluna de Ações --}}
                    <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                        {{-- Botão para abrir o Modal (Visualização) --}}
                        @can('documents.view')
                            <button type="button" @click="openDocumentModal({{ $document->id }})"
                                class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-200 focus:outline-none focus:underline"
                                title="Ver Detalhes do Documento">
                                <i class="mr-1 fas fa-eye"></i>Ver
                            </button>
                        @endcan

                        {{-- Botão Editar --}}
                        @can('documents.edit')
                            <a href="{{ route('documents.edit', $document) }}"
                                class="ml-2 font-medium text-primary dark:text-primary-light hover:text-primary-dark dark:hover:text-white focus:outline-none focus:underline"
                                title="Editar Documento">
                                <i class="mr-1 fas fa-edit"></i>Editar
                            </a>
                        @endcan

                        {{-- Botão Excluir --}}
                        @can('documents.delete')
                            <form method="POST" action="{{ route('documents.destroy', $document) }}"
                                class="inline ml-2"
                                onsubmit="return confirm('Tem certeza que deseja excluir este documento?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="font-medium text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-200 focus:outline-none focus:underline"
                                    title="Excluir Documento">
                                    <i class="mr-1 fas fa-trash-alt"></i>Excluir
                                </button>
                            </form>
                        @endcan
                    </td>
                </tr>
                @endcan
            @empty
                    {{-- Linha para quando não há documentos --}}
                    <tr>
                        <td colspan="{{ count($columns) + 1 }}"
                            class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <i class="mb-2 text-gray-400 fas fa-folder-open fa-3x"></i>
                                Nenhum documento encontrado.
                                @if (count(array_filter($requestParams)) > 0)
                                    <p class="mt-1 text-sm">Tente ajustar os filtros ou a pesquisa.</p>
                                    <a href="{{ route('documents.index', ['sort_by' => $requestParams['sort_by'] ?? null, 'sort_dir' => $requestParams['sort_dir'] ?? null, 'per_page' => $requestParams['per_page'] ?? null]) }}"
                                        class="mt-3 text-sm text-primary dark:text-primary-light hover:underline">
                                        Limpar filtros e pesquisa
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
