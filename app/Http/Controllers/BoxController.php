<?php

namespace App\Http\Controllers;

// Models
use App\Http\Requests\StoreBoxRequest;
use App\Http\Requests\UpdateBoxRequest;
use App\Models\Box;
use App\Models\CommissionMember; // Importar para o show
// Requests
use App\Models\Document;
use App\Models\Project;
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
    public function index(): View
    {
        // A view renderiza <livewire:box-list />, que faz sua própria busca/filtro/paginação
        // (a query que existia aqui era morta: executada e descartada a cada request).
        return view('boxes.index');
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
        $boxQuantity = $request->input('box_quantity', 1);

        // Validar a quantidade de caixas
        if ($boxQuantity < 1 || $boxQuantity > 200) {
            return back()->with('error', 'A quantidade de caixas deve estar entre 001 e 200.')->withInput();
        }

        try {
            DB::beginTransaction();
            $createdBoxes = 0;
            $baseNumber = $validatedBoxData['number'];

            // Criar caixas em lote
            for ($i = 0; $i < $boxQuantity; $i++) {
                // Gerar número sequencial para caixas adicionais
                if ($i > 0) {
                    $validatedBoxData['number'] = $this->generateSequentialNumber($baseNumber, $i);
                }

                Box::create($validatedBoxData);
                $createdBoxes++;
            }

            DB::commit();
            $message = $createdBoxes > 1
                ? "$createdBoxes caixas foram criadas com sucesso."
                : 'Caixa criada com sucesso.';

            return redirect()->route('boxes.index')->with('success', $message);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erro ao criar caixas em lote: '.$e->getMessage(), [
                'exception' => $e,
                'quantidade' => $boxQuantity,
                'base_number' => $baseNumber ?? null,
            ]);

            return back()->with('error', 'Erro ao salvar as caixas. Verifique os logs.')->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Box $box) // Sem tipo de retorno
    {
        $box->load(['project', 'commissionMember.user', 'documents' => function ($query) {
            // Usa orderByRaw para converter a coluna de texto para um número antes de ordenar.
            // Isso garante que '2' venha antes de '10'.
            $query->orderByRaw('CAST(item_number AS UNSIGNED) asc');
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
            Log::error("Erro ao atualizar caixa {$box->id}: ".$e->getMessage(), ['exception' => $e]);

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
            Log::error("Erro ao excluir caixa {$box->id}: ".$e->getMessage());
            // Verificar se o erro é devido a FKs restritivas (se não usou cascade/set null)
            if ($e instanceof \Illuminate\Database\QueryException && str_contains($e->getMessage(), 'constraint violation')) {
                return redirect()->route('boxes.index')->with('error', 'Não é possível excluir a caixa pois ela contém documentos.');
            }

            return redirect()->route('boxes.index')->with('error', 'Erro ao excluir a caixa.');
        }
    }

    /**
     * Remove múltiplas caixas selecionadas, desassociando documentos se existirem.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function batchDestroy(Request $request)
    {
        // 1. Validar os IDs recebidos
        // O nome do input no frontend é 'selected_boxes[]', então validamos 'selected_boxes'
        $validated = $request->validate([
            'selected_boxes' => 'required|array',
            'selected_boxes.*' => 'required|integer|exists:boxes,id',
        ]);

        $boxIds = $validated['selected_boxes']; // Usar o nome validado

        $deletedCount = 0; // Contador para caixas excluídas
        $orphanedCount = 0; // Contador para caixas com documentos desassociados
        $skippedCount = 0; // Contador para caixas não encontradas ou sem IDs (improvável com exists:boxes,id)

        try {
            // Iniciar Transação de Banco de Dados
            // Garante que se algo der errado durante o processo, tudo pode ser desfeito.
            DB::beginTransaction();

            // 2. Processar cada caixa selecionada
            foreach ($boxIds as $boxId) {
                $box = Box::find($boxId); // Usar find para obter o modelo ou null

                // Verificar se a caixa existe (redundante com exists:boxes,id na validação, mas seguro)
                if (! $box) {
                    $skippedCount++; // Conta como pulada se não encontrada

                    continue; // Pula para a próxima iteração se a caixa não for encontrada
                }

                // Verificar se a caixa contém documentos
                if ($box->documents()->count() > 0) {
                    // Se houver documentos, desassociar (setar box_id para NULL)
                    // Certifique-se que a coluna box_id na tabela documents aceita NULL
                    // O update em massa NÃO dispara eventos updated: registra trilha manual.
                    $disassociatedIds = $box->documents()->pluck('documents.id')->all();
                    $box->documents()->update(['box_id' => null]);
                    $box->auditManual(
                        'documents_disassociated',
                        ['box_id' => $box->id],
                        ['document_ids' => $disassociatedIds, 'count' => count($disassociatedIds)]
                    );
                    $orphanedCount++; // Incrementa contador de órfãos
                    // A caixa NÃO é excluída neste caso, conforme regra de negócio
                } else {
                    // Se a caixa estiver vazia, excluí-la
                    $box->delete();
                    $deletedCount++; // Incrementa contador de excluídas
                }
            }

            // Commit a transação se tudo ocorreu bem
            DB::commit();

            // 3. Preparar mensagem de feedback
            $message = [];
            if ($deletedCount > 0) {
                $message[] = "{$deletedCount} caixa(s) vazia(s) excluída(s) com sucesso.";
            }
            if ($orphanedCount > 0) {
                // Mensagem mais clara sobre o que aconteceu com as caixas com documentos
                $message[] = "Documentos de {$orphanedCount} caixa(s) foram desassociados. As caixas com documentos não foram excluídas.";
            }
            if ($skippedCount > 0) {
                $message[] = "{$skippedCount} caixa(s) selecionada(s) não foram encontradas.";
            }
            // Caso nenhum processamento elegível tenha ocorrido
            if ($deletedCount === 0 && $orphanedCount === 0 && $skippedCount === 0 && count($boxIds) > 0) {
                $message[] = 'Nenhuma caixa selecionada pôde ser processada (verifique se todas continham documentos e foram desassociadas).';
            } elseif (count($boxIds) === 0) {
                // Caso a requisição tenha vindo sem IDs (impedido pela validação 'required|array', mas bom ter)
                $message[] = 'Nenhuma caixa foi selecionada para processar.';
            }

            // 4. Redirecionar com mensagem
            // Usar 'success' ou 'warning' se alguma operação bem-sucedida (exclusão OU desassociação) ocorreu
            // Usar 'error' apenas se a transação inteira falhou (caiu no catch)
            $status = 'success'; // Padrão otimista
            if ($deletedCount === 0 && $orphanedCount === 0) {
                $status = 'warning'; // Nada foi excluído nem desassociado
                if ($skippedCount > 0) {
                    $status = 'warning';
                } // Pelo menos pulou algo
                if (count($boxIds) > 0 && $skippedCount == count($boxIds)) {
                    $status = 'warning';
                } // Todos pulados/não encontrados
                if (count($boxIds) === 0) {
                    $status = 'info';
                } // Nenhuma caixa selecionada
            }

            app(\App\Services\NotificationService::class)->send(
                auth()->user(),
                'Caixas processadas em lote',
                implode(' ', $message),
                'fa-boxes-stacked'
            );

            return redirect()->route('boxes.index')
                ->with($status, implode(' ', $message)); // Concatena as mensagens

        } catch (\Throwable $e) {
            // Em caso de erro genérico na transação, reverter tudo
            DB::rollBack();
            Log::error('Erro crítico ao processar exclusão em lote de caixas: '.$e->getMessage(), [
                'exception' => $e,
                'box_ids' => $boxIds ?? 'Não disponíveis',
            ]);

            // Redirecionar com mensagem de erro
            return redirect()->route('boxes.index')
                ->with('error', 'Ocorreu um erro crítico ao tentar processar a exclusão em lote. Verifique os logs do servidor.');
        }
    }

    /**
     * Remove múltiplos documentos de uma caixa específica.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function batchDestroyDocuments(Request $request, Box $box)
    {
        $request->validate([
            'document_ids' => 'required|array',
            'document_ids.*' => 'required|integer|exists:documents,id', // Valida se os IDs existem
        ]);

        $documentIds = $request->input('document_ids');

        try {
            $documents = Document::where('box_id', $box->id)
                ->whereIn('id', $documentIds)
                ->get();

            // Verificação por item (fail-closed): todos os IDs devem pertencer a esta caixa.
            if ($documents->count() !== count($documentIds)) {
                return redirect()->route('boxes.show', $box)
                    ->with('error', 'Um ou mais documentos selecionados não pertencem a esta caixa.');
            }

            // Autorização por item via DocumentPolicy registrada em AuthServiceProvider.
            // O middleware da rota (permission:documents.delete) é a primeira barreira; isto é a segunda.
            foreach ($documents as $document) {
                $this->authorize('delete', $document);
            }

            // Exclui por modelo (não em massa) para disparar os eventos deleted
            // e gerar um audit_log por documento.
            $deletedCount = 0;
            foreach ($documents as $document) {
                $document->delete();
                $deletedCount++;
            }

            if ($deletedCount > 0) {
                return redirect()->route('boxes.show', $box)
                    ->with('success', $deletedCount.' documento(s) excluído(s) com sucesso.');
            } else {
                return redirect()->route('boxes.show', $box)
                    ->with('warning', 'Nenhum documento correspondente foi encontrado para exclusão.');
            }
        } catch (\Exception $e) {
            Log::error('Erro ao excluir documentos em massa: '.$e->getMessage());
            return redirect()->route('boxes.show', $box)
                ->with('error', 'Ocorreu um erro ao tentar excluir os documentos.');
        }
    }

    // Método batchAssignChecker (se implementado) permanece aqui
    // public function batchAssignChecker(Request $request): RedirectResponse { ... }

    /**
     * Gera um número sequencial para caixas baseado no número base.
     *
     * @param  string  $baseNumber  Número base da caixa (ex: 'CX001' ou 'AD-2024-01')
     * @param  int  $sequence  Número da sequência (começando em 1)
     * @return string Novo número sequencial
     */
    private function generateSequentialNumber(string $baseNumber, int $sequence): string
    {
        // Encontrar números no final da string
        if (preg_match('/(\d+)$/', $baseNumber, $matches)) {
            $baseDigits = $matches[1];
            $prefix = substr($baseNumber, 0, -strlen($baseDigits));
            $newNumber = str_pad((int) $baseDigits + $sequence, strlen($baseDigits), '0', STR_PAD_LEFT);

            return $prefix.$newNumber;
        }

        // Se não encontrar números no final, apenas adiciona o número da sequência
        return $baseNumber.'-'.($sequence + 1);
    }
} // Fim da classe BoxController
