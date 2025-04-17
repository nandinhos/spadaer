<x-app-layout> {{-- Usa o layout app.blade.php --}}
    @section('title', 'Documentos') {{-- Define o título da página --}}
    @section('header-title', 'Listagem de Documentos') {{-- Define o título do header --}}

    {{-- O Alpine 'documentSystem' agora pode estar no layout ou aqui, se for específico --}}
    {{-- Se 'layout()' já tem as funções do modal, não precisa de outro x-data --}}
    {{-- <div x-data="documentPage()"> --}}

        {{-- Filtros --}}
        <x-document-filters
            :projects="$availableProjects"
            :years="$availableYears"
            :requestParams="$requestParams"
        />

         {{-- Filtros Ativos --}}
         @if($hasActiveFilters)
            <x-active-filters :requestParams="$requestParams" />
         @endif

        {{-- Estatísticas --}}
         <x-document-stats :stats="$stats" :hasActiveFilters="$hasActiveFilters" :totalDocuments="$stats['totalDocuments']" />

         {{-- Tabela de Documentos --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mt-6 overflow-hidden">
            <x-document-table
                :documents="$documents"
                :requestParams="$requestParams"
            />

             {{-- Paginação --}}
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                 {{-- Renderiza os links de paginação estilizados pelo Tailwind (default do Laravel) --}}
                 {{ $documents->links() }}
            </div>
        </div>

    {{-- Inclui o componente do modal --}}
    <x-document-modal />

    {{-- </div> --}}

    {{-- Adiciona o script Alpine específico da página ou do layout --}}
    @push('scripts')
    <script>
        // Funções Alpine que sobraram (principalmente UI)
        function layout() { // Renomeado de documentSystem para clareza
            return {
                // Estado inicial baseado no localStorage ou preferência do sistema
                sidebarOpen: localStorage.getItem('sidebarOpen') ? localStorage.getItem('sidebarOpen') === 'true' : true, // Default open
                darkMode: localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
                showFilters: false, // Para o acordeão de filtros
                showModal: false,
                selectedDocument: {}, // Para o modal
                loadingModal: false, // Estado de carregamento do modal

                init() {
                    this.updateDarkModeClass(); // Aplica a classe no início
                    // Ouve mudanças no sistema operacional
                    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                        if (!('darkMode' in localStorage)) { // Só muda se não houver preferência salva
                            this.darkMode = e.matches;
                            this.updateDarkModeClass();
                        }
                    });

                     // Persiste estado da sidebar
                     this.$watch('sidebarOpen', value => localStorage.setItem('sidebarOpen', value));
                     // Persiste estado do dark mode
                     this.$watch('darkMode', value => {
                         localStorage.setItem('darkMode', value);
                         this.updateDarkModeClass();
                     });
                },

                toggleSidebar() {
                    this.sidebarOpen = !this.sidebarOpen;
                },

                toggleDarkMode() {
                    this.darkMode = !this.darkMode;
                    // A persistência e atualização da classe ocorrem no $watch
                },

                updateDarkModeClass() {
                    if (this.darkMode) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                },

                // Função para abrir o modal, agora busca dados via AJAX
                async openDocumentModal(documentId) {
                    if (!documentId) return;
                    this.loadingModal = true;
                    this.showModal = true;
                    this.selectedDocument = {}; // Limpa dados anteriores

                    try {
                        const response = await fetch(`/documents/${documentId}`); // Usa a rota definida
                        if (!response.ok) {
                            throw new Error('Falha ao buscar detalhes do documento.');
                        }
                        const data = await response.json();
                        // Formata a data antes de exibir (ou faz no backend/model accessor)
                        data.formatted_date = this.formatDate(data.document_date);
                        this.selectedDocument = data;
                    } catch (error) {
                        console.error("Erro no modal:", error);
                        // Exibir mensagem de erro para o usuário, talvez
                        this.closeModal(); // Fecha o modal em caso de erro
                    } finally {
                        this.loadingModal = false;
                    }
                },

                closeModal() {
                    this.showModal = false;
                    this.selectedDocument = {}; // Limpa ao fechar
                },

                // Formatar data (pode ser feito no Blade com Carbon também)
                formatDate(dateString) {
                    if (!dateString) return 'N/D';
                    try {
                        // Adiciona 'T00:00:00' para garantir que a data seja interpretada corretamente
                        // independentemente do fuso horário local ao criar o objeto Date.
                        const date = new Date(dateString + 'T00:00:00');
                        // Verifica se a data é válida
                        if (isNaN(date.getTime())) {
                             return 'Data inválida';
                        }
                        return date.toLocaleDateString('pt-BR');
                    } catch (e) {
                        console.error("Erro ao formatar data:", dateString, e);
                        return 'Data inválida';
                    }
                },

                // Helper para gerar URL de ordenação
                getSortUrl(columnKey) {
                    const currentUrl = new URL(window.location.href);
                    const currentSortBy = currentUrl.searchParams.get('sort_by') || 'box_number';
                    const currentSortDir = currentUrl.searchParams.get('sort_dir') || 'asc';

                    let newSortDir = 'asc';
                    if (currentSortBy === columnKey && currentSortDir === 'asc') {
                        newSortDir = 'desc';
                    }

                    currentUrl.searchParams.set('sort_by', columnKey);
                    currentUrl.searchParams.set('sort_dir', newSortDir);
                    // Resetar para a primeira página ao ordenar
                    currentUrl.searchParams.delete('page');

                    return currentUrl.toString();
                },

                // Helper para limpar filtros (redireciona para a URL base)
                getClearFiltersUrl() {
                     return "{{ route('documents.index') }}"; // Gera a URL base da listagem
                }

            }
        }

        // Inicializa Alpine se ainda não foi inicializado pelo app.js
        // document.addEventListener('alpine:init', () => {
        //     Alpine.data('layout', layout);
        // });

    </script>
    @endpush

</x-app-layout>