<x-guest-layout>
    <div class="w-full max-w-[420px] animate-fade-in-up">
        <!-- Card Premium Glassmorphism -->
        <div class="relative backdrop-blur-2xl bg-white/85 dark:bg-gray-900/85 rounded-3xl shadow-[0_20px_60px_-15px_rgba(0,0,0,0.15),0_0_0_1px_rgba(255,255,255,0.4)] dark:shadow-[0_20px_60px_-15px_rgba(0,0,0,0.4),0_0_0_1px_rgba(255,255,255,0.05)] p-8 sm:p-10 overflow-hidden">
            
            <!-- Glow Effect Decorativo -->
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-primary/20 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-primary-light/20 rounded-full blur-3xl"></div>
            
            <!-- Logo e Branding -->
            <div class="relative text-center mb-6">
                <a href="/" class="inline-block transition-transform duration-300 hover:scale-105">
                    <img src="{{ asset('images/logo.png') }}" alt="SPADAER GAC-PAC" class="h-20 w-auto mx-auto mb-3 drop-shadow-lg">
                </a>
                <h1 class="text-2xl font-bold bg-gradient-to-r from-primary via-primary-light to-primary dark:from-primary-light dark:via-primary dark:to-primary-light bg-clip-text text-transparent tracking-tight">
                    Recuperar Senha
                </h1>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                    {{ __('Informe seu e-mail institucional cadastrado e enviaremos um link seguro para redefinição da sua senha.') }}
                </p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}" class="relative space-y-4">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" :required="true" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400 text-xs"></i>
                        </div>
                        <x-text-input id="email" class="block w-full pl-10" type="email" name="email" :value="old('email')" required autofocus placeholder="seu.email@exemplo.com" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <div class="pt-2 flex flex-col gap-3">
                    <x-ui.button type="submit" variant="primary" icon="fas fa-paper-plane" class="w-full justify-center">
                        {{ __('Enviar Link de Redefinição') }}
                    </x-ui.button>

                    <div class="text-center text-xs text-gray-500 dark:text-gray-400 mt-2">
                        <a class="font-bold text-primary dark:text-primary-light hover:underline flex items-center justify-center gap-1" href="{{ route('login') }}">
                            <i class="fas fa-arrow-left text-[10px]"></i>
                            <span>{{ __('Voltar para o Login') }}</span>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
