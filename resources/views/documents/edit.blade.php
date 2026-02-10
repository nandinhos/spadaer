{{-- resources/views/documents/edit.blade.php --}}
<x-app-layout>
    @section('title', 'Documentos')
    @section('header-title', 'Editar Documento')

    <x-slot name="header">
        <div class="flex flex-col items-start gap-4 sm:flex-row sm:items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('Editar Documento') }}: {{ $document->document_number }}
            </h2>
            <a href="{{ route('documents.index') }}" wire:navigate>
                <x-ui.button variant="secondary" icon="fas fa-arrow-left">
                    {{ __('Voltar para Lista') }}
                </x-ui.button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6 text-gray-900 md:p-8 dark:text-gray-100">

                    @if ($errors->any())
                        <div class="relative px-4 py-3 mb-6 text-red-700 bg-red-100 border border-red-400 rounded">
                            <strong class="font-bold">{{ __('Ops! Algo deu errado.') }}</strong>
                            <ul class="mt-2 text-sm list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('documents.update', $document) }}"
                        enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">

                            {{-- Caixa --}}
                            <div>
                                <x-input-label for="box_id" :value="__('Caixa')" />
                                <select id="box_id" name="box_id" required
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary focus:ring-primary">
                                    <option value="" disabled @selected(old('box_id', $document->box_id) === null)>
                                        {{ __('Selecione uma Caixa') }}
                                    </option>
                                    @isset($boxes)
                                        @foreach ($boxes as $id => $number)
                                            <option value="{{ $id }}" @selected(old('box_id', $document->box_id) == $id)>
                                                {{ $number }}
                                            </option>
                                        @endforeach
                                    @endisset
                                </select>
                                <x-input-error :messages="$errors->get('box_id')" class="mt-2" />
                            </div>

                            {{-- Item --}}
                            <div>
                                <x-input-label for="item_number" :value="__('Item (dentro da Caixa)')" />
                                <x-text-input id="item_number" name="item_number" type="text" required
                                    class="block w-full mt-1" placeholder="Ex: 001, 002..." :value="old('item_number', $document->item_number)" />
                                <x-input-error :messages="$errors->get('item_number')" class="mt-2" />
                            </div>

                            {{-- Projeto --}}
                            <div>
                                <x-input-label for="project_id" :value="__('Projeto (Opcional)')" />
                                <select id="project_id" name="project_id"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary focus:ring-primary">
                                    <option value="" @selected(old('project_id', $document->project_id) == '')>
                                        {{ __('-- Nenhum --') }}
                                    </option>
                                    @isset($projects)
                                        @foreach ($projects as $id => $name)
                                            <option value="{{ $id }}" @selected(old('project_id', $document->project_id) == $id)>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    @endisset
                                </select>
                                <x-input-error :messages="$errors->get('project_id')" class="mt-2" />
                            </div>

                            {{-- Código --}}
                            <div>
                                <x-input-label for="code" :value="__('Código do Documento (Opcional)')" />
                                <x-text-input id="code" name="code" type="text" class="block w-full mt-1"
                                    :value="old('code', $document->code)" />
                                <x-input-error :messages="$errors->get('code')" class="mt-2" />
                            </div>

                            {{-- Descritor --}}
                            <div>
                                <x-input-label for="descriptor" :value="__('Descritor (Opcional)')" />
                                <x-text-input id="descriptor" name="descriptor" type="text" class="block w-full mt-1"
                                    :value="old('descriptor', $document->descriptor)" />
                                <x-input-error :messages="$errors->get('descriptor')" class="mt-2" />
                            </div>

                            {{-- Número do Documento --}}
                            <div>
                                <x-input-label for="document_number" :value="__('Número do Documento')" />
                                <x-text-input id="document_number" name="document_number" type="text" required
                                    class="block w-full mt-1" :value="old('document_number', $document->document_number)" />
                                <x-input-error :messages="$errors->get('document_number')" class="mt-2" />
                            </div>

                            {{-- Título --}}
                            <div class="md:col-span-3">
                                <x-input-label for="title" :value="__('Título')" />
                                <x-textarea id="title" name="title" class="block w-full mt-1" rows="3"
                                    required>{{ old('title', $document->title) }}</x-textarea>
                                <x-input-error :messages="$errors->get('title')" class="mt-2" />
                            </div>

                            {{-- Data do Documento (Como Texto) --}}
                            <div>
                                <x-input-label for="document_date" :value="__('Data do Documento (Mês/Ano)')" />
                                {{-- Alterado para type="text" --}}
                                <x-text-input id="document_date" name="document_date" type="text" required
                                    class="block w-full mt-1" placeholder="Ex: JAN/2024, FEV/2023"
                                    {{-- Exibe a string original do banco ou o valor antigo --}} :value="old('document_date', $document->document_date)" />
                                <x-input-error :messages="$errors->get('document_date')" class="mt-2" />
                            </div>

                            {{-- Sigilo --}}
                            <div>
                                <x-input-label for="confidentiality" :value="__('Nível de Sigilo')" />
                                <select id="confidentiality" name="confidentiality" required
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary focus:ring-primary">
                                    {{-- Adiciona uma opção vazia/default --}}
                                    <option value="" @selected(old('confidentiality', $document->confidentiality) === null || old('confidentiality', $document->confidentiality) === '')>Selecione...</option>
                                    @foreach (['Público', 'Restrito', 'Confidencial'] as $level)
                                        {{-- Use aqui os valores exatos que você salva/valida --}}
                                        <option value="{{ $level }}" @selected(old('confidentiality', $document->confidentiality) === $level)>
                                            {{ $level }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('confidentiality')" class="mt-2" />
                            </div>

                            {{-- Versão --}}
                            <div>
                                <x-input-label for="version" :value="__('Versão (Opcional)')" />
                                <x-text-input id="version" name="version" type="text" class="block w-full mt-1"
                                    :value="old('version', $document->version)" />
                                <x-input-error :messages="$errors->get('version')" class="mt-2" />
                            </div>

                            {{-- Info Cópia (Como Texto) --}}
                            <div class="md:col-span-3"> {{-- Ocupa largura total --}}
                                <x-input-label for="is_copy" :value="__('Informação da Cópia (Opcional)')" />
                                {{-- Alterado para type="text" --}}
                                <x-text-input id="is_copy" name="is_copy" type="text" class="block w-full mt-1"
                                    placeholder="Ex: Cópia 1, V2, Revisão B" {{-- Exibe a string original do banco ou o valor antigo --}}
                                    :value="old('is_copy', $document->is_copy)" />
                                <x-input-error :messages="$errors->get('is_copy')" class="mt-2" />
                            </div>

                        </div>

                        {{-- Botões de Ação --}}
                        <div
                            class="flex items-center justify-end gap-4 pt-6 mt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('documents.index') }}" wire:navigate>
                                <x-ui.button type="button" variant="ghost">
                                    {{ __('Cancelar') }}
                                </x-ui.button>
                            </a>
                            <x-ui.button type="submit" variant="primary">
                                {{ __('Salvar Alterações') }}
                            </x-ui.button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

</x-app-layout>
