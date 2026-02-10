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
                    const response = await fetch(`/documents/${documentId}/details`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
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
            requiresObservation: false,
            observation: '',
            observationError: false,

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
                this.requiresObservation = config.requiresObservation || false;
                this.observation = '';
                this.observationError = false;
            },
            close() {
                this.show = false;
                this.observation = '';
                this.observationError = false;
            },
            handleConfirm() {
                // Sincronizar CSRF token (importante para wire:navigate no Livewire 3/4)
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                if (this.requiresObservation && !this.observation.trim()) {
                    this.observationError = true;
                    return;
                }

                if (this.onConfirm && typeof this.onConfirm === 'function') {
                    this.onConfirm(this.observation);
                    this.close();
                    return;
                }

                if (this.submitFormId) {
                    const form = document.getElementById(this.submitFormId);
                    if (form) {
                        // Atualizar token do form específico se existir
                        const tokenInput = form.querySelector('input[name="_token"]');
                        if (tokenInput && csrfToken) tokenInput.value = csrfToken;

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

                        // Atualizar token do form do modal
                        const tokenInput = form.querySelector('input[name="_token"]');
                        if (tokenInput && csrfToken) tokenInput.value = csrfToken;

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
