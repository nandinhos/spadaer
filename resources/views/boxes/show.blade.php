<x-app-layout>
    {{-- Header da Página --}}
    <x-slot name="header">
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    {{ __('Detalhes da Caixa') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Número: <span class="font-medium text-gray-700 dark:text-gray-300">{{ $box->number }}</span>
                </p>
            </div>
            {{-- Botões de Ação --}}
            <div class="flex items-center flex-shrink-0 space-x-2">
                {{-- @can('update', $box) --}} {{-- Descomente quando usar Policies --}}
                <x-secondary-button onclick="window.location='{{ route('boxes.edit', $box) }}'">
                    <i class="mr-1 fas fa-edit"></i> {{ __('Editar') }}
                </x-secondary-button>
                {{-- @endcan --}}

                {{-- @can('delete', $box) --}} {{-- Descomente quando usar Policies --}}
                <form method="POST" action="{{ route('boxes.destroy', $box) }}"
                    onsubmit="return confirm('{{ __('Tem certeza que deseja excluir esta caixa e TODOS os documentos contidos nela?') }}');">
                    @csrf
                    @method('DELETE')
                    <x-danger-button type="submit">
                        <i class="mr-1 fas fa-trash-alt"></i> {{ __('Excluir') }}
                    </x-danger-button>
                </form>
                {{-- @endcan --}}
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">

            {{-- Card: Informações da Caixa --}}
            <div class="bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                {{-- Header do Card --}}
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">
                        <i class="mr-2 text-gray-500 fas fa-info-circle"></i> {{ __('Informações da Caixa') }}
                    </h3>

                    {{-- Botão Editar Sutil no Card de Informações em boxes/show.blade.php --}}
                    {{-- @can('update', $box) --}}
                    <a href="{{ route('boxes.edit', ['box' => $box, 'redirect_to' => request()->fullUrl()]) }}"
                        {{-- Passa a URL atual --}}
                        class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 shadow-sm text-xs font-medium rounded text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                        title="{{ __('Editar Informações da Caixa') }}">
                        <i class="fas fa-edit mr-1"></i>
                        {{ __('Editar') }}
                    </a>
                    {{-- @endcan --}}
                </div>

                {{-- Corpo do Card (Definition List) --}}
                <dl class="px-6 py-6 space-y-4 text-sm">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-x-4 gap-y-4">
                        {{-- Número (já está no header, mas pode repetir) --}}
                        <div>
                            <dt class="font-medium text-gray-500 dark:text-gray-400">{{ __('Número') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $box->number }}</dd>
                        </div>
                        {{-- Local Físico --}}
                        <div class="md:col-span-2">
                            <dt class="font-medium text-gray-500 dark:text-gray-400">{{ __('Local Físico') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $box->physical_location ?: '--' }}
                            </dd>
                        </div>
                        {{-- Projeto --}}
                        <div>
                            <dt class="font-medium text-gray-500 dark:text-gray-400">{{ __('Projeto') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100">
                                {{ $box->project?->name ?: '-- Nenhum --' }}</dd>
                        </div>
                        {{-- Conferente --}}
                        <div>
                            <dt class="font-medium text-gray-500 dark:text-gray-400">{{ __('Conferente') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100">
                                {{ $box->commissionMember?->user?->name ?: '-- Nenhum --' }}</dd>
                        </div>
                        {{-- Data Conferência --}}
                        <div>
                            <dt class="font-medium text-gray-500 dark:text-gray-400">{{ __('Data Conferência') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100">
                                {{ $box->conference_date?->format('d/m/Y') ?: '--' }}</dd>
                        </div>
                    </div>
                </dl>
            </div>

            {{-- Card: Documentos na Caixa --}}
            <div class="bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                {{-- Header do Card --}}
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">
                        <i class="mr-2 text-gray-500 fas fa-file-alt"></i> {{ __('Documentos na Caixa') }}
                        ({{ $box->documents->count() }})
                    </h3>
                </div>
                {{-- Tabela de Documentos --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                    {{ __('Item') }}</th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                    {{ __('Número Doc.') }}</th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                    {{ __('Título') }}</th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                    {{ __('Data Doc.') }}</th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase dark:text-gray-300">
                                    {{ __('Ações') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            {{-- Loop pelos documentos carregados (e ordenados) pelo controller --}}
                            @forelse ($box->documents as $document)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-750/50">
                                    <td
                                        class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-gray-100">
                                        {{ $document->item_number }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap dark:text-gray-300">
                                        {{ $document->document_number }}</td>
                                    {{-- Truncate e title para títulos longos --}}
                                    <td class="max-w-xs px-6 py-4 text-sm text-gray-600 truncate dark:text-gray-300"
                                        title="{{ $document->title }}">{{ $document->title }}</td>
                                    {{-- Formata a data (o cast no model Document garante que é um objeto Carbon) --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                        {{ $document->document_date ?? '--' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                        {{-- Botão que ACIONA o modal global --}}
                                        {{-- @can('view', $document) --}} {{-- Descomente quando usar Policies --}}
                                        <button type="button" {{-- Chama a função Alpine definida no layout/app.js --}}
                                            @click="openDocumentModal({{ $document->id }})"
                                            class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-200 focus:outline-none focus:underline">
                                            {{ __('Ver Detalhes') }}
                                        </button>
                                        {{-- @endcan --}}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="mb-2 text-gray-400 fas fa-folder-open fa-3x"></i>
                                            {{ __('Nenhum documento encontrado nesta caixa.') }}
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Botão Voltar --}}
            <div class="flex justify-start mt-6">
                <a href="{{ route('boxes.index') }}"
                    class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-gray-700 uppercase transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:border-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25">
                    <i class="mr-2 fas fa-arrow-left"></i> {{ __('Voltar para Lista de Caixas') }}
                </a>
            </div>

        </div>
    </div>



</x-app-layout>
