{{-- Componente Modal Premium --}}
<div 
    x-data 
    x-show="$store.modals.showDocumentDetails" 
    x-trap.noscroll="$store.modals.showDocumentDetails"
    x-on:keydown.escape.window="$store.modals.closeDocumentDetails()" 
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="modal-title" 
    role="dialog" 
    aria-modal="true" 
    x-cloak
>
    @can('documents.view')
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:items-center sm:block sm:p-0">
        {{-- Background overlay --}}
        <div 
            x-show="$store.modals.showDocumentDetails" 
            x-transition:enter="ease-out duration-300" 
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" 
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" 
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 transition-opacity bg-gray-900/75 backdrop-blur-sm"
            x-on:click="$store.modals.closeDocumentDetails()" 
            aria-hidden="true"
        ></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        {{-- Conteúdo do Modal --}}
        <div
            x-show="$store.modals.showDocumentDetails"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block w-full overflow-hidden text-left align-bottom transition-all transform bg-white rounded-xl shadow-2xl dark:bg-gray-800 sm:my-8 sm:align-middle sm:max-w-2xl"
        >
            {{-- Header Premium --}}
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-primary/10 text-primary">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100" id="modal-title">
                                Detalhes do Documento
                            </h3>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"
                               x-text="$store.modals.selectedDocument?.document_number || 'Carregando...'">
                            </p>
                        </div>
                    </div>
                    <button
                        x-on:click="$store.modals.closeDocumentDetails()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors"
                    >
                        <i class="fas fa-times fa-lg"></i>
                    </button>
                </div>
            </div>

            {{-- Corpo --}}
            <div class="px-6 py-6">
                <div x-show="$store.modals.loadingDocument" class="flex flex-col items-center justify-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
                    <p class="mt-4 text-sm text-gray-500">Recuperando informações...</p>
                </div>

                <div x-show="!$store.modals.loadingDocument && $store.modals.selectedDocument?.id" x-transition>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Seção de Identificação --}}
                        <div class="space-y-4">
                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Título</label>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-1" x-text="$store.modals.selectedDocument?.title || '---'"></p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Projeto</label>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-1" x-text="$store.modals.selectedDocument?.project?.name || 'Não associado'"></p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Data do Documento</label>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-1" x-text="$store.modals.selectedDocument?.document_date || '---'"></p>
                            </div>
                        </div>

                        {{-- Seção Técnica --}}
                        <div class="space-y-4">
                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Localização</label>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-1">
                                    Caixa <span x-text="$store.modals.selectedDocument?.box?.number || '---'"></span> / Item <span x-text="$store.modals.selectedDocument?.item_number || '---'"></span>
                                </p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Sigilo</label>
                                <div class="mt-1">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border"
                                        :class="{
                                            'bg-red-50 text-red-700 border-red-100 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800': $store.modals.selectedDocument?.confidentiality === 'Confidencial',
                                            'bg-yellow-50 text-yellow-700 border-yellow-100 dark:bg-yellow-900/20 dark:text-yellow-400 dark:border-yellow-800': $store.modals.selectedDocument?.confidentiality === 'Restrito',
                                            'bg-green-50 text-green-700 border-green-100 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800': $store.modals.selectedDocument?.confidentiality === 'Público'
                                        }"
                                        x-text="$store.modals.selectedDocument?.confidentiality || 'Não definido'">
                                    </span>
                                </div>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Versão / Cópia</label>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-1">
                                    v<span x-text="$store.modals.selectedDocument?.version || '1.0'"></span> — 
                                    <span x-text="$store.modals.selectedDocument?.is_copy ? 'Cópia' : 'Original'"></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Premium --}}
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row-reverse gap-3">
                <x-secondary-button x-on:click="$store.modals.closeDocumentDetails()" class="w-full sm:w-auto justify-center">
                    Fechar
                </x-secondary-button>

                @can('documents.edit')
                    <x-primary-button 
                        x-show="$store.modals.selectedDocument?.id"
                        x-on:click="window.location.href='/documents/' + $store.modals.selectedDocument?.id + '/edit'"
                        class="w-full sm:w-auto justify-center"
                    >
                        <i class="fas fa-edit mr-2"></i>Editar
                    </x-primary-button>
                @endcan
            </div>
        </div>
    </div>
    @else
        <div class="p-4 m-4 text-sm text-red-700 bg-red-100 rounded-lg shadow-sm" role="alert" x-cloak>
            <i class="fas fa-exclamation-triangle mr-2"></i>Acesso negado para visualização de documentos.
        </div>
    @endcan
</div>