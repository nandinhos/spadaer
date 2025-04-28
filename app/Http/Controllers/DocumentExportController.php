<?php

namespace App\Http\Controllers;

use App\Exports\DocumentsExport; // Importar a classe de exportação
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse; // Tipo de retorno para download

class DocumentExportController extends Controller
{
    /**
     * Handle the export request and trigger the download.
     *
     * @return BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function exportExcel(Request $request) // Nome do método pode ser só 'export' se preferir
    {
        // Pega os parâmetros da query string (filtros, ordenação) para passar para a exportação
        $queryParams = $request->query();
        Log::info('Iniciando exportação de documentos com parâmetros:', $queryParams);

        // Define o nome do arquivo
        $filename = 'documentos_'.now()->format('Ymd_His').'.xlsx';

        // Cria uma instância da classe de exportação, passando a Request inteira
        // A classe DocumentsExport usará a request para aplicar filtros/sort
        $export = new DocumentsExport($request);

        // Tenta gerar e baixar o arquivo Excel
        try {
            // Retorna a resposta de download diretamente
            return Excel::download($export, $filename);

        } catch (\Throwable $e) {
            Log::error('Erro ao gerar exportação de documentos: '.$e->getMessage(), [
                'exception' => $e,
                'request_params' => $queryParams,
            ]);

            // Redireciona de volta para a lista com uma mensagem de erro
            return redirect()->route('documents.index') // Ou redirect()->back()
                ->with('error', 'Não foi possível gerar o arquivo de exportação. Verifique os logs.');
        }
    }

    // Você pode adicionar outros métodos aqui para diferentes formatos de exportação (PDF, CSV) no futuro
    // public function exportCsv(Request $request) { ... }
    // public function exportPdf(Request $request) { ... }
}
