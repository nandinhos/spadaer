<div class="space-y-8">
    {{-- Estatísticas Reativas --}}
    <x-document-stats 
        :stats="$stats" 
        :hasActiveFilters="$hasActiveFilters" 
        :totalDocuments="$stats['totalDocuments'] ?? 0" 
    />

    {{-- Filtros Reativos --}}
    <div x-data="{ open: @entangle('hasActiveFilters') }" class="mb-8 overflow-hidden bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm transition-all duration-300">
        <button 
            @click="open = !open" 
            class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors focus:outline-none"
        >
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-500 dark:text-gray-400">
                    <i class="fa-solid fa-sliders text-sm"></i>
                </div>
                <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-wider">
                    Filtros Avançados
                </h3>
            </div>
            <i class="fa-solid fa-chevron-down text-gray-400 transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
        </button>

        <div x-show="open" x-cloak x-collapse class="px-6 pb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4">
                <div>
                    <x-input-label for="filter_box_number" value="Nº da Caixa" class="text-[10px] font-black uppercase text-gray-400 mb-1.5" />
                    <x-text-input 
                        wire:model.live.debounce.300ms="filter_box_number"
                        id="filter_box_number" 
                        type="text" 
                        class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50" 
                        placeholder="Ex: AD001" 
                    />
                </div>

                <div>
                    <x-input-label for="filter_project_id" value="Projeto Vinculado" class="text-[10px] font-black uppercase text-gray-400 mb-1.5" />
                    <x-select-input 
                        wire:model.live="filter_project_id"
                        id="filter_project_id" 
                        class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50" 
                    >
                        <option value="">Todos os Projetos</option>
                        @foreach ($availableProjects as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </x-select-input>
                </div>

                <div>
                    <x-input-label for="filter_year" value="Ano de Referência" class="text-[10px] font-black uppercase text-gray-400 mb-1.5" />
                    <x-select-input 
                        wire:model.live="filter_year"
                        id="filter_year" 
                        class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50" 
                    >
                        <option value="">Todos os Anos</option>
                        @foreach ($availableYears as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </x-select-input>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-100 dark:border-gray-800">
                <x-ui.button 
                    type="button"
                    variant="ghost"
                    size="sm"
                    wire:click="clearFilters"
                >
                    Limpar Filtros
                </x-ui.button>
            </div>
        </div>
    </div>

    {{-- Tabela Reativa --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-gray-800 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex flex-wrap items-center gap-3">
                <h2 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight mr-2">
                    Documentos
                </h2>
                <a href="{{ route('documents.create') }}" wire:navigate>
                    <x-ui.button size="sm" icon="fas fa-plus" variant="primary">
                        Novo
                    </x-ui.button>
                </a>
                
                {{-- Ações de Lote / Ferramentas --}}
                <div class="flex items-center gap-2 ml-2 pl-4 border-l border-gray-100 dark:border-gray-800">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <x-ui.button size="sm" variant="success" icon="fas fa-download">
                                Exportar
                            </x-ui.button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('documents.export')" class="flex items-center gap-2">
                                <i class="fa-solid fa-file-excel text-green-600"></i> Excel (XLSX)
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('documents.export.pdf')" class="flex items-center gap-2">
                                <i class="fa-solid fa-file-pdf text-red-600"></i> PDF
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>

                    <form action="{{ route('documents.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                        @csrf
                        <div class="relative group">
                            <input type="file" name="csv_file" id="csv_file" class="hidden" onchange="this.form.submit()">
                            <label for="csv_file" class="cursor-pointer">
                                <x-ui.button variant="warning" size="sm" icon="fas fa-file-import" type="button" class="pointer-events-none">
                                    Importar CSV
                                </x-ui.button>
                            </label>
                        </div>
                    </form>

                    <a href="{{ asset('files/modelo_importacao.csv') }}" download>
                        <x-ui.button variant="outline" size="sm" icon="fa-solid fa-file-csv" type="button">
                            Modelo
                        </x-ui.button>
                    </a>
                </div>
            </div>

            <div class="relative w-full sm:w-80 group">
                <x-text-input 
                    wire:model.live.debounce.300ms="search"
                    placeholder="Busca rápida..." 
                    class="w-full pl-10 pr-4 py-2 rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 text-sm" 
                />
                <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse min-w-[800px] lg:min-w-full">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest cursor-pointer" wire:click="sortBy('boxes.number')">
                            Localização <i class="fa-solid {{ $sort_by === 'boxes.number' ? ($sort_dir === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort opacity-20' }} ml-1"></i>
                        </th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest cursor-pointer" wire:click="sortBy('documents.document_number')">
                            Identificação <i class="fa-solid {{ $sort_by === 'documents.document_number' ? ($sort_dir === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort opacity-20' }} ml-1"></i>
                        </th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest w-1/3 cursor-pointer" wire:click="sortBy('documents.title')">
                            Documento <i class="fa-solid {{ $sort_by === 'documents.title' ? ($sort_dir === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort opacity-20' }} ml-1"></i>
                        </th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Sigilo</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($documents as $document)
                        <tr wire:key="{{ $document->id }}" class="group hover:bg-primary/[0.02] dark:hover:bg-primary/[0.05] transition-colors">
                            <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-gray-100 uppercase tracking-tighter">
                                {{ $document->box->number ?? '---' }} <span class="ml-2 text-[10px] text-gray-400 font-normal">Item: {{ $document->item_number }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-gray-900 dark:text-gray-100 tracking-tight">{{ $document->document_number }}</span>
                                <p class="text-[10px] font-black text-primary/70 uppercase">{{ $document->code }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 leading-snug truncate max-w-sm">{{ $document->title }}</p>
                                <span class="text-[10px] font-bold text-gray-400 uppercase"><i class="fa-regular fa-calendar mr-1"></i> {{ $document->formatted_document_date }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider border
                                    {{ match(strtolower($document->confidentiality)) {
                                        'público', 'publico' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                        'restrito' => 'bg-amber-50 text-amber-700 border-amber-100',
                                        'confidencial' => 'bg-red-50 text-red-700 border-red-100',
                                        default => 'bg-gray-100'
                                    } }}">
                                    {{ $document->confidentiality }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <x-ui.button variant="ghost-primary" size="sm" icon="fas fa-eye" @click="$store.modals.openDocumentDetails({{ $document->id }})" title="Ver Detalhes" />
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-20 text-center text-gray-400 uppercase font-bold tracking-widest">Nenhum documento</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-6 border-t border-gray-100 dark:border-gray-800">
            {{ $documents->links(data: ['scrollTo' => false]) }}
        </div>
    </div>
</div>
