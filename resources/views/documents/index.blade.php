{{-- Em resources/views/documents/index.blade.php --}}
<x-app-layout>
    @section('title', 'Documentos')
    @section('header-title', 'Listagem de Documentos')

    {{-- Verificação principal de permissão para visualizar a página --}}
    @can('documents.view')
        {{-- Inicializa o Alpine.js para gerenciar o estado do modal de detalhes do documento --}}
        {{-- Todo o conteúdo que interage com o modal (botões de abrir, o modal em si)
             precisa estar dentro deste escopo do x-data. --}}
        <div x-data="documentViewer()">

            {{-- Estatísticas --}}
            @isset($stats)
                <x-document-stats :stats="$stats" :hasActiveFilters="$hasActiveFilters ?? false" :totalDocuments="$stats['totalDocuments'] ?? 0" />
            @endisset

            {{-- Formulário de importação (protegido por permissão) --}}
            @can('documents.import')
                @include('documents.import-form')
            @endcan

            @can('documents.export') {{-- Ou a permissão que você usa para exportar --}}
    <a href="{{ route('documents.export') }}" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
        Exportar Excel
    </a>
    <a href="{{ route('documents.export.pdf') }}" class="px-4 py-2 ml-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
        Exportar PDF
    </a>
@endcan

            {{-- Tabela de Documentos --}}
            {{-- O componente x-document-table precisa que seus botões "Ver" chamem openDocumentModal(documentId) --}}
            {{-- Exemplo de como o botão "Ver" DENTRO de x-document-table poderia ser:
                 <button type="button" @click="openDocumentModal({{ $document->id }})">Ver</button>
            --}}
            <div class="mt-6 overflow-hidden bg-white rounded-lg shadow dark:bg-gray-800">
                <x-document-table :documents="$documents" :requestParams="$requestParams ?? []" />
                {{-- Paginação --}}
                @if ($documents->hasPages())
                    <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 dark:bg-gray-700 dark:border-gray-600">
                        {{ $documents->links() }}
                    </div>
                @endif
            </div>

            {{-- Inclusão do Componente Modal --}}
            {{-- As variáveis showModal, loadingModal, selectedDocument e as funções
                 openDocumentModal, closeModal são fornecidas pelo x-data="documentViewer()" --}}
            <x-document-modal />

        </div> {{-- Fim do div x-data="documentViewer()" --}}

    @else
        {{-- Mensagem para usuários sem permissão de visualização --}}
        <div class="p-4 mt-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-900 dark:text-red-300" role="alert">
            <p class="font-medium">Acesso Negado</p>
            <p>Você não tem permissão para visualizar documentos.</p>
        </div>
    @endcan

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
                    this.selectedDocument = { id: null, document_number: 'Falha ao carregar' };
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