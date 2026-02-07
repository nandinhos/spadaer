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
                $q->where('documents.document_date', 'like', $filterYear.'-%')
                    ->orWhere('documents.document_date', 'like', '%/'.$filterYear);
            });
        }

        if ($sortBy === 'documents.item_number') {
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
        // Contagem de documentos (usando distinct para evitar inflação por joins se houver)
        $filteredDocumentsCount = (clone $query)->distinct()->count('documents.id');

        $yearRange = '--';
        if ($filteredDocumentsCount > 0) {
            $years = (clone $query)->reorder()
                ->select('document_date')
                ->whereNotNull('document_date')
                ->where('document_date', '!=', '')
                ->distinct()
                ->pluck('document_date')
                ->map(function ($date) {
                    $dateStr = (string) $date;
                    // Tenta YYYY-MM-DD
                    if (preg_match('/^(\d{4})/', $dateStr, $matches)) {
                        return (int) $matches[1];
                    }
                    // Tenta DD/MM/YYYY
                    if (preg_match('/\/(\d{4})$/', $dateStr, $matches)) {
                        return (int) $matches[1];
                    }

                    return null;
                })
                ->filter()
                ->unique();

            if ($years->isNotEmpty()) {
                $min = $years->min();
                $max = $years->max();
                $yearRange = ($min === $max) ? (string) $min : "{$min} - {$max}";
            }
        }

        return [
            'filteredDocumentsCount' => $filteredDocumentsCount,
            'yearRange' => $yearRange,
        ];
    }
}
