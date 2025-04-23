{{-- Componente Modal (resources/views/components/document-modal.blade.php) --}}
<div x-show="showModal" @keydown.escape.window="closeModal()" class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;" {{-- Alpine controla a visibilidade --}}>
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:items-center sm:block sm:p-0">
        {{-- Background overlay --}}
        <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-80"
            @click="closeModal()" aria-hidden="true"></div>

        {{-- Centraliza --}}
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>

        {{-- Conteúdo do Modal --}}
        <div x-show="showModal" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl dark:bg-gray-800 sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full"
            {{-- Max-width aumentado um pouco --}} @click.outside="closeModal()">
            {{-- Header --}}
            <div
                class="px-4 pt-5 pb-4 bg-white border-b border-gray-200 dark:bg-gray-800 sm:p-6 sm:pb-4 dark:border-gray-700">
                <div class="sm:flex sm:items-start">
                    <div
                        class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto rounded-full bg-primary-light bg-opacity-20 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="text-lg fas fa-file-alt text-primary"></i>
                    </div>
                    <div class="w-full mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100" id="modal-title">
                            Detalhes do Documento
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400"
                            x-text="selectedDocument.document_number ? selectedDocument.document_number : 'Carregando...'">
                        </p>
                    </div>
                    <button @click="closeModal()"
                        class="absolute text-gray-400 top-4 right-4 hover:text-gray-600 dark:hover:text-gray-300">
                        <span class="sr-only">Fechar</span>
                        <i class="fas fa-times fa-lg"></i>
                    </button>
                </div>
            </div>

            {{-- Corpo (Conteúdo) --}}
            <div class="px-4 py-5 sm:p-6">
                {{-- Indicador de Carregamento --}}
                <div x-show="loadingModal" class="flex items-center justify-center h-40">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                    <span class="ml-3 text-gray-600 dark:text-gray-400">Carregando dados...</span>
                </div>

                {{-- Tabela de Detalhes (mostra quando não está carregando e tem dados) --}}
                <div x-show="!loadingModal && selectedDocument.id" x-transition>
                    <table class="min-w-full text-sm">
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            {{-- Linhas da tabela com Label e Valor --}}
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750/50">
                                <td class="w-1/3 px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Título</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100"
                                    x-text="selectedDocument.title || '-'"></td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750/50">
                                <td class="px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Número do Documento
                                </td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100"
                                    x-text="selectedDocument.document_number || '-'"></td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750/50">
                                <td class="px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Caixa / Item</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                    <span x-text="selectedDocument.box?.number || 'N/A'"></span> /
                                    <span x-text="selectedDocument.item_number || 'N/A'"></span>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750/50">
                                <td class="px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Projeto</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100"
                                    x-text="selectedDocument.project?.name || '-- Nenhum --'"></td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750/50">
                                <td class="px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Data do Documento
                                </td>
                                {{-- Usa a propriedade formatada adicionada no JS --}}
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100"
                                    x-text="selectedDocument.formatted_date || '-'"></td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750/50">
                                <td class="px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Código</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100"
                                    x-text="selectedDocument.code || '-'"></td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750/50">
                                <td class="px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Descritor</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100"
                                    x-text="selectedDocument.descriptor || '-'"></td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750/50">
                                <td class="px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Sigilo</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                    {{-- Badge (requer :class dinâmico) --}}
                                    <span
                                        class="inline-flex px-2 text-xs font-semibold leading-5 capitalize rounded-full"
                                        :class="{
                                            'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': selectedDocument
                                                .confidentiality === 'Confidencial',
                                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': selectedDocument
                                                .confidentiality === 'Restrito',
                                            'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': selectedDocument
                                                .confidentiality === 'Público',
                                            'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300': !
                                                selectedDocument.confidentiality
                                        }"
                                        x-text="selectedDocument.confidentiality ? selectedDocument.confidentiality.toLowerCase() : '-'">
                                    </span>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750/50">
                                <td class="px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Versão</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100"
                                    x-text="selectedDocument.version || '-'"></td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750/50">
                                <td class="px-4 py-3 font-medium text-gray-500 dark:text-gray-400">É Cópia?</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100"
                                    x-text="selectedDocument.is_copy ? 'Sim' : 'Não'"></td>
                            </tr>
                            {{-- Adicione mais linhas para outros campos se necessário --}}
                        </tbody>
                    </table>
                </div>
                {{-- Mensagem se falhar ao carregar (opcional) --}}
                <div x-show="!loadingModal && !selectedDocument.id" class="py-10 text-center text-gray-500">
                    Não foi possível carregar os detalhes do documento.
                </div>
            </div>

            {{-- Footer --}}
            <div
                class="px-4 py-3 border-t border-gray-200 bg-gray-50 dark:bg-gray-700 sm:px-6 sm:flex sm:flex-row-reverse dark:border-gray-600">
                <x-secondary-button type="button" @click="closeModal()">
                    {{ __('Fechar') }}
                </x-secondary-button>
                {{-- Botão para Editar Documento (Exemplo) --}}
                {{-- <x-primary-button type="button"
                     x-show="selectedDocument.id"
                     @click="window.location.href='/documents/' + selectedDocument.id + '/edit'"
                     class="mr-3">
                     Editar Documento
                 </x-primary-button> --}}
            </div>
        </div>
    </div>
</div>
