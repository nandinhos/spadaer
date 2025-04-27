<?php

namespace App\Http\Controllers;

use App\Imports\DocumentsImport;
use App\Models\Document; // Importar Document para inserção
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Para transação
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

// Não precisamos mais da ValidationException do Excel aqui, pois tratamos no import
// use Maatwebsite\Excel\Validators\ValidationException;

class DocumentImportController extends Controller
{
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $userId = Auth::id();
        $import = new DocumentsImport($userId); // Instancia a classe

        try {
            // Executa a importação, que agora apenas lê e valida
            Excel::import($import, $file);

            // Pega os erros coletados pela classe de importação
            $errors = $import->getErrors();

            // ## LÓGICA PRINCIPAL: SÓ INSERE SE NÃO HOUVER ERROS ##
            if (empty($errors)) {
                // Pega os dados que passaram em TODAS as validações
                $validatedData = $import->getValidatedData();

                if (! empty($validatedData)) {
                    // Insere todos os documentos válidos de uma vez dentro de uma transação
                    DB::transaction(function () use ($validatedData) {
                        // Use insert para performance em muitos registros
                        Document::insert($validatedData);
                        // Alternativa: usar create() se precisar de eventos de model
                        // foreach ($validatedData as $data) {
                        //     Document::create($data);
                        // }
                    });
                    $importedCount = count($validatedData);

                    return redirect()->route('documents.index')
                        ->with('success', $importedCount.' documentos importados com sucesso.');
                } else {
                    // Arquivo estava vazio ou todas as linhas eram inválidas (mas sem erro fatal)
                    return redirect()->route('documents.index')
                        ->with('warning', 'Nenhum documento válido encontrado para importação no arquivo.');
                }

            } else {
                // Se HOUVE erros de validação, NÃO insere nada
                Log::warning('Importação falhou devido a erros de validação.', ['errors' => $errors]);

                return redirect()->route('documents.index')
                    ->with('error', 'A importação falhou. Corrija os erros no arquivo CSV e tente novamente.')
                    ->with('import_errors', $errors); // Envia os erros detalhados para a view
            }

        } catch (\Throwable $e) {
            // Captura erros inesperados DURANTE o processo de leitura/validação do Excel::import
            Log::error('Erro inesperado durante o processamento da importação: '.$e->getMessage());
            Log::error($e->getTraceAsString());

            return redirect()->route('documents.index')
                ->with('error', 'Ocorreu um erro inesperado ao processar o arquivo: '.$e->getMessage());
        }
    }
}
