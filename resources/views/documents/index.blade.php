<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight flex items-center justify-between">
            <span>{{ __('Gerenciamento de Documentos') }}</span>
            <a href="{{ route('documents.create') }}" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-plus mr-2"></i>Novo Documento
            </a>
        </h2>
    </x-slot>

    <div x-data="documentSystem({{ json_encode(['documents' => $documents, 'projects' => $projects, 'years' => $years]) }})" 
         class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Filtros -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Projeto</label>
                    <select x-model="filters.project" @change="applyFilters()" 
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:text-gray-300">
                        <option value="">Todos os projetos</option>
                        <template x-for="project in projects" :key="project">
                            <option :value="project" x-text="project"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ano</label>
                    <select x-model="filters.ano" @change="applyFilters()"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:text-gray-300">
                        <option value="">Todos os anos</option>
                        <template x-for="ano in years" :key="ano">
                            <option :value="ano" x-text="ano"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Buscar</label>
                    <input type="text" x-model="filters.search" @input="applyFilters()"
                           class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:text-gray-300"
                           placeholder="Buscar documentos...">
                </div>
            </div>
        </div>

        <!-- Tabela de Documentos -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Caixa</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Item</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Código</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Descritor</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Número</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Título</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <template x-for="(documento, index) in paginatedDocuments" :key="documento.id">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300" x-text="documento.box"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300" x-text="documento.item"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300" x-text="documento.code ?? 'N/A'"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300" x-text="documento.descriptor ?? 'N/A'"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300" x-text="documento.number ?? 'N/A'"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300 max-w-xs truncate" x-text="documento.title"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300" x-text="formatDate(documento.date)"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                    <div class="flex space-x-2">
                                        <button @click="showDocumentDetails(documento)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a :href="`/documents/${documento.id}/edit`" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button @click="deleteDocument(documento)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <button @click="previousPage" :disabled="currentPage === 1"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50">
                            Anterior
                        </button>
                        <button @click="nextPage" :disabled="currentPage >= totalPages"
                                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50">
                            Próxima
                        </button>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Mostrando
                                <span class="font-medium" x-text="((currentPage - 1) * perPage) + 1"></span>
                                até
                                <span class="font-medium" x-text="Math.min(currentPage * perPage, filteredDocuments.length)"></span>
                                de
                                <span class="font-medium" x-text="filteredDocuments.length"></span>
                                resultados
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <button @click="previousPage" :disabled="currentPage === 1"
                                        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50">
                                    <span class="sr-only">Anterior</span>
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button @click="nextPage" :disabled="currentPage >= totalPages"
                                        class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50">
                                    <span class="sr-only">Próxima</span>
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Detalhes -->
        <div x-show="showModal" class="fixed z-10 inset-0 overflow-y-auto" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                                    Detalhes do Documento
                                </h3>
                                <div class="mt-4 space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Caixa</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100" x-text="selectedDocument?.caixa"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Item</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100" x-text="selectedDocument?.item"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Código</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100" x-text="selectedDocument?.codigo ?? 'N/A'"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descritor</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100" x-text="selectedDocument?.descritor ?? 'N/A'"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Número</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100" x-text="selectedDocument?.numero ?? 'N/A'"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Título</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100" x-text="selectedDocument?.titulo"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100" x-text="formatDate(selectedDocument?.data)"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" @click="showModal = false"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Fechar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>