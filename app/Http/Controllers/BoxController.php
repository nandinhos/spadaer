<?php

namespace App\Http\Controllers;

// Models
use App\Http\Requests\StoreBoxRequest;
use App\Http\Requests\UpdateBoxRequest;
use App\Models\Box;
// Requests
use App\Models\CommissionMember; // <-- Use Form Request
use App\Models\Project; // <-- Use Form Request
use Illuminate\Http\RedirectResponse; // Use Request padrão apenas onde Form Request não se aplica
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
        $search = $request->input('search');
        $projectId = $request->input('project_id');
        $checkerMemberId = $request->input('checker_member_id');
        $sortBy = $request->input('sort_by', 'boxes.number');
        $sortDir = $request->input('sort_dir', 'asc');
        $perPage = $request->input('per_page', 15);

        // Colunas válidas para ordenação
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

        // Query Base com Joins e Selects
        $query = Box::query()
            ->select([
                'boxes.*',
                'projects.name as project_name',
                'checker_users.name as checker_name',
            ])
            ->leftJoin('projects', 'boxes.project_id', '=', 'projects.id')
            ->leftJoin('commission_members', 'boxes.checker_member_id', '=', 'commission_members.id')
            ->leftJoin('users as checker_users', 'commission_members.user_id', '=', 'checker_users.id');

        // Aplicar busca textual
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('boxes.number', 'like', "%{$search}%")
                    ->orWhere('boxes.physical_location', 'like', "%{$search}%")
                    ->orWhere('projects.name', 'like', "%{$search}%")
                    ->orWhere('checker_users.name', 'like', "%{$search}%");
            });
        }

        // Aplicar filtros específicos
        if ($projectId) {
            $query->where('boxes.project_id', $projectId);
        }
        if ($checkerMemberId) {
            $query->where('boxes.checker_member_id', $checkerMemberId);
        }

        // Validar e Aplicar Ordenação
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

        // Paginar resultados com groupBy
        $boxes = $query->groupBy('boxes.id')
            ->paginate($perPage)
            ->withQueryString();

        // --- Dados para Filtros ---
        $projects = Project::orderBy('name')->pluck('name', 'id');
        $activeMembers = CommissionMember::active()
            ->with('user:id,name')
            ->join('users', 'commission_members.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->get()
            ->mapWithKeys(fn ($member) => [$member->id => $member->user->name ?? 'Inválido']);

        return view('boxes.index', compact('boxes', 'projects', 'activeMembers', 'request'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $projects = Project::orderBy('name')->pluck('name', 'id');
        $activeMembers = CommissionMember::active()
            ->with('user:id,name')
            ->join('users', 'commission_members.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->get()
            ->mapWithKeys(fn ($member) => [$member->id => $member->user->name ?? 'Inválido']);

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
     * Removido o tipo de retorno para evitar conflito.
     */
    public function show(Box $box) // Sem tipo de retorno
    {
        // Carrega os relacionamentos necessários para a view show
        $box->load(['project', 'checkerMember.user', 'documents' => function ($query) {
            $query->orderBy('item_number', 'asc'); // Ordena documentos dentro da caixa
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
            ->with('user:id,name')
            ->join('users', 'commission_members.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->get()
            ->mapWithKeys(fn ($member) => [$member->id => $member->user->name ?? 'Inválido']);

        return view('boxes.edit', compact('box', 'projects', 'activeMembers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBoxRequest $request, Box $box): RedirectResponse
    {
        $validated = $request->validated();
        $box->update($validated);

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
