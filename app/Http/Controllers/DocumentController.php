<?php

namespace App\Http\Controllers;

// Models
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
// Requests
use App\Models\Box; // Usar para store
use App\Models\Document; // Usar para update
use App\Models\Project;
// Outros
use App\Services\DocumentService;
use Illuminate\Http\RedirectResponse;
// Importar a classe de exportação
use Illuminate\Http\Request; // Importar facade do Excel
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class DocumentController extends Controller
{
    protected DocumentService $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
        // $this->authorizeResource(Document::class, 'document');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // 1. Obter Parâmetros e utilizar o Service para a Query
        $params = $request->all();
        $query = $this->documentService->listDocuments($params);

        // 2. Paginar Resultados com Eager Loading
        $perPage = $request->input('per_page', 15);
        $documents = (clone $query)
            ->with(['box', 'project']) // Eager loading para evitar N+1
            ->paginate($perPage)
            ->withQueryString();

        // 3. Obter Estatísticas via Service
        $statsData = $this->documentService->getStatistics($query);

        // 4. Preparar Dados Auxiliares para a View
        $availableProjects = \App\Models\Project::orderBy('name')->pluck('name', 'id');

        // Otimização: Pegar apenas os últimos 4 dígitos das datas registradas
        $availableYears = Document::query()
            ->whereNotNull('document_date')
            ->selectRaw("DISTINCT 
                CASE 
                    WHEN document_date REGEXP '^[0-9]{4}' THEN SUBSTRING(document_date, 1, 4)
                    WHEN document_date REGEXP '/[0-9]{4}$' THEN RIGHT(document_date, 4)
                    ELSE NULL 
                END as year")
            ->pluck('year')
            ->filter()
            ->sortDesc()
            ->values();

        $stats = array_merge([
            'totalDocuments' => Document::count(),
            'totalBoxes' => \App\Models\Box::count(),
            'totalProjects' => \App\Models\Project::count(),
            'filteredBoxesCount' => $query->distinct()->count('documents.box_id'),
            'filteredProjectsCount' => $query->distinct()->count('documents.project_id'),
        ], $statsData);

        return view('documents.index', [
            'documents' => $documents,
            'availableProjects' => $availableProjects,
            'availableYears' => $availableYears,
            'requestParams' => $params,
            'stats' => $stats,
            'hasActiveFilters' => $request->filled(['search', 'filter_box_number', 'filter_project_id', 'filter_year']),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Document::class);

        // Busca caixas (ID => Número) ordenadas
        $boxes = Box::orderBy('number')->pluck('number', 'id');
        // Busca projetos (ID => Nome) ordenados
        $projects = Project::orderBy('name')->pluck('name', 'id');

        // Retorna a view 'documents.create' passando as coleções
        return view('documents.create', compact('boxes', 'projects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDocumentRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        try {
            Document::create($validated);

            return redirect()->route('documents.index')->with('success', 'Documento criado com sucesso.');
        } catch (\Throwable $e) {
            Log::error('Erro ao criar documento: '.$e->getMessage());

            return back()->with('error', 'Erro ao salvar o documento. Verifique os dados.')->withInput();
        }
    }

    /**
     * Display the specified resource details (for AJAX/Modal).
     */
    public function show(Document $document)
    {
        $this->authorize('view', $document);

        try {
            // Carrega relacionamentos necessários
            $document->load(['box:id,number', 'project:id,name']);

            // Registrar log de visualização
            $document->logView();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json($document);
            }

            return view('documents.show', compact('document'));
        } catch (\Throwable $e) {
            Log::error("Erro ao buscar detalhes do documento {$document->id}: ".$e->getMessage());

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['error' => 'Não foi possível carregar os detalhes do documento.'], 500);
            }

            return back()->with('error', 'Não foi possível carregar os detalhes do documento.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document): View
    {
        $this->authorize('update', $document);

        $boxes = Box::orderBy('number')->pluck('number', 'id');
        $projects = Project::orderBy('name')->pluck('name', 'id');

        return view('documents.edit', compact('document', 'boxes', 'projects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDocumentRequest $request, Document $document): RedirectResponse
    {
        $validated = $request->validated();
        try {
            $document->update($validated);

            return redirect()->route('documents.index')->with('success', 'Documento atualizado com sucesso.');
        } catch (\Throwable $e) {
            Log::error("Erro ao atualizar documento {$document->id}: ".$e->getMessage());

            return back()->with('error', 'Erro ao atualizar o documento. Verifique os dados.')->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document): RedirectResponse
    {
        $this->authorize('delete', $document);

        try {
            $document->delete();

            return redirect()->route('documents.index')->with('success', 'Documento excluído com sucesso.');
        } catch (\Throwable $e) {
            Log::error("Erro ao excluir documento {$document->id}: ".$e->getMessage());

            return redirect()->route('documents.index')->with('error', 'Erro ao excluir o documento.');
        }
    }

    /**
     * Retorna os detalhes de um documento específico em formato JSON.
     * Usado para popular o modal de detalhes do documento.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getJsonDetails(Document $document)
    {
        $this->authorize('view', $document);

        $document->logView();

        // Carregue os relacionamentos que seu modal precisa para exibir
        // O componente modal que você forneceu tenta acessar:
        // selectedDocument.box?.number
        // selectedDocument.project?.name
        $document->load(['box', 'project']);

        // Retorna o documento (com os relacionamentos carregados) como JSON
        return response()->json($document);
    }
}
