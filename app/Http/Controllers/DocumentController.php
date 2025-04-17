<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Para distinct e raw queries se necessário
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DocumentController extends Controller
{
    public function show(Document $document)
    {
        if (request()->wantsJson()) {
            return response()->json($document);
        }
        return view('documents.show', compact('document'));
    }

    public function index(Request $request): View
    {
        // Parâmetros de busca, filtro, ordenação e paginação
        $searchTerm = $request->input('search');
        $filterBox = $request->input('filter_box');
        $filterProject = $request->input('filter_project');
        $filterYear = $request->input('filter_year');
        $sortBy = $request->input('sort_by', 'box_number'); // Coluna padrão
        $sortDir = $request->input('sort_dir', 'asc'); // Direção padrão
        $perPage = $request->input('per_page', 10); // Itens por página

        // Valida coluna de ordenação para evitar erros
        $validSortColumns = ['box_number', 'item_number', 'code', 'descriptor', 'document_number', 'title', 'document_date', 'confidentiality', 'version', 'is_copy', 'project'];
        if (!in_array($sortBy, $validSortColumns)) {
            $sortBy = 'box_number';
        }
        // Valida direção
         if (!in_array(strtolower($sortDir), ['asc', 'desc'])) {
            $sortDir = 'asc';
        }

        // Construção da Query
        $query = Document::query();

        // Aplicar busca textual
        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('box_number', 'like', "%{$searchTerm}%")
                  ->orWhere('item_number', 'like', "%{$searchTerm}%")
                  ->orWhere('code', 'like', "%{$searchTerm}%")
                  ->orWhere('descriptor', 'like', "%{$searchTerm}%")
                  ->orWhere('document_number', 'like', "%{$searchTerm}%")
                  ->orWhere('title', 'like', "%{$searchTerm}%")
                  ->orWhere('project', 'like', "%{$searchTerm}%")
                  ->orWhere('confidentiality', 'like', "%{$searchTerm}%");
                // Não buscar em data, sigilo, versão, cópia diretamente com like simples
            });
        }

        // Aplicar filtros específicos
        if ($filterBox) {
            $query->where('box_number', 'like', "%{$filterBox}%");
        }
        if ($filterProject) {
            $query->where('project', $filterProject);
        }
        if ($filterYear) {
            $query->whereYear('document_date', $filterYear);
        }

        // Aplicar ordenação
        $query->orderBy($sortBy, $sortDir);

        // Paginar resultados
        $documents = $query->paginate($perPage)->withQueryString(); // withQueryString mantém os filtros/sort na paginação

        // Obter dados para os filtros (otimizado para buscar apenas o necessário)
        $availableProjects = Document::query()
            ->when($filterBox, fn($q) => $q->where('box_number', 'like', "%{$filterBox}%")) // Filtra projetos baseados em outros filtros ativos? Opcional.
            ->when($filterYear, fn($q) => $q->whereYear('document_date', $filterYear))
            ->select('project')
            ->whereNotNull('project')
            ->distinct()
            ->orderBy('project')
            ->pluck('project');

        $availableYears = Document::query()
            ->when($filterBox, fn($q) => $q->where('box_number', 'like', "%{$filterBox}%")) // Filtra anos baseados em outros filtros ativos? Opcional.
            ->when($filterProject, fn($q) => $q->where('project', $filterProject))
            ->select(DB::raw('YEAR(document_date) as year'))
            ->whereNotNull('document_date')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

         // Dados para as Estatísticas (Exemplo - pode ser mais elaborado)
         $totalDocuments = Document::count(); // Total geral
         $totalBoxes = Document::distinct('box_number')->count('box_number'); // Total geral de caixas
         $totalProjects = Document::whereNotNull('project')->distinct('project')->count('project');

         // Estatísticas filtradas baseadas na contagem do Paginator
         $filteredDocumentsCount = $documents->total();
         // Para caixas/projetos filtrados, seria preciso uma query separada no resultado filtrado *antes* da paginação,
         // ou contar a partir da coleção paginada (menos preciso se houver muitas páginas)
         // Exemplo simples contando na coleção atual:
         $filteredBoxesCount = $documents->pluck('box_number')->unique()->count();
         $filteredProjectsCount = $documents->pluck('project')->filter()->unique()->count(); // filter() remove nulls

         // Calcula o intervalo de anos para os documentos filtrados
         $yearsData = $documents->pluck('document_date')->map(fn($date) => $date?->year)->filter()->unique()->sort();
         $yearRange = $yearsData->isNotEmpty()
             ? ($yearsData->first() === $yearsData->last() ? $yearsData->first() : $yearsData->first() . ' - ' . $yearsData->last())
             : null;


        return view('documents.index', [
            'documents' => $documents,
            'availableProjects' => $availableProjects,
            'availableYears' => $availableYears,
            'requestParams' => $request->all(), // Passa todos os parâmetros da request para preencher filtros/sort
            'stats' => [
                'totalDocuments' => $totalDocuments,
                'totalBoxes' => $totalBoxes,
                'totalProjects' => $totalProjects,
                'filteredDocumentsCount' => $filteredDocumentsCount,
                'filteredBoxesCount' => $filteredBoxesCount, // Contagem baseada na página atual
                'filteredProjectsCount' => $filteredProjectsCount, // Contagem baseada na página atual
                'yearRange' => $yearRange, // Intervalo de anos dos documentos filtrados
            ],
            'hasActiveFilters' => $request->filled(['search', 'filter_box', 'filter_project', 'filter_year']), // Verifica se algum filtro está preenchido
        ]);
    }

     
    public function create()
    {
        return view('documents.create');
    }

    public function store(Request $request)
    {
        // Validation and document creation logic here
        $validated = $request->validate([
            'box_number' => 'required',
            'item_number' => 'required',
            'title' => 'required',
            'document_date' => 'required|date',
            'project' => 'required',
            // Add other validation rules as needed
        ]);
    
        // Create the document
        Document::create($validated);
    
        return redirect()->route('documents.index')
            ->with('success', 'Document created successfully.');
    }

    /**
     * Exibe o formulário para editar um documento existente
     */
    public function edit(Document $document): View
    {
        return view('documents.edit', compact('document'));
    }

    /**
     * Atualiza um documento existente no banco de dados
     */
    public function update(Request $request, Document $document): RedirectResponse
    {
        $validated = $request->validate([
            'box_number' => 'required|string|max:255',
            'item_number' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'descriptor' => 'nullable|string|max:255',
            'document_number' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'document_date' => 'required|date',
            'project' => 'required|string|max:255',
            'confidentially' => 'nullable|string|max:255',
            'version' => 'nullable|string|max:255',
            'is_copy' => 'nullable|boolean',
        ]);

        // Atualiza o documento com os dados validados
        $document->update($validated);

        return redirect()->route('documents.show', $document)
            ->with('success', 'Documento atualizado com sucesso!');
    }

    /**
     * Remove um documento do banco de dados
     */
    public function destroy(Document $document): RedirectResponse
    {
        $document->delete();

        return redirect()->route('documents.index')
            ->with('success', 'Documento excluído com sucesso!');
    }
}