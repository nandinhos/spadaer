<?php

namespace App\Http\Controllers;

// Models
use App\Models\Box;
use App\Models\CommissionMember;
use App\Models\Project;
use App\Models\Document; // Importar para o show
// Requests
use App\Http\Requests\StoreBoxRequest;
use App\Http\Requests\UpdateBoxRequest;
// Outros
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class BoxController extends Controller
{
    // Aplicar Policy (descomente quando criar BoxPolicy)
    // public function __construct()
    // {
    //     $this->authorizeResource(Box::class, 'box');
    // }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // 1. Obter Parâmetros
        $search = $request->input('search');
        $projectId = $request->input('project_id');
        $commissionMemberId = $request->input('commission_member_id');
        $filterStatus = $request->input('filter_status'); // Filtro de status
        $sortBy = $request->input('sort_by', 'boxes.number');
        $sortDir = $request->input('sort_dir', 'asc');
        $perPage = $request->input('per_page', 15);

        // 2. Validar Ordenação
        $validSortColumns = [
            'boxes.id',
            'boxes.number',
            'boxes.physical_location',
            'boxes.conference_date',
            'projects.name',
            'checker_users.name',
            'documents_count' // Inclui contagem
        ];
        if (! in_array($sortBy, $validSortColumns)) {
            $sortBy = 'boxes.number';
        }
        if (! in_array(strtolower($sortDir), ['asc', 'desc'])) {
            $sortDir = 'asc';
        }

        // 3. Construir Query Base
        $query = Box::query()
            ->withCount('documents') // Obtém documents_count
            ->with(['project:id,name', 'commissionMember.user:id,name']) // Eager load otimizado
            // Joins necessários para filtro/sort
            ->leftJoin('projects', 'boxes.project_id', '=', 'projects.id')
            ->leftJoin('commission_members', 'boxes.commission_member_id', '=', 'commission_members.id')
            ->leftJoin('users as checker_users', 'commission_members.user_id', '=', 'checker_users.id');

        // 4. Aplicar Busca Textual
        if ($search) { /* ... lógica where ... */
        }

        // 5. Aplicar Filtros Específicos
        if ($projectId) {
            $query->where('boxes.project_id', $projectId);
        }
        if ($commissionMemberId) {
            $query->where('boxes.commission_member_id', $commissionMemberId);
        }
        // Filtro de Status
        if ($filterStatus === 'with_docs') {
            $query->has('documents');
        } elseif ($filterStatus === 'empty') {
            $query->doesntHave('documents');
        }

        // 6. Aplicar Ordenação
        if ($sortBy === 'documents_count') {
            $query->orderBy('documents_count', $sortDir);
        } elseif (str_contains($sortBy, '.')) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->orderBy('boxes.' . $sortBy, $sortDir);
        }

        // 7. Paginar Resultados
        try {
            // REMOVIDO ->select('boxes.*') e ->groupBy('boxes.id')
            // Deixa o Eloquent/withCount gerenciar a seleção
            $boxes = $query
                ->paginate($perPage)
                ->withQueryString();
        } catch (\Throwable $e) {
            // Adicionado log da query SQL para depuração
            Log::error("Erro ao buscar caixas: " . $e->getMessage(), [
                'exception' => $e,
                'query' => $query->toSql(), // Loga a query SQL gerada
                'bindings' => $query->getBindings() // Loga os bindings da query
            ]);
            // Retornar view com erro ou redirecionar
            // É uma boa prática retornar algo útil aqui em caso de erro
            return view('boxes.index', [
                'boxes' => collect(), // Retorna uma coleção vazia para evitar erros na view
                'projects' => Project::orderBy('name')->pluck('name', 'id'),
                'activeMembers' => CommissionMember::active()
                    ->join('users', 'commission_members.user_id', '=', 'users.id')
                    ->orderBy('users.name')
                    ->select('commission_members.id', 'users.name as user_name')
                    ->get()->pluck('user_name', 'id'),
                'statusOptions' => ['' => 'Todos os Status', 'with_docs' => 'Com Documentos', 'empty' => 'Vazias'],
                'requestParams' => $request->all(),
                'request' => $request,
                'errorMessage' => 'Ocorreu um erro ao buscar as caixas. Por favor, tente novamente.' // Mensagem para o usuário
            ]);
        }

        // 8. Preparar Dados para Filtros da View
        $projects = Project::orderBy('name')->pluck('name', 'id');
        $activeMembers = CommissionMember::active()
            ->join('users', 'commission_members.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->select('commission_members.id', 'users.name as user_name')
            ->get()->pluck('user_name', 'id');
        $statusOptions = ['' => 'Todos os Status', 'with_docs' => 'Com Documentos', 'empty' => 'Vazias'];

        // 9. Passar Parâmetros da Request
        $requestParams = $request->all();

        // 10. Retornar a View
        return view('boxes.index', compact('boxes', 'projects', 'activeMembers', 'statusOptions', 'requestParams', 'request'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $projects = Project::orderBy('name')->pluck('name', 'id');
        $activeMembers = CommissionMember::active()
            ->join('users', 'commission_members.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->select('commission_members.id', 'users.name as user_name')
            ->get()->pluck('user_name', 'id');
        return view('boxes.create', compact('projects', 'activeMembers'));
    }

    /**
     * Store a newly created resource in storage.
     * REMOVIDA A LÓGICA DE IMPORTAÇÃO DAQUI
     */
    public function store(StoreBoxRequest $request): RedirectResponse
    {
        $validatedBoxData = $request->validated();
        // Remover 'documents_csv' se ele ainda existir na validação (deve ser removido do Form Request)
        // unset($validatedBoxData['documents_csv']);

        try {
            Box::create($validatedBoxData);
            return redirect()->route('boxes.index')->with('success', 'Caixa criada com sucesso.');
        } catch (\Throwable $e) {
            Log::error('Erro ao criar caixa: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Erro ao salvar a caixa. Verifique os logs.')->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Box $box) // Sem tipo de retorno
    {
        // Carrega relacionamentos necessários
        $box->load(['project', 'commissionMember.user', 'documents' => function ($query) {
            $query->orderBy('item_number', 'asc');
        }]);
        return view('boxes.show', compact('box'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Box $box): View
    {
        $projects = Project::orderBy('name')->pluck('name', 'id');
        $activeMembers = CommissionMember::active()
            ->join('users', 'commission_members.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->select('commission_members.id', 'users.name as user_name')
            ->get()->pluck('user_name', 'id');
        return view('boxes.edit', compact('box', 'projects', 'activeMembers'));
    }

    /**
     * Update the specified resource in storage.
     * REMOVIDA A LÓGICA DE IMPORTAÇÃO DAQUI
     */
    public function update(UpdateBoxRequest $request, Box $box): RedirectResponse
    {
        $validatedBoxData = $request->validated();
        // Remover 'documents_csv' se ele ainda existir na validação
        // unset($validatedBoxData['documents_csv']);

        try {
            $box->update($validatedBoxData);
            // Redireciona para a view da caixa após editar suas informações
            return redirect()->route('boxes.show', $box)->with('success', 'Caixa atualizada com sucesso.');
            // Ou redireciona para o index:
            // return redirect()->route('boxes.index')->with('success', 'Caixa atualizada com sucesso.');
        } catch (\Throwable $e) {
            Log::error("Erro ao atualizar caixa {$box->id}: " . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Erro ao salvar as alterações da caixa. Verifique os logs.')->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Box $box): RedirectResponse
    {
        // Adicionar lógica de autorização (Policy)
        // $this->authorize('delete', $box);
        try {
            // onDelete('cascade') na FK em documents deve cuidar dos documentos
            $box->delete();

            return redirect()->route('boxes.index')->with('success', 'Caixa excluída com sucesso.');
        } catch (\Throwable $e) {
            Log::error("Erro ao excluir caixa {$box->id}: " . $e->getMessage());
            // Verificar se o erro é devido a FKs restritivas (se não usou cascade/set null)
            if ($e instanceof \Illuminate\Database\QueryException && str_contains($e->getMessage(), 'constraint violation')) {
                return redirect()->route('boxes.index')->with('error', 'Não é possível excluir a caixa pois ela contém documentos.');
            }

            return redirect()->route('boxes.index')->with('error', 'Erro ao excluir a caixa.');
        }
    }

    // Método batchAssignChecker (se implementado) permanece aqui
    // public function batchAssignChecker(Request $request): RedirectResponse { ... }

} // Fim da classe BoxController