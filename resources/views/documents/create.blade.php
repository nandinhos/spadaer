{{-- resources/views/documents/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col items-start gap-4 sm:flex-row sm:items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('Adicionar Novo Documento') }}
            </h2>
            <a href="{{ route('documents.index') }}"
                class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-gray-700 uppercase transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:border-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                <i class="mr-2 fas fa-arrow-left"></i>{{ __('Voltar para Lista') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6 text-gray-900 md:p-8 dark:text-gray-100">

                    {{-- Exibição de Erros Gerais --}}
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

                    {{-- Formulário aponta para a rota store --}}
                    <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data"
                        class="space-y-6">
                        @csrf
                        {{-- Não precisa de @method('PUT') para create --}}

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">

                            {{-- Caixa --}}
                            <div>
                                <x-input-label for="box_id" :value="__('Caixa')" />
                                <select id="box_id" name="box_id" required
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="" disabled @selected(old('box_id') === null)> {{-- Default é desabilitado --}}
                                        {{ __('Selecione uma Caixa') }}
                                    </option>
                                    {{-- $boxes deve ser passado pelo DocumentController@create --}}
                                    @isset($boxes)
                                        @foreach ($boxes as $id => $number)
                                            <option value="{{ $id }}" @selected(old('box_id') == $id)>
                                                {{-- Usa old() para reter seleção --}}
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
                                    class="block w-full mt-1" placeholder="Ex: 001, 002..." :value="old('item_number')" />
                                {{-- Apenas old() --}}
                                <x-input-error :messages="$errors->get('item_number')" class="mt-2" />
                            </div>

                            {{-- Projeto --}}
                            <div>
                                <x-input-label for="project_id" :value="__('Projeto (Opcional)')" />
                                <select id="project_id" name="project_id"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="" @selected(old('project_id') == '')>{{ __('-- Nenhum --') }}
                                    </option> {{-- Default é '-- Nenhum --' --}}
                                    {{-- $projects deve ser passado pelo DocumentController@create --}}
                                    @isset($projects)
                                        @foreach ($projects as $id => $name)
                                            <option value="{{ $id }}" @selected(old('project_id') == $id)>
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
                                    :value="old('code')" /> {{-- Apenas old() --}}
                                <x-input-error :messages="$errors->get('code')" class="mt-2" />
                            </div>

                            {{-- Descritor --}}
                            <div>
                                <x-input-label for="descriptor" :value="__('Descritor (Opcional)')" />
                                <x-text-input id="descriptor" name="descriptor" type="text" class="block w-full mt-1"
                                    :value="old('descriptor')" /> {{-- Apenas old() --}}
                                <x-input-error :messages="$errors->get('descriptor')" class="mt-2" />
                            </div>

                            {{-- Número do Documento --}}
                            <div>
                                <x-input-label for="document_number" :value="__('Número do Documento')" />
                                <x-text-input id="document_number" name="document_number" type="text" required
                                    class="block w-full mt-1" :value="old('document_number')" /> {{-- Apenas old() --}}
                                <x-input-error :messages="$errors->get('document_number')" class="mt-2" />
                            </div>

                            {{-- Título --}}
                            <div class="md:col-span-3">
                                <x-input-label for="title" :value="__('Título')" />
                                <x-textarea id="title" name="title" class="block w-full mt-1" rows="3"
                                    required>{{ old('title') }}</x-textarea> {{-- Apenas old() --}}
                                <x-input-error :messages="$errors->get('title')" class="mt-2" />
                            </div>

                            {{-- Data do Documento (Como Texto) --}}
                            <div>
                                <x-input-label for="document_date" :value="__('Data do Documento (Mês/Ano)')" />
                                <x-text-input id="document_date" name="document_date" type="text" required
                                    class="block w-full mt-1" placeholder="Ex: JAN/2024, FEV/2023" :value="old('document_date')" />
                                {{-- Apenas old() --}}
                                <x-input-error :messages="$errors->get('document_date')" class="mt-2" />
                            </div>

                            {{-- Sigilo --}}
                            <div>
                                <x-input-label for="confidentiality" :value="__('Nível de Sigilo')" />
                                <select id="confidentiality" name="confidentiality" required
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="" disabled @selected(old('confidentiality') === null || old('confidentiality') === '')>
                                        {{ __('Selecione...') }}</option>
                                    @foreach (['Público', 'Restrito', 'Confidencial'] as $level)
                                        {{-- Use os valores exatos --}}
                                        <option value="{{ $level }}" @selected(old('confidentiality') === $level)>
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
                                    :value="old('version')" /> {{-- Apenas old() --}}
                                <x-input-error :messages="$errors->get('version')" class="mt-2" />
                            </div>

                            {{-- Info Cópia (Como Texto) --}}
                            <div class="md:col-span-3">
                                <x-input-label for="is_copy" :value="__('Informação da Cópia (Opcional)')" />
                                <x-text-input id="is_copy" name="is_copy" type="text" class="block w-full mt-1"
                                    placeholder="Ex: Cópia 1, V2, Revisão B" :value="old('is_copy')" />
                                {{-- Apenas old() --}}
                                <x-input-error :messages="$errors->get('is_copy')" class="mt-2" />
                            </div>

                        </div>

                        {{-- Botões de Ação --}}
                        <div
                            class="flex items-center justify-end gap-4 pt-6 mt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('documents.index') }}"
                                class="text-sm text-gray-600 rounded-md dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button>
                                {{ __('Criar Documento') }} {{-- Texto do botão ajustado --}}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
