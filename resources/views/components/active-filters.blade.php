@props([
    'requestParams' => [],
    'projects' => collect(),
])

<div class="p-4 mb-6 bg-blue-50 dark:bg-gray-700 border border-blue-200 dark:border-gray-600 rounded-lg">
    <div class="flex flex-wrap items-center justify-between gap-y-2">
        <div class="flex items-center">
            <i class="fas fa-info-circle text-blue-600 dark:text-blue-300 mr-2"></i>
            <span class="font-medium text-blue-800 dark:text-blue-200">Filtros Ativos:</span>

            <div class="flex flex-wrap items-center ml-2 gap-2">
                @if (!empty($requestParams['search']))
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-200">
                        Busca: <b class="ml-1">{{ $requestParams['search'] }}</b>
                    </span>
                @endif
                @if (!empty($requestParams['filter_box_number']))
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-200">
                        Caixa: <b class="ml-1">{{ $requestParams['filter_box_number'] }}</b>
                    </span>
                @endif
                @if (!empty($requestParams['filter_project_id']))
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-200">
                        Projeto: <b
                            class="ml-1">{{ $projects[$requestParams['filter_project_id']] ?? 'ID ' . $requestParams['filter_project_id'] }}</b>
                    </span>
                @endif
                @if (!empty($requestParams['filter_year']))
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-200">
                        Ano: <b class="ml-1">{{ $requestParams['filter_year'] }}</b>
                    </span>
                @endif
            </div>
        </div>

        <a href="{{ route('documents.index', ['sort_by' => $requestParams['sort_by'] ?? null, 'sort_dir' => $requestParams['sort_dir'] ?? null, 'per_page' => $requestParams['per_page'] ?? null]) }}"
            class="px-3 py-1 text-sm font-medium text-blue-800 dark:text-blue-200 bg-blue-100 dark:bg-gray-600 rounded-md hover:bg-blue-200 dark:hover:bg-gray-500 transition-colors duration-200 shrink-0">
            <i class="fas fa-times-circle mr-1"></i> Limpar Tudo
        </a>
    </div>
</div>
