<x-app-layout>
    @section('title', 'Comissões')
    @section('header-title', 'Gerenciamento de Comissões')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:commission-list />
        </div>
    </div>
</x-app-layout>