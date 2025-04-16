<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                {{ __('Importar Documentos') }}
            </h2>
            <a href="{{ route('documents.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('documents.import.store') }}" method="POST" class="space-y-6" id="importForm">
                        @csrf
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                Insira os documentos abaixo. Para adicionar mais linhas, clique no botão "Adicionar Documento".
                            </p>
                        </div>

                        <div id="documents-container" class="space-y-4">
                            <!-- Template para uma linha de documento -->
                            <div class="document-row grid grid-cols-11 gap-2 items-start">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Item</label>
                                    <input type="text" name="documents[0][item]" required
                                        class="mt-1 block w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Código</label>
                                    <input type="text" name="documents[0][codigo]" required
                                        class="mt-1 block w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Descritor</label>
                                    <input type="text" name="documents[0][descritor]" required
                                        class="mt-1 block w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Número</label>
                                    <input type="text" name="documents[0][numero]" required
                                        class="mt-1 block w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Título</label>
                                    <input type="text" name="documents[0][titulo]" required
                                        class="mt-1 block w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Data</label>
                                    <input type="date" name="documents[0][data]" required
                                        class="mt-1 block w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Sigilo</label>
                                    <select name="documents[0][sigilo]" required
                                        class="mt-1 block w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                        <option value="Público">Público</option>
                                        <option value="Restrito">Restrito</option>
                                        <option value="Confidencial">Confidencial</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Versão</label>
                                    <input type="text" name="documents[0][versao]"
                                        class="mt-1 block w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Cópia</label>
                                    <input type="text" name="documents[0][copia]"
                                        class="mt-1 block w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                </div>
                                <div class="flex items-end">
                                    <button type="button" onclick="removeDocumentRow(this)" class="mt-1 p-2 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between items-center mt-4">
                            <button type="button" onclick="addDocumentRow()" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors duration-200">
                                <i class="fas fa-plus mr-2"></i>Adicionar Documento
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200">
                                <i class="fas fa-save mr-2"></i>Salvar Todos
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let documentCount = 1;

        function addDocumentRow() {
            const container = document.getElementById('documents-container');
            const template = container.children[0].cloneNode(true);
            
            // Atualiza os nomes dos campos
            template.querySelectorAll('input, select').forEach(input => {
                input.name = input.name.replace('[0]', `[${documentCount}]`);
                input.value = '';
            });

            container.appendChild(template);
            documentCount++;
        }

        function removeDocumentRow(button) {
            const container = document.getElementById('documents-container');
            if (container.children.length > 1) {
                button.closest('.document-row').remove();
            }
        }
    </script>
    @endpush
</x-app-layout>