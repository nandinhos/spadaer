@props([
    'requestParams' => [],
])

<div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6 animate-fadeIn">
    <div class="flex flex-wrap items-center justify-between gap-y-2">
        <div class="flex items-center">
            <i class="fas fa-filter text-blue-500 dark:text-blue-300 mr-2"></i>
            <span class="font-medium text-blue-800 dark:text-blue-200">Filtros Ativos:</span>

            <div class="flex flex-wrap ml-2 gap-2">
                @if(!empty($requestParams['filter_box']))
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200">
                        Caixa: <span class="font-bold ml-1">{{ $requestParams['filter_box'] }}</span>
                    </span>
                @endif

                @if(!empty($requestParams['filter_project']))
                     <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200">
                        Projeto: <span class="font-bold ml-1">{{ $requestParams['filter_project'] }}</span>
                    </span>
                @endif

                 @if(!empty($requestParams['filter_year']))
                     <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200">
                        Ano: <span class="font-bold ml-1">{{ $requestParams['filter_year'] }}</span>
                    </span>
                @endif

                 @if(!empty($requestParams['search']))
                     <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200">
                        Pesquisa: <span class="font-bold ml-1">{{ $requestParams['search'] }}</span>
                    </span>
                @endif
            </div>
        </div>

        {{-- Botão para limpar filtros (redireciona para a URL base mantendo sort/per_page) --}}
        <button
            onclick="window.location.href='{{ route('documents.index', ['sort_by' => $requestParams['sort_by'] ?? null, 'sort_dir' => $requestParams['sort_dir'] ?? null, 'per_page' => $requestParams['per_page'] ?? null ]) }}'"
            {{-- Ou @click="window.location.href=getClearFiltersUrl()" --}}
            class="px-3 py-1 text-sm text-blue-800 dark:text-blue-200 bg-blue-100 dark:bg-blue-800 rounded hover:bg-blue-200 dark:hover:bg-blue-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 shrink-0"
        >
            <i class="fas fa-times-circle mr-1"></i> Limpar Filtros
        </button>
    </div>
</div>