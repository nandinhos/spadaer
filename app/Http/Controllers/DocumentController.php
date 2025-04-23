<?php

namespace App\Http\Controllers;

// Models
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Box;
// Requests
use App\Models\Document;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
// Outros
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Importar Log se for usar
use Illuminate\Support\Facades\Log;
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
    public function index(Request $request): View
    {
        // 1. Obter Parâmetros da Requisição com Defaults
        $searchTerm = $request->input('search');
        $filterBoxNumber = $request->input('filter_box_number');
        $filterProjectId = $request->input('filter_project_id');
        $filterYear = $request->input('filter_year');
        $sortBy = $request->input('sort_by', 'documents.document_date'); // Default sort
        $sortDir = $request->input('sort_dir', 'desc'); // Default direction for date
        $perPage = $request->input('per_page', 15);

        // 2. Validar Parâmetros de Ordenação
        $validSortColumns = [
            'documents.id', 'documents.item_number', 'documents.code', 'documents.descriptor',
            'documents.document_number', 'documents.title', 'documents.document_date',
            'documents.confidentiality', 'documents.version', 'documents.is_copy',
            'boxes.number', // Ordenar pelo número da caixa
            'projects.name', // Ordenar pelo nome do projeto
        ];
        if (! in_array($sortBy, $validSortColumns)) {
            $sortBy = 'documents.document_date'; // Reset para default seguro
        }
        if (! in_array(strtolower($sortDir), ['asc', 'desc'])) {
            $sortDir = 'desc'; // Reset para default seguro
        }

        // 3. Construir a Query Base com Joins e Seleção Explícita
        $query = Document::query()
            ->select([ // Selecionar colunas evita ambiguidades e pode otimizar
                'documents.*', // Todas as colunas da tabela principal
                'boxes.number as box_number', // Alias para número da caixa
                'projects.name as project_name', // Alias para nome do projeto
            ])
            ->leftJoin('boxes', 'documents.box_id', '=', 'boxes.id')
            ->leftJoin('projects', 'documents.project_id', '=', 'projects.id');

        // 4. Aplicar Busca Textual (se houver)
        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $searchTermWild = "%{$searchTerm}%"; // Adiciona wildcards uma vez
                $q->where('documents.item_number', 'like', $searchTermWild)
                    ->orWhere('documents.code', 'like', $searchTermWild)
                    ->orWhere('documents.descriptor', 'like', $searchTermWild)
                    ->orWhere('documents.document_number', 'like', $searchTermWild)
                    ->orWhere('documents.title', 'like', $searchTermWild)
                    // Busca nas tabelas relacionadas usando os aliases ou nomes qualificados
                    ->orWhere('boxes.number', 'like', $searchTermWild)
                    ->orWhere('projects.name', 'like', $searchTermWild);
            });
        }

        // 5. Aplicar Filtros Específicos (se houver)
        if ($filterBoxNumber) {
            $query->where('boxes.number', 'like', "%{$filterBoxNumber}%"); // Usa join
        }
        if ($filterProjectId) {
            $query->where('documents.project_id', $filterProjectId); // Filtra direto na FK
        }
        if ($filterYear) {
            // Garante que a coluna de data seja qualificada para evitar ambiguidade
            $query->whereYear('documents.document_date', $filterYear);
        }

        // 6. Aplicar Ordenação (já validada)
        $query->orderBy($sortBy, $sortDir);

        // 7. Paginar Resultados
        // O groupBy é crucial aqui por causa dos LEFT JOINs para garantir contagem correta
        $documents = $query->groupBy('documents.id')
            ->paginate($perPage)
            ->withQueryString(); // Mantém todos os params na URL da paginação

        // 8. Preparar Dados para os Filtros da View
        $availableProjects = Project::orderBy('name')->pluck('name', 'id');
        // Buscar anos distintos da coluna de data dos documentos *já filtrados* seria mais preciso,
        // mas pode ser complexo. Buscar todos os anos é mais simples:
        $availableYears = Document::query()
            ->select(DB::raw('YEAR(document_date) as year'))
            ->whereNotNull('document_date')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // 9. Preparar Dados para Estatísticas (Exemplo Simplificado)
        // Para estatísticas precisas *filtradas*, você precisaria clonar a query *antes* do groupBy e paginate
        // e fazer contagens distintas. Ex: $filteredQuery = (clone $query)->groupBy(null);
        // $filteredDocumentsCount = $filteredQuery->count('documents.id'); // Contagem distinta de documentos
        // $filteredBoxesCount = $filteredQuery->distinct()->count('documents.box_id'); // Contagem de caixas únicas
        // $filteredProjectsCount = $filteredQuery->distinct()->count('documents.project_id'); // Contagem de projetos únicos
        // Para simplificar, vamos usar os totais gerais e a contagem do paginator:
        $stats = [
            'totalDocuments' => Document::count(),
            'totalBoxes' => Box::count(),
            'totalProjects' => Project::count(),
            'filteredDocumentsCount' => $documents->total(), // Contagem do paginator (pode ser afetada por groupBy)
            // Contagens abaixo são aproximações baseadas na página atual
            'filteredBoxesCount' => $documents->pluck('box_id')->filter()->unique()->count(),
            'filteredProjectsCount' => $documents->pluck('project_id')->filter()->unique()->count(),
        ];
        $hasActiveFilters = $request->filled(['search', 'filter_box_number', 'filter_project_id', 'filter_year']);

        // 10. Criar array com parâmetros da request para a view
        $requestParams = $request->all(); // <<--- A LINHA QUE FALTAVA

        // 11. Retornar a View com todos os dados necessários
        return view('documents.index', compact(
            'documents',          // Coleção paginada de documentos
            'availableProjects',  // Para o select de filtro de projetos
            'availableYears',     // Para o select de filtro de ano
            'requestParams',      // Todos os parâmetros da request atual (para preencher filtros/links)
            'stats',              // Array com dados para os cards de estatísticas
            'hasActiveFilters'    // Booleano para indicar se filtros estão ativos
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
