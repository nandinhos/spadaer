<?php

namespace App\Http\Controllers;

// Models
use App\Http\Requests\StoreBoxRequest;
use App\Http\Requests\UpdateBoxRequest; // <--- IMPORTAR Project
use App\Models\Box;
use App\Models\CommissionMember;
// Requests
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
        // 1. Obter Parâmetros da Requisição
        $search = $request->input('search');
        $projectId = $request->input('project_id');
        // Nome do parâmetro atualizado para consistência
        $commissionMemberId = $request->input('commission_member_id');
        $sortBy = $request->input('sort_by', 'boxes.number');
        $sortDir = $request->input('sort_dir', 'asc');
        $perPage = $request->input('per_page', 15);

        // 2. Validar Parâmetros de Ordenação
        $validSortColumns = [
            'boxes.id', 'boxes.number', 'boxes.physical_location', 'boxes.conference_date',
            'projects.name', 'checker_users.name', // Mantendo alias do usuário
        ];
        if (! in_array($sortBy, $validSortColumns)) {
            $sortBy = 'boxes.number'; // Reset para default seguro
        }
        if (! in_array(strtolower($sortDir), ['asc', 'desc'])) {
            $sortDir = 'asc'; // Reset para default seguro
        }

        // 3. Construir a Query Base
        $query = Box::query()
            ->select([
                'boxes.*',
                'projects.name as project_name',
                'checker_users.name as checker_name', // Nome do usuário conferente
            ])
            ->leftJoin('projects', 'boxes.project_id', '=', 'projects.id')
            // CORREÇÃO AQUI: Join usando o nome da coluna RENOMEADA
            ->leftJoin('commission_members', 'boxes.commission_member_id', '=', 'commission_members.id') // <-- Nome da coluna corrigido
            // Join com users para pegar o nome
            ->leftJoin('users as checker_users', 'commission_members.user_id', '=', 'checker_users.id');

        // 4. Aplicar Busca Textual
        if ($search) {
            $query->where(function ($q) use ($search) {
                $searchTermWild = "%{$search}%";
                $q->where('boxes.number', 'like', $searchTermWild)
                    ->orWhere('boxes.physical_location', 'like', $searchTermWild)
                    ->orWhere('projects.name', 'like', $searchTermWild)
                    ->orWhere('checker_users.name', 'like', $searchTermWild); // Busca no nome do usuário conferente
            });
        }

        // 5. Aplicar Filtros Específicos
        if ($projectId) {
            $query->where('boxes.project_id', $projectId);
        }
        if ($commissionMemberId) {
            // CORREÇÃO AQUI: Filtrar pela coluna RENOMEADA
            $query->where('boxes.commission_member_id', $commissionMemberId); // <-- Nome da coluna corrigido
        }

        // 6. Validar e Aplicar Ordenação
        if (in_array($sortBy, $validSortColumns)) {
            if ($sortBy === 'checker_users.name') {
                $query->orderBy('checker_users.name', $sortDir);
            } elseif ($sortBy === 'projects.name') {
                $query->orderBy('projects.name', $sortDir);
            } else {
                $col = $sortBy;
                if (! str_contains($col, '.') && $col !== 'id') {
                    $col = 'boxes.'.$col;
                }
                $query->orderBy($col, $sortDir);
            }
        } else {
            $query->orderBy('boxes.number', 'asc');
        }

        // 7. Paginar Resultados com groupBy
        // O groupBy 'boxes.id' é essencial por causa dos joins
        try {
            $boxes = $query->groupBy('boxes.id')
                ->paginate($perPage)
                ->withQueryString();
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Erro na query de paginação BoxController@index: '.$e->getMessage().' SQL: '.$e->getSql());
            // Tratar o erro - talvez redirecionar com erro ou mostrar view de erro
            // Por enquanto, vamos retornar uma coleção vazia para evitar quebrar a view
            $boxes = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage);
            // Adicionar mensagem de erro para o usuário
            session()->flash('error', 'Ocorreu um erro ao listar as caixas. Verifique os filtros ou tente novamente.');
        }

        // 8. Preparar Dados para os Filtros da View
        $projects = Project::orderBy('name')->pluck('name', 'id');
        $activeMembers = CommissionMember::active()
            ->with('user:id,name')
            ->join('users', 'commission_members.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->get()
            ->mapWithKeys(fn ($member) => [$member->id => $member->user->name ?? 'Inválido']);

        // 9. Passar Parâmetros da Request para a View
        $requestParams = $request->all();

        // 10. Retornar a View
        return view('boxes.index', compact('boxes', 'projects', 'activeMembers', 'requestParams', 'request')); // Passa $request também se a view usar diretamente
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
