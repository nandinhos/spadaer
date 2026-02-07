@props([
    'stats' => [],
    'hasActiveFilters' => false,
    'totalDocuments' => 0,
])

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    {{-- Card Documentos --}}
    <x-stat-card 
        label="{{ $hasActiveFilters ? 'Documentos Filtrados' : 'Total de Documentos' }}"
        value="{{ number_format($hasActiveFilters ? ($stats['filteredDocumentsCount'] ?? 0) : ($stats['totalDocuments'] ?? 0)) }}"
        subvalue="{{ $hasActiveFilters ? 'de ' . number_format($stats['totalDocuments'] ?? 0) : '' }}"
        icon="fa-file-invoice"
        color="primary"
    />

    {{-- Card Caixas --}}
    <x-stat-card 
        label="{{ $hasActiveFilters ? 'Caixas Encontradas' : 'Total de Caixas' }}"
        value="{{ number_format($hasActiveFilters ? ($stats['filteredBoxesCount'] ?? 0) : ($stats['totalBoxes'] ?? 0)) }}"
        subvalue="{{ $hasActiveFilters ? 'de ' . number_format($stats['totalBoxes'] ?? 0) : '' }}"
        icon="fa-box-archive"
        color="green"
    />

    {{-- Card Projetos --}}
    <x-stat-card 
        label="{{ $hasActiveFilters ? 'Projetos Encontrados' : 'Total de Projetos' }}"
        value="{{ number_format($hasActiveFilters ? ($stats['filteredProjectsCount'] ?? 0) : ($stats['totalProjects'] ?? 0)) }}"
        subvalue="{{ $hasActiveFilters ? 'de ' . number_format($stats['totalProjects'] ?? 0) : '' }}"
        icon="fa-diagram-project"
        color="amber"
    />

    {{-- Card Intervalo de Anos --}}
    <x-stat-card 
        label="Cobertura Temporal"
        value="{{ $stats['yearRange'] ?? '--' }}"
        subvalue="Anos mapeados"
        icon="fa-calendar-range"
        color="blue"
    />
</div>