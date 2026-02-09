<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Database\Eloquent\Builder;

class DocumentService
{
    /**
     * Lista documentos com filtros e ordenação técnica.
     */
    public function listDocuments(array $params): Builder
    {
        $searchTerm = $params['search'] ?? null;
        $filterProjectId = $params['filter_project_id'] ?? null;
        $filterBoxNumber = $params['filter_box_number'] ?? null;
        $filterYear = $params['filter_year'] ?? null;
        $sortBy = $params['sort_by'] ?? 'documents.id';
        $sortDir = $params['sort_dir'] ?? 'desc';

        $query = Document::query()
            ->select([
                'documents.*',
                'boxes.number as box_number',
                'projects.name as project_name',
            ])
            ->leftJoin('boxes', 'documents.box_id', '=', 'boxes.id')
            ->leftJoin('projects', 'documents.project_id', '=', 'projects.id');

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $wildcard = "%{$searchTerm}%";
                $q->where('documents.title', 'like', $wildcard)
                    ->orWhere('documents.document_number', 'like', $wildcard)
                    ->orWhere('documents.code', 'like', $wildcard);
            });
        }

        if ($filterProjectId) {
            $query->where('documents.project_id', $filterProjectId);
        }

        if ($filterBoxNumber) {
            $query->where('boxes.number', 'like', "%{$filterBoxNumber}%");
        }

        if ($filterYear) {
            $query->where(function ($q) use ($filterYear) {
                // Suporte a formatos DD/MM/YYYY, MM/YYYY e YYYY-MM-DD
                $q->where('documents.document_date', 'like', $filterYear.'-%')
                    ->orWhere('documents.document_date', 'like', '%/'.$filterYear);
            });
        }

        if ($sortBy === 'documents.document_date') {
            // Lógica para converter string em data para ordenação correta
            $query->orderByRaw("
                CASE 
                    WHEN documents.document_date REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}' THEN STR_TO_DATE(documents.document_date, '%Y-%m-%d')
                    WHEN documents.document_date REGEXP '^[0-9]{2}/[0-9]{2}/[0-9]{4}' THEN STR_TO_DATE(documents.document_date, '%d/%m/%Y')
                    WHEN documents.document_date REGEXP '^[0-9]{2}/[0-9]{4}' THEN STR_TO_DATE(CONCAT('01/', documents.document_date), '%d/%m/%Y')
                    ELSE documents.document_date 
                END {$sortDir}
            ");
        } elseif ($sortBy === 'documents.item_number') {
            $query->orderByRaw('CAST(documents.item_number AS UNSIGNED) '.$sortDir);
        } else {
            $query->orderBy($sortBy, $sortDir);
        }

        return $query;
    }

    /**
     * Calcula estatísticas baseadas em uma query ativa.
     */
    public function getStatistics(Builder $query): array
    {
        // Otimização: Evitar clonagem excessiva e usar agregação direta
        $statsQuery = (clone $query)->reorder();
        
        // Limpa as colunas selecionadas anteriormente (documents.*, etc) para evitar erro de 'only_full_group_by'
        $statsQuery->getQuery()->columns = [];

        $stats = $statsQuery
            ->selectRaw('count(distinct documents.id) as total_count')
            ->selectRaw("MIN(
                CASE 
                    WHEN documents.document_date REGEXP '^[0-9]{4}' THEN SUBSTRING(documents.document_date, 1, 4)
                    WHEN documents.document_date REGEXP '/[0-9]{4}$' THEN RIGHT(documents.document_date, 4)
                    ELSE NULL 
                END
            ) as min_year")
            ->selectRaw("MAX(
                CASE 
                    WHEN documents.document_date REGEXP '^[0-9]{4}' THEN SUBSTRING(documents.document_date, 1, 4)
                    WHEN documents.document_date REGEXP '/[0-9]{4}$' THEN RIGHT(documents.document_date, 4)
                    ELSE NULL 
                END
            ) as max_year")
            ->first();

        $filteredDocumentsCount = $stats->total_count ?? 0;
        $min = $stats->min_year;
        $max = $stats->max_year;

        $yearRange = '--';
        if ($filteredDocumentsCount > 0 && $min && $max) {
            $yearRange = ($min === $max) ? (string) $min : "{$min} - {$max}";
        }

        return [
            'filteredDocumentsCount' => $filteredDocumentsCount,
            'yearRange' => $yearRange,
        ];
    }
}
