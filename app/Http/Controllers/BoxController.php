<?php

namespace App\Http\Controllers;

// Models
use App\Http\Requests\DestroyBatchBoxRequest;
use App\Http\Requests\StoreBoxRequest;
use App\Http\Requests\UpdateBoxRequest;
use App\Models\Box;
use App\Models\CommissionMember; // Importar para o show
// Requests
use App\Models\Document;
use App\Models\Project;
// Outros
use App\Services\BoxService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class BoxController extends Controller
{
    public function __construct(
        protected BoxService $boxService
    ) {}

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
        $validated = $request->validated();
        $boxQuantity = (int) ($validated['box_quantity'] ?? 1);

        try {
            $createdBoxes = $this->boxService->createBatch($validated, $boxQuantity);
            $message = $createdBoxes > 1
                ? "{$createdBoxes} caixas foram criadas com sucesso."
                : 'Caixa criada com sucesso.';

            return redirect()->route('boxes.index')->with('success', $message);
        } catch (\Throwable $e) {
            Log::error('Erro ao criar caixas em lote: '.$e->getMessage(), [
                'exception' => $e,
                'quantidade' => $boxQuantity,
                'base_number' => $validated['number'] ?? null,
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
     */
    public function batchDestroy(DestroyBatchBoxRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $result = $this->boxService->destroyBatch($validated['selected_boxes'], auth()->user());

            return redirect()->route('boxes.index')
                ->with($result['status'], $result['message']);
        } catch (\Throwable $e) {
            Log::error('Erro crítico ao processar exclusão em lote de caixas: '.$e->getMessage(), [
                'exception' => $e,
                'box_ids' => $validated['selected_boxes'] ?? [],
            ]);

            return redirect()->route('boxes.index')
                ->with('error', 'Ocorreu um erro crítico ao tentar processar a exclusão em lote. Verifique os logs do servidor.');
        }
    }

    /**
     * Remove múltiplos documentos de uma caixa específica.
     */
    public function batchDestroyDocuments(Request $request, Box $box): RedirectResponse
    {
        $request->validate([
            'document_ids' => ['required', 'array'],
            'document_ids.*' => ['required', 'integer', 'exists:documents,id'],
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

            // Autorização por item via DocumentPolicy
            foreach ($documents as $document) {
                $this->authorize('delete', $document);
            }

            $deletedCount = $this->boxService->destroyDocumentsFromBox($box, $documentIds);

            if ($deletedCount > 0) {
                return redirect()->route('boxes.show', $box)
                    ->with('success', $deletedCount.' documento(s) excluído(s) com sucesso.');
            }

            return redirect()->route('boxes.show', $box)
                ->with('warning', 'Nenhum documento correspondente foi encontrado para exclusão.');
        } catch (\Throwable $e) {
            Log::error('Erro ao excluir documentos em massa: '.$e->getMessage());

            return redirect()->route('boxes.show', $box)
                ->with('error', 'Ocorreu um erro ao tentar excluir os documentos.');
        }
    }
} // Fim da classe BoxController
