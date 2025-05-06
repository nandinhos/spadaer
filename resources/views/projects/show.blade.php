<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold">Detalhes do Projeto</h2>
                        <div class="space-x-2">
                            <a href="{{ route('projects.edit', $project) }}"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-edit mr-2"></i>Editar
                            </a>
                            <a href="{{ route('projects.index') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-arrow-left mr-2"></i>Voltar
                            </a>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-lg font-semibold mb-4">Informações Básicas</h3>
                                <dl class="space-y-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nome</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $project->name }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Código</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $project->code }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                        <dd class="mt-1 text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $project->status === 'ativo' ? 'bg-green-100 text-green-800' : 
                                                   ($project->status === 'concluído' ? 'bg-blue-100 text-blue-800' : 
                                                   'bg-yellow-100 text-yellow-800') }}">
                                                {{ ucfirst($project->status) }}
                                            </span>
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                            <div>
                                <div>
                                    {{-- REMOVER O TÍTULO H3 ABAIXO SE NÃO HOUVER MAIS NADA NESTA SEÇÃO --}}
                                    {{-- <h3 class="text-lg font-semibold mb-4">Datas e Descrição</h3> --}}
                                    <dl class="space-y-2">
                                        {{-- REMOVER ESTE BLOCO --}}
                                        {{-- <div> --}}
                                            {{-- <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Data de Início</dt> --}}
                                            {{-- <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100"> --}}
                                                {{-- {{ $project->start_date ? $project->start_date->format('d/m/Y') : 'Não definida' }} --}}
                                            {{-- </dd> --}}
                                        {{-- </div> --}}
                                        {{-- FIM DO BLOCO A REMOVER --}}
                                        
                                        {{-- REMOVER ESTE BLOCO --}}
                                        {{-- <div> --}}
                                            {{-- <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Data de Término</dt> --}}
                                            {{-- <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100"> --}}
                                                {{-- {{ $project->end_date ? $project->end_date->format('d/m/Y') : 'Não definida' }} --}}
                                            {{-- </dd> --}}
                                        {{-- </div> --}}
                                        {{-- FIM DO BLOCO A REMOVER --}}
                                        
                                        {{-- MANTER ESTE BLOCO --}}
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Descrição</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                {{ $project->description ?: 'Nenhuma descrição fornecida.' }}
                                            </dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Estatísticas -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="bg-white dark:bg-gray-700 rounded-lg p-6 shadow">
                            <h3 class="text-lg font-semibold mb-4">Documentos</h3>
                            <div class="text-3xl font-bold text-primary">{{ $project->documents->count() }}</div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">documentos associados</p>
                            @if($project->documents->count() > 0)
                            <a href="{{ route('documents.index', ['filter_project_id' => $project->id]) }}"
                                class="mt-4 inline-block text-primary hover:text-primary-dark text-sm">
                                Ver todos os documentos
                            </a>
                            @endif
                        </div>

                        <div class="bg-white dark:bg-gray-700 rounded-lg p-6 shadow">
                            <h3 class="text-lg font-semibold mb-4">Caixas</h3>
                            <div class="text-3xl font-bold text-primary">{{ $project->boxes->count() }}</div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">caixas associadas</p>
                            @if($project->boxes->count() > 0)
                            <a href="{{ route('boxes.index', ['project_id' => $project->id]) }}"
                                class="mt-4 inline-block text-primary hover:text-primary-dark text-sm">
                                Ver todas as caixas
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>