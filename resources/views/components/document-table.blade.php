@props([
    'documents', 
    'requestParams' => [],
])

@php
    $currentSortBy = $requestParams['sort_by'] ?? 'documents.id';
    $currentSortDir = $requestParams['sort_dir'] ?? 'desc';

    function sortLink($label, $columnKey, $currentSortBy, $currentSortDir, $params) {
        $newSortDir = ($currentSortBy == $columnKey && $currentSortDir == 'asc') ? 'desc' : 'asc';
        $url = route('documents.index', array_merge($params, ['sort_by' => $columnKey, 'sort_dir' => $newSortDir]));
        $active = $currentSortBy == $columnKey;
        $icon = $active ? ($currentSortDir == 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort opacity-20';
        
        return "<a href='{$url}' class='group flex items-center gap-1.5 hover:text-primary transition-colors " . ($active ? 'text-primary font-bold' : '') . "'>
                    <span class='whitespace-nowrap'>{$label}</span>
                    <i class='fa-solid {$icon} text-[10px]'></i>
                </a>";
    }
@endphp

<div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
    {{-- Header da Tabela --}}
    <div class="p-6 border-b border-gray-100 dark:border-gray-800 flex flex-col lg:flex-row justify-between items-center gap-4">
        <div class="flex items-center justify-between w-full lg:w-auto">
            <h2 class="text-lg font-black text-gray-900 dark:text-white tracking-tight uppercase flex items-center">
                Documentos 
                <span class="ml-3 px-2 py-0.5 bg-gray-100 dark:bg-gray-800 rounded-lg text-[10px] font-bold text-gray-500 dark:text-gray-400 normal-case tracking-normal">
                    {{ $documents->total() }} registros
                </span>
            </h2>
            
            {{-- Ações mobile - visível apenas em telas pequenas --}}
            <div class="lg:hidden">
                @can('documents.create')
                    <a href="{{ route('documents.create') }}" class="p-2 bg-primary text-white rounded-lg">
                        <i class="fa-solid fa-plus"></i>
                    </a>
                @endcan
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto">
            {{-- Busca Integrada --}}
            <form action="{{ route('documents.index') }}" method="GET" class="relative w-full sm:w-80 group">
                <x-text-input 
                    type="text" 
                    name="search" 
                    placeholder="Busca rápida..." 
                    class="w-full pl-10 pr-4 py-2 rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 focus:bg-white dark:focus:bg-gray-800 transition-all text-sm" 
                    :value="$requestParams['search'] ?? ''" 
                />
                <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary transition-colors">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </div>
            </form>

            {{-- Botões de Ação Desktop --}}
            <div class="hidden lg:flex items-center gap-2">
                @can('documents.create')
                    <x-ui.button variant="primary" size="sm" icon="fas fa-plus" onclick="window.location.href='{{ route('documents.create') }}'">
                        Novo
                    </x-ui.button>
                @endcan
                @can('documents.export.excel')
                    <x-ui.button variant="success" size="sm" icon="fas fa-file-export" onclick="window.location.href='{{ route('documents.export', request()->query()) }}'">
                        Exportar
                    </x-ui.button>
                @endcan
            </div>
        </div>
    </div>

    {{-- Tabela Responsiva --}}
    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left border-collapse min-w-[800px] lg:min-w-full">
            <thead>
                <tr class="bg-gray-50/50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                        {!! sortLink('Localização (Cx/Item)', 'boxes.number', $currentSortBy, $currentSortDir, $requestParams) !!}
                    </th>
                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                        {!! sortLink('Identificação (Nº/Cód)', 'documents.document_number', $currentSortBy, $currentSortDir, $requestParams) !!}
                    </th>
                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest w-1/3">
                        {!! sortLink('Documento (Título/Data)', 'documents.title', $currentSortBy, $currentSortDir, $requestParams) !!}
                    </th>
                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                        {!! sortLink('Projeto', 'projects.name', $currentSortBy, $currentSortDir, $requestParams) !!}
                    </th>
                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Sigilo</th>
                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                @forelse($documents as $document)
                    <tr class="group hover:bg-primary/[0.02] dark:hover:bg-primary/[0.05] transition-colors">
                        {{-- 1. Localização (Caixa / Item) --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-primary shrink-0">
                                    <i class="fa-solid fa-box-open text-xs"></i>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-gray-900 dark:text-gray-100 uppercase tracking-tighter">
                                        {{ $document->box->number ?? '---' }}
                                    </span>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase">Item: {{ $document->item_number ?? '--' }}</span>
                                </div>
                            </div>
                        </td>

                        {{-- 2. Identificação (Nº / Código) --}}
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-gray-900 dark:text-gray-100 tracking-tight">
                                    {{ $document->document_number }}
                                </span>
                                <span class="text-[10px] font-black text-primary/70 uppercase tracking-widest">{{ $document->code ?? 'S/C' }}</span>
                            </div>
                        </td>

                        {{-- 3. Documento (Título / Data) --}}
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 leading-snug truncate max-w-sm lg:max-w-md" title="{{ $document->title }}">
                                    {{ $document->title }}
                                </p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase flex items-center">
                                        <i class="fa-regular fa-calendar mr-1"></i> {{ $document->document_date ?? 'Sem data' }}
                                    </span>
                                    @if($document->version)
                                        <span class="text-[10px] font-black px-1.5 py-0.5 bg-gray-100 dark:bg-gray-800 rounded text-gray-500 uppercase tracking-tighter">v{{ $document->version }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- 4. Projeto --}}
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold text-gray-600 dark:text-gray-400 border-b border-dotted border-gray-300 dark:border-gray-700 cursor-help" title="{{ $document->project->name ?? '---' }}">
                                {{ $document->project->code ?? 'Geral' }}
                            </span>
                        </td>

                        {{-- 5. Sigilo --}}
                        <td class="px-6 py-4 text-center text-xs">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider border
                                {{ match(strtolower($document->confidentiality ?? '')) {
                                    'público', 'publico' => 'bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800',
                                    'restrito' => 'bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800',
                                    'confidencial' => 'bg-red-50 text-red-700 border-red-100 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800',
                                    default => 'bg-gray-50 text-gray-600 border-gray-100 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700'
                                } }}">
                                {{ $document->confidentiality ?? '---' }}
                            </span>
                        </td>

                        {{-- 6. Ações --}}
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1 lg:gap-2">
                                @can('documents.view')
                                    <x-ui.button variant="ghost-primary" size="sm" icon="fas fa-eye" @click="$store.modals.openDocumentDetails({{ $document->id }})" title="Ver Detalhes" />
                                @endcan
                                @can('documents.edit')
                                    <a href="{{ route('documents.edit', $document) }}" wire:navigate>
                                        <x-ui.button variant="ghost-warning" size="sm" icon="fas fa-edit" title="Editar" />
                                    </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-2xl bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-gray-300 dark:text-gray-600">
                                    <i class="fa-solid fa-folder-open text-3xl"></i>
                                </div>
                                <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Nenhum documento disponível</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
