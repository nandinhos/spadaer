<?php

namespace App\Http\Controllers;

use App\Imports\DocumentsImport;
use App\Models\Box;
use App\Models\Document;
use App\Models\Project;
// Para retornar JSON
use Illuminate\Http\RedirectResponse; // Para retornar JSON
// Para redirecionar
use Illuminate\Http\Request; // Para distinct e raw queries se necessário
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class DocumentController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        try {
            $import = new DocumentsImport;
            Excel::import($import, $request->file('csv_file'));

            $importedCount = $import->getImportedCount();
            $errors = $import->getErrors();

            if (count($errors) > 0) {
                return back()->with([
                    'success' => "$importedCount documentos importados com sucesso.",
                    'error' => "Erros encontrados durante a importação:\n".implode("\n", array_map(function ($error) {
                        return $error[0];
                    }, $errors)),
                ]);
            }

            return back()->with('success', "$importedCount documentos importados com sucesso.");
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao processar o arquivo: '.$e->getMessage());
        }
    }

    public function show(Document $document)
    {
        if (request()->wantsJson()) {
            return response()->json($document);
        }

        return view('documents.show', compact('document'));
    }

    public function index(Request $request): View
    {
        // Parâmetros de busca, filtro, ordenação e paginação
        $searchTerm = $request->input('search');
        // Filtros agora usam IDs ou valores das tabelas relacionadas
        $filterBoxNumber = $request->input('filter_box_number'); // Filtro pelo número da caixa
        $filterProjectId = $request->input('filter_project_id'); // Filtro pelo ID do projeto
        $filterYear = $request->input('filter_year');
        $sortBy = $request->input('sort_by', 'document_date'); // Default: data do documento (existe em documents)
        $sortDir = $request->input('sort_dir', 'desc'); // Default: descendente para data
        $perPage = $request->input('per_page', 15); // Aumentado para 15 como exemplo

        // Colunas válidas para ordenação (incluindo colunas relacionadas)
        $validSortColumns = [
            'documents.id', // Explicitando a tabela para evitar ambiguidade
            'documents.item_number',
            'documents.code',
            'documents.descriptor',
            'documents.document_number',
            'documents.title',
            'documents.document_date',
            'documents.confidentiality',
            'documents.version',
            'documents.is_copy',
            'boxes.number', // Coluna da tabela boxes
            'projects.name', // Coluna da tabela projects
        ];

        // Valida coluna de ordenação
        if (! in_array($sortBy, $validSortColumns)) {
            $sortBy = 'documents.document_date'; // Reset para default seguro
        }
        // Valida direção
        if (! in_array(strtolower($sortDir), ['asc', 'desc'])) {
            $sortDir = 'desc';
        }

        // --- Construção da Query ---
        // Seleciona colunas específicas para evitar ambiguidade de 'id', 'created_at', 'updated_at'
        $query = Document::query()
            ->select('documents.*', 'boxes.number as box_number', 'projects.name as project_name') // Seleciona e alias colunas relacionadas
            ->leftJoin('boxes', 'documents.box_id', '=', 'boxes.id') // Junta com boxes
            ->leftJoin('projects', 'documents.project_id', '=', 'projects.id'); // Junta com projects

        // Aplicar busca textual (agora pode incluir campos das tabelas unidas)
        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('documents.item_number', 'like', "%{$searchTerm}%")
                    ->orWhere('documents.code', 'like', "%{$searchTerm}%")
                    ->orWhere('documents.descriptor', 'like', "%{$searchTerm}%")
                    ->orWhere('documents.document_number', 'like', "%{$searchTerm}%")
                    ->orWhere('documents.title', 'like', "%{$searchTerm}%")
                  // Busca nas tabelas relacionadas
                    ->orWhere('boxes.number', 'like', "%{$searchTerm}%")
                    ->orWhere('projects.name', 'like', "%{$searchTerm}%");
            });
        }

        // Aplicar filtros específicos
        if ($filterBoxNumber) {
            // Filtra pelo número da caixa na tabela 'boxes'
            $query->where('boxes.number', 'like', "%{$filterBoxNumber}%");
        }
        if ($filterProjectId) {
            // Filtra pelo ID do projeto na tabela 'documents'
            $query->where('documents.project_id', $filterProjectId);
        }
        if ($filterYear) {
            $query->whereYear('documents.document_date', $filterYear);
        }

        // Aplicar ordenação (já validada)
        // O $sortBy já contém o nome da tabela (ex: 'boxes.number')
        $query->orderBy($sortBy, $sortDir);

        // Paginar resultados
        // IMPORTANTE: Como usamos JOIN e SELECT, a paginação pode precisar de groupBy para evitar duplicatas se um documento pudesse ter múltiplas caixas/projetos (não é o caso aqui, mas é bom saber).
        // Contudo, para este caso, deve funcionar.
        $documents = $query->paginate($perPage)->withQueryString();

        // --- Obter dados para os filtros ---
        // Busca projetos diretamente da tabela projects
        $availableProjects = Project::orderBy('name')->pluck('name', 'id');
        // Busca anos da tabela documents (considerando filtros ativos?)
        $availableYears = Document::query()
                            // ->when($filterBoxNumber, fn($q) => $q->whereHas('box', fn($bq) => $bq->where('number', 'like', "%{$filterBoxNumber}%"))) // Filtrar anos por caixa?
                            // ->when($filterProjectId, fn($q) => $q->where('project_id', $filterProjectId)) // Filtrar anos por projeto?
            ->select(DB::raw('YEAR(document_date) as year'))
            ->whereNotNull('document_date')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
        // Adicionar busca por caixas disponíveis (opcional, pode ser muitas)
        // $availableBoxes = Box::orderBy('number')->pluck('number', 'id'); // Pode ser ineficiente se houver muitas caixas

        // --- Dados para as Estatísticas (ajustar se necessário) ---
        // Contagem total precisa ser feita antes de aplicar filtros relacionados a JOINs que podem multiplicar linhas
        $totalDocuments = Document::count();
        $totalBoxes = Box::count(); // Total de caixas cadastradas
        $totalProjects = Project::count(); // Total de projetos cadastrados

        // Contagem filtrada - ATENÇÃO: A contagem do paginator ($documents->total()) reflete a query com JOINs e filtros.
        $filteredDocumentsCount = $documents->total();

        // Contagem de caixas/projetos únicos *nos resultados filtrados* é mais complexa com JOIN.
        // Solução simples (pode ser imprecisa se filtros removerem todas as caixas/projetos de um tipo):
        // Requereria uma query separada nos IDs filtrados antes da paginação ou contar na coleção atual.
        // Exemplo contando na coleção atual (pode não refletir o total real filtrado):
        $filteredBoxesCount = $documents->pluck('box_id')->filter()->unique()->count();
        $filteredProjectsCount = $documents->pluck('project_id')->filter()->unique()->count();

        // Verifica se há filtros ativos
        $hasActiveFilters = $request->filled(['search', 'filter_box_number', 'filter_project_id', 'filter_year']);

        return view('documents.index', [
            'documents' => $documents,
            'availableProjects' => $availableProjects,
            'availableYears' => $availableYears,
            // 'availableBoxes' => $availableBoxes, // Descomentar se buscar e passar caixas
            'requestParams' => $request->all(),
            'stats' => [
                'totalDocuments' => $totalDocuments,
                'totalBoxes' => $totalBoxes,
                'totalProjects' => $totalProjects,
                'filteredDocumentsCount' => $filteredDocumentsCount,
                'filteredBoxesCount' => $filteredBoxesCount, // Contagem baseada na página atual
                'filteredProjectsCount' => $filteredProjectsCount, // Contagem baseada na página atual
            ],
            'hasActiveFilters' => $hasActiveFilters,
        ]);
    }

    public function create()
    {
        return view('documents.create');
    }

    public function store(Request $request)
    {
        // Validation and document creation logic here
        $validated = $request->validate([
            'box_number' => 'required|string|max:255',
            'item_number' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'descriptor' => 'nullable|string|max:255',
            'document_number' => 'nullable|string|max:255|unique:documents,document_number', // Adicionado unique
            'title' => 'required|string',
            'document_date' => 'required|string|max:255', // Alterado de date para string
            'project' => 'nullable|string|max:255', // Alterado para nullable como no update
            'confidentiality' => 'nullable|string|max:255',
            'version' => 'nullable|string|max:255',
            'is_copy' => 'nullable|string|max:255', // Alterado de boolean para string
        ]);

        // Create the document
        Document::create($validated);

        return redirect()->route('documents.index')
            ->with('success', 'Document created successfully.');
    }

    /**
     * Exibe o formulário para editar um documento existente
     */
    public function edit(Document $document): View
    {
        return view('documents.edit', compact('document'));
    }

    /**
     * Atualiza um documento existente no banco de dados
     */
    public function update(Request $request, Document $document): RedirectResponse
    {
        $validated = $request->validate([
            'box_number' => 'required|string|max:255',
            'item_number' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'descriptor' => 'nullable|string|max:255',
            'document_number' => 'nullable|string|max:255|unique:documents,document_number,'.$document->id, // Adicionado unique e ignorar atual
            'title' => 'required|string',
            'document_date' => 'required|string|max:255', // Alterado de date para string
            'project' => 'nullable|string|max:255',
            'confidentiality' => 'nullable|string|max:255', // Corrigido typo e tipo
            'version' => 'nullable|string|max:255',
            'is_copy' => 'nullable|string|max:255', // Alterado de boolean para string
        ]);

        // Atualiza o documento com os dados validados
        $document->update($validated);

        return redirect()->route('documents.show', $document)
            ->with('success', 'Documento atualizado com sucesso!');
    }

    /**
     * Remove um documento do banco de dados
     */
    public function destroy(Document $document): RedirectResponse
    {
        $document->delete();

        return redirect()->route('documents.index')
            ->with('success', 'Documento excluído com sucesso!');
    }
}
