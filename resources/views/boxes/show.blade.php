<x-app-layout>
    {{-- ========================== Header da Página ========================== --}}
    <x-slot name="header">
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            {{-- Título e Número da Caixa --}}
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    {{ __('Detalhes da Caixa') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Número: <span class="font-medium text-gray-700 dark:text-gray-300">{{ $box->number }}</span>
                </p>
            </div>
            {{-- Botões de Ação Principais (Editar/Excluir Caixa) --}}
            <div class="flex items-center flex-shrink-0 space-x-2">
                <x-secondary-button onclick="window.location='{{ route('boxes.edit', $box) }}';">
                    <i class="mr-1 fas fa-edit"></i> {{ __('Editar Caixa') }}
                </x-secondary-button>

                <form method="POST" action="{{ route('boxes.destroy', $box) }}"
                    onsubmit="return confirm({{ json_encode(__('Tem certeza que deseja excluir esta caixa e TODOS os documentos contidos nela?')) }});">
                    @csrf
                    @method('DELETE')
                    <x-danger-button type="submit">
                        <i class="mr-1 fas fa-trash-alt"></i> {{ __('Excluir Caixa') }}
                    </x-danger-button>
                </form>
            </div>
        </div>
    </x-slot>

    {{-- ========================== Conteúdo Principal ========================== --}}
    <div class="py-12">
        <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">

            {{-- ------------------------- Card: Informações da Caixa ------------------------- --}}
            {{-- Card: Informações da Caixa --}}
            <div class="bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                {{-- Header do Card com Botão Editar Sutil --}}
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">
                        <i class="mr-2 text-gray-500 fas fa-info-circle"></i> {{ __('Informações da Caixa') }}
                    </h3>
                    <a href="{{ route('boxes.edit', $box) }}"
                        class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 shadow-sm text-xs font-medium rounded text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                        title="{{ __('Editar Informações da Caixa') }}">
                        <i class="fas fa-edit mr-1"></i>
                        {{ __('Editar') }}
                    </a>
                </div>
                <dl class="px-6 py-6 space-y-4 text-sm">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-x-4 gap-y-4">
                        <div>
                            <dt class="font-medium text-gray-500 dark:text-gray-400">{{ __('Número') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $box->number }}</dd>
                        </div>
                        <div class="md:col-span-2">
                            <dt class="font-medium text-gray-500 dark:text-gray-400">{{ __('Local Físico') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $box->physical_location ?: '--' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500 dark:text-gray-400">{{ __('Projeto') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100">
                                {{ $box->project?->name ?: '-- Nenhum --' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500 dark:text-gray-400">{{ __('Conferente') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100">
                                {{ $box->commissionMember?->user?->name ?: '-- Nenhum --' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500 dark:text-gray-400">{{ __('Data Conferência') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100">
                                {{ $box->conference_date?->format('d/m/Y') ?: '--' }}</dd>
                        </div>
                    </div>
                </dl>
            </div>

            {{-- ------------------------- Card: Documentos na Caixa ------------------------- --}}
            <div x-data="{
                    selectedDocuments: [],
                    allSelected: false,
                    documentsOnPage: {{ json_encode($box->documents->pluck('id')->toArray()) }},
                    toggleAll() {
                        this.allSelected = !this.allSelected;
                        if (this.allSelected) {
                            this.selectedDocuments = [...this.documentsOnPage];
                        } else {
                            this.selectedDocuments = [];
                        }
                    },
                    toggleCheckbox(docId) {
                        if (this.selectedDocuments.includes(docId)) {
                            this.selectedDocuments = this.selectedDocuments.filter(id => id !== docId);
                        } else {
                            this.selectedDocuments.push(docId);
                        }
                        this.updateSelectAllState();
                    },
                    updateSelectAllState() {
                        this.allSelected = this.documentsOnPage.length > 0 && this.documentsOnPage.every(id => this.selectedDocuments.includes(id));
                    },
                    init() {
                        this.updateSelectAllState();
                    }
                }"
                x-init="init()"
                class="bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">

                {{-- Header do Card com Botão Importar E BOTÃO EXCLUIR SELECIONADOS --}}
                <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">
                        <i class="mr-2 text-gray-500 fas fa-file-alt"></i> {{ __('Documentos na Caixa') }}
                        ({{ $box->documents->count() }})
                    </h3>
                    <div class="flex items-center gap-2">
                        {{-- Botão Excluir Selecionados --}}
                        <form x-show="selectedDocuments.length > 0"
                              action="{{ route('boxes.documents.batchDestroy', $box) }}"
                              method="POST"
                              onsubmit="return confirm('Tem certeza que deseja excluir os documentos selecionados? Esta ação não pode ser desfeita.');"
                              class="inline">
                            @csrf
                            @method('DELETE')
                            <template x-for="docId in selectedDocuments" :key="docId">
                                <input type="hidden" name="document_ids[]" :value="docId">
                            </template>
                            <x-danger-button type="submit" x-bind:disabled="selectedDocuments.length === 0">
                                <i class="mr-1 fas fa-trash-alt"></i> Excluir Selecionados (<span x-text="selectedDocuments.length"></span>)
                            </x-danger-button>
                        </form>

                        {{-- Botão Importar Documentos --}}
                        <button type="button"
                            class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                            @click="$store.modals.openBoxImportModal()">
                            <i class="fas fa-upload mr-1.5"></i> {{ __('Importar Documentos') }}
                        </button>
                    </div>
                </div>

                {{-- Área para exibir erros/mensagens --}}
                @if (session()->hasAny(['import_error_message', 'import_errors', 'warning', 'success']))
                    <div x-data="{ showImportMessages: true }" x-show="showImportMessages" x-transition class="px-6 pb-4 mt-4">
                        {{-- Mensagem Geral (Erro ou Aviso ou Sucesso vindo da importação contextual) --}}
                        @if (session('import_error_message'))
                            <div class="px-4 py-2 mb-3 text-sm text-red-800 bg-red-100 border border-red-300 rounded dark:bg-red-900 dark:text-red-200 dark:border-red-700"
                                role="alert">
                                {!! session('import_error_message') !!}
                            </div>
                        @elseif (session('warning'))
                            <div class="px-4 py-2 mb-3 text-sm text-yellow-800 bg-yellow-100 border border-yellow-300 rounded dark:bg-yellow-900 dark:text-yellow-200 dark:border-yellow-700"
                                role="alert">
                                {!! session('warning') !!}
                            </div>
                        @endif

                        {{-- Erros detalhados (passados via session('import_errors')) --}}
                        @if (session('import_errors') && is_array(session('import_errors')) && count(session('import_errors')) > 0)
                            <div
                                class="px-4 py-3 text-sm text-red-800 bg-red-100 border border-red-300 rounded dark:bg-red-900 dark:text-red-200 dark:border-red-700">
                                <p class="mb-2 font-semibold"><strong>Detalhes dos erros encontrados na
                                        importação:</strong></p>
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach (session('import_errors') as $errorDetail)
                                        <li>
                                            <strong>Linha {{ $errorDetail['row'] ?? 'Desconhecida' }}:</strong>
                                            <ul class="ml-4 list-disc list-inside">
                                                @if (isset($errorDetail['errors']) && is_array($errorDetail['errors']))
                                                    @foreach ($errorDetail['errors'] as $field => $messages)
                                                        @foreach ($messages as $message)
                                                            <li>{{ $message }} (Campo: {{ $field }})</li>
                                                        @endforeach
                                                    @endforeach
                                                @else
                                                    <li>Erro desconhecido nesta linha.</li>
                                                @endif
                                            </ul>
                                            @if (isset($errorDetail['values']) && !empty($errorDetail['values']))
                                                <div class="mt-1 text-xs text-gray-600 dark:text-gray-400">Dados:
                                                    {{ json_encode($errorDetail['values']) }}</div>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        {{-- Botão para fechar a área de mensagens --}}
                        <div class="flex justify-end mt-3">
                            <button type="button" class="text-sm text-gray-600 dark:text-gray-400 hover:underline"
                                @click="showImportMessages = false">
                                {{ __('Fechar Mensagens') }}
                            </button>
                        </div>
                    </div>
                @endif

                {{-- Tabela de Documentos na Caixa --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                {{-- Checkbox Selecionar Todos --}}
                                <th scope="col" class="w-12 px-6 py-3">
                                    <label for="select-all-checkbox" class="sr-only">Selecionar todos</label>
                                    <input id="select-all-checkbox" type="checkbox"
                                           @click="toggleAll()"
                                           :checked="allSelected"
                                           class="text-indigo-600 border-gray-300 rounded dark:bg-gray-900 dark:border-gray-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800">
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                    {{ __('Item') }}</th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                    {{ __('Número Doc.') }}</th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                    {{ __('Título') }}</th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                    {{ __('Data Doc.') }}</th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase dark:text-gray-300">
                                    {{ __('Ações') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            {{-- Loop pelos documentos --}}
                            @forelse ($box->documents as $document)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-750/50">
                                    {{-- Checkbox Individual --}}
                                    <td class="px-6 py-4">
                                        <label for="doc-checkbox-{{ $document->id }}" class="sr-only">Selecionar documento {{ $document->id }}</label>
                                        <input id="doc-checkbox-{{ $document->id }}" type="checkbox"
                                               :value="{{ $document->id }}"
                                               @click="toggleCheckbox({{ $document->id }})"
                                               :checked="selectedDocuments.includes({{ $document->id }})"
                                               class="text-indigo-600 border-gray-300 rounded dark:bg-gray-900 dark:border-gray-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800">
                                    </td>
                                    <td
                                        class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-gray-100">
                                        {{ $document->item_number }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap dark:text-gray-300">
                                        {{ $document->document_number ?? '--' }}</td>
                                    <td class="max-w-xs px-6 py-4 text-sm text-gray-600 truncate dark:text-gray-300"
                                        title="{{ $document->title }}">{{ Str::limit($document->title, 60) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                        {{ $document->document_date ?? '--' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                        @can('documents.view')
                                            <a href="{{ route('documents.show', $document) }}"
                                                class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-200"><i
                                                    class="mr-1 fas fa-eye"></i></a>
                                        @endcan
                                        @can('documents.edit')
                                            <a href="{{ route('documents.edit', $document) }}"
                                                class="text-primary dark:text-primary-light hover:text-primary-dark dark:hover:text-white"><i
                                                    class="mr-1 fas fa-edit"></i></a>
                                        @endcan
                                        @can('documents.delete')
                                            <form method="POST" action="{{ route('documents.destroy', $document) }}"
                                                class="inline"
                                                onsubmit="return confirm('Tem certeza que deseja excluir este documento?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-200"><i
                                                        class="mr-1 fas fa-trash-alt"></i></button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="mb-2 text-gray-400 fas fa-folder-open fa-3x"></i>
                                            {{ __('Nenhum documento encontrado nesta caixa.') }}
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div> {{-- Fim Card Documentos --}}

        </div> {{-- Fim mx-auto --}}
    </div> {{-- Fim py-12 --}}

    
    {{-- ========================== Modal de Importação para Caixa ========================== --}}
    {{-- Este modal é específico desta view e usa o Alpine Store 'modals' --}}
    <div x-show="$store.modals.showBoxImportModal" x-cloak @keydown.escape.window="$store.modals.closeBoxImportModal()"
        class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" aria-labelledby="box-import-modal-title"
        role="dialog" aria-modal="true">

        {{-- Overlay de Fundo --}}
        <div x-show="$store.modals.showBoxImportModal" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-80 transition-opacity"
            @click="$store.modals.closeBoxImportModal()" aria-hidden="true"></div>

        {{-- Conteúdo --}}
        <div
            class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0 sm:items-center">
            {{-- Truque para centralizar verticalmente --}}
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>

            {{-- Painel do Modal --}}
            <div x-show="$store.modals.showBoxImportModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block w-full overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl dark:bg-gray-800 sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                role="document">

                {{-- Formulário de Upload --}}
                <form action="{{ route('boxes.documents.import', $box) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    {{-- Header do Modal --}}
                    <div class="px-4 pt-5 pb-4 bg-white border-b dark:bg-gray-800 sm:p-6 sm:pb-4 dark:border-gray-700">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-indigo-100 rounded-full dark:bg-indigo-900 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="text-lg text-indigo-600 fas fa-upload dark:text-indigo-300"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100"
                                    id="box-import-modal-title">
                                    {{ __('Importar Documentos para Caixa') }} <span
                                        class="font-bold">{{ $box->number }}</span>
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Selecione um arquivo CSV. Os documentos serão adicionados a esta caixa.
                                        <a href="{{ asset('files/modelo_importacao_docs_caixa.csv') }}"
                                            class="ml-1 font-medium text-indigo-600 dark:text-indigo-400 hover:underline"
                                            download>
                                            Baixar modelo
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        {{-- Botão Fechar no Header --}}
                        <button type="button" @click="$store.modals.closeBoxImportModal()"
                            class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                            <span class="sr-only">{{ __('Fechar') }}</span>
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Corpo do Modal (Input) --}}
                    <div class="px-4 py-5 bg-white dark:bg-gray-800 sm:p-6">
                        <x-input-label for="documents_csv_modal" :value="__('Arquivo CSV')" class="mb-1" />
                        <input type="file" id="documents_csv_modal" name="documents_csv" accept=".csv, text/csv"
                            class="block w-full text-sm text-gray-500 border border-gray-300 rounded-lg cursor-pointer dark:text-gray-400 focus:outline-none dark:border-gray-600 dark:placeholder-gray-400
                                      file:mr-4 file:py-2 file:px-4 file:rounded-l-md file:border-0
                                      file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700
                                      dark:file:bg-gray-700 dark:file:text-gray-300
                                      hover:file:bg-indigo-100 dark:hover:file:bg-gray-600"
                            required />
                        {{-- Exibe erros de validação específicos do upload, se houver na sessão --}}
                        @if (session()->has('errors'))
                            @foreach (session('errors')->get('documents_csv') as $error)
                                <p class="text-red-500 text-xs mt-1">{{ $error }}</p>
                            @endforeach
                        @endif
                    </div>

                    {{-- Footer do Modal (Botões) --}}
                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900 sm:px-6 sm:flex sm:flex-row-reverse">
                        <x-primary-button type="submit" class="w-full sm:ml-3 sm:w-auto">
                            {{ __('Importar') }}
                        </x-primary-button>
                        <x-secondary-button type="button" class="w-full mt-3 sm:mt-0 sm:w-auto"
                            @click="$store.modals.closeBoxImportModal()">
                            {{ __('Cancelar') }}
                        </x-secondary-button>
                    </div>
                </form> {{-- Fim do formulário --}}
            </div> {{-- Fim do Painel do Modal --}}
        </div> {{-- Fim do container flex --}}
    </div> {{-- Fim do wrapper do modal --}}
    {{-- *** FIM DO MODAL DE IMPORTAÇÃO PARA CAIXA *** --}}


</x-app-layout>