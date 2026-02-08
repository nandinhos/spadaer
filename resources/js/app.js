import './bootstrap';
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

// Função para registrar todos os stores globais
function registerStores(Alpine) {
    if (!Alpine.store('modals')) {
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
                    if (!response.ok) throw new Error('Falha na requisicao');
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
            },
        });
    }

    if (!Alpine.store('confirmDelete')) {
        Alpine.store('confirmDelete', {
            show: false,
            title: 'Confirmar Exclusao',
            message: 'Tem certeza que deseja excluir este item? Esta acao nao pode ser desfeita.',
            action: '',
            method: 'DELETE',
            submitFormId: null,
            onConfirm: null,
            confirmText: 'Excluir',
            cancelText: 'Cancelar',

            open(config) {
                this.show = true;
                this.title = config.title || 'Confirmar Exclusao';
                this.message = config.message || 'Tem certeza que deseja excluir este item?';
                this.action = config.action || '';
                this.method = config.method || 'DELETE';
                this.submitFormId = config.submitFormId || null;
                this.onConfirm = config.onConfirm || null;
                this.confirmText = config.confirmText || 'Excluir';
                this.cancelText = config.cancelText || 'Cancelar';
            },
            close() {
                this.show = false;
            },
            handleConfirm() {
                if (this.onConfirm && typeof this.onConfirm === 'function') {
                    this.onConfirm();
                    this.close();
                    return;
                }

                if (this.submitFormId) {
                    const form = document.getElementById(this.submitFormId);
                    if (form) {
                        form.submit();
                        this.close();
                        return;
                    }
                }

                // Submeter o form interno do modal (exclusao via action URL)
                if (this.action) {
                    const form = document.getElementById('confirm-delete-form');
                    if (form) {
                        form.action = this.action;
                        // Garantir que o _method esta correto
                        const methodInput = form.querySelector('input[name="_method"]');
                        if (methodInput) {
                            methodInput.value = this.method || 'DELETE';
                        }
                        form.submit();
                    }
                }
            }
        });
    }
}

// Registrar stores antes do Alpine inicializar (primeira carga)
registerStores(Alpine);

// Re-registrar stores apos navegacao wire:navigate (quando Alpine reinicializa)
document.addEventListener('livewire:navigated', () => {
    registerStores(Alpine);
});

// Iniciar Livewire (que tambem inicia o Alpine)
Livewire.start();
