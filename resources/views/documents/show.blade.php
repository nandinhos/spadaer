<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                {{ __('Visualizar Documento') }}
                @if($document->isSecret())
                    <span class="ml-2 text-amber-500" title="Documento Sigiloso (Acesso Auditado)">
                        <i class="fa-solid fa-lock text-sm"></i>
                    </span>
                @endif
            </h2>
            <div class="space-x-4">
                @can('documents.edit')
                <a href="{{ route('documents.edit', $document) }}" wire:navigate>
                    <x-ui.button variant="primary" icon="fas fa-edit">Editar</x-ui.button>
                </a>
                @endcan
                <a href="{{ route('documents.index') }}" wire:navigate>
                    <x-ui.button variant="secondary" icon="fas fa-arrow-left">Voltar</x-ui.button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Informações do Documento -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Caixa e Item -->
                        <div class="col-span-1">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Caixa</h3>
                            <p class="mt-1 text-lg font-semibold">{{ $document->box_number }}</p>
                        </div>

                        <div class="col-span-1">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Item</h3>
                            <p class="mt-1 text-lg font-semibold">{{ $document->item_number }}</p>
                        </div>

                        <!-- Código e Descritor -->
                        <div class="col-span-1">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Código</h3>
                            <p class="mt-1 text-lg font-semibold">{{ $document->code ?? 'N/A' }}</p>
                        </div>

                        <div class="col-span-1">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Descritor</h3>
                            <p class="mt-1 text-lg font-semibold">{{ $document->descriptor ?? 'N/A' }}</p>
                        </div>

                        <!-- Número e Título -->
                        <div class="col-span-1">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Número</h3>
                            <p class="mt-1 text-lg font-semibold">{{ $document->document_number ?? 'N/A' }}</p>
                        </div>

                        <div class="col-span-2">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Título</h3>
                            <p class="mt-1 text-lg font-semibold">{{ $document->title }}</p>
                        </div>

                        <!-- Data e Projeto -->
                        <div class="col-span-1">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Data (Texto)</h3>
                            <p class="mt-1 text-lg font-semibold">{{ $document->document_date ?? 'N/A' }}</p>
                        </div>

                        <div class="col-span-1">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Projeto</h3>
                            <p class="mt-1 text-lg font-semibold">{{ $document->project ?? 'N/A' }}</p>
                        </div>

                        <!-- Sigilo e Versão -->
                            <p class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-black uppercase tracking-wider border shadow-sm
                                    {{ match(mb_strtoupper($document->confidentiality ?? '')) {
                                        'PÚBLICO', 'PUBLICO', 'OSTENSIVO', 'UNCLASSIFIED' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800',
                                        'RESTRITO', 'RESERVADO' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/30 dark:text-amber-400 dark:border-amber-800',
                                        'CONFIDENCIAL', 'SECRETO', 'ULTRASSECRETO', 'SECRET', 'TOP SECRET' => 'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800',
                                        default => 'bg-gray-50 text-gray-600 border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700'
                                    } }}">
                                    @if($document->isSecret())
                                        <i class="fa-solid fa-shield-halved mr-1 text-[10px]"></i>
                                    @endif
                                    {{ $document->confidentiality ?? 'N/A' }}
                                </span>
                            </p>

                        <div class="col-span-1">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Versão</h3>
                            <p class="mt-1 text-lg font-semibold">{{ $document->version ?? 'N/A' }}</p>
                        </div>

                        <!-- Cópia -->
                        <div class="col-span-1">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">É Cópia? (Texto)</h3>
                            <p class="mt-1 text-lg font-semibold">
                                {{-- Exibe o valor como string. Pode adicionar lógica para 'Sim'/'Não' se preferir --}}
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                    {{ $document->is_copy ?? 'N/A' }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Ações -->
                                        <div class="mt-8 flex justify-between items-center border-t dark:border-gray-700 pt-6">
                        @can('documents.delete')
                            <x-ui.button 
                                variant="danger" 
                                icon="fas fa-trash-alt" 
                                type="button"
                                @click="$store.confirmDelete.open({
                                    action: '{{ route('documents.destroy', $document) }}',
                                    title: 'Excluir Documento',
                                    message: 'Tem certeza que deseja excluir este documento ({{ $document->document_number }})? Esta ação não pode ser desfeita.'
                                })"
                            >
                                Excluir Documento
                            </x-ui.button>
                        @endcan

                        <div class="space-x-4">
                            @can('documents.edit')
                            <a href="{{ route('documents.edit', $document) }}" wire:navigate>
                                <x-ui.button variant="primary" icon="fas fa-edit">Editar</x-ui.button>
                            </a>
                            @endcan
                            <a href="{{ route('documents.index') }}" wire:navigate>
                                <x-ui.button variant="secondary" icon="fas fa-arrow-left">Voltar</x-ui.button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>