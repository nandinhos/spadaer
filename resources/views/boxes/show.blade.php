<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detalhes da Caixa') }}
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Links de Navegação -->
            <div class="flex justify-end space-x-4 mb-6">
                <a href="{{ route('boxes.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Voltar
                </a>
            </div>

            <div class="max-w-2xl mx-auto">
                <!-- Card da Caixa (Capa) -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 text-center border-4 border-gray-800 dark:border-gray-600">
                    <div class="mb-6">
                        <img src="{{ asset('images/logo.png') }}" alt="COPAC" class="w-24 h-24 mx-auto mb-4">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">COPAC</h2>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200">GAC-PAC</h3>
                    </div>

                    <div class="mb-6">
                        <div class="text-5xl font-bold text-gray-900 dark:text-gray-100 mb-4">{{ $boxInfo['number'] }}</div>
                        <h4 class="text-lg font-medium text-gray-700 dark:text-gray-300">{{ implode(', ', $boxInfo['projects']) }}</h4>
                    </div>

                    <div class="text-2xl font-bold text-gray-700 dark:text-gray-300 mb-8">CX {{ $boxInfo['number'] }}</div>

                    <div class="space-y-3 text-left border-t border-gray-300 dark:border-gray-600 pt-6">
                        <p class="text-gray-700 dark:text-gray-300">
                            <span class="font-medium">Total de Documentos:</span> {{ $boxInfo['totalDocuments'] }}
                        </p>
                        <p class="text-gray-700 dark:text-gray-300">
                            <span class="font-medium">Projetos:</span> {{ implode(', ', $boxInfo['projects']) }}
                        </p>
                    </div>
                </div>

                <!-- Lista de Documentos -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                        <h2 class="text-2xl font-bold mb-6">Documentos na Caixa</h2>

                        @if($documents->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Item</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Código</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Título</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($documents as $document)
                                            <tr>
                                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">{{ $document->item_number }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">{{ $document->code }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">{{ $document->title }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">
                                                    {{ \Carbon\Carbon::parse($document->document_date)->format('d/m/Y') }}
                                                </td>
                                                <td class="px-6 py-4 text-sm font-medium space-x-2">
                                                    <a href="{{ route('documents.show', $document) }}" class="text-primary hover:text-primary-dark dark:text-primary-light dark:hover:text-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('documents.edit', $document) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-6">
                                {{ $documents->links() }}
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400 text-center py-4">Nenhum documento encontrado nesta caixa.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>