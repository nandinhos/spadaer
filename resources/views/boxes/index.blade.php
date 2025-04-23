<x-app-layout>
    @section('title', 'Caixas')
    @section('header-title', 'Gerenciamento de Caixas')

    <div class="mb-4 p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
        {{-- Formulário de Filtro/Busca aqui (usando GET) --}}
        <form method="GET" action="{{ route('boxes.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <x-input-label for="search" value="Buscar Nº/Local" />
                    <x-text-input id="search" name="search" :value="request('search')" class="w-full mt-1" />
                </div>
                <div>
                    <x-input-label for="project_id" value="Projeto" />
                    <x-select-input id="project_id" name="project_id" class="w-full mt-1" :currentValue="request('project_id')">
                        <option value="">Todos</option>
                        @foreach($projects as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </x-select-input>
                </div>
                <div>
                    <x-input-label for="checker_id" value="Conferente" />
                    <x-select-input id="checker_id" name="checker_id" class="w-full mt-1" :currentValue="request('checker_id')">
                        <option value="">Todos</option>
                        @foreach($checkers as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </x-select-input>
                </div>
                <div class="flex items-end space-x-2">
                    <x-primary-button type="submit">Filtrar</x-primary-button>
                    <x-secondary-button onclick="window.location.href='{{ route('boxes.index') }}'">Limpar</x-secondary-button>
                </div>
            </div>
        </form>
    </div>

    <div class="mb-4 flex justify-end">
        <x-primary-button onclick="window.location.href='{{ route('boxes.create') }}'">
            <i class="fas fa-plus mr-2"></i> Adicionar Caixa
        </x-primary-button>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        {{-- Cabeçalhos da Tabela (Número, Local, Projeto, Conferente, Data Conf., Ações) --}}
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Número</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Local Físico</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Projeto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Conferente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data Conferência</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($boxes as $box)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-750/50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $box->number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">{{ $box->physical_location ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">{{ $box->project?->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">{{ $box->checker?->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">{{ $box->conference_date?->format('d/m/Y') ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('boxes.show', $box) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-200 mr-2">Ver</a>
                            <a href="{{ route('boxes.edit', $box) }}" class="text-primary dark:text-primary-light hover:text-primary-dark dark:hover:text-white">Editar</a>
                            {{-- Botão Excluir com confirmação --}}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                            Nenhuma caixa encontrada.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Paginação --}}
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $boxes->links() }}
        </div>
    </div>
</x-app-layout>