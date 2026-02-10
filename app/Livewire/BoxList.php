<?php

namespace App\Livewire;

use App\Models\Box;
use App\Models\Project;
use App\Models\CommissionMember;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class BoxList extends Component
{
    use WithPagination;

    // Filtros
    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $sort_by = 'boxes.number';

    #[Url(history: true)]
    public $sort_dir = 'asc';

    public $per_page = 15;
    public $filter_status = '';
    public $project_id = '';
    public $commission_member_id = '';

    // Seleção em massa (via Alpine.js)
    public $selectedBoxes = [];

    // Dados auxiliares
    public $statusOptions = [];
    public $projects = [];
    public $activeMembers = [];

    public function mount()
    {
        $this->statusOptions = [
            'empty' => 'Vazia',
            'has_documents' => 'Com Documentos',
            'orphaned' => 'Órfãos (desassociados)',
        ];

        $this->projects = Project::pluck('name', 'id');
        $this->activeMembers = CommissionMember::with('user')
            ->get()
            ->pluck('user.name', 'id');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedProjectId()
    {
        $this->resetPage();
    }

    public function updatedCommissionMemberId()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
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

    // Deleção Individual
    public function deleteBox($id)
    {
        $this->authorize('boxes.delete');

        $box = Box::findOrFail($id);

        try {
            DB::beginTransaction();

            if ($box->documents()->count() > 0) {
                // Desassociar documentos
                $box->documents()->update(['box_id' => null]);
                DB::commit();
                session()->flash('warning', "Caixa {$box->number} não pode ser excluída pois contém documentos. Documentos foram desassociados.");
            } else {
                $box->delete();
                DB::commit();
                session()->flash('success', "Caixa {$box->number} excluída com sucesso.");
            }
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erro ao excluir caixa: ' . $e->getMessage());
        }
    }

    // Deleção em Massa
    public function batchDelete($observation = null)
    {
        $this->authorize('boxes.delete');

        if (empty($this->selectedBoxes)) {
            return;
        }

        $deletedCount = 0;
        $orphanedCount = 0;

        try {
            DB::beginTransaction();

            foreach ($this->selectedBoxes as $boxId) {
                $box = Box::find($boxId);

                if (! $box) continue;

                if ($box->documents()->count() > 0) {
                    $box->documents()->update(['box_id' => null]);
                    $orphanedCount++;

                    $box->auditManual('box_orphaned_bulk', [], [
                        'box' => $box->number,
                        'reason' => $observation
                    ]);
                } else {
                    $box->auditManual('box_deleted_bulk', [], [
                        'box' => $box->number,
                        'reason' => $observation
                    ]);
                    $box->delete();
                    $deletedCount++;
                }
            }

            DB::commit();

            $message = [];
            if ($deletedCount > 0) {
                $message[] = "{$deletedCount} caixa(s) vazia(s) excluída(s).";
            }
            if ($orphanedCount > 0) {
                $message[] = "{$orphanedCount} caixa(s) tiveram documentos desassociados.";
            }

            session()->flash('success', implode(' ', $message));
            $this->selectedBoxes = []; // Limpar seleção
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erro ao processar deleção em massa: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $boxes = Box::query()
            ->with(['project', 'commissionMember.user', 'documents'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('boxes.number', 'like', '%'.$this->search.'%')
                      ->orWhere('physical_location', 'like', '%'.$this->search.'%')
                      ->orWhereHas('project', function ($pq) {
                          $pq->where('name', 'like', '%'.$this->search.'%');
                      })
                      ->orWhereHas('commissionMember.user', function ($cq) {
                          $cq->where('name', 'like', '%'.$this->search.'%');
                      });
                });
            })
            ->when($this->filter_status === 'empty', function ($query) {
                $query->whereDoesntHave('documents');
            })
            ->when($this->filter_status === 'has_documents', function ($query) {
                $query->has('documents');
            })
            ->when($this->filter_status === 'orphaned', function ($query) {
                $query->whereNull('project_id');
            })
            ->when($this->project_id, function ($query) {
                $query->where('project_id', $this->project_id);
            })
            ->when($this->commission_member_id, function ($query) {
                $query->where('commission_member_id', $this->commission_member_id);
            })
            ->orderBy($this->sort_by, $this->sort_dir)
            ->paginate($this->per_page);

        return view('livewire.box-list', [
            'boxes' => $boxes,
        ]);
    }
}
