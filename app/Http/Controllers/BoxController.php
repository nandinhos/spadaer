<?php

namespace App\Http\Controllers;

// Models
use App\Http\Requests\StoreBoxRequest;
use App\Http\Requests\UpdateBoxRequest;
use App\Models\Box;
// Requests
use App\Models\CommissionMember;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
// Outros
use Illuminate\Http\Request;
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
        // 1. Obter Parâmetros (com nomes consistentes)
        $search = $request->input('search');
        $projectId = $request->input('project_id');
        // *** Pega o ID do membro enviado pelo filtro ***
        $commissionMemberId = $request->input('commission_member_id');
        $sortBy = $request->input('sort_by', 'boxes.number');
        $sortDir = $request->input('sort_dir', 'asc');
        $perPage = $request->input('per_page', 15);

        // Log para depuração do valor recebido
        Log::debug('Filtro - commission_member_id recebido:', ['id' => $commissionMemberId]);

        // 2. Validar Ordenação (como antes)
        $validSortColumns = [
            'boxes.id', 'boxes.number', 'boxes.physical_location', 'boxes.conference_date',
            'projects.name', 'checker_users.name',
        ];
        if (! in_array($sortBy, $validSortColumns)) {
            $sortBy = 'boxes.number';
        }
        if (! in_array(strtolower($sortDir), ['asc', 'desc'])) {
            $sortDir = 'asc';
        }

        // 3. Construir Query Base com Joins e Selects
        $query = Box::query()
            ->select([
                'boxes.*',
                'projects.name as project_name',
                'checker_users.name as checker_name', // Nome do usuário conferente
            ])
            ->leftJoin('projects', 'boxes.project_id', '=', 'projects.id')
            ->leftJoin('commission_members', 'boxes.commission_member_id', '=', 'commission_members.id') // Usa a coluna correta
            ->leftJoin('users as checker_users', 'commission_members.user_id', '=', 'checker_users.id');

        // 4. Aplicar Busca Textual (como antes)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $searchTermWild = "%{$search}%";
                $q->where('boxes.number', 'like', $searchTermWild)
                    ->orWhere('boxes.physical_location', 'like', $searchTermWild)
                    ->orWhere('projects.name', 'like', $searchTermWild)
                    ->orWhere('checker_users.name', 'like', $searchTermWild);
            });
        }

        // 5. Aplicar Filtros Específicos
        if ($projectId) {
            $query->where('boxes.project_id', $projectId);
        }
        // *** Aplicar filtro do conferente ***
        if ($commissionMemberId) {
            // *** Garante que está filtrando pela coluna correta 'boxes.commission_member_id' ***
            $query->where('boxes.commission_member_id', $commissionMemberId);
        }

        // 6. Aplicar Ordenação (como antes)
        $query->orderBy($sortBy, $sortDir);

        // 7. Paginar Resultados com groupBy
        try {
            $boxes = $query->groupBy('boxes.id')
                ->paginate($perPage)
                ->withQueryString();
        } catch (\Throwable $e) { // Use Throwable para pegar mais tipos de erro
            Log::error('Erro na query de paginação BoxController@index: '.$e->getMessage().' SQL: '.($e instanceof \Illuminate\Database\QueryException ? $e->getSql() : 'N/A'));
            session()->flash('error', 'Ocorreu um erro ao listar as caixas. Verifique os filtros ou tente novamente.');
            $boxes = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage);
        }

        // 8. Preparar Dados para Filtros da View
        $projects = Project::orderBy('name')->pluck('name', 'id');
        // *** Geração do $activeMembers - Garantir que ID e Nome estão corretos ***
        $activeMembers = CommissionMember::active() // Pega apenas membros ativos?
            ->join('users', 'commission_members.user_id', '=', 'users.id') // Join obrigatório para pegar o nome
            ->orderBy('users.name') // Ordena pelo nome do usuário
            ->select('commission_members.id', 'users.name as user_name') // Seleciona o ID do MEMBRO e o NOME do usuário
            ->get()
            ->pluck('user_name', 'id'); // Cria o array [commission_member_id => user_name]

        // Log para depurar o array passado para a view
        Log::debug('Filtro - activeMembers gerado:', $activeMembers->toArray());

        // 9. Passar Parâmetros da Request
        $requestParams = $request->all();

        // 10. Retornar a View
        return view('boxes.index', compact('boxes', 'projects', 'activeMembers', 'requestParams', 'request'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // ***** BUSCAR PROJETOS *****
        $projects = Project::orderBy('name')->pluck('name', 'id'); // Busca ID e Nome

        // Buscar Membros Ativos (código que você já tem)
        $activeMembers = CommissionMember::active()
            ->with('user:id,name')
            ->join('users', 'commission_members.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->select('commission_members.id', 'users.name as user_name')
            ->get()
            ->pluck('user_name', 'id');

        // Passar AMBOS para a view
        return view('boxes.create', compact('projects', 'activeMembers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBoxRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        Box::create($validated);

        return redirect()->route('boxes.index')->with('success', 'Caixa criada com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Box $box)
    {
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
        // ***** BUSCAR PROJETOS *****
        $projects = Project::orderBy('name')->pluck('name', 'id'); // Busca ID e Nome

        // Buscar Membros Ativos (código que você já tem)
        $activeMembers = CommissionMember::active()
            ->with('user:id,name')
            ->join('users', 'commission_members.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->select('commission_members.id', 'users.name as user_name')
            ->get()
            ->pluck('user_name', 'id');

        // Passar TUDO para a view
        return view('boxes.edit', compact('box', 'projects', 'activeMembers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBoxRequest $request, Box $box): RedirectResponse
    {
        $validated = $request->validated();
        Log::info('Validation passed via Form Request.', $validated);

        try {
            $box->update($validated);
            Log::info("Caixa ID {$box->id} atualizada.");
        } catch (\Throwable $e) {
            Log::error('Erro ao atualizar caixa: '.$e->getMessage());

            return back()->with('error', 'Erro ao atualizar dados da caixa.')->withInput();
        }

        return redirect()->route('boxes.index')->with('success', 'Caixa atualizada com sucesso.');
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
            Log::error("Erro ao excluir caixa {$box->id}: ".$e->getMessage());
            // Verificar se o erro é devido a FKs restritivas (se não usou cascade/set null)
            if ($e instanceof \Illuminate\Database\QueryException && str_contains($e->getMessage(), 'constraint violation')) {
                return redirect()->route('boxes.index')->with('error', 'Não é possível excluir a caixa pois ela contém documentos.');
            }

            return redirect()->route('boxes.index')->with('error', 'Erro ao excluir a caixa.');
        }
    }
}
