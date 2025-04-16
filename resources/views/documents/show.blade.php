<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                {{ __('Visualizar Documento') }}
            </h2>
            <div class="space-x-4">
                <a href="{{ route('documents.edit', $document) }}" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors duration-200">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
                <a href="{{ route('documents.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Voltar
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
                            <p class="mt-1 text-lg font-semibold">{{ $document->caixa }}</p>
                        </div>

                        <div class="col-span-1">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Item</h3>
                            <p class="mt-1 text-lg font-semibold">{{ $document->item }}</p>
                        </div>

                        <!-- Código e Descritor -->
                        <div class="col-span-1">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Código</h3>
                            <p class="mt-1 text-lg font-semibold">{{ $document->codigo ?? 'N/A' }}</p>
                        </div>

                        <div class="col-span-1">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Descritor</h3>
                            <p class="mt-1 text-lg font-semibold">{{ $document->descritor ?? 'N/A' }}</p>
                        </div>

                        <!-- Número e Título -->
                        <div class="col-span-1">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Número</h3>
                            <p class="mt-1 text-lg font-semibold">{{ $document->numero ?? 'N/A' }}</p>
                        </div>

                        <div class="col-span-2">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Título</h3>
                            <p class="mt-1 text-lg font-semibold">{{ $document->titulo }}</p>
                        </div>

                        <!-- Data e Projeto -->
                        <div class="col-span-1">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Data</h3>
                            <p class="mt-1 text-lg font-semibold">{{ $document->data->format('d/m/Y') }}</p>
                        </div>

                        <div class="col-span-1">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Projeto</h3>
                            <p class="mt-1 text-lg font-semibold">{{ $document->projeto }}</p>
                        </div>

                        <!-- Sigilo e Versão -->
                        <div class="col-span-1">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Sigilo</h3>
                            <p class="mt-1 text-lg font-semibold">
                                @if($document->sigilo)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $document->sigilo == 'confidencial' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($document->sigilo) }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Público
                                    </span>
                                @endif
                            </p>
                        </div>

                        <div class="col-span-1">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Versão</h3>
                            <p class="mt-1 text-lg font-semibold">{{ $document->versao ?? 'N/A' }}</p>
                        </div>

                        <!-- Cópia -->
                        <div class="col-span-1">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</h3>
                            <p class="mt-1 text-lg font-semibold">
                                @if($document->copia)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Cópia
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Original
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Ações -->
                    <div class="mt-8 flex justify-between items-center border-t dark:border-gray-700 pt-6">
                        <form action="{{ route('documents.destroy', $document) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200"
                                onclick="return confirm('Tem certeza que deseja excluir este documento?')">
                                <i class="fas fa-trash-alt mr-2"></i>Excluir Documento
                            </button>
                        </form>

                        <div class="space-x-4">
                            <a href="{{ route('documents.edit', $document) }}" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors duration-200">
                                <i class="fas fa-edit mr-2"></i>Editar
                            </a>
                            <a href="{{ route('documents.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                                <i class="fas fa-arrow-left mr-2"></i>Voltar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>