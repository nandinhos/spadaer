<?php

namespace App\Livewire;

use App\Models\Project;
use App\Support\SortHelper;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectList extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $sort_by = 'code';

    #[Url(history: true)]
    public $sort_dir = 'asc';

    public $per_page = 10;

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

    public function deleteProject($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        session()->flash('message', 'Projeto excluído com sucesso.');
    }

    public function render()
    {
        // Props Livewire vêm do frontend: sanitiza antes de interpolar no SQL.
        [$this->sort_by, $this->sort_dir] = SortHelper::sanitize(
            $this->sort_by, $this->sort_dir, ['code', 'name'], 'code', 'asc',
        );
        $this->per_page = min(max((int) $this->per_page, 1), 100);

        $projects = Project::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('code', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sort_by, $this->sort_dir)
            ->paginate($this->per_page);

        return view('livewire.project-list', [
            'projects' => $projects,
        ]);
    }
}
