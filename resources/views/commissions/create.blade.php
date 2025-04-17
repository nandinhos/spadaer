<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Nova Comissão') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('commissions.store') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="name" :value="__('Nome da Comissão')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Descrição')" />
                            <x-textarea id="description" name="description" class="mt-1 block w-full" required>{{ old('description') }}</x-textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="ordinance_number" :value="__('Número da Portaria')" />
                            <x-text-input id="ordinance_number" name="ordinance_number" type="text" class="mt-1 block w-full" :value="old('ordinance_number')" required />
                            <x-input-error :messages="$errors->get('ordinance_number')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="ordinance_date" :value="__('Data da Portaria')" />
                            <x-text-input id="ordinance_date" name="ordinance_date" type="date" class="mt-1 block w-full" :value="old('ordinance_date')" required />
                            <x-input-error :messages="$errors->get('ordinance_date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="ordinance_file" :value="__('Arquivo da Portaria (PDF)')" />
                            <input id="ordinance_file" name="ordinance_file" type="file" accept=".pdf" class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" required />
                            <x-input-error :messages="$errors->get('ordinance_file')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="members" :value="__('Membros')" />
                            <select id="members" name="members[]" multiple class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm" required>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->rank }} {{ $user->full_name }} ({{ $user->order_number }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('members')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Salvar') }}</x-primary-button>
                            <a href="{{ route('commissions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                {{ __('Cancelar') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>