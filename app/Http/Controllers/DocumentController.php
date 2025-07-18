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
use Maatwebsite\Excel\Facades\Excel;

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
        $sortBy = $request->input('sort_by', 'documents.id'); // Mudar default para ID talvez? Ou data?
        $sortDir = $request->input('sort_dir', 'desc'); // Descendente para ID ou data é comum
        $perPage = $request->input('per_page', 15);
        $hasActiveFilters = $request->filled(['search', 'filter_box_number', 'filter_project_id', 'filter_year']);

        // 2. Validar Parâmetros de Ordenação
        $validSortColumns = [
            'documents.id', 'documents.item_number', 'documents.code', 'documents.descriptor',
            'documents.document_number', 'documents.title', 'documents.document_date', // Ordenar por string MES/ANO?
            'documents.confidentiality', 'documents.version', 'documents.is_copy',
            'boxes.number', // Ordenar pelo número da caixa
            'projects.name', // Ordenar pelo nome do projeto
        ];
        if (! in_array($sortBy, $validSortColumns)) {
            $sortBy = 'documents.id'; // Default mais seguro: ID
        }
        if (! in_array(strtolower($sortDir), ['asc', 'desc'])) {
            $sortDir = 'desc';
        }
        // Atenção: Ordenar por 'documents.document_date' (VARCHAR MES/ANO) não será cronológico.
        // Se precisar ordenar por data, considere adicionar a coluna document_year ou uma data completa.

        // 3. Construir a Query Base com Joins e Seleção Explícita
        $query = Document::query()
            ->select([
                'documents.*',
                'boxes.number as box_number',
                'projects.name as project_name',
            ])
            ->leftJoin('boxes', 'documents.box_id', '=', 'boxes.id')
            ->leftJoin('projects', 'documents.project_id', '=', 'projects.id');

        // 4. Aplicar Busca Textual (se houver)
        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $searchTermWild = "%{$searchTerm}%";
                $q->where('documents.item_number', 'like', $searchTermWild)
                    ->orWhere('documents.code', 'like', $searchTermWild)
                    ->orWhere('documents.descriptor', 'like', $searchTermWild)
                    ->orWhere('documents.document_number', 'like', $searchTermWild)
                    ->orWhere('documents.title', 'like', $searchTermWild)
                    ->orWhere('documents.document_date', 'like', $searchTermWild) // Busca na string MES/ANO
                    ->orWhere('boxes.number', 'like', $searchTermWild)
                    ->orWhere('projects.name', 'like', $searchTermWild);
            });
        }

        // 5. Aplicar Filtros Específicos (se houver)
        if ($filterBoxNumber) {
            $query->where('boxes.number', 'like', "%{$filterBoxNumber}%");
        }
        if ($filterProjectId) {
            $query->where('documents.project_id', $filterProjectId);
        }
        if ($filterYear) {
            // Filtra pela string document_date terminando com /YYYY
            $query->where('documents.document_date', 'like', '%/'.$filterYear);
        }

        // 6. Aplicar Ordenação (já validada)
        if ($request->has('sort_by')) {
            // O usuário clicou em um cabeçalho, usar a ordenação solicitada
            if ($sortBy === 'documents.item_number') {
                // Se for a coluna 'item', usar ordenação numérica
                $query->orderByRaw('CAST(documents.item_number AS UNSIGNED) '.$sortDir);
            } else {
                // Para outras colunas, usar a ordenação padrão
                $query->orderBy($sortBy, $sortDir);
            }
        } else {
            // Nenhuma ordenação solicitada na URL, aplicar o default inteligente:
            // 1. Ordenar por número da caixa (alfabeticamente)
            // 2. Ordenar por item (numericamente)
            $query->orderBy('boxes.number', 'asc')
                ->orderByRaw('CAST(documents.item_number AS UNSIGNED) asc');
        }

        // 7. Paginar Resultados
        // Clonar a query *antes* do groupBy e paginate para calcular estatísticas filtradas totais
        $filteredQuery = clone $query;
        $documents = $query->groupBy('documents.id') // Agrupa para paginação correta com joins
            ->paginate($perPage)
            ->withQueryString();

        // 8. Preparar Dados para os Filtros da View
        $availableProjects = Project::orderBy('name')->pluck('name', 'id');
        // Extrai anos disponíveis da string "MES/ANO"
        $availableYears = Document::query()
            ->whereNotNull('document_date')
            ->select('document_date')
            ->distinct()
            ->pluck('document_date')
            ->map(function ($dateString) {
                if (preg_match('/\/(\d{4})$/', $dateString, $matches)) {
                    return (int) $matches[1];
                }

                return null;
            })
            ->filter()->unique()->sortDesc()->values();

        // 9. Preparar Dados para Estatísticas
        $totalDocuments = Document::count();
        $totalBoxes = Box::count();
        $totalProjects = Project::count();

        // Calcular estatísticas filtradas TOTAIS (usando a query clonada ANTES do paginate/groupBy)
        // Nota: count() sem groupBy pode inflar se joins duplicarem linhas, mas count(DISTINCT documents.id) resolve.
        $filteredDocumentsCount = $filteredQuery->distinct()->count('documents.id');
        $filteredBoxesCount = $filteredQuery->distinct()->count('documents.box_id'); // Conta IDs de caixa únicos nos resultados
        $filteredProjectsCount = $filteredQuery->distinct()->count('documents.project_id'); // Conta IDs de projeto únicos nos resultados

        // Calcular intervalo de anos filtrados (precisa executar a query clonada)
        // Calcular intervalo de anos filtrados (precisa executar a query clonada)
        $yearRange = '--';
        if ($filteredDocumentsCount > 0) {
            // Remove qualquer ordenação anterior da query clonada
            // e seleciona apenas a coluna de data para o distinct
            $yearsInData = $filteredQuery->reorder() // <--- REMOVE ordens anteriores
                ->select('document_date') // Seleciona SÓ a data
                ->whereNotNull('document_date')
                ->distinct() // Pega datas únicas
                ->pluck('document_date') // Pega a coleção de strings "MES/ANO"
                ->map(function ($dateString) {
                    if ($dateString && preg_match('/\/(\d{4})$/', $dateString, $matches)) {
                        return (int) $matches[1];
                    }

                    return null;
                })
                ->filter()
                ->unique(); // Pega anos únicos

            if ($yearsInData->isNotEmpty()) {
                $minYear = $yearsInData->min();
                $maxYear = $yearsInData->max();
                $yearRange = ($minYear === $maxYear) ? (string) $minYear : "{$minYear} - {$maxYear}";
            }
        }
        // --- Fim do Cálculo do Intervalo ---

        $stats = [
            'totalDocuments' => $totalDocuments,
            'totalBoxes' => $totalBoxes,
            'totalProjects' => $totalProjects,
            'filteredDocumentsCount' => $filteredDocumentsCount, // Contagem filtrada total
            'filteredBoxesCount' => $filteredBoxesCount,         // Contagem filtrada total
            'filteredProjectsCount' => $filteredProjectsCount,    // Contagem filtrada total
            'yearRange' => $yearRange,                            // Intervalo de anos filtrado total
        ];
        $hasActiveFilters = $request->filled(['search', 'filter_box_number', 'filter_project_id', 'filter_year']);

        // 10. Criar array com parâmetros da request para a view
        $requestParams = $request->all();

        // 11. Retornar a View com todos os dados necessários
        return view('documents.index', compact(
            'documents',
            'availableProjects',
            'availableYears',
            'requestParams',
            'stats',
            'hasActiveFilters'
        ));
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
    public function show(Document $document) // Sem tipo de retorno para compatibilidade
    {
        try {
            // Carrega relacionamentos necessários, selecionando colunas específicas
            $document->load(['box:id,number', 'project:id,name']);

            return response()->json($document);
        } catch (\Throwable $e) {
            Log::error("Erro ao buscar detalhes do documento {$document->id}: ".$e->getMessage());

            // Retorna um erro JSON padrão
            return response()->json(['error' => 'Não foi possível carregar os detalhes do documento.'], 500);
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
