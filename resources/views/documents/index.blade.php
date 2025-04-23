<x-app-layout> {{-- Usa o layout app.blade.php --}}
    @section('title', 'Documentos') {{-- Define o título da página --}}
    @section('header-title', 'Listagem de Documentos') {{-- Define o título do header --}}

    {{-- Filtros Avançados (Se você tiver um componente para isso) --}}
    {{-- <x-document-filters :projects="$availableProjects" :years="$availableYears" :requestParams="$requestParams" /> --}}
    {{-- Ou coloque o HTML dos filtros aqui diretamente --}}

    {{-- Filtros Ativos (Se tiver o componente) --}}
    {{-- @if ($hasActiveFilters)
        <x-active-filters :requestParams="$requestParams" />
    @endif --}}

    {{-- Estatísticas --}}
    {{-- Garanta que $stats e $hasActiveFilters são passados pelo controller --}}
    @isset($stats)
        <x-document-stats :stats="$stats" :hasActiveFilters="$hasActiveFilters ?? false" :totalDocuments="$stats['totalDocuments'] ?? 0" />
    @endisset

    {{-- Formulário de Importação (se existir) --}}
    {{-- @include('documents.import-form') --}}

    {{-- Tabela de Documentos --}}
    <div class="mt-6 overflow-hidden bg-white rounded-lg shadow dark:bg-gray-800">
        {{-- Passe as variáveis necessárias para o componente da tabela --}}
        <x-document-table :documents="$documents" :requestParams="$requestParams ?? []" />

        {{-- Paginação --}}
        @if ($documents->hasPages())
            {{-- Mostra paginação apenas se houver mais de uma página --}}
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                {{ $documents->links() }}
            </div>
        @endif
    </div>

    {{--
        O Modal NÃO vai aqui. Ele está no app.blade.php.
        O Alpine global (layout()) cuidará de abri-lo
        quando um botão com @click="openDocumentModal(...)" for clicado.
    --}}
    {{-- <x-document-modal /> --}} {{-- NÃO É NECESSÁRIO AQUI --}}

    {{-- REMOVIDO o @push('scripts') daqui --}}

</x-app-layout>
