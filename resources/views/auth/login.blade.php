<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <div class="w-full max-w-[420px] animate-fade-in-up">
        <!-- Card Premium Glassmorphism -->
        <div class="relative backdrop-blur-2xl bg-white/85 dark:bg-gray-900/85 rounded-3xl shadow-[0_20px_60px_-15px_rgba(0,0,0,0.15),0_0_0_1px_rgba(255,255,255,0.4)] dark:shadow-[0_20px_60px_-15px_rgba(0,0,0,0.4),0_0_0_1px_rgba(255,255,255,0.05)] p-8 sm:p-10 overflow-hidden">
            
            <!-- Glow Effect Decorativo -->
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-indigo-500/20 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-blue-500/20 rounded-full blur-3xl"></div>
            
            <!-- Logo e Branding -->
            <div class="relative text-center mb-10">
                <a href="/" class="inline-block transition-transform duration-300 hover:scale-105">
                    <img src="{{ asset('images/logo.png') }}" alt="SPADAER GAC-PAC" class="h-28 w-auto mx-auto mb-5 drop-shadow-lg">
                </a>
                <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-indigo-600 via-blue-500 to-indigo-600 dark:from-indigo-400 dark:via-blue-400 dark:to-indigo-400 bg-clip-text text-transparent tracking-tight">
                    SPADAER GAC-PAC
                </h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Sistema de Gestão de Documentos Analisados pelo GAC-PAC</p>
            </div>

            <!-- Formulário -->
            <form method="POST" action="{{ route('login') }}" class="relative space-y-5">
                @csrf

                <!-- Email Input Premium -->
                <div class="group">
                    <x-input-label for="email" :value="__('Email')" class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5 block" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400 group-focus-within:text-indigo-500 transition-colors duration-200"></i>
                        </div>
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            :value="old('email')" 
                            required 
                            autofocus 
                            autocomplete="username"
                            placeholder="seu.email@exemplo.com"
                            class="block w-full pl-11 pr-4 py-3.5 bg-gray-50/80 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all duration-200 hover:bg-white dark:hover:bg-gray-800"
                        />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-red-500" />
                </div>

                <!-- Password Input Premium -->
                <div class="group">
                    <x-input-label for="password" :value="__('Senha')" class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5 block" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400 group-focus-within:text-indigo-500 transition-colors duration-200"></i>
                        </div>
                        <input 
                            id="password" 
                            type="password" 
                            name="password" 
                            required 
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="block w-full pl-11 pr-4 py-3.5 bg-gray-50/80 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all duration-200 hover:bg-white dark:hover:bg-gray-800"
                        />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-red-500" />
                </div>

                <!-- Remember Me + Esqueceu Senha -->
                <div class="flex items-center justify-between pt-1">
                    <label for="remember_me" class="flex items-center cursor-pointer group">
                        <input 
                            id="remember_me" 
                            type="checkbox" 
                            name="remember"
                            class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500/20 focus:ring-2 transition-all duration-200 cursor-pointer"
                        >
                        <span class="ml-2.5 text-sm text-gray-600 dark:text-gray-400 group-hover:text-gray-800 dark:group-hover:text-gray-200 transition-colors duration-200">{{ __('Lembrar-me') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a 
                            href="{{ route('password.request') }}" 
                            class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors duration-200 hover:underline underline-offset-4"
                        >
                            {{ __('Esqueceu sua senha?') }}
                        </a>
                    @endif
                </div>

                <!-- Botão Entrar -->
                <div class="pt-4">
                    <x-ui.button type="submit" variant="primary" class="w-full py-4 text-base" icon="fas fa-sign-in-alt">
                        {{ __('Entrar') }}
                    </x-ui.button>
                </div>
            </form>
        </div>
        
        <!-- Footer -->
        <p class="mt-6 text-center text-xs text-gray-500 dark:text-gray-500">
            © {{ date('Y') }} SPADAER GAC-PAC. Todos os direitos reservados.
        </p>
    </div>
</x-guest-layout>
