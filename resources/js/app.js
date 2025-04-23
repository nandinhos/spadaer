import './bootstrap'; // Inclui dependências como Axios

import Alpine from 'alpinejs';

// Sua função layout() que define o estado e métodos globais
function layout() {
    return {
        // Variáveis de estado
        sidebarOpen: localStorage.getItem('sidebarOpen') ? localStorage.getItem('sidebarOpen') === 'true' : true,
        darkMode: localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
        showModal: false,
        selectedDocument: {},
        loadingModal: false,

        // Métodos (init, toggleSidebar, toggleDarkMode, openDocumentModal, closeModal, formatDate...)
        init() {
            this.updateDarkModeClass(); // Aplica classe inicial
            // ... outros watchers e listeners ...
            this.$watch('sidebarOpen', value => localStorage.setItem('sidebarOpen', value));
            this.$watch('darkMode', value => {
                localStorage.setItem('darkMode', value);
                this.updateDarkModeClass();
            });
            console.log('Alpine layout initialized.'); // Mensagem de teste
        },
        toggleSidebar() { this.sidebarOpen = !this.sidebarOpen; /* ... */ },
        toggleDarkMode() { this.darkMode = !this.darkMode; /* ... */ },
        updateDarkModeClass() { /* ... lógica para adicionar/remover classe 'dark' no <html> ... */
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        },
        async openDocumentModal(documentId) { /* ... lógica fetch AJAX ... */
            if (!documentId) return;
            console.log(`Opening modal for document ID: ${documentId}`); // Teste
            this.loadingModal = true;
            this.showModal = true;
            this.selectedDocument = {};
            try {
                const response = await fetch(`/documents/${documentId}`);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const data = await response.json();
                data.formatted_date = this.formatDate(data.document_date);
                this.selectedDocument = data;
                console.log('Document data loaded:', this.selectedDocument); // Teste
            } catch (error) {
                console.error("Erro ao buscar detalhes do documento:", error);
                this.closeModal();
            } finally {
                this.loadingModal = false;
            }
        },
        closeModal() { this.showModal = false; this.selectedDocument = {}; console.log('Modal closed.'); }, // Teste
        formatDate(dateString) { /* ... lógica de formatação ... */
            if (!dateString) return 'N/D';
            try {
                const date = new Date(dateString + 'T00:00:00');
                if (isNaN(date.getTime())) return 'Data inválida';
                return date.toLocaleDateString('pt-BR');
            } catch (e) { return 'Data inválida'; }
        },
        // ... outras funções ...
    }
}

// Registrar o componente Alpine globalmente
document.addEventListener('alpine:init', () => {
    Alpine.data('layout', layout);
    console.log('Alpine layout data registered.'); // Mensagem de teste
});


// Tornar Alpine globalmente acessível (o Breeze faz isso)
window.Alpine = Alpine;

// Iniciar Alpine
Alpine.start();

console.log('Alpine started.'); // Mensagem de teste