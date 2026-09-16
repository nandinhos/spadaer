<?php

namespace App\Livewire;

use App\Models\Commission;
use App\Support\SortHelper;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CommissionList extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $status = '';

    #[Url(history: true)]
    public $sort_by = 'ordinance_date';

    #[Url(history: true)]
    public $sort_dir = 'desc';

    public $per_page = 10;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatus()
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

    public function deleteCommission($id)
    {
        $commission = Commission::findOrFail($id);
        $commission->delete();

        session()->flash('message', 'Comissão excluída com sucesso.');
    }

    public function render()
    {
        // Props Livewire vêm do frontend: sanitiza antes de interpolar no SQL.
        [$this->sort_by, $this->sort_dir] = SortHelper::sanitize(
            $this->sort_by, $this->sort_dir, ['name', 'ordinance_number', 'ordinance_date'], 'ordinance_date', 'desc',
        );
        $this->per_page = min(max((int) $this->per_page, 1), 100);

        $commissions = Commission::query()
            ->withCount('members')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('ordinance_number', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->orderBy($this->sort_by, $this->sort_dir)
            ->paginate($this->per_page);

        return view('livewire.commission-list', [
            'commissions' => $commissions,
        ]);
    }
}
