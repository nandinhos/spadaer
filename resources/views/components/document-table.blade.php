@props([
    'documents', // Coleção paginada
    'requestParams' => [],
])

@php
    // Definição das colunas para facilitar a manutenção
    $columns = [
        ['key' => 'box_number', 'label' => 'Caixa'],
        ['key' => 'item_number', 'label' => 'Item'],
        ['key' => 'code', 'label' => 'Código'],
        ['key' => 'descriptor', 'label' => 'Descritor'],
        ['key' => 'document_number', 'label' => 'Número'],
        ['key' => 'title', 'label' => 'Título'],
        ['key' => 'document_date', 'label' => 'Data'],
        ['key' => 'confidentiality', 'label' => 'Sigilo'],
        ['key' => 'version', 'label' => 'Versão'],
        ['key' => 'is_copy', 'label' => 'Cópia'],
    ];
    $currentSortBy = $requestParams['sort_by'] ?? 'box_number';
    $currentSortDir = $requestParams['sort_dir'] ?? 'asc';
@endphp

<div class="p-4 border-b border-gray-200 dark:border-gray-700">
     {{-- Formulário para busca e itens por página --}}
    <form action="{{ route('documents.index') }}" method="GET" class="mb-4">
        {{-- Mantém filtros e ordenação --}}
         @if(isset($requestParams['filter_box'])) <input type="hidden" name="filter_box" value="{{ $requestParams['filter_box'] }}"> @endif
         @if(isset($requestParams['filter_project'])) <input type="hidden" name="filter_project" value="{{ $requestParams['filter_project'] }}"> @endif
         @if(isset($requestParams['filter_year'])) <input type="hidden" name="filter_year" value="{{ $requestParams['filter_year'] }}"> @endif
         @if(isset($requestParams['sort_by'])) <input type="hidden" name="sort_by" value="{{ $requestParams['sort_by'] }}"> @endif
         @if(isset($requestParams['sort_dir'])) <input type="hidden" name="sort_dir" value="{{ $requestParams['sort_dir'] }}"> @endif

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Documentos
                <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                   ({{ $documents->total() > 0 ? $documents->firstItem() . '-' . $documents->lastItem() : 0 }} de {{ $documents->total() }})
                </span>
            </h2>

            {{-- Campo de Pesquisa --}}
            <div class="relative flex-grow md:max-w-md">
                <x-text-input
                    type="text"
                    name="search"
                    placeholder="Pesquisar documentos..."
                    class="w-full px-4 py-2 pr-10 text-base"
                    :value="old('search', $requestParams['search'] ?? '')"
                    aria-label="Pesquisar documentos"
                />
                 {{-- Botão Limpar (opcional, pode ser feito com JS se preferir não submeter) --}}
                @if(!empty($requestParams['search']))
                <a href="{{ route('documents.index', array_merge($requestParams, ['search' => '', 'page' => 1])) }}" class="absolute right-10 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary-light" title="Limpar pesquisa">
                     <i class="fas fa-times-circle"></i>
                </a>
                @endif
                <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400" aria-label="Pesquisar">
                    <i class="fas fa-search"></i>
                </button>
            </div>

            {{-- Seleção de Itens por Página --}}
            <div class="flex items-center gap-2 shrink-0">
                <x-input-label for="per_page" value="Por página:" class="whitespace-nowrap"/>
                <x-select-input
                    id="per_page"
                    name="per_page"
                    class="p-1 text-sm"
                     onchange="this.form.submit()" {{-- Submete o form ao mudar --}}
                     :currentValue="old('per_page', $requestParams['per_page'] ?? 10)"
                >
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                 </x-select-input>
            </div>
        </div>
    </form>

    <!-- Botões de Ações Rápidas -->
    <div class="flex flex-wrap gap-2 mt-2">
        <a href="{{ route('documents.create') }}" class="inline-flex items-center px-4 py-2 bg-black dark:bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-700 focus:bg-gray-700 dark:focus:bg-gray-700 active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
            <i class="fas fa-plus-circle mr-1.5"></i> Adicionar
        </a>
         <x-secondary-button type="button" onclick="alert('Ação Exportar não implementada')">
             <i class="fas fa-file-export mr-1.5"></i> Exportar
        </x-secondary-button>
         <x-secondary-button type="button" onclick="alert('Ação Imprimir não implementada')">
             <i class="fas fa-print mr-1.5"></i> Imprimir
         </x-secondary-button>
    </div>
