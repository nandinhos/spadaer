<?php

namespace App\Http\Controllers; // Namespace do Controller

use App\Imports\DocumentsImport;
use Illuminate\Http\RedirectResponse; // Importa a classe de importação
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException; // Adicionar tipo de retorno

class DocumentImportController extends Controller // Define a classe Controller
{
    public function import(Request $request): RedirectResponse // Define o método import
    {
        // ... código do método import que chama Excel::import(new DocumentsImport(...), ...) ...
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $userId = Auth::id();

        try {
            $import = new DocumentsImport($userId); // Instancia a classe de importação
            Excel::import($import, $file); // Usa a instância

            $importedCount = $import->getImportedCount();
            $skippedCount = $import->getSkippedCount();
            $errors = $import->getErrors();

            $message = $importedCount.' documentos importados com sucesso.';
            if ($skippedCount > 0 || ! empty($errors)) {
                $message .= ' '.$skippedCount.' linhas foram puladas devido a erros.';
            }

            return redirect()->route('documents.index')
                ->with('success', $message)
                ->with('import_errors', $errors);

        } catch (ValidationException $e) {
            // ... tratamento ValidationException ...
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $rowNumber = $failure->row() ?? 'N/A';
                $rowErrors = $failure->errors();
                $attribute = $failure->attribute() ?? 'Desconhecido';
                $errors[] = [/* ... */];
            }
            Log::error('Erro de validação na importação CSV: ', $errors);

            return redirect()->route('documents.index')
                ->with('error', 'Falha na validação do arquivo CSV.')
                ->with('import_errors', $errors);
        } catch (\Throwable $e) {
            // ... tratamento Throwable ...
            Log::error('Erro inesperado na importação CSV: '.$e->getMessage());
            Log::error($e->getTraceAsString());

            return redirect()->route('documents.index')
                ->with('error', 'Ocorreu um erro inesperado durante a importação.');
        }
    }
} // FIM DA CLASSE DocumentImportController
