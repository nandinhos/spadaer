{{-- Em resources/views/documents/index.blade.php --}}
<x-app-layout>
    @section('title', 'Documentos')
    @section('header-title', 'Listagem de Documentos')

    <div x-data="{ documentViewer: documentViewer(), showFilters: {{ request()->hasAny(['filter_box_number', 'filter_project_id', 'filter_year']) ? 'true' : 'false' }} }">

        {{-- Estatísticas --}}
        @isset($stats)
            <x-document-stats :stats="$stats" :hasActiveFilters="$hasActiveFilters" :totalDocuments="$stats['totalDocuments'] ?? 0" />
        @endisset

        {{-- Exibe o painel de filtros ativos se houver algum --}}
        @if ($hasActiveFilters)
            <x-active-filters :requestParams="$requestParams" :projects="$availableProjects" />
        @endif

        {{-- NOVO PAINEL DE FILTROS AVANÇADOS --}}
        <x-document-filters :projects="$availableProjects" :years="$availableYears" :requestParams="$requestParams" />

        {{-- Formulário de importação --}}
        @can('documents.import')
            @include('documents.import-form')
        @endcan

        {{-- Tabela de Documentos (MANTÉM A BUSCA GERAL AQUI DENTRO) --}}
        <div class="mt-6 overflow-hidden bg-white rounded-lg shadow dark:bg-gray-800">
            <x-document-table :documents="$documents" :requestParams="$requestParams ?? []" />
            @if ($documents->hasPages())
                <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 dark:bg-gray-700 dark:border-gray-600">
                    {{ $documents->links() }}
                </div>
            @endif
        </div>

        {{-- Modal de Detalhes --}}
        <div @document-open.window="documentViewer.openDocumentModal($event.detail.id)">
            <x-document-modal />
        </div>
    </div>



    {{-- O script Alpine.js é colocado no final, idealmente antes do fechamento do body ou em um @push('scripts') se seu layout tiver um @stack('scripts') --}}
    @push('scripts')
        <script>
            function documentViewer() {
                return {
                    showModal: false,
                    loadingModal: false,
                    selectedDocument: {},

                    openDocumentModal(documentId) {
                        if (!documentId) {
                            console.error('ID do documento não fornecido para openDocumentModal.');
                            return;
                        }
                        this.showModal = true;
                        this.loadingModal = true;
                        this.selectedDocument = {};

                        // ATUALIZE ESTA URL para usar a nova rota web que retorna JSON
                        fetch(`/documents/${documentId}/details`) // << MUDANÇA AQUI
                            .then(response => {
                                if (!response.ok) {
                                    console.error(`Erro HTTP ${response.status} ao buscar o documento.`);
                                    return response.json().then(errData => {
                                        // Tenta pegar uma mensagem de erro da resposta JSON, se houver
                                        throw new Error(errData.message || `Erro ${response.status}`);
                                    });
                                }
                                return response.json();
                            })
                            .then(data => {
                                // Como o controller agora retorna o objeto do documento diretamente:
                                this.selectedDocument = data;
                                // Se você mudou o controller para retornar {'document': data}, então use:
                                // this.selectedDocument = data.document;
                                this.loadingModal = false;
                            })
                            .catch(error => {
                                console.error('Falha ao carregar detalhes do documento:', error);
                                this.loadingModal = false;
                                this.selectedDocument = {
                                    id: null,
                                    document_number: 'Falha ao carregar'
                                };
                                // Você pode querer mostrar uma mensagem de erro mais específica aqui, se `error.message` for útil.
                            });
                    },

                    closeModal() {
                        this.showModal = false;
                    }
                };
            }
        </script>
    @endpush

</x-app-layout>
