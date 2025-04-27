{{-- Em resources/views/documents/index.blade.php --}}
<x-app-layout>
    @section('title', 'Documentos')
    @section('header-title', 'Listagem de Documentos')

    {{-- Inclui o formulário de importação --}}
    @include('documents.import-form')

    {{-- Estatísticas --}}
    @isset($stats)
    <x-document-stats :stats="$stats" :hasActiveFilters="$hasActiveFilters ?? false" :totalDocuments="$stats['totalDocuments'] ?? 0" />
    @endisset

    {{-- Tabela de Documentos --}}
    <div class="mt-6 overflow-hidden bg-white rounded-lg shadow dark:bg-gray-800">
        <x-document-table :documents="$documents" :requestParams="$requestParams ?? []" />
        {{-- Paginação --}}
        @if ($documents->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
            {{ $documents->links() }}
        </div>
        @endif
    </div>

    {{-- O Modal está no layout principal app.blade.php --}}

</x-app-layout>