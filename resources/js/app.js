// Exemplo em resources/js/app.js (ou importado)
import Alpine from 'alpinejs';

window.Alpine = Alpine;

function documentSystem(initialData) { // Recebe dados do Blade
    return {
        // --- Estado ---
        searchTerm: '',
        showFilters: false,
        sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false', // Mantém lógica do localStorage
        darkMode: Alpine.$store.darkMode.on, // Usa o store do Breeze se disponível, ou sua lógica original
        filters: {
            caixa: '',
            projeto: '',
            ano: ''
        },
        sortColumn: 'caixa',
        sortDirection: 'asc',
        currentPage: 1,
        itemsPerPage: 10, // Ou buscar do backend
        showModal: false,
        selectedDocument: {},

        // --- Dados ---
        documentos: initialData.documents || [],      // Dados originais vindos do Laravel
        projetos: initialData.projects || [],        // Projetos para o filtro
        anos: initialData.years || [],            // Anos para o filtro
        filteredDocuments: [],                   // Array para os documentos filtrados/ordenados

        // --- Colunas (pode manter ou buscar do backend) ---
        columns: [
            { key: 'caixa', label: 'Caixa' },
            { key: 'item', label: 'Item' },
            { key: 'codigo', label: 'Código' },
            { key: 'descritor', label: 'Descritor' },
            { key: 'numero', label: 'Número' },
            { key: 'titulo', label: 'Título' },
            { key: 'data', label: 'Data' },
            { key: 'sigilo', label: 'Sigilo' },
            { key: 'versao', label: 'Versão' },
            { key: 'copia', label: 'Cópia' }
        ],

        // --- Métodos ---
        init() {
            // Inicializar dark mode (pode usar o Alpine store do Breeze)
            // Se não usar o store do Breeze:
            // this.darkMode = localStorage.getItem('darkMode') === 'true' || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
            // this.updateDarkMode();

            // Inicializar dados filtrados
            this.applyFilters(); // Aplicar filtros e ordenação inicial

            // Watch para dark mode do sistema (se não usar store)
            // window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => { ... });

            // Watch para mudanças no store dark mode do Breeze (se aplicável)
            // this.$watch('$store.darkMode.on', value => this.darkMode = value);
        },

        toggleDarkMode() {
            // Se usar store do Breeze:
             Alpine.$store.darkMode.toggle();
            // Se usar sua lógica:
            // this.darkMode = !this.darkMode;
            // localStorage.setItem('darkMode', this.darkMode);
            // this.updateDarkMode();
        },

        updateDarkMode() { // Necessário apenas se não usar o store do Breeze
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        },

        toggleSidebar() { // Manter lógica do localStorage
            this.sidebarOpen = !this.sidebarOpen;
            localStorage.setItem('sidebarOpen', this.sidebarOpen);
        },

        // ... (copiar/adaptar os outros métodos:
        // totalPages, paginatedDocuments, hasActiveFilters,
        // getTotalCaixas, getFilteredCaixas, getFilteredProjetos,
        // getYearRange, searchDocuments, applyFilters, resetFilters,
        // sortBy, sortDocuments, openDocumentModal, formatDate, getVisiblePages)

        // AJUSTE PRINCIPAL em applyFilters:
        applyFilters() {
            this.currentPage = 1; // Reset page on filter change

            let tempDocs = [...this.documentos]; // Start with all documents

            // Filter by advanced filters
            tempDocs = tempDocs.filter(doc => {
                const caixaMatch = !this.filters.caixa ||
                    doc.caixa?.toLowerCase().includes(this.filters.caixa.toLowerCase()); // Use optional chaining ?.

                const projetoMatch = !this.filters.projeto ||
                    doc.projeto === this.filters.projeto;

                const anoMatch = !this.filters.ano ||
                    (doc.data && new Date(doc.data).getFullYear().toString() === this.filters.ano); // Check if data exists

                return caixaMatch && projetoMatch && anoMatch;
            });

            // Filter by search term
            if (this.searchTerm) {
                const lowerSearchTerm = this.searchTerm.toLowerCase();
                tempDocs = tempDocs.filter(doc =>
                    Object.values(doc).some(value =>
                        value && value.toString().toLowerCase().includes(lowerSearchTerm)
                    )
                );
            }

            this.filteredDocuments = tempDocs;
            this.sortDocuments(); // Apply sorting after filtering
        },

         // AJUSTE em formatDate para evitar erros com datas inválidas/null
        formatDate(dateString) {
            if (!dateString) return 'N/A'; // Ou string vazia
            try {
                // Adicionar 'T00:00:00' para garantir que seja interpretado como data local
                const date = new Date(dateString + 'T00:00:00');
                // Verificar se a data é válida
                if (isNaN(date.getTime())) {
                    return 'Data inválida';
                }
                return date.toLocaleDateString('pt-BR');
            } catch (e) {
                console.error("Erro ao formatar data:", dateString, e);
                return 'Erro';
            }
        },


        // ... (restante dos métodos adaptados)
    };
}

// Registrar o componente Alpine
Alpine.data('documentSystem', documentSystem);

// Iniciar Alpine
Alpine.start();

// Se Breeze não tiver Dark Mode Store, adicione o seu:
// Alpine.store('darkMode', {
//     on: localStorage.getItem('darkMode') === 'true',
//     toggle() {
//         this.on = !this.on;
//         localStorage.setItem('darkMode', this.on);
//         document.documentElement.classList.toggle('dark', this.on);
//     }
// });
// document.documentElement.classList.toggle('dark', Alpine.store('darkMode').on);