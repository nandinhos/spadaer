<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold">Projetos</h2>
                        <a href="{{ route('projects.create') }}"
                            class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-plus mr-2"></i>Novo Projeto
                        </a>
                    </div>

                    <!-- Filtros e Busca -->
                    <div class="mb-6">
                        <form action="{{ route('projects.index') }}" method="GET" class="flex gap-4">
                            <div class="flex-1">
                                <x-text-input
                                    type="text"
                                    name="search"
                                    placeholder="Buscar por nome, código ou descrição..."
                                    value="{{ request('search') }}"
                                    class="w-full" />
                            </div>
                            <x-primary-button type="submit">
                                <i class="fas fa-search mr-2"></i>Buscar
                            </x-primary-button>
                        </form>
                    </div>

                    <!-- Tabela de Projetos -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <a href="{{ route('projects.index', array_merge(request()->query(), ['sort_by' => 'code', 'sort_dir' => request('sort_by') === 'code' && request('sort_dir') === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center">
                                            Código
                                            @if(request('sort_by') === 'code')
                                            <i class="fas fa-sort-{{ request('sort_dir') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                            @else
                                            <i class="fas fa-sort ml-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <a href="{{ route('projects.index', array_merge(request()->query(), ['sort_by' => 'name', 'sort_dir' => request('sort_by') === 'name' && request('sort_dir') === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center">
                                            Nome
                                            @if(request('sort_by') === 'name')
                                            <i class="fas fa-sort-{{ request('sort_dir') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                            @else
                                            <i class="fas fa-sort ml-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    {{-- Adicionado Cabeçalho Descrição --}}
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <a href="{{ route('projects.index', array_merge(request()->query(), ['sort_by' => 'description', 'sort_dir' => request('sort_by') === 'description' && request('sort_dir') === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center">
                                            Descrição
                                            @if(request('sort_by') === 'description')
                                            <i class="fas fa-sort-{{ request('sort_dir') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                            @else
                                            <i class="fas fa-sort ml-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    {{-- Removido Cabeçalho Criado Em --}}
                                    {{-- Removido Cabeçalho Atualizado Em --}}
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Ações</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($projects as $project)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $project->code }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $project->name }}
                                        </td>
                                        {{-- Adicionada Célula Descrição --}}
                                        <td class="px-6 py-4 whitespace-normal text-sm text-gray-500 dark:text-gray-300 max-w-xs break-words">
                                            {{ $project->description }}
                                        </td>
                                        {{-- Removida Célula Criado Em --}}
                                        {{-- Removida Célula Atualizado Em --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            <a href="{{ route('projects.show', $project) }}"
                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('projects.edit', $project) }}"
                                                class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-600" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline-block" onsubmit="return confirm('Tem certeza que deseja excluir este projeto?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-600" title="Excluir">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                <tr>
                                    {{-- Ajustado colspan devido à remoção da coluna Status --}}
                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                        Nenhum projeto encontrado.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginação -->
                    <div class="mt-4">
                        {{ $projects->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>