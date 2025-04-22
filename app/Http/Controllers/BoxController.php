<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class BoxController extends DocumentController
{
    public function index(Request $request): View
    {
        // Obtém caixas únicas com contagem de documentos e datas
        $query = Document::select(
            'box_number',
            DB::raw('COUNT(*) as document_count'),
            DB::raw('MIN(YEAR(document_date)) as min_year'),
            DB::raw('MAX(YEAR(document_date)) as max_year')
        )
        ->groupBy('box_number')
        ->orderBy('box_number');

        // Aplicar busca se houver
        $searchTerm = $request->input('search');
        if ($searchTerm) {
            $query->where('box_number', 'like', "%{$searchTerm}%");
        }

        $boxes = $query->paginate(12)->withQueryString();

        // Transformar os resultados para incluir os anos
        $boxes->getCollection()->transform(function ($box) {
            // Se tiver apenas um ano, mostra ele sozinho
            if ($box->min_year == $box->max_year) {
                $box->year_range = $box->min_year;
            } else {
                $box->year_range = "{$box->min_year} - {$box->max_year}";
            }
            
            // Calcula os períodos baseado no ano mais recente
            $box->current_year = $box->max_year + 5;
            $box->intermediate_year = $box->current_year + 10;
            
            return $box;
        });

        // Estatísticas
        $stats = [
            'totalBoxes' => Document::distinct('box_number')->count(),
            'totalDocuments' => Document::count(),
            'totalProjects' => Document::distinct('project')->count(),
        ];

        return view('boxes.index', [
            'boxes' => $boxes,
            'stats' => $stats,
            'hasActiveFilters' => !empty($searchTerm),
        ]);
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