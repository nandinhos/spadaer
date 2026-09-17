{{-- resources/views/commissions/edit.blade.php --}}
<x-app-layout>
    @section('title', 'Editar Comissão')
    @section('header-title', 'Editar Comissão')

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold leading-tight text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-pen-to-square text-primary dark:text-primary-light"></i>
                    <span>{{ __('Editar Comissão') }}: <span class="text-primary dark:text-primary-light">{{ $commission->name }}</span></span>
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Atualize os dados normativos, portaria e membros desta comissão.</p>
            </div>
            <a href="{{ route('commissions.show', $commission) }}" wire:navigate>
                <x-ui.button variant="secondary" icon="fas fa-arrow-left">
                    {{ __('Voltar para Detalhes') }}
                </x-ui.button>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <x-ui.card>
                <x-ui.form-errors />

                <form method="POST" action="{{ route('commissions.update', $commission) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Seção 1: Identificação da Comissão --}}
                    <x-ui.form-section 
                        title="Identificação da Comissão" 
                        description="Denominação oficial e escopo das atividades da comissão."
                        icon="fas fa-circle-info">
                        
                        <div class="space-y-6">
                            {{-- Nome da Comissão --}}
                            <div>
                                <x-input-label for="name" :value="__('Nome da Comissão')" :required="true" />
                                <x-text-input id="name" name="name" type="text" class="block w-full"
                                    :value="old('name', $commission->name)" required autofocus placeholder="Ex: Comissão Permanente de Avaliação de Documentos (CPAD)" />
                                <x-input-error :messages="$errors->get('name')" />
                            </div>

                            {{-- Descrição --}}
                            <div>
                                <x-input-label for="description" :value="__('Descrição / Finalidade')" :required="true" />
                                <x-textarea id="description" name="description" required rows="3"
                                    class="block w-full" placeholder="Finalidade institucional e atribuições desta comissão...">{{ old('description', $commission->description) }}</x-textarea>
                                <x-input-error :messages="$errors->get('description')" />
                            </div>
                        </div>
                    </x-ui.form-section>

                    {{-- Seção 2: Portaria Instituidora --}}
                    <x-ui.form-section 
                        title="Portaria Instituidora" 
                        description="Atos normativos e publicação oficial de designação."
                        icon="fas fa-file-contract">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Número da Portaria --}}
                            <div>
                                <x-input-label for="ordinance_number" :value="__('Número da Portaria')" :required="true" />
                                <x-text-input id="ordinance_number" name="ordinance_number" type="text"
                                    class="block w-full" :value="old('ordinance_number', $commission->ordinance_number)" required placeholder="Ex: Portaria nº 123/DIRAD/2024" />
                                <x-input-error :messages="$errors->get('ordinance_number')" />
                            </div>

                            {{-- Data da Portaria --}}
                            <div>
                                <x-input-label for="ordinance_date" :value="__('Data da Portaria')" :required="true" />
                                <x-text-input id="ordinance_date" name="ordinance_date" type="date"
                                    class="block w-full" :value="old('ordinance_date', $commission->ordinance_date?->format('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('ordinance_date')" />
                            </div>

                            {{-- Arquivo da Portaria --}}
                            <div class="md:col-span-2">
                                <x-input-label for="ordinance_file" :value="__('Arquivo da Portaria (PDF)')" />
                                @if ($commission->ordinance_file)
                                    <div class="mb-2 p-2.5 rounded-xl bg-primary/10 border border-primary/20 flex items-center justify-between">
                                        <div class="flex items-center gap-2 text-xs font-semibold text-primary dark:text-primary-light">
                                            <i class="fas fa-file-pdf text-sm text-rose-500"></i>
                                            <span>Documento atual anexado</span>
                                        </div>
                                        <a href="{{ Storage::url($commission->ordinance_file) }}" target="_blank"
                                            class="text-xs font-bold text-primary hover:underline dark:text-primary-light flex items-center gap-1">
                                            <span>Visualizar arquivo atual</span>
                                            <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                                        </a>
                                    </div>
                                @endif
                                <input id="ordinance_file" name="ordinance_file" type="file" accept=".pdf"
                                    class="block w-full text-xs text-gray-500 dark:text-gray-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary dark:file:text-primary-light hover:file:bg-primary/20 border border-gray-200 dark:border-gray-700/80 rounded-xl bg-white dark:bg-gray-800/80 cursor-pointer focus:outline-hidden" />
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Deixe em branco para manter o arquivo da portaria atual.</p>
                                <x-input-error :messages="$errors->get('ordinance_file')" />
                            </div>
                        </div>
                    </x-ui.form-section>

                    {{-- Seção 3: Membros Designados --}}
                    <x-ui.form-section 
                        title="Membros Designados" 
                        description="Selecione os integrantes que compõem a comissão."
                        icon="fas fa-user-group">
                        
                        <div>
                            <x-input-label for="members" :value="__('Integrantes da Comissão')" :required="true" />
                            <select id="members" name="members[]" multiple rows="6"
                                class="block w-full border border-gray-200 dark:border-gray-700/80 bg-white dark:bg-gray-800/80 text-gray-900 dark:text-gray-100 focus:border-primary dark:focus:border-primary-light focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary-light/20 rounded-xl shadow-xs transition-all duration-200 p-2 text-sm"
                                required>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                                        {{ in_array($user->id, old('members', $commission->members->pluck('user_id')->toArray())) ? 'selected' : '' }}>
                                        {{ $user->rank ?? '' }} {{ $user->name ?? $user->email }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                <i class="fas fa-circle-info text-[11px]"></i>
                                <span>Segure <strong>Ctrl</strong> ou <strong>Command</strong> para selecionar múltiplos integrantes.</span>
                            </p>
                            <x-input-error :messages="$errors->get('members')" />
                            <x-input-error :messages="$errors->get('members.*')" />
                        </div>
                    </x-ui.form-section>

                    {{-- Ações do Formulário --}}
                    <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-800">
                        <a href="{{ route('commissions.index') }}" wire:navigate class="w-full sm:w-auto">
                            <x-ui.button type="button" variant="secondary" class="w-full sm:w-auto justify-center">
                                {{ __('Cancelar') }}
                            </x-ui.button>
                        </a>
                        <x-ui.button type="submit" variant="primary" icon="fas fa-save" class="w-full sm:w-auto justify-center">
                            {{ __('Salvar') }}
                        </x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
