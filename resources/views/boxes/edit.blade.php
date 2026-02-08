{{-- resources/views/boxes/edit.blade.php --}}
<x-app-layout>
    @can('boxes.edit')
    <x-slot name="header">
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('Editar Caixa') }}: {{ $box->number }}
            </h2>
            {{-- Botão Voltar para Detalhes da Caixa --}}
            <a href="{{ route('boxes.show', $box) }}" wire:navigate>
                <x-ui.button variant="secondary" icon="fas fa-arrow-left">
                    {{ __('Voltar para Detalhes') }}
                </x-ui.button>
            </a>
            {{-- REMOVIDO: Botão para abrir Modal de Importação (Ele estará na view 'show') --}}
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6 text-gray-900 md:p-8 dark:text-gray-100">

                    {{-- Exibição de Erros Gerais do Formulário (Validação da Caixa) --}}
                    @if ($errors->any())
                    <div class="relative px-4 py-3 mb-6 text-red-700 bg-red-100 border border-red-400 rounded"
                        role="alert">
                        <strong class="font-bold">{{ __('Ops! Algo deu errado.') }}</strong>
                        <ul class="mt-2 text-sm list-disc list-inside">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Formulário para Editar Caixa --}}
                    {{-- REMOVIDO: enctype="multipart/form-data" (não é necessário sem upload) --}}
                    <form method="POST" action="{{ route('boxes.update', $box) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        {{-- Número da Caixa --}}
                        <div>
                            <x-input-label for="number" :value="__('Número da Caixa')" />
                            <x-text-input id="number" name="number" type="text" class="block w-full mt-1"
                                :value="old('number', $box->number)" required autofocus placeholder="Ex: AD001, CX-2024-05" />
                            <x-input-error :messages="$errors->get('number')" class="mt-2" />
                        </div>

                        {{-- Local Físico --}}
                        <div>
                            <x-input-label for="physical_location" :value="__('Local Físico')" />
                            <x-text-input id="physical_location" name="physical_location" type="text"
                                class="block w-full mt-1" :value="old('physical_location', $box->physical_location)"
                                placeholder="Ex: Prateleira A-1 / Nível 1" />
                            <x-input-error :messages="$errors->get('physical_location')" class="mt-2" />
                        </div>

                        {{-- Projeto (Select) --}}
                        <div>
                            <x-input-label for="project_id" :value="__('Projeto Associado (Opcional)')" />
                            <select id="project_id" name="project_id"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                                {{-- Seleciona '-- Nenhum --' se o valor antigo/atual for vazio/nulo --}}
                                <option value="" @selected(old('project_id', $box->project_id) == '')>{{ __('-- Nenhum --') }}</option>
                                @isset($projects)
                                @foreach ($projects as $id => $name)
                                {{-- Usa @selected para comparar o valor antigo/atual com o ID da opção --}}
                                <option value="{{ $id }}" @selected(old('project_id', $box->project_id) == $id)>
                                    {{ $name }}
                                </option>
                                @endforeach
                                @endisset
                            </select>
                            <x-input-error :messages="$errors->get('project_id')" class="mt-2" />
                        </div>

                        {{-- Conferente (Select) --}}
                        <div>
                            <x-input-label for="commission_member_id" :value="__('Conferente (Opcional)')" />
                            <select id="commission_member_id" name="commission_member_id"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                                {{-- Seleciona '-- Nenhum --' se o valor antigo/atual for vazio/nulo --}}
                                <option value="" @selected(old('commission_member_id', $box->commission_member_id) == '')>{{ __('-- Nenhum --') }}</option>
                                @isset($activeMembers)
                                {{-- $id é commission_member.id, $name é user.name --}}
                                @foreach ($activeMembers as $id => $name)
                                {{-- Seleciona se o valor antigo/atual corresponder ao ID do CommissionMember --}}
                                <option value="{{ $id }}" @selected(old('commission_member_id', $box->commission_member_id) == $id)>
                                    {{ $name }}
                                </option>
                                @endforeach
                                @endisset
                            </select>
                            <x-input-error :messages="$errors->get('commission_member_id')" class="mt-2" />
                        </div>

                        {{-- Data da Conferência --}}
                        <div>
                            <x-input-label for="conference_date" :value="__('Data da Conferência (Opcional)')" />
                            <x-text-input id="conference_date" name="conference_date" type="date"
                                class="block w-full mt-1" :value="old('conference_date', optional($box->conference_date)->format('Y-m-d'))" />
                            <x-input-error :messages="$errors->get('conference_date')" class="mt-2" />
                            <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">Preencha apenas se um conferente
                                for selecionado.</p>
                        </div>

                        {{-- REMOVIDO: Seção Opcional de Importação de Documentos --}}

                        {{-- Botões no final do formulário --}}
                        <div
                            class="flex items-center justify-end gap-4 pt-6 mt-8 border-t border-gray-200 dark:border-gray-700">
                            {{-- Botão Cancelar --}}
                            <a href="{{ route('boxes.show', $box) }}" wire:navigate>
                                <x-ui.button type="button" variant="ghost">
                                    {{ __('Cancelar') }}
                                </x-ui.button>
                            </a>
                            {{-- Botão Atualizar --}}
                            <x-ui.button type="submit" variant="primary">
                                {{ __('Atualizar Caixa') }}
                            </x-ui.button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- REMOVIDO: Bloco completo do Modal de Importação (Ele estará na view 'show') --}}
    {{-- REMOVIDO: O Modal de Detalhes do Documento (Geralmente fica no layout principal) --}}

    @endcan
</x-app-layout>