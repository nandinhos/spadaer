<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommissionRequest;
use App\Http\Requests\UpdateCommissionRequest;
use App\Models\Commission;
use App\Models\CommissionMember;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // <--- ADICIONE ESTA LINHA
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class CommissionController extends Controller
{
    /**
     * Exibe a lista de comissões.
     */
    public function index()
    {
        
        //dd(Auth::user()->roles);
        
        $commissions = Commission::orderBy('id')->paginate(10);

        return view('commissions.index', compact('commissions'));

        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Buscar todos os usuários ou filtrar por elegíveis
        // Adiciona orderBy('id', 'asc') aqui
        $users = User::orderBy('id', 'asc')->get(); // Ordena pelo ID do usuário

        // Alternativa: Ordenar por outro campo, se 'id' não for a antiguidade
        // $users = User::orderBy('order_number', 'asc')->get(); // Ex: se order_number define a ordem
        // $users = User::orderBy('rank_sort_order')->orderBy('name')->get(); // Ex: se tiver uma coluna para ordenar ranks

        // Retornar a view passando a variável 'users' já ordenada
        return view('commissions.create', compact('users'));
    }

    /**
     * Armazena uma nova comissão.
     */
    public function store(StoreCommissionRequest $request) // Type hint com o Form Request
    {
        // A validação já ocorreu! $request->validated() contém os dados validados.
        $validated = $request->validated();

        // -- Lógica para upload do arquivo (pode ficar aqui ou mover para um Service) --
        $path = null;
        if ($request->hasFile('ordinance_file') && $request->file('ordinance_file')->isValid()) {
            $path = $request->file('ordinance_file')->store('ordinances', 'public');
        }

        // -- Lógica de criação (mais limpa) --
        $commission = Commission::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'ordinance' => $validated['ordinance_number'], // Use o nome validado
            'ordinance_date' => $validated['ordinance_date'],
            'ordinance_file' => $path,
        ]);

        // Adicionar membros
        foreach ($validated['members'] as $userId) {
            CommissionMember::create([
                'commission_id' => $commission->id,
                'user_id' => $userId,
                'role' => 'member', // Ajuste se necessário
                'is_active' => true,
            ]);
        }

        return redirect()->route('commissions.index')
            ->with('success', 'Comissão criada com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Commission $commission): View
    {
        // Carrega a comissão e personaliza a query para carregar membros
        $commission->load(['members' => function ($query) {
            // Ordena os CommissionMember pela coluna user_id ascendente
            $query->orderBy('user_id', 'asc')
                  // Carrega o relacionamento 'user' para cada membro
                ->with('user');
        }]);

        // -- Explicação --
        // 1. 'members => function ($query) {...}' : Permite customizar como os membros são carregados.
        // 2. $query->orderBy('user_id', 'asc') : Aplica a ordenação desejada na query que busca
        //                                         os registros da tabela `commission_members`.
        // 3. ->with('user') : Dentro da closure, garantimos que para cada CommissionMember carregado
        //                      (já ordenado por user_id), os dados do User associado também sejam
        //                      carregados eficientemente (evita N+1 queries na view).

        return view('commissions.show', compact('commission'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Commission $commission): View
    {
        // Buscar todos os usuários ordenados por ID (ou outro critério)
        $users = User::orderBy('id', 'asc')->get();

        // Carregar os membros atuais da comissão para preencher o select (já deve estar fazendo isso)
        // Não precisa carregar aqui se a view já faz $commission->members->pluck('user_id')
        $commission->load('members'); // Opcional, dependendo da view

        // Passar a comissão e a lista de usuários ordenada para a view
        return view('commissions.edit', compact('commission', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommissionRequest $request, Commission $commission): RedirectResponse // <-- Use o Form Request e defina tipo de retorno
    {
        // A validação e autorização básica (do authorize()) já ocorreram!
        // $this->authorize('update', $commission); // <-- Chame a Policy aqui se não usar authorizeResource() no construtor

        $validated = $request->validated(); // Pega apenas os dados validados

        // --- Lógica de Upload e Deleção de Arquivo ---
        $path = $commission->ordinance_file; // Manter o arquivo antigo por padrão
        if ($request->hasFile('ordinance_file') && $request->file('ordinance_file')->isValid()) {
            // Deletar o arquivo antigo se existir
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                Log::info('Arquivo antigo da portaria deletado: '.$path);
            }
            // Salvar o novo arquivo
            $path = $request->file('ordinance_file')->store('ordinances', 'public');
            Log::info('Novo arquivo da portaria salvo: '.$path);
        }

        // --- Atualizar Comissão ---
        try {
            $commission->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'ordinance' => $validated['ordinance_number'], // Use o nome validado/consistente
                'ordinance_date' => $validated['ordinance_date'],
                'ordinance_file' => $path,
            ]);
            Log::info("Comissão ID {$commission->id} atualizada.");
        } catch (\Throwable $e) {
            Log::error('Erro ao atualizar comissão: '.$e->getMessage());

            return back()->with('error', 'Erro ao atualizar dados da comissão. Verifique os logs.')->withInput();
        }

        // --- Gerenciar Membros ---
        try {
            $newUserIds = collect($validated['members'])->map(fn ($id) => (int) $id)->unique()->toArray();
            $currentUserIds = $commission->members()->pluck('user_id')->toArray(); // Pega user_id dos membros atuais

            // Remover membros antigos
            $userIdsToRemove = array_diff($currentUserIds, $newUserIds);
            if (! empty($userIdsToRemove)) {
                $commission->members()->whereIn('user_id', $userIdsToRemove)->delete();
                Log::info("Membros removidos da Comissão ID {$commission->id}: ".implode(', ', $userIdsToRemove));
            }

            // Adicionar novos membros
            $userIdsToAdd = array_diff($newUserIds, $currentUserIds);
            foreach ($userIdsToAdd as $userId) {
                CommissionMember::create([
                    'commission_id' => $commission->id,
                    'user_id' => $userId,
                    'role' => 'member', // Ajuste se necessário
                    'is_active' => true,
                ]);
                Log::info("Membro adicionado à Comissão ID {$commission->id}: User ID {$userId}");
            }
        } catch (\Throwable $e) {
            Log::error('Erro ao atualizar membros da comissão: '.$e->getMessage());

            return back()->with('error', 'Erro ao atualizar membros da comissão. Verifique os logs.')->withInput();
        }

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
