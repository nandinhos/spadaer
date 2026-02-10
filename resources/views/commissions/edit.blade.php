<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Editar Comissão') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('commissions.update', $commission) }}"
                        enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="name" :value="__('Nome da Comissão')" />
                            <x-text-input id="name" name="name" type="text" class="block w-full mt-1"
                                :value="old('name', $commission->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Descrição')" />
                            <x-textarea id="description" name="description" class="block w-full mt-1"
                                required>{{ old('description', $commission->description) }}</x-textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="ordinance_number" :value="__('Número da Portaria')" />
                            <x-text-input id="ordinance_number" name="ordinance_number" type="text"
                                class="block w-full mt-1" :value="old('ordinance_number', $commission->ordinance_number)" required />
                            <x-input-error :messages="$errors->get('ordinance_number')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="ordinance_date" :value="__('Data da Portaria')" />
                            <x-text-input id="ordinance_date" name="ordinance_date" type="date"
                                class="block w-full mt-1" :value="old('ordinance_date', $commission->ordinance_date->format('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('ordinance_date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="ordinance_file" :value="__('Arquivo da Portaria (PDF)')" />
                            @if ($commission->ordinance_file)
                                <div class="mb-2">
                                    <a href="{{ Storage::url($commission->ordinance_file) }}" target="_blank"
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200">
                                        Visualizar arquivo atual
                                    </a>
                                </div>
                            @endif
                            <input id="ordinance_file" name="ordinance_file" type="file" accept=".pdf"
                                class="block w-full mt-1 text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" />
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Deixe em branco para manter o
                                arquivo atual</p>
                            <x-input-error :messages="$errors->get('ordinance_file')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="members" :value="__('Membros')" />
                            <select id="members" name="members[]" multiple
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary dark:focus:border-primary focus:ring-primary dark:focus:ring-primary"
                                required>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{-- Verifica se o ID do usuário atual está na coleção de user_ids dos membros da comissão --}} {{-- Usamos old() para priorizar dados submetidos anteriormente em caso de erro de validação --}}
                                        {{ in_array($user->id, old('members', $commission->members->pluck('user_id')->toArray())) ? 'selected' : '' }}>
                                        {{-- Conteúdo da opção --}}
                                        {{ $user->rank ?? '' }} {{ $user->name ?? $user->email }}
                                        {{-- {{ $user->rank }} {{ $user->full_name }} ({{ $user->order_number }}) --}}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('members')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-ui.button type="submit" variant="primary">
                                {{ __('Salvar') }}
                            </x-ui.button>
                            <a href="{{ route('commissions.index') }}" wire:navigate>
                                <x-ui.button type="button" variant="secondary">
                                    {{ __('Cancelar') }}
                                </x-ui.button>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
