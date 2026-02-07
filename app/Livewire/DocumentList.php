<?php

namespace App\Livewire;

use App\Models\Box;
use App\Models\Document;
use App\Models\Project;
use App\Services\DocumentService;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class DocumentList extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $filter_project_id = '';

    #[Url(history: true)]
    public $filter_box_number = '';

    #[Url(history: true)]
    public $filter_year = '';

    #[Url(history: true)]
    public $sort_by = 'documents.id';

    #[Url(history: true)]
    public $sort_dir = 'desc';

    public $per_page = 15;

    public $hasActiveFilters = false;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function sortBy($column)
    {
        if ($this->sort_by === $column) {
            $this->sort_dir = $this->sort_dir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sort_by = $column;
            $this->sort_dir = 'asc';
        }
    }

    public function clearFilters()
    {
        $this->reset(['search', 'filter_project_id', 'filter_box_number', 'filter_year']);
        $this->resetPage();
    }

    public function render(DocumentService $service)
    {
        $params = [
            'search' => $this->search,
            'filter_project_id' => $this->filter_project_id,
            'filter_box_number' => $this->filter_box_number,
            'filter_year' => $this->filter_year,
            'sort_by' => $this->sort_by,
            'sort_dir' => $this->sort_dir,
        ];

        $query = $service->listDocuments($params);
        $statsData = $service->getStatistics($query);

        $documents = $query->paginate($this->per_page);

        $this->hasActiveFilters = ! empty($this->search) || ! empty($this->filter_project_id) || ! empty($this->filter_box_number) || ! empty($this->filter_year);

        return view('livewire.document-list', [
            'documents' => $documents,
            'stats' => array_merge([
                'totalDocuments' => Document::count(),
                'totalBoxes' => Box::count(),
                'totalProjects' => Project::count(),
                'filteredBoxesCount' => (clone $query)->distinct()->count('documents.box_id'),
                'filteredProjectsCount' => (clone $query)->distinct()->count('documents.project_id'),
            ], $statsData),
            'availableProjects' => Project::orderBy('name')->pluck('name', 'id'),
            'availableYears' => Document::query()
                ->whereNotNull('document_date')
                ->where('document_date', '!=', '')
                ->select('document_date')
                ->distinct()
                ->pluck('document_date')
                ->map(function ($d) {
                    $dStr = (string)$d;
                    if (preg_match('/^(\d{4})/', $dStr, $m)) {
                        return (int)$m[1];
                    }
                    if (preg_match('/\/(\d{4})$/', $dStr, $m)) {
                        return (int)$m[1];
                    }
                    return null;
                })
                ->filter()->unique()->sortDesc()->values(),
        ]);
    }
}
