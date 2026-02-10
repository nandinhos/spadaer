{{-- resources/views/documents/import-form.blade.php --}}
{{-- Este componente será incluído na view principal (ex: documents.index) --}}
<div class="p-4 mb-6 bg-white rounded-lg shadow dark:bg-gray-800">
    <div class="flex flex-col items-start gap-2 mb-3 sm:flex-row sm:items-center sm:justify-between">
        {{-- Título --}}
        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">
            <i class="mr-2 fas fa-file-import text-gray-500"></i> Importar Documentos (CSV)
        </h3>
        {{-- Link para Baixar Modelo --}}
        <a href="{{ asset('files/modelo_importacao.csv') }}" {{-- Certifique-se que este arquivo existe na pasta /public/files --}}
            class="text-sm font-medium text-primary dark:text-primary-light hover:text-primary-800 dark:hover:text-primary-200"
            download>
            <i class="mr-1 fas fa-download"></i> Baixar modelo CSV
        </a>
    </div>

    <form action="{{ route('documents.import') }}" method="POST" enctype="multipart/form-data" class="mt-4">
        @csrf
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
            {{-- Input de Arquivo --}}
            <div class="flex-grow">
                <label for="csv_file" class="sr-only">Selecione o arquivo CSV</label> {{-- Screen reader only label --}}
                <input type="file" id="csv_file" name="csv_file" accept=".csv, text/csv" {{-- Aceita .csv e o mime type --}}
                    class="block w-full text-sm text-gray-500 border border-gray-300 rounded-lg cursor-pointer dark:text-gray-400 focus:outline-none dark:border-gray-600 dark:placeholder-gray-400
                              file:mr-4 file:py-2 file:px-4 file:rounded-l-md file:border-0
                              file:text-sm file:font-semibold
                              file:bg-primary-50 file:text-primary-dark
                              dark:file:bg-gray-700 dark:file:text-gray-300
                              hover:file:bg-primary-100 dark:hover:file:bg-gray-600"
                    required>
                <x-input-error :messages="$errors->get('csv_file')" class="mt-2" />
            </div>
            {{-- Botão Importar --}}
            <div class="flex-shrink-0">
                <x-primary-button type="submit">
                    <i class="mr-2 fas fa-upload"></i> Importar Arquivo
                </x-primary-button>
            </div>
        </div>
    </form>


    {{-- Área para exibir mensagens de sucesso, erro ou erros de validação da importação --}}
    <div class="mt-4 space-y-3 text-sm">
        {{-- Mensagem de Sucesso Geral (Permite HTML) --}}
        @if (session('success'))
            <div class="px-4 py-2 text-green-800 bg-green-100 border border-green-300 rounded dark:bg-green-900 dark:text-green-200 dark:border-green-700"
                role="alert">
                {!! session('success') !!} {{-- Usa {!! !!} para renderizar o <strong> --}}
            </div>
        @endif

        {{-- Mensagem de Erro Geral (Permite HTML) --}}
        {{-- Usando a nova chave 'import_error_message' --}}
        @if (session('import_error_message'))
            <div class="px-4 py-2 text-red-800 bg-red-100 border border-red-300 rounded dark:bg-red-900 dark:text-red-200 dark:border-red-700"
                role="alert">
                {!! session('import_error_message') !!} {{-- Usa {!! !!} para renderizar o <strong> --}}
            </div>
        @endif

        {{-- Mensagem de Aviso (Permite HTML) --}}
        @if (session('warning'))
            <div class="px-4 py-2 text-yellow-800 bg-yellow-100 border border-yellow-300 rounded dark:bg-yellow-900 dark:text-yellow-200 dark:border-yellow-700"
                role="alert">
                {!! session('warning') !!} {{-- Usa {!! !!} para renderizar o <strong> --}}
            </div>
        @endif


        {{-- Erros específicos da validação da importação (passados via sessão 'import_errors') --}}
        @if (session('import_errors') && is_array(session('import_errors')) && count(session('import_errors')) > 0)
            <div
                class="px-4 py-3 text-sm text-red-800 bg-red-100 border border-red-300 rounded dark:bg-red-900 dark:text-red-200 dark:border-red-700">
                <p class="mb-2 font-semibold"><strong>Detalhes dos erros encontrados:</strong></p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach (session('import_errors') as $errorDetail)
                        <li>
                            <strong>Linha {{ $errorDetail['row'] ?? 'Desconhecida' }}:</strong>
                            <ul class="ml-4 list-disc list-inside">
                                @if (isset($errorDetail['errors']) && is_array($errorDetail['errors']))
                                    @foreach ($errorDetail['errors'] as $field => $messages)
                                        @foreach ($messages as $message)
                                            <li>{{ $message }} (Campo: {{ $field }})</li>
                                        @endforeach
                                    @endforeach
                                @else
                                    <li>Erro desconhecido nesta linha.</li>
                                @endif
                            </ul>
                            {{-- Opcional: Mostrar os valores da linha que falhou --}}
                            @if (isset($errorDetail['values']) && !empty($errorDetail['values']))
                                <div class="mt-1 text-xs text-gray-600 dark:text-gray-400">Dados:
                                    {{ json_encode($errorDetail['values']) }}</div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>
