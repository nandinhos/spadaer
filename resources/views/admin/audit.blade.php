<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl bg-clip-text text-transparent bg-gradient-to-r from-primary to-primary-light dark:from-primary-light dark:to-white leading-tight">
                {{ __('Auditoria do Sistema') }}
            </h2>
            <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                <i class="fas fa-history"></i>
                <span>Histórico de Ações</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:audit-list />
        </div>
    </div>
</x-app-layout>
