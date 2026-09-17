<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Support\SortHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $query = Project::query();

        // Aplicar busca se houver termo
        if ($search = $request->input('search')) {
            $searchWild = '%'.$search.'%';
            $query->where(function ($q) use ($searchWild) {
                $q->where('name', 'like', $searchWild)
                    ->orWhere('code', 'like', $searchWild)
                    ->orWhere('description', 'like', $searchWild);
            });
        }

        // Ordenação (allowlist: coluna/direção vão crus para o SQL)
        [$sortBy, $sortDir] = SortHelper::sanitize(
            $request->input('sort_by'),
            $request->input('sort_dir'),
            ['id', 'name', 'code', 'description'],
            'name',
            'asc',
        );
        $query->orderBy($sortBy, $sortDir);

        $projects = $query->paginate(10)->withQueryString();

        return view('projects.index', compact('projects'));
    }

    public function create(): View
    {
        return view('projects.create');
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        Project::create($request->validated());

        return redirect()->route('projects.index')
            ->with('success', 'Projeto criado com sucesso!');
    }

    public function show(Project $project): View
    {
        $project->load(['boxes' => function ($query) {
            $query->orderBy('number');
        }]);

        return view('projects.show', compact('project'));
    }

    public function edit(Project $project): View
    {
        return view('projects.edit', compact('project'));
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $project->update($request->validated());

        return redirect()->route('projects.index')
            ->with('success', 'Projeto atualizado com sucesso!');
    }

    public function destroy(Project $project): RedirectResponse
    {
        // Verificar se há documentos ou caixas associados
        if ($project->documents()->exists() || $project->boxes()->exists()) {
            return redirect()->route('projects.index')
                ->with('error', 'Não é possível excluir o projeto pois existem documentos ou caixas associados.');
        }

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Projeto excluído com sucesso.');
    }
}
