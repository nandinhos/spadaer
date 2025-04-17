<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CommissionController extends Controller
{
    /**
     * Exibe a lista de comissões.
     */
    public function index()
    {
        $commissions = Commission::orderBy('id')->paginate(10);
        return view('commissions.index', compact('commissions'));
    }

    /**
     * Mostra o formulário para criar uma nova comissão.
     */
    public function create()
    {
        $users = \App\Models\User::orderBy('rank')->orderBy('full_name')->get();
        return view('commissions.create', compact('users'));
    }

    /**
     * Armazena uma nova comissão.
     */
    public function store(Request $request)
    {
        // Validação dos dados
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'members' => 'required|array',
            'members.*' => 'exists:users,id',
            'ordinance_number' => 'required|string|max:255',
            'ordinance_date' => 'required|date',
            'ordinance_file' => 'required|file|mimes:pdf|max:10240'
        ]);

        // Upload do arquivo da portaria
        $ordinanceFile = $request->file('ordinance_file');
        $path = $ordinanceFile->store('ordinances', 'public');

        // Criar a comissão
        $commission = Commission::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'ordinance_number' => $validated['ordinance_number'],
            'ordinance_date' => $validated['ordinance_date'],
            'ordinance_file' => $path
        ]);

        // Adicionar membros à comissão
        foreach ($validated['members'] as $memberId) {
            $commission->members()->attach($memberId, ['role' => 'member']);
        }

        return redirect()->route('commissions.index')
            ->with('success', 'Comissão criada com sucesso.');
    }

    /**
     * Exibe uma comissão específica.
     */
    public function show($id)
    {
        $commission = Commission::findOrFail($id);
        return view('commissions.show', compact('commission'));
    }

    /**
     * Mostra o formulário para editar uma comissão.
     */
    public function edit($id)
    {
        $commission = Commission::findOrFail($id);
        $users = \App\Models\User::orderBy('rank')->orderBy('full_name')->get(); // Adiciona a busca por usuários
        return view('commissions.edit', compact('commission', 'users')); // Passa a variável $users para a view
    }

    /**
     * Atualiza uma comissão específica.
     */
    public function update(Request $request, Commission $commission)
    {
        // Validação dos dados
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'members' => 'required|array',
            'members.*' => 'exists:users,id',
            'ordinance_number' => 'required|string|max:255',
            'ordinance_date' => 'required|date',
            'ordinance_file' => 'nullable|file|mimes:pdf|max:10240'
        ]);

        // Atualizar arquivo da portaria se fornecido
        if ($request->hasFile('ordinance_file')) {
            // Remover arquivo antigo
            if ($commission->ordinance_file) {
                Storage::disk('public')->delete($commission->ordinance_file);
            }
            
            // Upload do novo arquivo
            $ordinanceFile = $request->file('ordinance_file');
            $path = $ordinanceFile->store('ordinances', 'public');
            $commission->ordinance_file = $path;
        }

        // Atualizar a comissão
        $commission->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'ordinance_number' => $validated['ordinance_number'],
            'ordinance_date' => $validated['ordinance_date']
        ]);

        // Atualizar membros
        $commission->members()->sync($validated['members']);

        return redirect()->route('commissions.index')
            ->with('success', 'Comissão atualizada com sucesso.');
    }

    /**
     * Remove uma comissão específica.
     */
    public function destroy(Commission $commission)
    {
        // Remover arquivo da portaria
        if ($commission->ordinance_file) {
            Storage::disk('public')->delete($commission->ordinance_file);
        }

        // Deletar a comissão
        $commission->delete();

        return redirect()->route('commissions.index')
            ->with('success', 'Comissão removida com sucesso.');
    }
}