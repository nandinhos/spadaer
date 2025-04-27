<?php

namespace App\Http\Controllers;

use App\Imports\DocumentsImport;
use App\Models\Document; // Importar Document
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Para transação
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

// Não precisamos da ValidationException aqui, pois é tratada internamente se usarmos getErrors()
// use Maatwebsite\Excel\Validators\ValidationException;

class DocumentImportController extends Controller
{
    public function import(Request $request): RedirectResponse
    {
        // 1. Validação inicial do arquivo
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120', // Ex: Max 5MB
        ], [
            'csv_file.required' => 'Nenhum arquivo CSV foi selecionado.',
            'csv_file.file' => 'O item enviado não é um arquivo válido.',
            'csv_file.mimes' => 'O arquivo deve ser do tipo CSV ou TXT.',
            'csv_file.max' => 'O arquivo CSV não pode ser maior que 5MB.',
        ]);

        $file = $request->file('csv_file');
        $userId = Auth::id();
        $import = new DocumentsImport($userId); // Instancia a classe de importação

        Log::info('Iniciando importação do arquivo: '.$file->getClientOriginalName());

        $importedCount = 0; // Contador para sucesso real
        $errorMessages = []; // Array para erros durante a inserção

        try {
            // 2. Executa a importação (leitura e validação interna)
            Excel::import($import, $file);

            // 3. Pega os erros de validação coletados pela classe de importação
            $validationErrors = $import->getErrors();

            // 4. Se HOUVER erros de validação, NÃO prossegue com a inserção
            if (! empty($validationErrors)) {
                Log::warning('Importação abortada devido a erros de validação no arquivo CSV.', ['errors' => $validationErrors]);

                return redirect()->route('documents.index')
                    ->with('error', 'A importação falhou. Corrija os erros no arquivo CSV e tente novamente.')
                    ->with('import_errors', $validationErrors); // Envia os erros detalhados
            }

            // 5. Se NÃO HOUVER erros de validação, pega os dados validados
            $validatedData = $import->getValidatedData();
            Log::info('Validação do CSV concluída. '.count($validatedData).' linhas prontas para inserção.');

            // 6. Tenta inserir os dados válidos dentro de uma transação
            if (! empty($validatedData)) {
                DB::beginTransaction(); // Inicia a transação

                try {
                    foreach ($validatedData as $index => $data) {
                        $rowNumberForLog = $index + 2; // Estimar número da linha no CSV original (cabeçalho + índice baseado em 0)
                        Log::debug("[DB Insert - Row ~{$rowNumberForLog}] Attempting Document::create with:", $data);

                        // Tenta criar o documento individualmente
                        Document::create($data);

                        $importedCount++; // Incrementa SÓ SE create() não lançar exceção
                        Log::info("[DB Insert - Row ~{$rowNumberForLog}] Document created successfully.");
                    }

                    DB::commit(); // Confirma a transação se TUDO deu certo
                    Log::info('Importação concluída. Total de documentos criados: '.$importedCount);

                    return redirect()->route('documents.index')
                        ->with('success', $importedCount.' documentos importados com sucesso.');

                } catch (\Throwable $e) {
                    DB::rollBack(); // DESFAZ a transação em caso de erro durante o loop
                    $errorMessages[] = $e->getMessage(); // Guarda a mensagem de erro
                    Log::error('Erro DURANTE a inserção no banco de dados (transação revertida): '.$e->getMessage(), [
                        'exception' => $e,
                        'failed_data' => $data ?? 'N/A', // Loga os dados que falharam
                    ]);

                    // Retorna com erro geral e os erros de validação (que não deveriam existir aqui, mas por segurança)
                    return redirect()->route('documents.index')
                        ->with('error', 'Ocorreu um erro ao salvar os documentos no banco de dados. Nenhuma linha foi importada. Erro: '.$e->getMessage())
                        ->with('import_errors', $validationErrors); // Envia erros de validação se houver
                }

            } else {
                // Arquivo vazio ou todas as linhas tinham erros de validação
                Log::info('Nenhum dado válido encontrado para importação após a validação.');

                return redirect()->route('documents.index')
                    ->with('warning', 'Nenhum documento válido encontrado para importação no arquivo.');
            }

        } catch (\Throwable $e) {
            // Captura erros inesperados DURANTE o processo de leitura/validação do Excel::import
            // (Ex: erro de permissão de arquivo, falha de memória, erro no pacote Excel)
            Log::error('Erro crítico durante o processamento da importação: '.$e->getMessage());
            Log::error($e->getTraceAsString());

            return redirect()->route('documents.index')
                ->with('error', 'Ocorreu um erro crítico ao processar o arquivo: '.$e->getMessage());
        }
    }
}
