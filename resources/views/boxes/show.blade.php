{{-- resources/views/boxes/show.blade.php --}}
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
            {{-- Botões de Ação no Header (Editar e Excluir Caixa) --}}
            <div class="flex items-center flex-shrink-0 space-x-2">
                {{-- @can('update', $box) --}}
                <x-secondary-button onclick="window.location='{{ route('boxes.edit', $box) }}'">
                    <i class="mr-1 fas fa-edit"></i> {{ __('Editar') }}
                </x-secondary-button>
                {{-- @endcan --}}

                {{-- @can('delete', $box) --}}
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
                {{-- Header do Card com Botão Editar Sutil --}}
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">
                        <i class="mr-2 text-gray-500 fas fa-info-circle"></i> {{ __('Informações da Caixa') }}
                    </h3>
                    {{-- @can('update', $box) --}}
                    <a href="{{ route('boxes.edit', $box) }}"
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
                        <div>
                            <dt class="font-medium text-gray-500 dark:text-gray-400">{{ __('Número') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $box->number }}</dd>
                        </div>
                        <div class="md:col-span-2">
                            <dt class="font-medium text-gray-500 dark:text-gray-400">{{ __('Local Físico') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $box->physical_location ?: '--' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500 dark:text-gray-400">{{ __('Projeto') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100">
                                {{ $box->project?->name ?: '-- Nenhum --' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500 dark:text-gray-400">{{ __('Conferente') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100">
                                {{ $box->commissionMember?->user?->name ?: '-- Nenhum --' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500 dark:text-gray-400">{{ __('Data Conferência') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100">
                                {{ $box->conference_date?->format('d/m/Y') ?: '--' }}
                            </dd>
                        </div>
                    </div>
                </dl>
            </div>

            {{-- Card: Documentos na Caixa --}}
            <div class="bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                {{-- Header do Card com Botão Importar --}}
                <div
                    class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between flex-wrap gap-3">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">
                        <i class="mr-2 text-gray-500 fas fa-file-alt"></i> {{ __('Documentos na Caixa') }}
                        ({{ $box->documents->count() }})
                    </h3>

                    {{-- Botão para abrir Modal de Importação (Usa a função GLOBAL) --}}
                    {{-- @can('importDocumentsForBox', $box) --}}
                    {{-- Container para agrupar o link de download e o botão de importar --}}
                    {{-- ADICIONADO: div com classes flex flex-col items-end gap-1 --}}
                    <div class="flex flex-col items-end gap-1">

                        {{-- Link para Baixar Modelo --}}
                        <a href="{{ asset('files/modelo_importacao_docs_caixa.csv') }}" {{-- Link para NOVO modelo --}}
                            {{-- ALTERADO: class="text-xs ..." para deixar pequeno, removido ml-2 --}}
                            class="p-2 font-medium text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-200 focus:outline-none focus:underline" download>
                            <i class="mr-1 fas fa-download"></i> Baixar modelo CSV
                        </a>

                        {{-- Botão para abrir Modal de Importação (Usa a função GLOBAL) --}}
                        {{-- @can('importDocumentsForBox', $box) --}}
                        <button type="button"
                            class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                            @click="$store.modals.openBoxImportModal()">
                            <i class="fas fa-upload mr-1.5"></i> {{ __('Importar Documentos') }}
                        </button>
                        {{-- @endcan --}}

                    </div> {{-- Fim do Container para Link/Botão --}}
                    {{-- @endcan --}}
                </div>

                {{-- Área para exibir erros de importação APÓS redirect --}}
                {{-- Visível se showBoxImportModal for true (modal aberto) OU se houver mensagens na sessão --}}
                {{-- session()->hasAny(['import_error_message', 'import_errors']) verifica se ALGUMA das chaves existe --}}
                <div x-data="{ showImportMessages: {{ session()->hasAny(['import_error_message', 'import_errors']) ? 'true' : 'false' }} }" x-show="showImportMessages" x-transition x-init="$watch('showImportMessages', value => { if (!value) { /* Opcional: Limpar URL ou sessão aqui */ } })">

                    @if (session()->hasAny(['import_error_error_message', 'import_errors']))
                    <div class="px-6 pb-4 mt-4">
                        {{-- Mensagem Geral de Erro ou Sucesso (ImportController) --}}
                        @if (session('import_error_message'))
                        <div class="px-4 py-2 text-red-800 bg-red-100 border border-red-300 rounded dark:bg-red-900 dark:text-red-200 dark:border-red-700"
                            role="alert">
                            {!! session('import_error_message') !!}
                        </div>
                        @elseif (session('warning'))
                        {{-- Se você usar session('warning') também --}}
                        <div class="px-4 py-2 text-yellow-800 bg-yellow-100 border border-yellow-300 rounded dark:bg-yellow-900 dark:text-yellow-200 dark:border-yellow-700"
                            role="alert">
                            {!! session('warning') !!}
                        </div>
                        @elseif (session('success'))
                        {{-- Se você usar session('success') aqui (menos comum para import em show) --}}
                        <div class="px-4 py-2 text-green-800 bg-green-100 border border-green-300 rounded dark:bg-green-900 dark:text-green-200 dark:border-green-700"
                            role="alert">
                            {!! session('success') !!}
                        </div>
                        @endif

                        {{-- Erros detalhados (passados via session('import_errors')) --}}
                        @if (session('import_errors') && is_array(session('import_errors')) && count(session('import_errors')) > 0)
                        <div
                            class="px-4 py-3 mt-2 text-sm text-red-800 bg-red-100 border border-red-300 rounded dark:bg-red-900 dark:text-red-200 dark:border-red-700">
                            <p class="mb-2 font-semibold"><strong>Detalhes dos erros encontrados:</strong></p>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach (session('import_errors') as $errorDetail)
                                <li>
                                    <strong>Linha {{ $errorDetail['row'] ?? 'Desconhecida' }}:</strong>
                                    <ul class="ml-4 list-disc list-inside">
                                        @if (isset($errorDetail['errors']) && is_array($errorDetail['errors']))
                                        @foreach ($errorDetail['errors'] as $field => $messages)
                                        @foreach ($messages as $message)
                                        <li>{{ $message }} (Campo: {{ $field }})
                                        </li>
                                        @endforeach
                                        @endforeach
                                        @else
                                        <li>Erro desconhecido nesta linha.</li>
                                        @endif
                                    </ul>
                                    @if (isset($errorDetail['values']) && !empty($errorDetail['values']))
                                    <div class="mt-1 text-xs text-gray-600 dark:text-gray-400">Dados:
                                        {{ json_encode($errorDetail['values']) }}
                                    </div>
                                    @endif
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        {{-- Opcional: Botão para fechar a área de mensagens --}}
                        <div class="flex justify-end mt-3">
                            <button type="button" class="text-sm text-gray-600 dark:text-gray-400 hover:underline"
                                @click="showImportMessages = false">
                                {{ __('Fechar Mensagens de Importação') }}
                            </button>
                        </div>
                    </div>
                    @endif
                </div>


                {{-- Tabela de Documentos na Caixa --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                    {{ __('Item') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                    {{ __('Número Doc.') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                    {{ __('Título') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                    {{ __('Data Doc.') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase dark:text-gray-300">
                                    {{ __('Ações') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($box->documents as $document)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750/50">
                                <td
                                    class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-gray-100">
                                    {{ $document->item_number }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap dark:text-gray-300">
                                    {{ $document->document_number }}
                                </td>
                                <td class="max-w-xs px-6 py-4 text-sm text-gray-600 truncate dark:text-gray-300"
                                    title="{{ $document->title }}">{{ Str::limit($document->title, 60) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                    {{ $document->document_date ?? '--' }} {{-- Exibe a string MES/ANO --}}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                    {{-- Botão que ACIONA o modal global de Detalhes do Documento --}}
                                    {{-- @can('view', $document) --}}
                                    <button type="button" @click="openDocumentModal({{ $document->id }})"
                                        {{-- Chama a função GLOBAL openDocumentModal() --}}
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

            {{-- Botão Voltar para Lista de Caixas --}}
            <div class="flex justify-start mt-6">
                <a href="{{ route('boxes.index') }}"
                    class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-gray-700 uppercase transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:border-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25">
                    <i class="mr-2 fas fa-arrow-left"></i> {{ __('Voltar para Lista de Caixas') }}
                </a>
            </div>

        </div> {{-- Fim mx-auto --}}
    </div> {{-- Fim py-12 --}}

    {{--
        O Modal de Detalhes do Documento (visualização de um documento)
        é um componente global (<x-document-modal />) incluído no layout principal (app.blade.php).
        Ele é acionado pelo botão "Ver Detalhes" na tabela de documentos.
     --}}


    {{-- *** MODAL DE IMPORTAÇÃO PARA CAIXA (Com Alpine.js) *** --}}
    {{-- showBoxImportModal é a variável GLOBAL que controla a visibilidade (definida em layout()) --}}
    <div x-data x-show="$store.modals.showBoxImportModal" x-cloak>
        {{-- O overlay principal agora é flex e centraliza --}}
        {{-- ADICIONADAS: flex items-center justify-center --}}
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center">

            {{-- O fundo escuro, dentro do container flex. Mantido como absolute para cobrir tudo. --}}
            <div x-show="$store.modals.showBoxImportModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                {{-- CLASSE MANTIDA: absolute inset-0 ... --}}
                class="absolute inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-80 transition-opacity"
                @click="$store.modals.closeBoxImportModal()" aria-hidden="true"></div>

            {{-- REMOVIDO: O span auxiliar não é mais necessário --}}
            {{-- <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span> --}}

            {{-- O conteúdo do modal, também dentro do container flex. Ajustado para centralização. --}}
            <div x-show="$store.modals.showBoxImportModal" x-transition
                {{-- REMOVIDAS: inline-block align-bottom sm:align-middle sm:my-8 --}}
                {{-- ADICIONADAS/ALTERADAS: relative z-50 m-4 (margem para evitar que toque nas bordas) --}}
                class="relative z-50 bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full m-4">

                <div class="bg-white dark:bg-gray-800 p-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">
                        {{ __('Importar Documentos para Caixa') }} {{ $box->number }}
                    </h3>

                    <form action="{{ route('boxes.documents.import', $box) }}" method="POST"
                        enctype="multipart/form-data" class="mt-4 space-y-4">
                        @csrf

                        <input type="file" name="documents_csv" accept=".csv, text/csv" required
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-l-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">

                        <div class="flex justify-end gap-2">
                            <button type="button" @click="$store.modals.closeBoxImportModal()"
                                class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-600">
                                {{ __('Cancelar') }}
                            </button>

                            <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                                {{ __('Importar') }}
                            </button>
                        </div>
                    </form>
                </div>

            </div> {{-- Fim do Conteúdo do Modal --}}
        </div> {{-- Fim do Overlay Principal --}}
    </div> {{-- Fim do x-data --}}

    {{-- *** FIM DO MODAL DE IMPORTAÇÃO PARA CAIXA *** --}}


    {{--
        O Modal de Detalhes do Documento (visualização de um documento)
        é um componente global (<x-document-modal />) incluído no layout principal (app.blade.php).
        Ele é acionado pelo botão "Ver Detalhes" na tabela de documentos.
     --}}


</x-app-layout>