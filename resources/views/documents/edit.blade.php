<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                {{ __('Editar Documento') }}
            </h2>
            <div class="space-x-4">
                <a href="{{ route('documents.show', $document) }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                    <i class="fas fa-eye mr-2"></i>Visualizar
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
                    <form action="{{ route('documents.update', $document) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Caixa e Item -->
                            <div class="col-span-1">
                                <label for="box" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Caixa</label>
                                <input type="text" name="box" id="box" value="{{ old('box', $document->box) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            </div>

                            <div class="col-span-1">
                                <label for="item" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Item</label>
                                <input type="text" name="item" id="item" value="{{ old('item', $document->item) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            </div>

                            <!-- Código e Descritor -->
                            <div class="col-span-1">
                                <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Código</label>
                                <input type="text" name="code" id="code" value="{{ old('code', $document->code) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            </div>

                            <div class="col-span-1">
                                <label for="descriptor" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descritor</label>
                                <input type="text" name="descriptor" id="descriptor" value="{{ old('descriptor', $document->descriptor) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            </div>

                            <!-- Número e Título -->
                            <div class="col-span-1">
                                <label for="number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Número</label>
                                <input type="text" name="number" id="number" value="{{ old('number', $document->number) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            </div>

                            <div class="col-span-2">
                                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Título</label>
                                <input type="text" name="title" id="title" value="{{ old('title', $document->title) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            </div>

                            <!-- Data e Projeto -->
                            <div class="col-span-1">
                                <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data</label>
                                <input type="date" name="date" id="date" value="{{ old('date', $document->date->format('Y-m-d')) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            </div>

                            <div class="col-span-1">
                                <label for="project" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Projeto</label>
                                <input type="text" name="project" id="project" value="{{ old('project', $document->project) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            </div>

                            <!-- Sigilo e Versão -->
                            <div class="col-span-1">
                                <label for="secrecy" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sigilo</label>
                                <select name="secrecy" id="secrecy"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                    <option value="">Nenhum</option>
                                    <option value="confidential" {{ old('secrecy', $document->secrecy) == 'confidential' ? 'selected' : '' }}>Confidencial</option>
                                    <option value="restricted" {{ old('secrecy', $document->secrecy) == 'restricted' ? 'selected' : '' }}>Restrito</option>
                                </select>
                            </div>

                            <div class="col-span-1">
                                <label for="version" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Versão</label>
                                <input type="text" name="version" id="version" value="{{ old('version', $document->version) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            </div>

                            <!-- Cópia -->
                            <div class="col-span-1">
                                <label class="inline-flex items-center mt-6">
                                    <input type="checkbox" name="copy" value="1" {{ old('copy', $document->copy) ? 'checked' : '' }}
                                        class="rounded border-gray-300 dark:border-gray-600 text-primary shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">É uma cópia?</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('documents.show', $document) }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                                Cancelar
                            </a>
                            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors duration-200">
                                Atualizar Documento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>