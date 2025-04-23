<?php

namespace App\Http\Controllers;

// Models
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Box;
// Requests
use App\Models\Document; // <-- Use Form Request
use App\Models\Project; // <-- Use Form Request
use Illuminate\Http\RedirectResponse; // Use Request padrão apenas onde Form Request não se aplica
// Outros
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// Para o método show/ajax
use Illuminate\View\View;

class DocumentController extends Controller
{
    // Aplicar Policy (descomente quando criar DocumentPolicy)
    // public function __construct()
    // {
    //     $this->authorizeResource(Document::class, 'document');
    // }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View // Request padrão aqui para pegar filtros/sort/etc.
    {
        // Parâmetros (mantendo nomes consistentes com a última refatoração)
        $searchTerm = $request->input('search');
        $filterBoxNumber = $request->input('filter_box_number');
        $filterProjectId = $request->input('filter_project_id');
        $filterYear = $request->input('filter_year');
        $sortBy = $request->input('sort_by', 'documents.document_date'); // Default
        $sortDir = $request->input('sort_dir', 'desc');
        $perPage = $request->input('per_page', 15);

        // Colunas válidas para ordenação
        $validSortColumns = [
            'documents.id', 'documents.item_number', 'documents.code', 'documents.descriptor',
            'documents.document_number', 'documents.title', 'documents.document_date',
            'documents.confidentiality', 'documents.version', 'documents.is_copy',
            'boxes.number', 'projects.name',
        ];
        if (! in_array($sortBy, $validSortColumns)) {
            $sortBy = 'documents.document_date';
        }
        if (! in_array(strtolower($sortDir), ['asc', 'desc'])) {
            $sortDir = 'desc';
        }

        // Query Base com Joins e Selects
        $query = Document::query()
            ->select('documents.*', 'boxes.number as box_number', 'projects.name as project_name')
            ->leftJoin('boxes', 'documents.box_id', '=', 'boxes.id')
            ->leftJoin('projects', 'documents.project_id', '=', 'projects.id');

        // Aplicar busca textual
        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('documents.item_number', 'like', "%{$searchTerm}%")
                    ->orWhere('documents.code', 'like', "%{$searchTerm}%")
                    ->orWhere('documents.descriptor', 'like', "%{$searchTerm}%")
                    ->orWhere('documents.document_number', 'like', "%{$searchTerm}%")
                    ->orWhere('documents.title', 'like', "%{$searchTerm}%")
                    ->orWhere('boxes.number', 'like', "%{$searchTerm}%")
                    ->orWhere('projects.name', 'like', "%{$searchTerm}%");
            });
        }

        // Aplicar filtros específicos
        if ($filterBoxNumber) {
            $query->where('boxes.number', 'like', "%{$filterBoxNumber}%");
        }
        if ($filterProjectId) {
            $query->where('documents.project_id', $filterProjectId);
        }
        if ($filterYear) {
            $query->whereYear('documents.document_date', $filterYear);
        }

        // Aplicar ordenação
        $query->orderBy($sortBy, $sortDir);

        // Paginar resultados (com groupBy por causa dos joins)
        $documents = $query->groupBy('documents.id') // Garante unicidade do documento
            ->paginate($perPage)
            ->withQueryString();

        // --- Dados para Filtros ---
        $availableProjects = Project::orderBy('name')->pluck('name', 'id');
        $availableYears = Document::query()
            ->select(DB::raw('YEAR(document_date) as year'))
            ->whereNotNull('document_date')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // --- Estatísticas (simplificado) ---
        $stats = [/* ... lógica de estatísticas ... */]; // Recalcular se necessário
        $hasActiveFilters = $request->filled(['search', 'filter_box_number', 'filter_project_id', 'filter_year']);

        return view('documents.index', compact(
            'documents',
            'availableProjects',
            'availableYears',
            'requestParams', // Passar $request->all()
            'stats',
            'hasActiveFilters'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Passar dados necessários para selects no formulário
        $boxes = Box::orderBy('number')->pluck('number', 'id');
        $projects = Project::orderBy('name')->pluck('name', 'id');

        return view('documents.create', compact('boxes', 'projects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDocumentRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        // Adicionar lógica para lidar com upload de arquivo de documento se houver
        Document::create($validated);

        return redirect()->route('documents.index')->with('success', 'Documento criado com sucesso.');
    }

    /**
     * Display the specified resource for AJAX/Modal.
     * Removido o tipo de retorno para evitar conflito. O Laravel detectará
     * a requisição AJAX ou retornará JSON devido ao response()->json().
     */
    public function show(Document $document) // Sem tipo de retorno
    {
        // Carrega os relacionamentos necessários para o modal
        $document->load(['box:id,number', 'project:id,name']); // Carrega apenas colunas necessárias

        return response()->json($document);
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
        // Adicionar lógica para lidar com upload de arquivo se houver
        $document->update($validated);

        return redirect()->route('documents.index')->with('success', 'Documento atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document): RedirectResponse
    {
        // Adicionar lógica de autorização aqui (Policy) antes de deletar
        // $this->authorize('delete', $document);
        try {
            // Adicionar lógica para deletar arquivo associado se houver
            $document->delete();

            return redirect()->route('documents.index')->with('success', 'Documento excluído com sucesso.');
        } catch (\Throwable $e) {
            Log::error("Erro ao excluir documento {$document->id}: ".$e->getMessage());

            return redirect()->route('documents.index')->with('error', 'Erro ao excluir o documento.');
        }
    }
}
