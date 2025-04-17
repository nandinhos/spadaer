@props([
    'stats' => [],
    'hasActiveFilters' => false,
    'totalDocuments' => 0, // Passando o total geral explicitamente
])

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    {{-- Card Documentos --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border-l-4 border-primary">
        <div class="flex justify-between items-center">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                {{ $hasActiveFilters ? 'Documentos Filtrados' : 'Total de Documentos' }}
            </span>
            <span class="text-primary dark:text-primary-light"><i class="fas fa-file-alt"></i></span>
        </div>
        <div class="flex items-baseline mt-2"> {{-- Use items-baseline para alinhar textos --}}
            <p class="text-2xl font-bold">{{ number_format($stats['filteredDocumentsCount'] ?? 0) }}</p>
            @if($hasActiveFilters && $totalDocuments > 0)
                <p class="text-sm text-gray-500 dark:text-gray-400 ml-2">
                    de {{ number_format($totalDocuments) }}
                </p>
            @endif
        </div>
    </div>

    {{-- Card Caixas --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border-l-4 border-green-500">
        <div class="flex justify-between items-center">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                 {{ $hasActiveFilters ? 'Caixas Encontradas' : 'Total de Caixas' }} {{-- Label ajustado --}}
            </span>
            <span class="text-green-500"><i class="fas fa-box"></i></span>
        </div>
         <div class="flex items-baseline mt-2">
            <p class="text-2xl font-bold">{{ number_format($stats['filteredBoxesCount'] ?? 0) }}</p>
            @if($hasActiveFilters && ($stats['totalBoxes'] ?? 0) > 0)
                <p class="text-sm text-gray-500 dark:text-gray-400 ml-2">
                    de {{ number_format($stats['totalBoxes']) }}
                </p>
            @endif
        </div>
    </div>

    {{-- Card Projetos --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border-l-4 border-yellow-500">
        <div class="flex justify-between items-center">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                 {{ $hasActiveFilters ? 'Projetos Encontrados' : 'Total de Projetos' }} {{-- Label ajustado --}}
            </span>
            <span class="text-yellow-500"><i class="fas fa-project-diagram"></i></span>
        </div>
        <div class="flex items-baseline mt-2">
            <p class="text-2xl font-bold">{{ number_format($stats['filteredProjectsCount'] ?? 0) }}</p>
            @if($hasActiveFilters && ($stats['totalProjects'] ?? 0) > 0)
                <p class="text-sm text-gray-500 dark:text-gray-400 ml-2">
                    de {{ number_format($stats['totalProjects']) }}
                </p>
            @endif
        </div>
    </div>

     {{-- Card Intervalo de Anos (Lógica no Controller ou Model seria melhor) --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border-l-4 border-blue-500">
        <div class="flex justify-between items-center">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                Intervalo Anos (Docs) {{-- Label simplificado --}}
            </span>
            <span class="text-blue-500"><i class="fas fa-calendar-alt"></i></span>
        </div>
        <div class="flex items-end mt-2">
            {{-- Idealmente, calcular min/max ano no backend --}}
            <p class="text-2xl font-bold">{{ $stats['yearRange'] ?? '—' }}</p>
        </div>
    </div>
</div>