<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Box;
use App\Models\Project; // Para filtros
use App\Models\User;    // Para filtros
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class BoxController extends DocumentController
{
    public function index(Request $request): View
    {
        // Lógica similar ao DocumentController para busca, filtro, sort, paginação
        $query = Box::query()->with(['project', 'checker']); // Eager load relacionamentos

        // Exemplo de filtro por número
        if ($request->filled('search')) {
            $query->where('number', 'like', '%' . $request->input('search') . '%')
                ->orWhere('physical_location', 'like', '%' . $request->input('search') . '%');
        }

        // Exemplo filtro por projeto
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->input('project_id'));
        }

        // Exemplo filtro por conferente
        if ($request->filled('checker_id')) {
            $query->where('checker_id', $request->input('checker_id'));
        }

        // Ordenação (ex: por número)
        $sortBy = $request->input('sort_by', 'number');
        $sortDir = $request->input('sort_dir', 'asc');
        if (in_array($sortBy, ['number', 'physical_location', 'conference_date'])) { // Validar colunas
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->orderBy('number', 'asc'); // Default
        }


        $boxes = $query->paginate(15)->withQueryString(); // Paginar

        // Dados para filtros
        $projects = Project::orderBy('name')->pluck('name', 'id');
        $checkers = User::orderBy('name')->pluck('name', 'id'); // Ou filtrar por roles específicas

        return view('boxes.index', compact('boxes', 'projects', 'checkers', 'request'));
    }

    /**
     * Exibe os documentos de uma caixa específica.
     */
    public function show(Document $document): View
    {
        $boxNumber = $document->box_number;
        $documents = Document::where('box_number', $boxNumber)
            ->orderBy('item_number')
            ->paginate(20);

        $boxInfo = [
            'number' => $boxNumber,
            'totalDocuments' => $documents->total(),
            'projects' => Document::where('box_number', $boxNumber)
                ->distinct('project')
                ->pluck('project')
                ->toArray(),
        ];

        return view('boxes.show', [
            'documents' => $documents,
            'boxInfo' => $boxInfo,
        ]);
    }
}
