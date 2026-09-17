{{-- resources/views/documents/edit.blade.php --}}
<x-app-layout>
    @section('title', 'Documentos')
    @section('header-title', 'Editar Documento')

    <x-slot name="header">
        <div class="flex flex-col items-start gap-4 sm:flex-row sm:items-center justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-file-pen text-primary dark:text-primary-light"></i>
                    <span>{{ __('Editar Documento') }}: <span class="text-primary dark:text-primary-light">{{ $document->document_number }}</span></span>
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Atualize os metadados, sigilo, localização e informações de controle deste documento.</p>
            </div>
            <a href="{{ route('documents.index') }}" wire:navigate>
                <x-ui.button variant="secondary" icon="fas fa-arrow-left">
                    {{ __('Voltar para Lista') }}
                </x-ui.button>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <x-ui.card>
                <x-ui.form-errors />

                <form method="POST" action="{{ route('documents.update', $document) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Seção 1: Localização & Arquivamento --}}
                    <x-ui.form-section 
                        title="Localização & Arquivamento" 
                        description="Vincule o documento à caixa física, número do item e projeto de origem."
                        icon="fas fa-box-archive">
                        
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            {{-- Caixa --}}
                            <div>
                                <x-input-label for="box_id" :value="__('Caixa')" :required="true" />
                                <x-select-input id="box_id" name="box_id" required class="block w-full">
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
                                </x-select-input>
                                <x-input-error :messages="$errors->get('box_id')" />
                            </div>

                            {{-- Item --}}
                            <div>
                                <x-input-label for="item_number" :value="__('Item (dentro da Caixa)')" :required="true" />
                                <x-text-input id="item_number" name="item_number" type="text" required
                                    class="block w-full" placeholder="Ex: 001, 002..." :value="old('item_number', $document->item_number)" />
                                <x-input-error :messages="$errors->get('item_number')" />
                            </div>

                            {{-- Projeto --}}
                            <div>
                                <x-input-label for="project_id" :value="__('Projeto Associado')" />
                                <x-select-input id="project_id" name="project_id" class="block w-full">
                                    <option value="" @selected(old('project_id', $document->project_id) == '')>{{ __('-- Nenhum --') }}</option>
                                    @isset($projects)
                                        @foreach ($projects as $id => $name)
                                            <option value="{{ $id }}" @selected(old('project_id', $document->project_id) == $id)>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    @endisset
                                </x-select-input>
                                <x-input-error :messages="$errors->get('project_id')" />
                            </div>
                        </div>
                    </x-ui.form-section>

                    {{-- Seção 2: Identificação do Documento --}}
                    <x-ui.form-section 
                        title="Identificação do Documento" 
                        description="Dados de referência oficial, numeração, código e título descritivo."
                        icon="fas fa-file-lines">
                        
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            {{-- Número do Documento --}}
                            <div>
                                <x-input-label for="document_number" :value="__('Número do Documento')" :required="true" />
                                <x-text-input id="document_number" name="document_number" type="text" required
                                    class="block w-full" placeholder="Ex: OF-2024-001" :value="old('document_number', $document->document_number)" />
                                <x-input-error :messages="$errors->get('document_number')" />
                            </div>

                            {{-- Código --}}
                            <div>
                                <x-input-label for="code" :value="__('Código Interno')" />
                                <x-text-input id="code" name="code" type="text" class="block w-full"
                                    placeholder="Ex: COD-A4" :value="old('code', $document->code)" />
                                <x-input-error :messages="$errors->get('code')" />
                            </div>

                            {{-- Descritor --}}
                            <div>
                                <x-input-label for="descriptor" :value="__('Descritor')" />
                                <x-text-input id="descriptor" name="descriptor" type="text" class="block w-full"
                                    placeholder="Ex: Administrativo, Técnico..." :value="old('descriptor', $document->descriptor)" />
                                <x-input-error :messages="$errors->get('descriptor')" />
                            </div>

                            {{-- Título --}}
                            <div class="md:col-span-3">
                                <x-input-label for="title" :value="__('Título / Ementa do Documento')" :required="true" />
                                <x-textarea id="title" name="title" class="block w-full" rows="3"
                                    placeholder="Descreva detalhadamente o assunto ou ementa deste documento..."
                                    required>{{ old('title', $document->title) }}</x-textarea>
                                <x-input-error :messages="$errors->get('title')" />
                            </div>
                        </div>
                    </x-ui.form-section>

                    {{-- Seção 3: Classificação & Controle --}}
                    <x-ui.form-section 
                        title="Classificação & Sigilo" 
                        description="Nível de acesso, datação temporal e informações de cópia ou revisão."
                        icon="fas fa-shield-halved">
                        
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            {{-- Data do Documento --}}
                            <div>
                                <x-input-label for="document_date" :value="__('Data (Mês/Ano)')" :required="true" />
                                <x-text-input id="document_date" name="document_date" type="text" required
                                    class="block w-full" placeholder="Ex: JAN/2024, FEV/2023" :value="old('document_date', $document->document_date)" />
                                <x-input-error :messages="$errors->get('document_date')" />
                            </div>

                            {{-- Sigilo --}}
                            <div>
                                <x-input-label for="confidentiality" :value="__('Nível de Sigilo')" :required="true" />
                                <x-select-input id="confidentiality" name="confidentiality" required class="block w-full">
                                    <option value="" disabled @selected(old('confidentiality', $document->confidentiality) === null || old('confidentiality', $document->confidentiality) === '')>
                                        {{ __('Selecione...') }}
                                    </option>
                                    @foreach (['Público', 'Restrito', 'Confidencial'] as $level)
                                        <option value="{{ $level }}" @selected(old('confidentiality', $document->confidentiality) === $level)>
                                            {{ $level }}
                                        </option>
                                    @endforeach
                                </x-select-input>
                                <x-input-error :messages="$errors->get('confidentiality')" />
                            </div>

                            {{-- Versão --}}
                            <div>
                                <x-input-label for="version" :value="__('Versão')" />
                                <x-text-input id="version" name="version" type="text" class="block w-full"
                                    placeholder="Ex: 1.0, Rev 2" :value="old('version', $document->version)" />
                                <x-input-error :messages="$errors->get('version')" />
                            </div>

                            {{-- Info Cópia --}}
                            <div class="md:col-span-3">
                                <x-input-label for="is_copy" :value="__('Informação da Cópia / Observações')" />
                                <x-text-input id="is_copy" name="is_copy" type="text" class="block w-full"
                                    placeholder="Ex: Cópia 1, Original arquivado, Revisão B" :value="old('is_copy', $document->is_copy)" />
                                <x-input-error :messages="$errors->get('is_copy')" />
                            </div>
                        </div>
                    </x-ui.form-section>

                    {{-- Ações do Formulário --}}
                    <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-800">
                        <a href="{{ route('documents.index') }}" wire:navigate class="w-full sm:w-auto">
                            <x-ui.button type="button" variant="secondary" class="w-full sm:w-auto justify-center">
                                {{ __('Cancelar') }}
                            </x-ui.button>
                        </a>
                        <x-ui.button type="submit" variant="primary" icon="fas fa-save" class="w-full sm:w-auto justify-center">
                            {{ __('Atualizar Documento') }}
                        </x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
