import './bootstrap';
import Alpine from 'alpinejs';

// Função layout() APENAS para sidebar, dark mode e modal de DETALHES de Documento
function layout() {
    return {
        // --- Variáveis de Estado Globais ---
        sidebarOpen: localStorage.getItem('sidebarOpen') ? localStorage.getItem('sidebarOpen') === 'true' : true,
        darkMode: localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),

        // Estado do Modal de Detalhes do Documento
        showModal: false, // << Para detalhes do Documento
        selectedDocument: {},
        loadingModal: false,

        // REMOVIDO: showBoxImportModal (será controlado pelo Alpine.store)

        // --- Métodos ---
        init() {
            this.updateDarkModeClass();
            this.$watch('sidebarOpen', value => localStorage.setItem('sidebarOpen', value));
            this.$watch('darkMode', value => {
                localStorage.setItem('darkMode', value);
                this.updateDarkModeClass();
            });
            console.log('Alpine layout initialized.');
        },

        // Sidebar, Dark Mode (sem alterações)
        toggleSidebar() { this.sidebarOpen = !this.sidebarOpen; },
        toggleDarkMode() { this.darkMode = !this.darkMode; },
        updateDarkModeClass() {
            if (this.darkMode) { document.documentElement.classList.add('dark'); }
            else { document.documentElement.classList.remove('dark'); }
        },

        // Modal de Detalhes do Documento (sem alterações)
        async openDocumentModal(documentId) {
            if (!documentId) return;
            console.log(`Opening document details modal for ID: ${documentId}`);
            this.loadingModal = true;
            this.showModal = true; // Controla o modal de DETALHES
            this.selectedDocument = {};
            try {
                const response = await fetch(`/documents/${documentId}`);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const data = await response.json();
                // data.formatted_date = this.formatDisplayDate(data.document_date); // Se precisar formatar data MES/ANO
                this.selectedDocument = data;
                console.log('Document details data loaded:', this.selectedDocument);
            } catch (error) {
                console.error("Erro ao buscar detalhes do documento:", error);
                this.closeDocumentModal();
            } finally {
                this.loadingModal = false;
            }
        },
        closeDocumentModal() { // Fecha o modal de DETALHES
            console.log('Document details modal closed.');
            this.showModal = false;
            this.selectedDocument = {};
        },

        // REMOVIDO: openBoxImportModal()
        // REMOVIDO: closeBoxImportModal()

        // Função Auxiliar de Formatação de Data (Exemplo - opcional)
        // formatDisplayDate(dateStringMesAno) { /* ... */ },

    }; // Fim do return
} // Fim layout()

// Registrar o componente layout
document.addEventListener('alpine:init', () => {
    Alpine.data('layout', layout);
    console.log('Alpine layout data registered.');

    // *** REGISTRAR O STORE PARA MODAIS (Como no seu app.blade.php) ***
    // É melhor registrar stores aqui também para organização
    Alpine.store('modals', {
        showBoxImportModal: false, // Estado inicial
        // Outros modais podem ser adicionados aqui

        openBoxImportModal() {
            console.log('Opening box import modal via store.');
            this.showBoxImportModal = true;
        },
        closeBoxImportModal() {
            console.log('Closing box import modal via store.');
            this.showBoxImportModal = false;
        }
    });
    console.log('Alpine modals store registered.');

});

// Inicialização
window.Alpine = Alpine;
Alpine.start();
console.log('Alpine started.');