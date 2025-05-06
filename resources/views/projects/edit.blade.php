<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold">Editar Projeto</h2>
                        <a href="{{ route('projects.index') }}"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-arrow-left mr-2"></i>Voltar
                        </a>
                    </div>

                    <form action="{{ route('projects.update', $project) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        {{-- Grid apenas para Nome e Código --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nome do Projeto -->
                            <div>
                                <x-input-label for="name" value="Nome do Projeto" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                    value="{{ old('name', $project->name) }}" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- Código do Projeto -->
                            <div>
                                <x-input-label for="code" value="Código" />
                                <x-text-input id="code" name="code" type="text" class="mt-1 block w-full"
                                    value="{{ old('code', $project->code) }}" required />
                                <x-input-error :messages="$errors->get('code')" class="mt-2" />
                            </div>
                        </div> {{-- Fim do Grid --}}

                        {{-- Descrição movida para fora do grid --}}
                        <div>
                            <x-input-label for="description" value="Descrição" />
                            <textarea id="description" name="description"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary dark:focus:border-primary focus:ring-primary dark:focus:ring-primary rounded-md shadow-sm"
                                rows="4">{{ old('description', $project->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        {{-- Botão movido para fora do grid --}}
                        <div class="flex justify-end">
                            <x-primary-button>
                                <i class="fas fa-save mr-2"></i>Atualizar Projeto
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>