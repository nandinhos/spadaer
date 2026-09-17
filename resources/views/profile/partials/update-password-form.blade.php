<section>
    <div class="flex items-center gap-3 pb-4 mb-6 border-b border-gray-100 dark:border-gray-800">
        <div class="p-2 rounded-xl bg-primary/10 text-primary dark:text-primary-light">
            <i class="fas fa-key"></i>
        </div>
        <div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white">
                {{ __('Segurança & Senha') }}
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                {{ __('Recomendamos uma senha longa com letras, números e símbolos para maior proteção.') }}
            </p>
        </div>
    </div>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <x-input-label for="update_password_current_password" :value="__('Senha Atual')" :required="true" />
                <x-text-input id="update_password_current_password" name="current_password" type="password" class="block w-full" autocomplete="current-password" />
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="update_password_password" :value="__('Nova Senha')" :required="true" />
                <x-text-input id="update_password_password" name="password" type="password" class="block w-full" autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="update_password_password_confirmation" :value="__('Confirmar Nova Senha')" :required="true" />
                <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="block w-full" autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center gap-4 pt-4 border-t border-gray-100 dark:border-gray-800">
            <x-ui.button type="submit" variant="primary" icon="fas fa-lock" class="justify-center">
                {{ __('Atualizar Senha') }}
            </x-ui.button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-xs font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5"
                >
                    <i class="fas fa-check-circle"></i>
                    <span>{{ __('Senha alterada com sucesso!') }}</span>
                </p>
            @endif
        </div>
    </form>
</section>
