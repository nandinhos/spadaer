<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Nova Comissão') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Bloco para exibir TODOS os erros de validação (opcional, mas recomendado) --}}
                    @if ($errors->any())
                        <div class="relative px-4 py-3 mb-4 text-red-700 bg-red-100 border border-red-400 rounded"
                            role="alert">
                            <strong class="font-bold">{{ __('Ops! Algo deu errado.') }}</strong>
                            <ul class="mt-2 text-sm list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('commissions.store') }}" enctype="multipart/form-data"
                        class="space-y-6">
                        @csrf

                        {{-- Nome da Comissão --}}
                        <div>
                            <x-input-label for="name" :value="__('Nome da Comissão')" />
                            <x-text-input id="name" name="name" type="text" class="block w-full mt-1"
                                :value="old('name')" required autofocus />
                            {{-- Exibe erro específico do campo 'name' --}}
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        {{-- Descrição --}}
                        <div>
                            <x-input-label for="description" :value="__('Descrição')" />
                            {{-- Textarea repopulada corretamente com o conteúdo --}}
                            <x-textarea id="description" name="description" required
                                class="block w-full mt-1">{{ old('description') }}</x-textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        {{-- Número da Portaria --}}
                        <div>
                            <x-input-label for="ordinance_number" :value="__('Número da Portaria')" />
                            <x-text-input id="ordinance_number" name="ordinance_number" type="text"
                                class="block w-full mt-1" :value="old('ordinance_number')" required />
                            <x-input-error :messages="$errors->get('ordinance_number')" class="mt-2" />
                        </div>

                        {{-- Data da Portaria --}}
                        <div>
                            <x-input-label for="ordinance_date" :value="__('Data da Portaria')" />
                            <x-text-input id="ordinance_date" name="ordinance_date" type="date"
                                class="block w-full mt-1" :value="old('ordinance_date')" required />
                            <x-input-error :messages="$errors->get('ordinance_date')" class="mt-2" />
                        </div>

                        {{-- Arquivo da Portaria --}}
                        <div>
                            <x-input-label for="ordinance_file" :value="__('Arquivo da Portaria (PDF)')" />
                            <input id="ordinance_file" name="ordinance_file" type="file" accept=".pdf"
                                class="block w-full mt-1 text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" />
                            {{-- Removido 'required' do HTML, a validação deve ser feita no backend --}}
                            <x-input-error :messages="$errors->get('ordinance_file')" class="mt-2" />
                        </div>

                        {{-- Membros --}}
                        <div>
                            <x-input-label for="members" :value="__('Membros da Comissão')" />
                            <select id="members" name="members[]" multiple
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600"
                                required>
                                {{-- Garante que $users existe antes de iterar --}}
                                @isset($users)
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{-- Verifica se este user ID estava selecionado anteriormente (em caso de erro de validação) --}}
                                            {{ in_array($user->id, old('members', [])) ? 'selected' : '' }}>
                                            {{-- Adapte para mostrar os campos corretos do seu modelo User --}}
                                            {{ $user->rank ?? '' }} {{ $user->name ?? $user->email }} {{-- Mostra rank e nome, ou email como fallback --}}
                                            {{-- {{ $user->rank }} {{ $user->full_name }} ({{ $user->order_number }}) --}} {{-- Sua versão anterior --}}
                                        </option>
                                    @endforeach
                                @else
                                    <option disabled>Nenhum usuário disponível para seleção.</option>
                                @endisset
                            </select>
                            {{-- Exibe erro geral para 'members' ou erros específicos para 'members.*' --}}
                            <x-input-error :messages="$errors->get('members')" class="mt-2" />
                            <x-input-error :messages="$errors->get('members.*')" class="mt-2" />
                        </div>

                        {{-- Botões --}}
                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Salvar Comissão') }}</x-primary-button>
                           
                            <x-secondary-button type="button">
                                <a href="{{ route('commissions.index') }}" class="block w-full h-full flex items-center justify-center">
                                    {{ __('Cancelar') }}
                                </a>
                            </x-secondary-button>
                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
