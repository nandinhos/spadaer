<?php

namespace App\Livewire;

use App\Models\AuditLog;
use Livewire\Component;
use Livewire\WithPagination;

class AuditList extends Component
{
    use WithPagination;

    public $search = '';

    public $event = '';

    public $perPage = 15;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = AuditLog::with('user')
            ->where('event', '!=', 'viewed')
            ->when($this->search, function ($q) {
                $q->where('auditable_type', 'like', '%'.$this->search.'%')
                    ->orWhere('auditable_id', 'like', '%'.$this->search.'%')
                    ->orWhereHas('user', function ($uq) {
                        $uq->where('name', 'like', '%'.$this->search.'%');
                    });
            })
            ->when($this->event, function ($q) {
                $q->where('event', $this->event);
            })
            ->latest();

        return view('livewire.audit-list', [
            'logs' => $query->paginate($this->perPage),
        ]);
    }
}
