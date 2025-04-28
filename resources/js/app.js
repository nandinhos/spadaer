import './bootstrap'; // Inclui dependências como Axios

import Alpine from 'alpinejs';

// Sua função layout() que define o estado e métodos globais
function layout() {
    return {
        // --- Variáveis de estado para modais e layout ---
        sidebarOpen: localStorage.getItem('sidebarOpen') ? localStorage.getItem('sidebarOpen') === 'true' : true,
        darkMode: localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),

        // Estado para o modal de detalhes de Documento
        showModal: false, // <-- Já existe
        selectedDocument: {}, // <-- Já existe
        loadingModal: false, // <-- Já existe

        // *** NOVO: Estado para o modal de importação de Caixa ***
        showBoxImportModal: false, // Inicializa como false

        // --- Métodos ---
        init() {
            this.updateDarkModeClass();
            // ... outros watchers ...
            this.$watch('sidebarOpen', value => localStorage.setItem('sidebarOpen', value));
            this.$watch('darkMode', value => {
                localStorage.setItem('darkMode', value);
                this.updateDarkModeClass();
            });
            console.log('Alpine layout initialized.');
        },

        toggleSidebar() { this.sidebarOpen = !this.sidebarOpen; },
        toggleDarkMode() { this.darkMode = !this.darkMode; },
        updateDarkModeClass() {
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        },

        // --- Métodos para o modal de detalhes de Documento ---
        async openDocumentModal(documentId) {
            if (!documentId) return;
            console.log(`Opening document details modal for ID: ${documentId}`);
            this.loadingModal = true;
            this.showModal = true; // Abre o modal de DETALHES
            this.selectedDocument = {};
            try {
                const response = await fetch(`/documents/${documentId}`);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const data = await response.json();
                data.formatted_date = this.formatDate(data.document_date);
                this.selectedDocument = data;
                console.log('Document details data loaded:', this.selectedDocument);
            } catch (error) {
                console.error("Error fetching document details:", error);
                this.closeModal(); // Fecha o modal de DETALHES em caso de erro
            } finally {
                this.loadingModal = false;
            }
        },
        closeModal() { // Fecha o modal de detalhes de Documento
            console.log('Document details modal closed.');
            this.showModal = false;
            this.selectedDocument = {};
        },

        // *** NOVO: Métodos para o modal de importação de Caixa ***
        openBoxImportModal() {
            console.log('Opening box import modal.');
            this.showBoxImportModal = true; // Abre o modal de IMPORTAÇÃO
            // Opcional: Aqui você pode resetar campos de formulário dentro do modal se necessário
        },
        closeBoxImportModal() {
            console.log('Box import modal closed.');
            this.showBoxImportModal = false; // Fecha o modal de IMPORTAÇÃO
            // Opcional: Aqui você pode limpar campos de formulário dentro do modal se necessário
        },

        // --- Métodos Auxiliares ---
        formatDate(dateString) {
            if (!dateString) return 'N/D';
            try {
                const date = new Date(dateString + 'T00:00:00');
                if (isNaN(date.getTime())) return 'Data inválida';
                return date.toLocaleDateString('pt-BR');
            } catch (e) { return 'Data inválida'; }
        },

        // ... outras funções auxiliares se houver ...
    }
}

// Registrar o componente Alpine globalmente
document.addEventListener('alpine:init', () => {
    Alpine.data('layout', layout);
    console.log('Alpine layout data registered.');
});


// Tornar Alpine globalmente acessível (o Breeze faz isso)
window.Alpine = Alpine;

// Iniciar Alpine
Alpine.start();

console.log('Alpine started.');