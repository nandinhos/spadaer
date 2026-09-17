{{-- resources/views/boxes/edit.blade.php --}}
<x-app-layout>
    @section('title', 'Editar Caixa')
    @section('header-title', 'Editar Caixa')

    @can('boxes.edit')
    <x-slot name="header">
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h2 class="text-xl font-bold leading-tight text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-pen-to-square text-primary dark:text-primary-light"></i>
                    <span>{{ __('Editar Caixa') }}: <span class="text-primary dark:text-primary-light">{{ $box->number }}</span></span>
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Atualize as informações de identificação, localização e conferência da caixa.</p>
            </div>
            <a href="{{ route('boxes.show', $box) }}" wire:navigate>
                <x-ui.button variant="secondary" icon="fas fa-arrow-left">
                    {{ __('Voltar para Detalhes') }}
                </x-ui.button>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-ui.card>
                <x-ui.form-errors />

                <form method="POST" action="{{ route('boxes.update', $box) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Seção: Identificação da Caixa --}}
                    <x-ui.form-section 
                        title="Identificação & Localização" 
                        description="Atualize o identificador da caixa e a prateleira/nível onde está arquivada."
                        icon="fas fa-barcode">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Número da Caixa --}}
                            <div>
                                <x-input-label for="number" :value="__('Número da Caixa')" :required="true" />
                                <x-text-input id="number" name="number" type="text" class="block w-full"
                                    :value="old('number', $box->number)" required autofocus placeholder="Ex: AD001, CX-2024-05" />
                                <x-input-error :messages="$errors->get('number')" />
                            </div>

                            {{-- Local Físico --}}
                            <div>
                                <x-input-label for="physical_location" :value="__('Local Físico')" />
                                <x-text-input id="physical_location" name="physical_location" type="text"
                                    class="block w-full" :value="old('physical_location', $box->physical_location)"
                                    placeholder="Ex: Prateleira A-1 / Nível 1" />
                                <x-input-error :messages="$errors->get('physical_location')" />
                            </div>
                        </div>
                    </x-ui.form-section>

                    {{-- Seção: Vínculos & Conferência --}}
                    <x-ui.form-section 
                        title="Projeto & Conferência" 
                        description="Associação a projetos institucionais e dados de revisão física."
                        icon="fas fa-clipboard-check">
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {{-- Projeto (Select) --}}
                            <div>
                                <x-input-label for="project_id" :value="__('Projeto Associado')" />
                                <x-select-input id="project_id" name="project_id" class="block w-full">
                                    <option value="" @selected(old('project_id', $box->project_id) == '')>{{ __('-- Nenhum --') }}</option>
                                    @isset($projects)
                                        @foreach ($projects as $id => $name)
                                            <option value="{{ $id }}" @selected(old('project_id', $box->project_id) == $id)>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    @endisset
                                </x-select-input>
                                <x-input-error :messages="$errors->get('project_id')" />
                            </div>

                            {{-- Conferente (Select) --}}
                            <div>
                                <x-input-label for="commission_member_id" :value="__('Conferente')" />
                                <x-select-input id="commission_member_id" name="commission_member_id" class="block w-full">
                                    <option value="" @selected(old('commission_member_id', $box->commission_member_id) == '')>{{ __('-- Nenhum --') }}</option>
                                    @isset($activeMembers)
                                        @foreach ($activeMembers as $id => $name)
                                            <option value="{{ $id }}" @selected(old('commission_member_id', $box->commission_member_id) == $id)>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    @endisset
                                </x-select-input>
                                <x-input-error :messages="$errors->get('commission_member_id')" />
                            </div>

                            {{-- Data da Conferência --}}
                            <div>
                                <x-input-label for="conference_date" :value="__('Data da Conferência')" />
                                <x-text-input id="conference_date" name="conference_date" type="date"
                                    class="block w-full" :value="old('conference_date', optional($box->conference_date)->format('Y-m-d'))" />
                                <x-input-error :messages="$errors->get('conference_date')" />
                            </div>
                        </div>
                    </x-ui.form-section>

                    {{-- Ações do Formulário --}}
                    <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-800">
                        <a href="{{ route('boxes.show', $box) }}" wire:navigate class="w-full sm:w-auto">
                            <x-ui.button type="button" variant="secondary" class="w-full sm:w-auto justify-center">
                                {{ __('Cancelar') }}
                            </x-ui.button>
                        </a>
                        <x-ui.button type="submit" variant="primary" icon="fas fa-save" class="w-full sm:w-auto justify-center">
                            {{ __('Atualizar Caixa') }}
                        </x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        </div>
    </div>
    @endcan
</x-app-layout>