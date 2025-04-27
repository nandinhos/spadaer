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
            class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-200"
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
                <input type="file"
                    id="csv_file"
                    name="csv_file"
                    accept=".csv, text/csv" {{-- Aceita .csv e o mime type --}}
                    class="block w-full text-sm text-gray-500 border border-gray-300 rounded-lg cursor-pointer dark:text-gray-400 focus:outline-none dark:border-gray-600 dark:placeholder-gray-400
                              file:mr-4 file:py-2 file:px-4 file:rounded-l-md file:border-0
                              file:text-sm file:font-semibold
                              file:bg-indigo-50 file:text-indigo-700
                              dark:file:bg-gray-700 dark:file:text-gray-300
                              hover:file:bg-indigo-100 dark:hover:file:bg-gray-600"
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
        {{-- Mensagem de Sucesso Geral --}}
        @if (session('success'))
        <div class="px-4 py-2 text-green-800 bg-green-100 border border-green-300 rounded dark:bg-green-900 dark:text-green-200 dark:border-green-700">
            {{ session('success') }}
        </div>
        @endif

        {{-- Mensagem de Erro Geral --}}
        @if (session('error'))
        <div class="px-4 py-2 text-red-800 bg-red-100 border border-red-300 rounded dark:bg-red-900 dark:text-red-200 dark:border-red-700">
            {{ session('error') }}
        </div>
        @endif

        {{-- Erros específicos da validação da importação (passados via sessão) --}}
        @if (session('import_errors'))
        <div class="px-4 py-3 text-red-800 bg-red-100 border border-red-300 rounded dark:bg-red-900 dark:text-red-200 dark:border-red-700">
            <p class="mb-2 font-semibold"><strong>Foram encontrados erros durante a importação:</strong></p>
            <ul class="list-disc list-inside">
                @foreach (session('import_errors') as $errorDetail)
                {{-- Assume que $errorDetail é um array ['row' => numero, 'errors' => [...]] --}}
                @if(is_array($errorDetail) && isset($errorDetail['row']) && isset($errorDetail['errors']))
                <li>
                    <strong>Linha {{ $errorDetail['row'] }}:</strong>
                    <ul class="ml-4 list-disc list-inside">
                        @foreach($errorDetail['errors'] as $field => $message)
                        <li>{{ $message[0] ?? 'Erro desconhecido' }} (Campo: {{ $field }})</li>
                        @endforeach
                    </ul>
                </li>
                @elseif(is_string($errorDetail))
                {{-- Caso seja apenas uma string de erro --}}
                <li>{{ $errorDetail }}</li>
                @endif
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>