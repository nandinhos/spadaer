{{-- resources/views/documents/import-form.blade.php --}}
<div class="mb-8">
    <x-ui.card>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-4 mb-4 border-b border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-3">
                <div class="p-2.5 rounded-xl bg-primary/10 text-primary dark:text-primary-light">
                    <i class="fas fa-file-import text-lg"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">
                        {{ __('Importar Documentos (CSV)') }}
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Envie planilhas padronizadas para inclusão em lote de documentos no acervo.</p>
                </div>
            </div>

            {{-- Link para Baixar Modelo --}}
            <a href="{{ asset('files/modelo_importacao.csv') }}" download>
                <x-ui.button variant="secondary" icon="fas fa-download" class="text-xs py-2">
                    {{ __('Baixar modelo CSV') }}
                </x-ui.button>
            </a>
        </div>

        <form action="{{ route('documents.import') }}" method="POST" enctype="multipart/form-data" 
              x-data="{ fileName: '', isDragging: false }" class="mt-2">
            @csrf
            
            {{-- Dropzone / File Picker Moderno --}}
            <div 
                @dragover.prevent="isDragging = true" 
                @dragleave.prevent="isDragging = false" 
                @drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; fileName = $event.dataTransfer.files[0] ? $event.dataTransfer.files[0].name : ''"
                :class="isDragging ? 'border-primary bg-primary/5 dark:bg-primary/10' : 'border-gray-200 dark:border-gray-700/80 bg-gray-50/50 dark:bg-gray-800/40 hover:bg-gray-50 dark:hover:bg-gray-800/70'"
                class="relative border-2 border-dashed rounded-2xl p-6 text-center transition-all duration-200 cursor-pointer group"
                @click="$refs.fileInput.click()">
                
                <input type="file" id="csv_file" name="csv_file" accept=".csv, text/csv"
                       x-ref="fileInput"
                       @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                       class="sr-only" required>

                <div class="flex flex-col items-center justify-center gap-2">
                    <div class="w-12 h-12 rounded-2xl bg-white dark:bg-gray-800 shadow-xs border border-gray-100 dark:border-gray-700 flex items-center justify-center text-primary dark:text-primary-light group-hover:scale-105 transition-transform duration-200">
                        <i class="fas fa-cloud-arrow-up text-xl"></i>
                    </div>

                    <div class="text-sm">
                        <span class="font-bold text-primary dark:text-primary-light hover:underline">
                            {{ __('Clique para selecionar') }}
                        </span>
                        <span class="text-gray-500 dark:text-gray-400"> {{ __('ou arraste o arquivo CSV até aqui') }}</span>
                    </div>

                    <p class="text-xs text-gray-400 dark:text-gray-500">Formato aceito: .CSV (Codificação UTF-8 ou ISO-8859-1)</p>

                    {{-- Nome do arquivo selecionado --}}
                    <template x-if="fileName">
                        <div class="mt-2 inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-primary/10 text-primary dark:text-primary-light text-xs font-semibold">
                            <i class="fas fa-file-csv text-sm"></i>
                            <span x-text="fileName"></span>
                        </div>
                    </template>
                </div>
            </div>

            <x-input-error :messages="$errors->get('csv_file')" class="mt-2" />

            <div class="mt-4 flex justify-end">
                <x-ui.button type="submit" variant="primary" icon="fas fa-upload" class="justify-center">
                    {{ __('Importar Arquivo') }}
                </x-ui.button>
            </div>
        </form>

        {{-- Alertas e Feedback de Importação --}}
        <div class="mt-4 space-y-3 text-sm">
            @if (session('success'))
                <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-800 dark:text-emerald-300 flex items-start gap-3" role="alert">
                    <i class="fas fa-circle-check text-emerald-500 mt-0.5 shrink-0 text-base"></i>
                    <div class="flex-1 font-medium">{!! session('success') !!}</div>
                </div>
            @endif

            @if (session('import_error_message'))
                <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-800 dark:text-rose-300 flex items-start gap-3" role="alert">
                    <i class="fas fa-circle-xmark text-rose-500 mt-0.5 shrink-0 text-base"></i>
                    <div class="flex-1 font-medium">{!! session('import_error_message') !!}</div>
                </div>
            @endif

            @if (session('warning'))
                <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-800 dark:text-amber-300 flex items-start gap-3" role="alert">
                    <i class="fas fa-triangle-exclamation text-amber-500 mt-0.5 shrink-0 text-base"></i>
                    <div class="flex-1 font-medium">{!! session('warning') !!}</div>
                </div>
            @endif

            @if (session('import_errors') && is_array(session('import_errors')) && count(session('import_errors')) > 0)
                <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-800 dark:text-rose-300">
                    <div class="flex items-center gap-2 mb-2 font-bold text-sm">
                        <i class="fas fa-triangle-exclamation text-rose-500"></i>
                        <span>{{ __('Detalhes dos erros encontrados na planilha:') }}</span>
                    </div>
                    <ul class="list-disc list-inside space-y-1 text-xs">
                        @foreach (session('import_errors') as $errorDetail)
                            <li>
                                <strong>Linha {{ $errorDetail['row'] ?? 'Desconhecida' }}:</strong>
                                <ul class="ml-4 list-disc list-inside mt-0.5 space-y-0.5">
                                    @if (isset($errorDetail['errors']) && is_array($errorDetail['errors']))
                                        @foreach ($errorDetail['errors'] as $field => $messages)
                                            @foreach ($messages as $message)
                                                <li>{{ $message }} <span class="text-gray-500">(Campo: {{ $field }})</span></li>
                                            @endforeach
                                        @endforeach
                                    @else
                                        <li>Erro desconhecido nesta linha.</li>
                                    @endif
                                </ul>
                                @if (isset($errorDetail['values']) && !empty($errorDetail['values']))
                                    <div class="mt-1 text-[11px] text-gray-500 dark:text-gray-400 font-mono">Dados: {{ json_encode($errorDetail['values']) }}</div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </x-ui.card>
</div>
