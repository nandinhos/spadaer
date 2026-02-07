import './bootstrap';
import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import collapse from '@alpinejs/collapse';

// Registrar Plugins
Alpine.plugin(focus);
Alpine.plugin(collapse);

// Função layout() para sidebar e dark mode
function layout() {
    return {
        sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false', // Default true
        darkMode: localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),

        init() {
            this.updateDarkModeClass();
            this.$watch('sidebarOpen', value => {
                localStorage.setItem('sidebarOpen', value);
                document.documentElement.classList.toggle('sidebar-collapsed', !value);
            });
            this.$watch('darkMode', value => {
                localStorage.setItem('darkMode', value);
                this.updateDarkModeClass();
            });
            console.log('Alpine layout initialized.');
        },

        toggleSidebar() { this.sidebarOpen = !this.sidebarOpen; },
        toggleDarkMode() { this.darkMode = !this.darkMode; },
        updateDarkModeClass() {
            if (this.darkMode) { document.documentElement.classList.add('dark'); }
            else { document.documentElement.classList.remove('dark'); }
        }
    };
}

// Global Store para Modais
document.addEventListener('alpine:init', () => {
    Alpine.data('layout', layout);

    Alpine.store('modals', {
        showBoxImportModal: false,
        selectedDocument: {
            id: null,
            document_number: '',
            title: '',
            document_date: '',
            version: '',
            confidentiality: '',
            box: { number: '' },
            project: { name: '' }
        },
        loadingDocument: false,
        showDocumentDetails: false,

        openBoxImportModal() { this.showBoxImportModal = true; },
        closeBoxImportModal() { this.showBoxImportModal = false; },

        async openDocumentDetails(documentId) {
            if (!documentId) return;
            this.loadingDocument = true;
            this.showDocumentDetails = true;
            // Limpar estado anterior com estrutura segura
            this.selectedDocument = { id: null, box: {}, project: {} };
            
            try {
                const response = await fetch(`/documents/${documentId}`);
                if (!response.ok) throw new Error('Falha na requisição');
                this.selectedDocument = await response.json();
            } catch (error) {
                console.error("Erro ao buscar detalhes:", error);
                this.showDocumentDetails = false;
            } finally {
                this.loadingDocument = false;
            }
        },
        closeDocumentDetails() {
            this.showDocumentDetails = false;
            // Reset seguro
            setTimeout(() => {
                this.selectedDocument = { id: null, box: {}, project: {} };
            }, 300);
        }
    });
});

window.Alpine = Alpine;
// Alpine.start(); // Gerenciado pelo Livewire 3