</div>

<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                @foreach ($columns as $column)
                    <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                        {{-- Link para ordenação (gerado no Blade ou com Alpine) --}}
                        {{-- Usando Alpine: @click="window.location.href=getSortUrl('{{ $column['key'] }}')" class="cursor-pointer" --}}
                        <a href="{{ route('documents.index', array_merge($requestParams, [
                            'sort_by' => $column['key'],
                            'sort_dir' => $currentSortBy == $column['key'] && $currentSortDir == 'asc' ? 'desc' : 'asc',
                            'page' => 1, // Resetar para primeira página ao ordenar
                        ])) }}" class="flex items-center space-x-1 hover:text-gray-700 dark:hover:text-gray-100">
                            <span>{{ $column['label'] }}</span>
                            @if ($currentSortBy == $column['key'])
                                <i class="fas {{ $currentSortDir == 'asc' ? 'fa-sort-up' : 'fa-sort-down' }}"></i>
                            @else
                                {{-- Ícone placeholder para indicar que é ordenável (opcional) --}}
                                {{-- <i class="fas fa-sort text-gray-300 dark:text-gray-600"></i> --}}
                            @endif
                         </a>
                    </th>
                @endforeach
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Ações
                </th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse ($documents as $document)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-750/50">
                     {{-- Mapeia as colunas definidas no array $columns para os dados do $document --}}
                    @foreach ($columns as $column)
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                            @switch($column['key'])
                                @case('document_date')
                                    {{ $document->document_date ? $document->document_date->format('d/m/Y') : 'N/D' }}
                                    @break
                                @case('is_copy')
                                    {{ $document->is_copy ? 'Sim' : 'Não' }}
                                    @break
                                 @case('confidentiality')
                                     <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize
                                            @if($document->confidentiality === 'Confidencial') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                            @elseif($document->confidentiality === 'Restrito') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @else bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @endif"
                                     >
                                        {{ strtolower($document->confidentiality) }}
                                    </span>
                                    @break
                                 @case('title')
                                     {{-- Limita o tamanho do título --}}
                                     <span title="{{ $document->{$column['key']} }}">{{ Str::limit($document->{$column['key']}, 50) }}</span>
                                    @break

                                @default
                                     {{ $document->{$column['key']} ?? 'N/D' }}
                            @endswitch
                        </td>
                    @endforeach
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button
                            {{-- Chama a função Alpine para abrir o modal --}}
                            @click="openDocumentModal({{ $document->id }})"
                            class="text-primary dark:text-primary-light hover:text-primary-dark dark:hover:text-white font-medium"
                        >
                            <i class="fas fa-eye mr-1"></i> Ver
                        </button>
                        {{-- Adicionar botões de Editar/Excluir aqui, se necessário --}}
                        {{-- <a href="{{ route('documents.edit', $document) }}" class="text-indigo-600 hover:text-indigo-900 ml-2">Editar</a> --}}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columns) + 1 }}" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                         <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-folder-open fa-3x text-gray-400 mb-2"></i>
                            Nenhum documento encontrado.
                            @if(count(array_filter($requestParams)) > 0) {{-- Verifica se há filtros ativos --}}
                                <p class="mt-1 text-sm">Tente ajustar os filtros ou a pesquisa.</p>
                                <button
                                     onclick="window.location.href='{{ route('documents.index', ['sort_by' => $requestParams['sort_by'] ?? null, 'sort_dir' => $requestParams['sort_dir'] ?? null, 'per_page' => $requestParams['per_page'] ?? null ]) }}'"
                                     class="mt-3 text-sm text-primary dark:text-primary-light hover:underline"
                                 >
                                     Limpar filtros
                                </button>
                            @endif
                         </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>