<section class="space-y-6">
    <div class="flex items-center gap-3 pb-4 border-b border-gray-100 dark:border-gray-800">
        <div class="p-2 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400">
            <i class="fas fa-triangle-exclamation"></i>
        </div>
        <div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white">
                {{ __('Excluir Conta') }}
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                {{ __('Após a exclusão da conta, todos os recursos associados e dados pessoais serão permanentemente removidos.') }}
            </p>
        </div>
    </div>

    <x-ui.button
        variant="danger"
        icon="fas fa-trash-can"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >
        {{ __('Excluir Conta') }}
    </x-ui.button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <div class="flex items-center gap-3 mb-4">
                <div class="p-3 rounded-2xl bg-rose-500/10 text-rose-600 dark:text-rose-400">
                    <i class="fas fa-triangle-exclamation text-xl"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ __('Confirmar Exclusão de Conta') }}
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        {{ __('Esta ação é definitiva e irreversível.') }}
                    </p>
                </div>
            </div>

            <p class="text-sm text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">
                {{ __('Por favor, confirme sua senha para autorizar a exclusão definitiva do seu usuário e permissões.') }}
            </p>

            <div>
                <x-input-label for="password" :value="__('Senha')" :required="true" />
                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="block w-full"
                    placeholder="{{ __('Digite sua senha atual') }}"
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                <x-ui.button type="button" variant="secondary" x-on:click="$dispatch('close')">
                    {{ __('Cancelar') }}
                </x-ui.button>

                <x-ui.button variant="danger" type="submit" icon="fas fa-trash-can">
                    {{ __('Excluir Conta') }}
                </x-ui.button>
            </div>
        </form>
    </x-modal>
</section>
