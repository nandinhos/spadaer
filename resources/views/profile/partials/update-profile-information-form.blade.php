<section>
    <div class="flex items-center gap-3 pb-4 mb-6 border-b border-gray-100 dark:border-gray-800">
        <div class="p-2 rounded-xl bg-primary/10 text-primary dark:text-primary-light">
            <i class="fas fa-user-gear"></i>
        </div>
        <div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white">
                {{ __('Informações do Perfil') }}
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                {{ __('Atualize seu nome de exibição e endereço de e-mail institucional.') }}
            </p>
        </div>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <x-input-label for="name" :value="__('Nome Completo')" :required="true" />
                <x-text-input id="name" name="name" type="text" class="block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Endereço de E-mail')" :required="true" />
                <x-text-input id="email" name="email" type="email" class="block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-2 p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-800 dark:text-amber-300 text-xs">
                        <p>
                            {{ __('Seu endereço de e-mail não foi verificado.') }}
                            <button form="send-verification" class="underline font-bold text-primary dark:text-primary-light hover:underline ml-1">
                                {{ __('Clique aqui para reenviar o e-mail de verificação.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-bold text-emerald-600 dark:text-emerald-400">
                                {{ __('Um novo link de verificação foi enviado para seu e-mail.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-4 pt-4 border-t border-gray-100 dark:border-gray-800">
            <x-ui.button type="submit" variant="primary" icon="fas fa-check" class="justify-center">
                {{ __('Salvar Alterações') }}
            </x-ui.button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-xs font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5"
                >
                    <i class="fas fa-check-circle"></i>
                    <span>{{ __('Informações atualizadas com sucesso!') }}</span>
                </p>
            @endif
        </div>
    </form>
</section>
