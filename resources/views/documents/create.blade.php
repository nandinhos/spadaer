<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                {{ __('Novo Documento') }}
            </h2>
            <a href="{{ route('documents.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('documents.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Caixa e Item -->
                            <div class="col-span-1">
                                <label for="box_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Caixa</label>
                                <input type="text" name="box_number" id="box_number" value="{{ old('box_number') }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            </div>

                            <div class="col-span-1">
                                <label for="item_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Item</label>
                                <input type="text" name="item_number" id="item_number" value="{{ old('item_number') }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            </div>

                            <!-- Código e Descritor -->
                            <div class="col-span-1">
                                <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Código</label>
                                <input type="text" name="code" id="code" value="{{ old('code') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            </div>

                            <div class="col-span-1">
                                <label for="descriptor" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descritor</label>
                                <input type="text" name="descriptor" id="descriptor" value="{{ old('descriptor') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            </div>

                            <!-- Número e Título -->
                            <div class="col-span-1">
                                <label for="document_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Número</label>
                                <input type="text" name="document_number" id="document_number" value="{{ old('document_number') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            </div>

                            <div class="col-span-2">
                                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Título</label>
                                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            </div>

                            <!-- Data e Projeto -->
                            <div class="col-span-1">
                                <label for="document_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data (Texto)</label>
                                <input type="text" name="document_date" id="document_date" value="{{ old('document_date') }}" required placeholder="Ex: 2024-12-31 ou 31/12/2024"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            </div>

                            <div class="col-span-1">
                                <label for="project" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Projeto</label>
                                <input type="text" name="project" id="project" value="{{ old('project') }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            </div>

                            <!-- Sigilo e Versão -->
                            <div class="col-span-1">
                                <label for="confidentiality" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sigilo (Texto)</label>
                                <input type="text" name="confidentiality" id="confidentiality" value="{{ old('confidentiality') }}" placeholder="Ex: Público, Restrito, Confidencial"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                {{-- Se preferir um select, mantenha-o, mas o valor será a string --}}
                                {{-- <select name="confidentiality" id="confidentiality"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                    <option value="" {{ old('confidentiality') == '' ? 'selected' : '' }}>Nenhum</option>
                                    <option value="Público" {{ old('confidentiality') == 'Público' ? 'selected' : '' }}>Público</option>
                                    <option value="Restrito" {{ old('confidentiality') == 'Restrito' ? 'selected' : '' }}>Restrito</option>
                                    <option value="Confidencial" {{ old('confidentiality') == 'Confidencial' ? 'selected' : '' }}>Confidencial</option>
                                </select> --}}
                            </div>

                            <div class="col-span-1">
                                <label for="version" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Versão</label>
                                <input type="text" name="version" id="version" value="{{ old('version') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            </div>

                            <!-- Cópia -->
                            <div class="col-span-1">
                                <label for="is_copy" class="block text-sm font-medium text-gray-700 dark:text-gray-300">É Cópia? (Texto)</label>
                                <input type="text" name="is_copy" id="is_copy" value="{{ old('is_copy') }}" placeholder="Ex: Sim, Não, True, False, 1, 0"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                {{-- Alternativa com Select se preferir limitar as opções --}}
                                {{-- <select name="is_copy" id="is_copy"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                    <option value="" {{ old('is_copy') == '' ? 'selected' : '' }}>Não especificado</option>
                                    <option value="Sim" {{ old('is_copy') == 'Sim' ? 'selected' : '' }}>Sim</option>
                                    <option value="Não" {{ old('is_copy') == 'Não' ? 'selected' : '' }}>Não</option>
                                </select> --}}
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <button type="reset" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                                Limpar
                            </button>
                            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors duration-200">
                                Salvar Documento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>