<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $query = Project::query();

        // Aplicar busca se houver termo
        if ($search = $request->input('search')) {
            $searchWild = '%' . $search . '%';
            $query->where(function ($q) use ($searchWild) {
                $q->where('name', 'like', $searchWild)
                    ->orWhere('code', 'like', $searchWild)
                    ->orWhere('description', 'like', $searchWild);
            });
        }

        // Ordenação
        $sortBy = $request->input('sort_by', 'name');
        $sortDir = $request->input('sort_dir', 'asc');
        $query->orderBy($sortBy, $sortDir);

        $projects = $query->paginate(10)->withQueryString();

        return view('projects.index', compact('projects'));
    }

    public function create(): View
    {
        return view('projects.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:projects',
            'description' => 'nullable|string',
            //'status' => 'required|string|in:ativo,concluído,suspenso',
            //'start_date' => 'nullable|date',
            //'end_date' => 'nullable|date|after_or_equal:start_date'
        ]);

        Project::create($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Projeto criado com sucesso.');
    }

    public function show(Project $project): View
    {
        $project->load(['documents', 'boxes']);
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project): View
    {
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:projects,code,' . $project->id,
            'description' => 'nullable|string',
            //'status' => 'required|string|in:ativo,concluído,suspenso',
            //'start_date' => 'nullable|date',
            //'end_date' => 'nullable|date|after_or_equal:start_date'
        ]);

        $project->update($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Projeto atualizado com sucesso.');
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
