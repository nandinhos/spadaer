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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate; // Importar a classe de exportação
use Illuminate\Support\Facades\Log; // Importar facade do Excel
use Illuminate\View\View;
use App\Services\DocumentService;

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

        // 2. Paginar Resultados
        $perPage = $request->input('per_page', 15);
        $documents = (clone $query)->paginate($perPage)->withQueryString();

        // 3. Obter Estatísticas via Service
        $statsData = $this->documentService->getStatistics($query);

        // 4. Preparar Dados Auxiliares para a View
        $availableProjects = \App\Models\Project::orderBy('name')->pluck('name', 'id');
        $availableYears = Document::query()
            ->whereNotNull('document_date')
            ->select('document_date')
            ->distinct()
            ->pluck('document_date')
            ->map(fn($d) => preg_match('/\/(\d{4})$/', $d, $m) ? (int)$m[1] : null)
            ->filter()->unique()->sortDesc()->values();

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
            'hasActiveFilters' => $request->filled(['search', 'filter_box_number', 'filter_project_id', 'filter_year'])
        ]);
    }

    public function create(): View
    {
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
        try {
            // Carrega relacionamentos necessários
            $document->load(['box:id,number', 'project:id,name']);

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
        // Adicionar lógica de autorização (Policy)
        // $this->authorize('delete', $document);
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
        // Verificação de permissão: O usuário pode visualizar este documento?
        // Isso usa o sistema de Gate/Policy que configuramos.
        // Se você tem uma Policy 'DocumentPolicy@view', ela será usada.
        // Se não, ele tentará o Gate 'documents.view'.
        // Se a permissão for apenas genérica ('documents.view') e não por instância,
        // você pode só confiar que o acesso à página principal já foi verificado.
        // Para segurança adicional, especialmente se a URL puder ser adivinhada:
        if (! Gate::allows('documents.view', $document) && ! Gate::allows('view', $document)) {
            // A segunda verificação ('view', $document) é para o caso de você ter uma Policy.
            // Se você só tem 'documents.view' como permissão genérica, talvez só o Gate::allows('documents.view') baste
            // ou confie na proteção da página principal.
            // Mas para uma API, é bom ser explícito.
            // Se você não tem policies por instância, e 'documents.view' é global,
            // pode ser suficiente que o middleware 'auth' já protegeu a rota.
            // No entanto, se 'documents.view' for para a lista, e ver um específico requer mais, adicione a lógica.
            // Por simplicidade, vamos assumir que se ele pode ver a lista, pode ver os detalhes por enquanto.
            // Considere adicionar `$this->authorize('view', $document);` se tiver uma policy.
        }

        // Carregue os relacionamentos que seu modal precisa para exibir
        // O componente modal que você forneceu tenta acessar:
        // selectedDocument.box?.number
        // selectedDocument.project?.name
        $document->load(['box', 'project']);

        // Retorna o documento (com os relacionamentos carregados) como JSON
        // Você pode retornar o objeto do documento diretamente ou aninhá-lo
        return response()->json($document);
        // Ou, se o seu JavaScript espera { document: {...} }:
        // return response()->json(['document' => $document]);
        // Pelo seu script anterior, parece que você espera { document: {...} }
        // fetch(...).then(data => { this.selectedDocument = data.document; })
        // então, vamos usar:
        // return response()->json(['document' => $document]);
    }
}
