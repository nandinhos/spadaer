{{-- resources/views/profile/edit.blade.php --}}
<x-app-layout>
    @section('title', 'Configurações de Perfil')
    @section('header-title', 'Perfil de Usuário')

    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2.5 rounded-xl bg-primary/10 text-primary dark:text-primary-light">
                <i class="fas fa-id-card-clip text-lg"></i>
            </div>
            <div>
                <h2 class="font-bold text-xl text-gray-900 dark:text-white leading-tight">
                    {{ __('Configurações de Perfil') }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Gerencie suas informações cadastrais e credenciais de acesso ao SPADAER.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Dados Pessoais / E-mail --}}
            <x-ui.card>
                @include('profile.partials.update-profile-information-form')
            </x-ui.card>

            {{-- Segurança & Senha --}}
            <x-ui.card>
                @include('profile.partials.update-password-form')
            </x-ui.card>

            {{-- Área de Risco / Exclusão --}}
            <x-ui.card class="border-rose-200/60 dark:border-rose-900/40">
                @include('profile.partials.delete-user-form')
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
