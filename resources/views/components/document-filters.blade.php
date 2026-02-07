@props([
    'projects' => [],
    'years' => [],
    'requestParams' => [],
])

<div x-data="{ open: {{ request()->hasAny(['filter_box_number', 'filter_project_id', 'filter_year']) ? 'true' : 'false' }} }" class="mb-8 overflow-hidden bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm transition-all duration-300">
    {{-- Header Clicável --}}
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

    {{-- Formulário --}}
    <form 
        x-show="open" 
        x-cloak
        x-collapse
        action="{{ route('documents.index') }}" 
        method="GET" 
        class="px-6 pb-6"
    >
        {{-- Preservar outros parâmetros --}}
        @foreach(['sort_by', 'sort_dir', 'search', 'per_page'] as $param)
            @if(isset($requestParams[$param]))
                <input type="hidden" name="{{ $param }}" value="{{ $requestParams[$param] }}">
            @endif
        @endforeach

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4">
            <div>
                <x-input-label for="filter_box_number" value="Nº da Caixa" class="text-[10px] font-black uppercase text-gray-400 mb-1.5" />
                <x-text-input 
                    id="filter_box_number" 
                    name="filter_box_number" 
                    type="text" 
                    class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50" 
                    :value="$requestParams['filter_box_number'] ?? ''" 
                    placeholder="Ex: AD001" 
                />
            </div>

            <div>
                <x-input-label for="filter_project_id" value="Projeto Vinculado" class="text-[10px] font-black uppercase text-gray-400 mb-1.5" />
                <x-select-input 
                    id="filter_project_id" 
                    name="filter_project_id" 
                    class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50" 
                    :currentValue="$requestParams['filter_project_id'] ?? ''"
                >
                    <option value="">Todos os Projetos</option>
                    @foreach ($projects as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </x-select-input>
            </div>

            <div>
                <x-input-label for="filter_year" value="Ano de Referência" class="text-[10px] font-black uppercase text-gray-400 mb-1.5" />
                <x-select-input 
                    id="filter_year" 
                    name="filter_year" 
                    class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50" 
                    :currentValue="$requestParams['filter_year'] ?? ''"
                >
                    <option value="">Todos os Anos</option>
                    @foreach ($years as $year)
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
                onclick="window.location.href='{{ route('documents.index') }}'"
            >
                Limpar
            </x-ui.button>
            <x-ui.button variant="primary" size="sm" type="submit">
                Filtrar
            </x-ui.button>
        </div>
    </form>
</div>