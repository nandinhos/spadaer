import './bootstrap';
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

// Plugins focus e collapse já estão incluídos no bundle do Livewire 4

// Global Store para Modais
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
        setTimeout(() => {
            this.selectedDocument = { id: null, box: {}, project: {} };
        }, 300);
    }
});

// Iniciar Livewire (que também inicia o Alpine)
Livewire.start();
