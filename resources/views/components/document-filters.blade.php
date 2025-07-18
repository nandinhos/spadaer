@props([
    'projects' => [],
    'years' => [],
    'requestParams' => [], // Array com os parâmetros da request atual
])

{{-- O estado showFilters é controlado pelo Alpine na view principal --}}
<div class="p-4 mb-6 bg-gray-100 dark:bg-gray-800 rounded-lg shadow-sm">
    {{-- Cabeçalho clicável para expandir/recolher --}}
    <div class="flex items-center justify-between cursor-pointer" @click="showFilters = !showFilters">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            <i class="mr-2 fas fa-filter"></i> Filtros Avançados / Relatórios
        </h3>
        <button class="text-primary dark:text-primary-light" aria-label="Alternar filtros avançados">
            <i x-show="!showFilters" class="fas fa-chevron-down"></i>
            <i x-show="showFilters" class="fas fa-chevron-up"></i>
        </button>
    </div>

    {{-- Formulário GET para aplicar filtros --}}
    <form x-show="showFilters" x-transition action="{{ route('documents.index') }}" method="GET" class="mt-4">
        {{-- Mantém a ordenação, busca e paginação atuais ao filtrar --}}
        @if (isset($requestParams['sort_by']))
            <input type="hidden" name="sort_by" value="{{ $requestParams['sort_by'] }}">
        @endif
        @if (isset($requestParams['sort_dir']))
            <input type="hidden" name="sort_dir" value="{{ $requestParams['sort_dir'] }}">
        @endif
        @if (isset($requestParams['search']))
            <input type="hidden" name="search" value="{{ $requestParams['search'] }}">
        @endif
        @if (isset($requestParams['per_page']))
            <input type="hidden" name="per_page" value="{{ $requestParams['per_page'] }}">
        @endif

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            {{-- Filtro por Número da Caixa --}}
            <div>
                <x-input-label for="filter_box_number" value="Número da Caixa" />
                <x-text-input id="filter_box_number" name="filter_box_number" type="text" class="w-full mt-1"
                    :value="old('filter_box_number', $requestParams['filter_box_number'] ?? '')" placeholder="Ex: AD001" />
            </div>

            {{-- Filtro por Projeto --}}
            <div>
                <x-input-label for="filter_project_id" value="Projeto" />
                <x-select-input id="filter_project_id" name="filter_project_id" class="w-full mt-1" :currentValue="old('filter_project_id', $requestParams['filter_project_id'] ?? '')">
                    <option value="">Todos os Projetos</option>
                    @foreach ($projects as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </x-select-input>
            </div>

            {{-- Filtro por Ano --}}
            <div>
                <x-input-label for="filter_year" value="Ano do Documento" />
                <x-select-input id="filter_year" name="filter_year" class="w-full mt-1" :currentValue="old('filter_year', $requestParams['filter_year'] ?? '')">
                    <option value="">Todos os Anos</option>
                    @foreach ($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </x-select-input>
            </div>
        </div>

        {{-- Botões de Ação --}}
        <div class="flex justify-end gap-2 pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
            <x-secondary-button type="button"
                onclick="window.location.href='{{ route('documents.index', ['sort_by' => $requestParams['sort_by'] ?? null, 'sort_dir' => $requestParams['sort_dir'] ?? null, 'per_page' => $requestParams['per_page'] ?? null]) }}'">
                Limpar Filtros
            </x-secondary-button>
            <x-primary-button type="submit">
                Aplicar Filtros
            </x-primary-button>
        </div>
    </form>
</div>
