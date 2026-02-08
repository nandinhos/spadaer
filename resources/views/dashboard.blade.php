<x-app-layout>
    @section('header-title', 'Página Inicial')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-8">
                <h3 class="text-2xl font-bold text-gray-800">Bem-vindo ao DocBox SPADAER!</h3>
                <p class="mt-4 text-gray-600">Sistema de Consulta e Gerenciamento de Documentos da Subcomissão Permanente de Avaliação de Documentos (SPADAER) do GAC-PAC.</p>
                <p class="mt-2 text-sm text-gray-500">Gerencie e consulte os documentos relevantes acessando no botão ENTRAR, no menu ao lado.</p>
            </div>
        </div>
    </div>
</x-app-layout>
