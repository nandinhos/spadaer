{{-- Este componente é controlado inteiramente pelo Alpine definido no layout ou na view --}}
<div
    x-show="showModal"
    @keydown.escape.window="closeModal()"
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
    style="display: none;" {{-- Inicialmente oculto, Alpine controla --}}
>
    <div class="flex items-end sm:items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        {{-- Background overlay --}}
        <div
            x-show="showModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-80 transition-opacity"
            @click="closeModal()" {{-- Fecha ao clicar fora --}}
            aria-hidden="true"
        ></div>

        {{-- Centraliza o conteúdo do modal verticalmente --}}
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>

        {{-- Conteúdo do Modal --}}
        <div
            x-show="showModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
             @click.outside="closeModal()" {{-- Garante fechar ao clicar fora, mas não dentro --}}
        >
            {{-- Header do Modal --}}
            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-200 dark:border-gray-700">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-primary-light bg-opacity-20 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-file-alt text-primary text-lg"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                            Detalhes do Documento
                        </h3>
                         {{-- Subtítulo opcional com ID ou número --}}
                         <p class="text-sm text-gray-500 dark:text-gray-400" x-text="selectedDocument.document_number ? 'Documento: ' + selectedDocument.document_number : 'Carregando...'"></p>
                    </div>
                     {{-- Botão de fechar no header --}}
                     <button @click="closeModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                         <span class="sr-only">Fechar</span>
                         <i class="fas fa-times fa-lg"></i>
                     </button>
                </div>
            </div>

            {{-- Corpo do Modal --}}
            <div class="px-4 py-5 sm:p-6">
                {{-- Estado de Carregamento --}}
                 <div x-show="loadingModal" class="flex justify-center items-center h-40">
                     <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                     <span class="ml-2 text-gray-600 dark:text-gray-400">Carregando dados...</span>
                 </div>

                 {{-- Conteúdo Detalhado (mostra quando não está carregando e tem dados) --}}
                <div x-show="!loadingModal && selectedDocument.id" x-transition class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                        <div class="border-b pb-2 dark:border-gray-700">
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold">Caixa</p>
                            <p class="font-medium text-gray-800 dark:text-gray-200" x-text="selectedDocument.box_number || '-'"></p>
                        </div>
                        <div class="border-b pb-2 dark:border-gray-700">
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold">Item</p>
                            <p class="font-medium text-gray-800 dark:text-gray-200" x-text="selectedDocument.item_number || '-'"></p>
                        </div>
                        <div class="border-b pb-2 dark:border-gray-700">
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold">Código</p>
                            <p class="font-medium text-gray-800 dark:text-gray-200" x-text="selectedDocument.code || '-'"></p>
                        </div>
                        <div class="border-b pb-2 dark:border-gray-700">
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold">Descritor</p>
                            <p class="font-medium text-gray-800 dark:text-gray-200" x-text="selectedDocument.descriptor || '-'"></p>
                        </div>
                        <div class="md:col-span-2 border-b pb-2 dark:border-gray-700">
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold">Número do Documento</p>
                            <p class="font-medium text-gray-800 dark:text-gray-200" x-text="selectedDocument.document_number || '-'"></p>
                        </div>
                        <div class="md:col-span-2 border-b pb-2 dark:border-gray-700">
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold">Título</p>
                            <p class="font-medium text-gray-800 dark:text-gray-200" x-text="selectedDocument.title || '-'"></p>
                        </div>
                        <div class="border-b pb-2 dark:border-gray-700">
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold">Data</p>
                            {{-- Usa a data formatada pela função Alpine/JS --}}
                            <p class="font-medium text-gray-800 dark:text-gray-200" x-text="selectedDocument.formatted_date || '-'"></p>
                        </div>
                        <div class="border-b pb-2 dark:border-gray-700">
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold">Projeto</p>
                            <p class="font-medium text-gray-800 dark:text-gray-200" x-text="selectedDocument.project || '-'"></p>
                        </div>
                        <div class="border-b pb-2 dark:border-gray-700">
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold">Sigilo</p>
                            <p>
                                 {{-- Badge de Sigilo (replica a lógica da tabela) --}}
                                <span
                                    class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full capitalize"
                                    :class="{
                                        'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': selectedDocument.confidentiality === 'Confidencial',
                                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': selectedDocument.confidentiality === 'Restrito',
                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': selectedDocument.confidentiality === 'Público',
                                        'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300': !selectedDocument.confidentiality
                                    }"
                                    x-text="selectedDocument.confidentiality ? selectedDocument.confidentiality.toLowerCase() : '-'">
                                </span>
                            </p>
                        </div>
                         <div class="border-b pb-2 dark:border-gray-700">
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold">Versão</p>
                            <p class="font-medium text-gray-800 dark:text-gray-200" x-text="selectedDocument.version || '-'"></p>
                        </div>
                        <div class="border-b pb-2 dark:border-gray-700">
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold">Cópia</p>
                            <p class="font-medium text-gray-800 dark:text-gray-200" x-text="selectedDocument.is_copy ? 'Sim' : 'Não'"></p>
                        </div>
                    </div>

                    {{-- Localização Física --}}
                    <div class="mt-5 bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Localização Física</p>
                        <div class="flex items-center space-x-2 text-sm">
                            <i class="fas fa-box text-primary"></i>
                            <p class="text-gray-800 dark:text-gray-200">
                                Caixa <strong x-text="selectedDocument.box_number || '?'"></strong> - Item <strong x-text="selectedDocument.item_number || '?'"></strong>
                            </p>
                        </div>
                    </div>
                </div>
                  {{-- Mensagem caso não carregue dados --}}
                  <div x-show="!loadingModal && !selectedDocument.id">
                     <p class="text-center text-gray-500 dark:text-gray-400 py-5">Não foi possível carregar os detalhes do documento.</p>
                 </div>
            </div>

            {{-- Footer do Modal --}}
            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200 dark:border-gray-600">
                <x-secondary-button
                    type="button"
                     @click="closeModal()"
                    class="w-full sm:w-auto"
                >
                    Fechar
                </x-secondary-button>
                 {{-- Botão de Editar (Exemplo) --}}
                 {{-- <x-primary-button
                     type="button"
                     x-show="selectedDocument.id"
                     @click="window.location.href='/documents/' + selectedDocument.id + '/edit'"
                     class="w-full sm:w-auto sm:ml-3 mt-3 sm:mt-0"
                 >
                     Editar Documento
                 </x-primary-button> --}}
            </div>
        </div>
    </div>
</div>