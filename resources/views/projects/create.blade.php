{{-- resources/views/projects/create.blade.php --}}
<x-app-layout>
    @section('title', 'Projetos')
    @section('header-title', 'Novo Projeto')

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold leading-tight text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-diagram-project text-primary dark:text-primary-light"></i>
                    <span>{{ __('Novo Projeto') }}</span>
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Cadastre um novo projeto institucional para agrupamento de caixas e acervo documental.</p>
            </div>
            <a href="{{ route('projects.index') }}" wire:navigate>
                <x-ui.button variant="secondary" icon="fas fa-arrow-left">
                    {{ __('Voltar para Lista') }}
                </x-ui.button>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-ui.card>
                <x-ui.form-errors />

                <form action="{{ route('projects.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <x-ui.form-section 
                        title="Dados do Projeto" 
                        description="Defina a nomenclatura oficial, sigla/código de referência e escopo."
                        icon="fas fa-circle-info">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nome do Projeto -->
                            <div>
                                <x-input-label for="name" :value="__('Nome do Projeto')" :required="true" />
                                <x-text-input id="name" name="name" type="text" class="block w-full"
                                    :value="old('name')" required autofocus placeholder="Ex: Modernização do Sistema de Armamento" />
                                <x-input-error :messages="$errors->get('name')" />
                            </div>

                            <!-- Código do Projeto -->
                            <div>
                                <x-input-label for="code" :value="__('Código / Sigla')" :required="true" />
                                <x-text-input id="code" name="code" type="text" class="block w-full"
                                    :value="old('code')" required placeholder="Ex: PRJ-ARM-2024" />
                                <x-input-error :messages="$errors->get('code')" />
                            </div>

                            <!-- Descrição -->
                            <div class="md:col-span-2">
                                <x-input-label for="description" :value="__('Descrição do Projeto')" />
                                <x-textarea id="description" name="description" class="block w-full"
                                    rows="4" placeholder="Descreva os objetivos, comissões envolvidas e escopo do projeto...">{{ old('description') }}</x-textarea>
                                <x-input-error :messages="$errors->get('description')" />
                            </div>
                        </div>
                    </x-ui.form-section>

                    {{-- Ações do Formulário --}}
                    <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-800">
                        <a href="{{ route('projects.index') }}" wire:navigate class="w-full sm:w-auto">
                            <x-ui.button type="button" variant="secondary" class="w-full sm:w-auto justify-center">
                                {{ __('Cancelar') }}
                            </x-ui.button>
                        </a>
                        <x-ui.button type="submit" variant="primary" icon="fas fa-save" class="w-full sm:w-auto justify-center">
                            {{ __('Salvar Projeto') }}
                        </x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>