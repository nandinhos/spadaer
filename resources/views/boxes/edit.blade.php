<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('Editar Caixa') }}: {{ $box->number }}
            </h2>
            <a href="{{ route('boxes.index') }}"
                class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-gray-700 uppercase transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:border-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                <i class="mr-2 fas fa-arrow-left"></i>{{ __('Voltar para Lista') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6 text-gray-900 md:p-8 dark:text-gray-100">

                    {{-- Exibição de Erros Gerais --}}
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
                            {{-- Removido :currentValue, usando @selected --}}
                            <select id="project_id" name="project_id"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
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
                            <x-input-label for="checker_member_id" :value="__('Conferente (Opcional)')" />
                            {{-- Removido :currentValue, usando @selected --}}
                            <select id="checker_member_id" name="checker_member_id"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                                <option value="" @selected(old('checker_member_id', $box->checker_member_id) == '')>{{ __('-- Nenhum --') }}</option>
                                @isset($activeMembers)
                                    @foreach ($activeMembers as $id => $name)
                                        {{-- Usa @selected para comparar o valor antigo/atual com o ID da opção --}}
                                        <option value="{{ $id }}" @selected(old('checker_member_id', $box->checker_member_id) == $id)>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                            <x-input-error :messages="$errors->get('checker_member_id')" class="mt-2" />
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

                        {{-- Botões --}}
                        <div class="flex items-center gap-4 pt-6 mt-8 border-t border-gray-200 dark:border-gray-700">
                            <x-primary-button>{{ __('Atualizar Caixa') }}</x-primary-button>
                            <a href="{{ route('boxes.index') }}"
                                class="text-sm text-gray-600 rounded-md dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                {{ __('Cancelar') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
