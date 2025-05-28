@role('admin')
<x-modal name="edit-user-roles-{{ $user->id }}" focusable>
    <form method="POST" action="{{ route('admin.users.roles.update', $user) }}" class="p-6">
        @csrf
        @method('PUT')

        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Editar Funções do Usuário') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Selecione as funções que deseja atribuir a este usuário.') }}
        </p>

        <div class="mt-6">
            @foreach($roles as $role)
            <div class="flex items-center mt-4">
                <input
                    type="checkbox"
                    name="roles[]"
                    value="{{ $role->id }}"
                    id="role-{{ $role->id }}-{{ $user->id }}"
                    @checked($user->roles->contains($role))
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                >

                <label
                    for="role-{{ $role->id }}-{{ $user->id }}"
                    class="ml-2 text-sm text-gray-600 dark:text-gray-400"
                >
                    {{ $role->display_name ?? $role->name }}
                </label>
            </div>
            @endforeach
        </div>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">
                {{ __('Cancelar') }}
            </x-secondary-button>

            <x-primary-button class="ml-3">
                {{ __('Salvar') }}
            </x-primary-button>
        </div>
    </form>
</x-modal>
@endrole