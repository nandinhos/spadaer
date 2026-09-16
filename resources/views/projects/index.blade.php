<x-app-layout>
    @section('title', 'Projetos')
    @section('header-title', 'Gerenciamento de Projetos')
    
    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <livewire:project-list />
        </div>
    </div>
</x-app-layout>