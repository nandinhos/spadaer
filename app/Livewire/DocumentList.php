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

    // Seleção em massa
    public $selectedDocuments = [];

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

    // Deleção Individual com Observação
    public function deleteDocument($id, $observation = null)
    {
        $this->authorize('documents.delete');

        $document = Document::findOrFail($id);

        try {
            $document->auditManual('document_deleted', [], [
                'document' => $document->document_number,
                'title' => $document->title,
                'reason' => $observation
            ]);

            $document->delete();

            session()->flash('success', "Documento {$document->document_number} excluído com sucesso.");
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao excluir documento: ' . $e->getMessage());
        }
    }

    // Deleção em Massa com Observação
    public function batchDelete($observation = null)
    {
        $this->authorize('documents.delete');

        if (empty($this->selectedDocuments)) {
            return;
        }

        $count = count($this->selectedDocuments);

        try {
            foreach ($this->selectedDocuments as $id) {
                $document = Document::find($id);
                if ($document) {
                    $document->auditManual('document_bulk_deleted', [], [
                        'document' => $document->document_number,
                        'reason' => $observation,
                        'batch' => true
                    ]);
                    $document->delete();
                }
            }

            session()->flash('success', "{$count} documento(s) excluído(s) com sucesso.");
            $this->selectedDocuments = [];
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao excluir documentos em massa: ' . $e->getMessage());
        }
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
