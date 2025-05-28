<x-app-layout>
    @can('boxes.view')
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Caixas') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Estatísticas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Total de Caixas</h3>
                    <p class="text-3xl font-bold text-primary">{{ $stats['totalBoxes'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Total de Documentos</h3>
                    <p class="text-3xl font-bold text-primary">{{ $stats['totalDocuments'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Total de Projetos</h3>
                    <p class="text-3xl font-bold text-primary">{{ $stats['totalProjects'] }}</p>
                </div>
            </div>

            <!-- Barra de Ações -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex-1 max-w-lg">
                            <form action="{{ route('boxes.index') }}" method="GET" class="flex gap-2">
                                <input type="text"
                                    name="search"
                                    value="{{ request('search') }}"
                                    placeholder="Pesquisar caixas..."
                                    class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors duration-200">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Grid de Caixas -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @forelse ($boxes as $box)
                        <a href="{{ route('boxes.show', $box->box_number) }}" class="block">
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-200">
                                <div class="p-6 border-4 border-gray-800 dark:border-gray-600 m-4 text-center">
                                    <div class="mb-4">
                                        <img src="{{ asset('images/logo.png') }}" alt="COPAC" class="w-16 h-16 mx-auto">
                                    </div>
                                    <h2 class="text-lg font-bold mb-2">COPAC<br>GAC-PAC</h2>

                                    <!-- Código da Caixa em Destaque -->
                                    <div class="text-6xl font-bold mb-4">{{ $box->code }}</div>

                                    <!-- Informações do Projeto -->
                                    @if($box->project)
                                    <div class="text-lg uppercase font-semibold mb-4">
                                        PROJETO DE PESQUISA<br>
                                        E DESENVOLVIMENTO<br>
                                        {{ $box->project_year ?? '2009-2010' }}
                                    </div>
                                    @endif

                                    <div class="text-lg font-bold mb-4">CX {{ str_pad($box->box_number, 3, '0', STR_PAD_LEFT) }}</div>

                                    <!-- Rodapé com Informações de Arquivo -->
                                    <div class="mt-6 pt-4 border-t border-gray-300 dark:border-gray-600 text-[10px]">
                                        <p class="mb-1">Período: {{ $box->year_range }}</p>
                                        <p class="mb-1">Arquivo Corrente: {{ $box->current_year }}</p>
                                        <p class="mb-1">Arquivo Intermediário: {{ $box->intermediate_year }}</p>
                                        <p>Destinação Final: Guarda permanente</p>
                                    </div>

                                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                        Total de Documentos: {{ $box->document_count }}
                                    </div>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="col-span-full text-center py-12">
                            <p class="text-gray-500 dark:text-gray-400">Nenhuma caixa encontrada.</p>
                        </div>
                        @endforelse
                    </div>

                    <!-- Paginação -->
                    <div class="mt-6">
                        {{ $boxes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcan
</x-app-layout>