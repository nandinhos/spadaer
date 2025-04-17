@props([
    'projects' => [],
    'years' => [],
    'requestParams' => [], // Array com os parâmetros da request atual
])

{{-- O estado showFilters é controlado pelo Alpine no layout ou na view principal --}}
<div class="mb-6 bg-gray-100 dark:bg-gray-800 rounded-lg p-4 shadow-sm">
    <div class="flex items-center justify-between mb-2 cursor-pointer" @click="showFilters = !showFilters">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Filtros Avançados</h2>
        <button class="text-primary dark:text-primary-light">
            <i x-bind:class="showFilters ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
        </button>
    </div>

    {{-- Formulário GET para aplicar filtros --}}
    <form action="{{ route('documents.index') }}" method="GET">
        {{-- Mantém a ordenação atual ao filtrar --}}
        @if(isset($requestParams['sort_by']))
            <input type="hidden" name="sort_by" value="{{ $requestParams['sort_by'] }}">
        @endif
        @if(isset($requestParams['sort_dir']))
            <input type="hidden" name="sort_dir" value="{{ $requestParams['sort_dir'] }}">
        @endif
         {{-- Mantém a busca atual ao filtrar --}}
         @if(isset($requestParams['search']))
             <input type="hidden" name="search" value="{{ $requestParams['search'] }}">
         @endif
         {{-- Mantém itens por página --}}
         @if(isset($requestParams['per_page']))
             <input type="hidden" name="per_page" value="{{ $requestParams['per_page'] }}">
         @endif

        <div x-show="showFilters" x-transition class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <div>
                <x-input-label for="filter_box" value="Número da Caixa" class="mb-1" />
                <x-text-input
                    id="filter_box"
                    name="filter_box"
                    type="text"
                    class="w-full text-base"
                    :value="old('filter_box', $requestParams['filter_box'] ?? '')" {{-- Preenche com valor antigo ou da request --}}
                    placeholder="Filtrar por caixa"
                />
            </div>
            <div>
                <x-input-label for="filter_project" value="Projeto" class="mb-1" />
                <x-select-input
                    id="filter_project"
                    name="filter_project"
                    class="w-full text-base"
                    :currentValue="old('filter_project', $requestParams['filter_project'] ?? '')"
                >
                    <option value="">Todos os projetos</option>
                    @foreach($projects as $project)
                        <option value="{{ $project }}">{{ $project }}</option>
                    @endforeach
                </x-select-input>
            </div>
            <div>
                <x-input-label for="filter_year" value="Ano" class="mb-1" />
                <x-select-input
                    id="filter_year"
                    name="filter_year"
                    class="w-full text-base"
                     :currentValue="old('filter_year', $requestParams['filter_year'] ?? '')"
                >
                    <option value="">Todos os anos</option>
                     @foreach($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </x-select-input>
            </div>
            <div class="md:col-span-3 flex flex-col sm:flex-row justify-end gap-2">
                 {{-- Link para limpar (redireciona sem parâmetros de filtro) --}}
                <x-secondary-button
                     type="button" {{-- Tipo button para não submeter o form --}}
                     onclick="window.location.href='{{ route('documents.index', ['sort_by' => $requestParams['sort_by'] ?? null, 'sort_dir' => $requestParams['sort_dir'] ?? null, 'per_page' => $requestParams['per_page'] ?? null ]) }}'"
                     {{-- Ou usar a função Alpine: @click="window.location.href=getClearFiltersUrl()" --}}
                     class="justify-center"
                >
                     Limpar Filtros
                </x-secondary-button>
                <x-primary-button type="submit" class="justify-center">
                     Aplicar Filtros
                </x-primary-button>
            </div>
        </div>
    </form>
</div>