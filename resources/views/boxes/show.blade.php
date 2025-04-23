<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('Detalhes da Caixa') }}: {{ $box->number }}
            </h2>
            {{-- Botões de Ação no Header --}}
            <div class="flex items-center space-x-2">
                <x-secondary-button onclick="window.location='{{ route('boxes.edit', $box) }}'">
                    <i class="mr-1 fas fa-edit"></i> Editar
                </x-secondary-button>
                <form method="POST" action="{{ route('boxes.destroy', $box) }}"
                    onsubmit="return confirm('Tem certeza que deseja excluir esta caixa e TODOS os documentos contidos nela?');">
                    @csrf
                    @method('DELETE')
                    <x-danger-button type="submit">
                        <i class="mr-1 fas fa-trash-alt"></i> Excluir
                    </x-danger-button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">

            {{-- Card de Informações da Caixa --}}
            <div class="bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">
                        Informações da Caixa
                    </h3>
                </div>
                <dl class="p-6 space-y-4 text-sm">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <dt class="font-medium text-gray-500 dark:text-gray-400">Número</dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $box->number }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="font-medium text-gray-500 dark:text-gray-400">Local Físico</dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $box->physical_location ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500 dark:text-gray-400">Projeto</dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100">
                                {{ $box->project?->name ?: '-- Nenhum --' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500 dark:text-gray-400">Conferente</dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100">
                                {{ $box->checkerMember?->user?->name ?: '-- Nenhum --' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500 dark:text-gray-400">Data Conferência</dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100">
                                {{ $box->conference_date?->format('d/m/Y') ?: '-' }}</dd>
                        </div>
                    </div>
                </dl>
            </div>

            {{-- Card de Documentos na Caixa --}}
            <div class="bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">
                        Documentos na Caixa ({{ $box->documents->count() }})
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                    Item</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                    Número Doc.</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                    Título</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                    Data Doc.</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase dark:text-gray-300">
                                    Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @forelse ($box->documents as $document)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-750/50">
                                    <td
                                        class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-gray-100">
                                        {{ $document->item_number }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap dark:text-gray-300">
                                        {{ $document->document_number }}</td>
                                    <td class="max-w-xs px-6 py-4 text-sm text-gray-600 truncate dark:text-gray-300"
                                        title="{{ $document->title }}">{{ $document->title }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap dark:text-gray-300">
                                        {{ $document->document_date?->format('d/m/Y') ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                        {{-- Link para ver o documento (se existir rota) ou abrir modal --}}
                                        <button {{-- Assumindo que a função Alpine openDocumentModal existe no escopo --}} {{-- Caso contrário, link para documents.show --}} {{-- href="{{ route('documents.show', $document) }}" --}}
                                            @click="openDocumentModal({{ $document->id }})"
                                            class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-200">
                                            Ver Detalhes
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                        Nenhum documento encontrado nesta caixa.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-start mt-6">
                <a href="{{ route('boxes.index') }}"
                    class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-gray-700 uppercase transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:border-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25">
                    ← Voltar para Lista de Caixas
                </a>
            </div>

        </div>
    </div>

    {{-- Incluir o modal de documento se a função Alpine for usada --}}
    <x-document-modal />
</x-app-layout>
