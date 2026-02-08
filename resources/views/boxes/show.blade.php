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
                @can('boxes.edit')
                    <a href="{{ route('boxes.edit', $box) }}" wire:navigate>
                        <x-ui.button variant="secondary" icon="fas fa-edit">
                            {{ __('Editar Caixa') }}
                        </x-ui.button>
                    </a>
                @endcan

                @can('boxes.delete')
                    <x-ui.button 
                        variant="danger" 
                        icon="fas fa-trash-alt" 
                        type="button"
                        @click="$store.confirmDelete.open({
                            action: '{{ route('boxes.destroy', $box) }}',
                            title: 'Excluir Caixa',
                            message: 'Tem certeza que deseja excluir esta caixa ({{ $box->number }}) e TODOS os documentos contidos nela? Esta ação não pode ser desfeita.'
                        })"
                    >
                        {{ __('Excluir Caixa') }}
                    </x-ui.button>
                @endcan
            </div>
        </div>
    </x-slot>

    {{-- Botão Voltar para a Lista --}}
    <div class="flex justify-start mt-6">
        <a href="{{ route('boxes.index') }}" wire:navigate>
            <x-ui.button variant="secondary" icon="fas fa-arrow-left">
                {{ __('Voltar para a Lista de Caixas') }}
            </x-ui.button>
        </a>
    </div>

    {{-- ========================== Conteúdo Principal com Lógica Alpine.js ========================== --}}
    {{-- O escopo do Alpine.js agora envolve todo o conteúdo principal da página --}}
    <div>

        <div class="py-12">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">

                {{-- Card: Informações da Caixa (sem alterações) --}}
                <div class="bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div
                        class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">
                            <i class="mr-2 text-gray-500 fas fa-info-circle"></i> {{ __('Informações da Caixa') }}
                        </h3>
                        @can('boxes.edit')
                            <a href="{{ route('boxes.edit', $box) }}" wire:navigate>
                                <x-ui.button variant="ghost-warning" size="sm" icon="fas fa-edit" title="{{ __('Editar Informações da Caixa') }}">
                                    {{ __('Editar') }}
                                </x-ui.button>
                            </a>
                        @endcan
                    </div>
                    <dl class="px-6 py-6 space-y-4 text-sm">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-x-4 gap-y-4">
                            <div>
                                <dt class="font-medium text-gray-500 dark:text-gray-400">{{ __('Número') }}</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $box->number }}</dd>
                            </div>
                            <div class="md:col-span-2">
                                <dt class="font-medium text-gray-500 dark:text-gray-400">{{ __('Local Físico') }}
                                </dt>
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
                                <dt class="font-medium text-gray-500 dark:text-gray-400">{{ __('Data Conferência') }}
                                </dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">
                                    {{ $box->conference_date?->format('d/m/Y') ?: '--' }}</dd>
                            </div>
                        </div>
                    </dl>
                </div>

                {{-- Card: Documentos na Caixa --}}
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
                        const index = this.selectedDocuments.indexOf(docId);
                        if (index > -1) {
                            this.selectedDocuments.splice(index, 1);
                        } else {
                            this.selectedDocuments.push(docId);
                        }
                        this.updateSelectAllState();
                    },
                    updateSelectAllState() {
                        this.allSelected = this.documentsOnPage.length > 0 && this.documentsOnPage.every(id => this.selectedDocuments.includes(id));
                    }
                }" x-init="updateSelectAllState()"
                    class="bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">

                    {{-- Header do card de documentos (sem alterações) --}}
                    <div
                        class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">
                            <i class="mr-2 text-gray-500 fas fa-file-alt"></i> {{ __('Documentos na Caixa') }}
                            ({{ $box->documents->count() }})
                        </h3>
                        <div class="flex items-center gap-2">
                            @can('documents.delete')
                                <form x-show="selectedDocuments.length > 0" x-cloak
                                    action="{{ route('boxes.documents.batchDestroy', $box) }}" method="POST"
                                    id="batch-delete-docs-form"
                                    class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <template x-for="docId in selectedDocuments" :key="docId">
                                        <input type="hidden" name="document_ids[]" :value="docId">
                                    </template>
                                    <x-ui.button 
                                        variant="danger" 
                                        type="button" 
                                        x-bind:disabled="selectedDocuments.length === 0" 
                                        icon="fas fa-trash-alt"
                                        @click="$store.confirmDelete.open({
                                            submitFormId: 'batch-delete-docs-form',
                                            title: 'Excluir Documentos',
                                            message: 'Tem certeza que deseja excluir os documentos selecionados? Esta ação não pode ser desfeita.'
                                        })"
                                    >
                                        Excluir (<span x-text="selectedDocuments.length"></span>)
                                    </x-ui.button>
                                </form>
                            @endcan
                            @can('documents.import')
                                <x-ui.button variant="warning" icon="fas fa-upload" type="button" @click="$store.modals.openBoxImportModal()">
                                    {{ __('Importar Documentos') }}
                                </x-ui.button>
                            @endcan
                        </div>
                    </div>

                    {{-- Área de mensagens (sem alterações) --}}
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
                                                                <li>{{ $message }} (Campo: {{ $field }})
                                                                </li>
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

                    {{-- Tabela de Documentos --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="w-12 px-6 py-3">
                                        <input id="select-all-checkbox" type="checkbox" @click="toggleAll()"
                                            :checked="allSelected" class="text-indigo-600 border-gray-300 rounded ...">
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left ...">{{ __('Item') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left ...">{{ __('Número Doc.') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left ...">{{ __('Título') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left ...">{{ __('Data Doc.') }}</th>
                                    <th scope="col" class="px-6 py-3 text-right ...">{{ __('Ações') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($box->documents as $document)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-750/50">
                                        <td class="px-6 py-4">
                                            <input type="checkbox" :value="{{ $document->id }}"
                                                x-model="selectedDocuments"
                                                class="text-indigo-600 border-gray-300 rounded ...">
                                        </td>
                                        <td class="px-6 py-4 ...">{{ $document->item_number }}</td>
                                        <td class="px-6 py-4 ...">{{ $document->document_number ?? '--' }}</td>
                                        <td class="px-6 py-4 ... truncate" title="{{ $document->title }}">
                                            {{ Str::limit($document->title, 60) }}</td>
                                        <td class="px-6 py-4 ...">{{ $document->document_date ?? '--' }}</td>
                                                                                <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                            @can('documents.view')
                                                <x-ui.button variant="ghost-primary" size="sm" icon="fas fa-eye" @click="$store.modals.openDocumentDetails({{ $document->id }})" title="Ver Detalhes" />
                                            @endcan

                                            @can('documents.edit')
                                                <a href="{{ route('documents.edit', $document) }}" wire:navigate>
                                                    <x-ui.button variant="ghost-warning" size="sm" icon="fas fa-edit" title="Editar" />
                                                </a>
                                            @endcan

                                            @can('documents.delete')
                                                <x-ui.button 
                                                    variant="ghost-danger" 
                                                    size="sm" 
                                                    icon="fas fa-trash-alt" 
                                                    type="button" 
                                                    title="Excluir"
                                                    @click="$store.confirmDelete.open({
                                                        action: '{{ route('documents.destroy', $document) }}',
                                                        title: 'Excluir Documento',
                                                        message: 'Tem certeza que deseja excluir o documento {{ $document->document_number }}?'
                                                    })"
                                                />
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-10 text-center ...">
                                            {{-- ... (mensagem de nenhum documento) ... --}}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>



        </div>

    </div>

    {{-- Modal de Importação --}}
    @can('documents.import')
        <div x-show="$store.modals.showBoxImportModal" x-cloak
            @keydown.escape.window="$store.modals.closeBoxImportModal()" class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:items-center sm:block sm:p-0">
                <div x-show="$store.modals.showBoxImportModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75"
                    @click="$store.modals.closeBoxImportModal()" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="$store.modals.showBoxImportModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative z-10 inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl dark:bg-gray-800 sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <div class="flex items-center justify-center w-12 h-12 mx-auto bg-indigo-100 rounded-full dark:bg-indigo-900">
                            <i class="text-indigo-600 fas fa-upload dark:text-indigo-400"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100" id="modal-title">
                                {{ __('Importar Documentos para esta Caixa') }}
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('Selecione um arquivo CSV para importar documentos diretamente para a caixa ') }} <strong>{{ $box->number }}</strong>.
                                </p>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('boxes.documents.import', $box) }}" method="POST"
                        enctype="multipart/form-data" class="mt-5 sm:mt-6">
                        @csrf
                        <div class="space-y-4">
                            <input type="file" name="csv_file" accept=".csv" required
                                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                            
                            <div class="flex items-center justify-between p-3 border border-indigo-100 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 dark:border-indigo-800">
                                <div class="flex items-center gap-2">
                                    <i class="text-indigo-500 fas fa-info-circle"></i>
                                    <span class="text-xs font-medium text-indigo-700 dark:text-indigo-300">Layout Sugerido</span>
                                </div>
                                <a href="{{ asset('files/modelo_importacao.csv') }}" download class="text-xs font-bold text-indigo-600 uppercase hover:underline dark:text-indigo-400">
                                    Download Modelo
                                </a>
                            </div>
                        </div>

                        <div class="mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                            <x-ui.button type="submit" variant="primary" class="w-full sm:col-start-2">
                                {{ __('Importar') }}
                            </x-ui.button>
                            <x-ui.button type="button" variant="ghost" class="w-full mt-3 sm:mt-0 sm:col-start-1" @click="$store.modals.closeBoxImportModal()">
                                {{ __('Cancelar') }}
                            </x-ui.button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    {{-- ===== SCRIPT PARA CONTROLAR O MODAL DE VISUALIZAÇÃO ===== --}}
</x-app-layout>
