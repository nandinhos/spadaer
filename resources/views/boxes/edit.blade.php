<x-app-layout>
    <x-slot name="title">
        Editar Caixa
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Editar Caixa
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <!-- Links de Navegação -->
            <div class="flex justify-end space-x-4 mb-6">
                <a href="{{ route('boxes.show', $box) }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                    <i class="fas fa-eye mr-2"></i>Ver Detalhes
                </a>
                <a href="{{ route('boxes.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Voltar
                </a>
            </div>

            <!-- Formulário -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <form action="{{ route('boxes.update', $box) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Número da Caixa -->
                    <div>
                        <label for="number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Número da Caixa</label>
                        <input type="text" name="number" id="number" value="{{ old('number', $box->number) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    </div>

                    <!-- Projeto -->
                    <div>
                        <label for="project" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Projeto</label>
                        <input type="text" name="project" id="project" value="{{ old('project', $box->project) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    </div>

                    <!-- Período -->
                    <div>
                        <label for="year_range" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Período</label>
                        <input type="text" name="year_range" id="year_range" value="{{ old('year_range', $box->year_range) }}" required
                            placeholder="Ex: 2010-2011"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    </div>

                    <!-- Arquivo Corrente -->
                    <div>
                        <label for="current_archive" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Arquivo Corrente (Ano)</label>
                        <input type="number" name="current_archive" id="current_archive" value="{{ old('current_archive', $box->current_archive) }}" required
                            min="1900" max="2100"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    </div>

                    <!-- Arquivo Intermediário -->
                    <div>
                        <label for="intermediate_archive" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Arquivo Intermediário (Ano)</label>
                        <input type="number" name="intermediate_archive" id="intermediate_archive" value="{{ old('intermediate_archive', $box->intermediate_archive) }}" required
                            min="1900" max="2100"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    </div>

                    <!-- Destinação Final -->
                    <div>
                        <label for="final_destination" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Destinação Final</label>
                        <select name="final_destination" id="final_destination" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            <option value="">Selecione...</option>
                            <option value="Guarda permanente" {{ old('final_destination', $box->final_destination) == 'Guarda permanente' ? 'selected' : '' }}>Guarda permanente</option>
                            <option value="Eliminação" {{ old('final_destination', $box->final_destination) == 'Eliminação' ? 'selected' : '' }}>Eliminação</option>
                        </select>
                    </div>

                    <!-- Botões -->
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('boxes.show', $box) }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                            Cancelar
                        </a>
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors duration-200">
                            <i class="fas fa-save mr-2"></i>Atualizar Caixa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>