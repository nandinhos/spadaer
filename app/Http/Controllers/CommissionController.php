<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\CommissionMember;
use App\Models\User;
use Illuminate\Http\Request;
// use App\Http\Requests\UpdateCommissionRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // <--- ADICIONE ESTA LINHA
use Illuminate\View\View;

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
    public function store(Request $request) // Simplificando para teste
    {
        // dd($request->all()); // Mantenha comentado por enquanto

        try {
            $validated = $request->validate([
                // ... suas regras de validação ...
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'ordinance_number' => 'required|string|max:100', // Usando o nome do request
                'ordinance_date' => 'required|date',
                'ordinance_file' => 'nullable|file|mimes:pdf|max:2048',
                'members' => 'required|array',
                'members.*' => 'required|exists:users,id',
            ]);
            \Log::info('Validation passed.'); // Log para confirmar
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed: '.$e->getMessage(), $e->errors());

            return back()->withErrors($e->errors())->withInput(); // Retorna com erros
        } catch (\Throwable $e) {
            \Log::error('Error during validation step: '.$e->getMessage());
            throw $e; // Relança outros erros
        }

        $path = null;
        try {
            if ($request->hasFile('ordinance_file') && $request->file('ordinance_file')->isValid()) {
                $path = $request->file('ordinance_file')->store('ordinances', 'public');
                \Log::info('File uploaded: '.$path);
            }
        } catch (\Throwable $e) {
            \Log::error('Error during file upload: '.$e->getMessage());
            // Decida se quer continuar sem o arquivo ou retornar erro
            // return back()->with('error', 'Falha no upload do arquivo.')->withInput();
        }

        $commission = null;
        try {
            $commission = Commission::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'ordinance' => $validated['ordinance_number'], // Ajustado para nome do request/validação
                'ordinance_date' => $validated['ordinance_date'],
                'ordinance_file' => $path,
            ]);
            \Log::info('Commission created with ID: '.$commission->id);
        } catch (\Throwable $e) {
            // Log detalhado do erro SQL
            \Log::error('Error creating commission: '.$e->getMessage().' SQL: '.($e instanceof \Illuminate\Database\QueryException ? $e->getSql() : 'N/A'), $validated);

            return back()->with('error', 'Erro ao criar a comissão. Verifique os logs.')->withInput();
        }

        try {
            foreach ($validated['members'] as $userId) {
                \Log::info("Attempting to create CommissionMember for User ID: {$userId} and Commission ID: {$commission->id}");
                CommissionMember::create([
                    'commission_id' => $commission->id, // <<<<<< VERIFIQUE SE ESTA COLUNA EXISTE EM commission_members
                    'user_id' => $userId,
                    'role' => 'member',
                    'is_active' => true,
                ]);
                \Log::info("CommissionMember created for User ID: {$userId}");
            }
        } catch (\Throwable $e) {
            // Log detalhado do erro SQL
            \Log::error('Error creating commission member: '.$e->getMessage().' SQL: '.($e instanceof \Illuminate\Database\QueryException ? $e->getSql() : 'N/A'), ['userId' => $userId ?? null, 'commission_id' => $commission->id]);

            // Você pode querer deletar a comissão criada se a adição de membros falhar (transação seria ideal)
            // $commission->delete();
            return back()->with('error', 'Erro ao adicionar membros à comissão. Verifique os logs.')->withInput();
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
    public function update(/* UpdateCommissionRequest */ Request $request, Commission $commission)
    {
        // Validação (Exemplo, use Form Request)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ordinance_number' => 'required|string|max:100',
            'ordinance_date' => 'required|date',
            'ordinance_file' => 'nullable|file|mimes:pdf|max:2048',
            'members' => 'required|array',
            'members.*' => 'required|exists:users,id',
        ]);

        $path = $commission->ordinance_file; // Manter o arquivo antigo por padrão
        // Lógica para deletar arquivo antigo se um novo for enviado
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

        // Atualizar os dados principais da Comissão
        try {
            $commission->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'ordinance' => $validated['ordinance_number'], // Assumindo que ordinance é o campo no DB
                'ordinance_date' => $validated['ordinance_date'],
                'ordinance_file' => $path, // Atualiza com o novo path ou mantém o antigo
            ]);
            Log::info("Comissão ID {$commission->id} atualizada.");
        } catch (\Throwable $e) {
            Log::error('Erro ao atualizar comissão: '.$e->getMessage());

            return back()->with('error', 'Erro ao atualizar dados da comissão.')->withInput();
        }

        // --- Gerenciar Membros (Substituir sync()) ---

        try {
            // 1. Obter os IDs dos usuários que DEVEM ser membros (da requisição)
            $newUserIds = collect($validated['members'])->map(fn ($id) => (int) $id)->unique()->toArray(); // Garante que são inteiros e únicos

            // 2. Obter os IDs dos usuários que SÃO membros ATUALMENTE desta comissão
            $currentUserIds = $commission->members()->pluck('user_id')->toArray();

            // !!!!! ADICIONE LOGS AQUI !!!!!
            Log::debug('Updating members for Commission ID: '.$commission->id);
            Log::debug('New User IDs from request: '.json_encode($newUserIds));
            Log::debug('Current User IDs from DB: '.json_encode($currentUserIds));

            // 3. Identificar usuários a serem REMOVIDOS
            $userIdsToRemove = array_diff($currentUserIds, $newUserIds);
            if (! empty($userIdsToRemove)) {
                // Deleta os registros CommissionMember correspondentes
                $commission->members()->whereIn('user_id', $userIdsToRemove)->delete();
                Log::info("Membros removidos da Comissão ID {$commission->id}: ".implode(', ', $userIdsToRemove));
            }

            // 4. Identificar usuários a serem ADICIONADOS
            $userIdsToAdd = array_diff($newUserIds, $currentUserIds);
            Log::debug('User IDs to Add: '.json_encode($userIdsToAdd));
            foreach ($userIdsToAdd as $userId) {
                // Cria o novo registro CommissionMember
                CommissionMember::create([
                    'commission_id' => $commission->id,
                    'user_id' => $userId,
                    'role' => 'member', // Ou obter do request se houver diferentes roles
                    'is_active' => true,
                    // 'start_date' => now(), // Se aplicável
                ]);
                Log::info("Membro adicionado à Comissão ID {$commission->id}: User ID {$userId}");
            }

            // 5. (Opcional) Atualizar membros existentes?
            // Se você precisasse atualizar o 'role' ou 'is_active' dos membros que permaneceram,
            // você identificaria os $userIdsToKeep = array_intersect($currentUserIds, $newUserIds);
            // e faria um update nos CommissionMembers correspondentes.
            // Por enquanto, vamos manter simples (apenas adiciona/remove).

        } catch (\Throwable $e) {
            Log::error('Erro ao atualizar membros da comissão: '.$e->getMessage());

            // Idealmente, aqui você faria um rollback da atualização da comissão se possível (usando transações)
            return back()->with('error', 'Erro ao atualizar membros da comissão.')->withInput();
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
