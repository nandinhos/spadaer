# Plano de Implementação: Conversão da Listagem de Caixas para Livewire

## 🎯 Objetivo
Converter a página de listagem de caixas (`/boxes`) de Blade tradicional para Livewire, seguindo o padrão do módulo Projetos, mantendo compatibilidade com a deleção em massa existente.

---

## 📊 Situação Atual

### Estrutura Atual (Blade Tradicional)
```
resources/views/boxes/index.blade.php (319 linhas)
├── Filtros (Form GET tradicional)
├── Paginação tradicional
├── Tabela com Alpine.js para seleção em massa
├── Form de deleção em massa (funciona ✅)
├── Botão de deleção individual (não funciona ❌)
└── Componente confirmation-modal global
```

### Arquivos Envolvidos
- `resources/views/boxes/index.blade.php`
- `app/Http/Controllers/BoxController.php` (método `index`, `batchDestroy`)
- `routes/web.php` (rotas de boxes)

### Deleção em Massa (Funcionando)
- **Mecanismo**: Alpine.js + Form POST tradicional
- **Rota**: `DELETE /boxes/batch-destroy`
- **Controller**: `BoxController::batchDestroy()`
- **Lógica**: 
  - Caixas vazias → excluídas
  - Caixas com documentos → documentos desassociados (box_id = null)

---

## 🏗️ Arquitetura Proposta

### Nova Estrutura (Livewire)
```
resources/views/boxes/index.blade.php (simplificado)
└── <livewire:box-list />

app/Livewire/BoxList.php (novo componente)
├── Propriedades: search, sort_by, sort_dir, per_page
├── Propriedade: selectedBoxes[] (para deleção em massa)
├── Método: mount() - inicialização
├── Método: updatedSearch() - reset page
├── Método: sortBy() - ordenação
├── Método: deleteBox($id) - deleção individual ✅
├── Método: batchDelete() - deleção em massa ✅
└── Método: render() - view

resources/views/livewire/box-list.blade.php (nova view)
├── Filtros (wire:model.live)
├── Tabela com checkboxes (Alpine.js mantido)
├── Botões de ação (via $wire calls)
├── Confirmação via $store.confirmDelete
└── Paginação Livewire
```

---

## 🔍 Análise de Impacto

### ✅ O que MANTEM funcionando

| Funcionalidade | Status | Justificativa |
|----------------|--------|---------------|
| Filtros de busca | ✅ | Migrado para `wire:model.live` |
| Ordenação | ✅ | Migrado para propriedades Livewire |
| Paginação | ✅ | Usa `WithPagination` trait |
| Seleção em massa | ✅ | Alpine.js mantido no componente |
| Lógica de deleção | ✅ | Reutilizada no componente Livewire |
| Permissões (Gates) | ✅ | Verificadas nos métodos PHP |
| Feedback flash | ✅ | Via `session()->flash()` |

### ⚠️ O que PRECISA ser adaptado

| Item | Mudança | Esforço |
|------|---------|---------|
| Controller `index()` | Remover lógica de filtro | Baixo |
| Controller `batchDestroy()` | Mover para componente | Médio |
| Rotas | Manter compatibilidade | Baixo |
| View Blade | Simplificar | Baixo |
| JavaScript | Manter Alpine para seleção | Baixo |

### ❌ O que NÃO é impactado

- Página de detalhes da caixa (`boxes/show.blade.php`)
- Página de criação/edição (`boxes/create.blade.php`, `boxes/edit.blade.php`)
- Deleção de documentos dentro da caixa
- Todas as outras funcionalidades do sistema

---

## 📋 Plano de Implementação

### Fase 1: Criar Componente Livewire (30 min)

**Arquivo**: `app/Livewire/BoxList.php`

```php
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
    public function batchDelete()
    {
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
                } else {
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
            ->orderByRaw($this->sort_by . ' ' . $this->sort_dir . ' NULLS LAST')
            ->paginate($this->per_page);

        return view('livewire.box-list', [
            'boxes' => $boxes,
        ]);
    }
}
```

### Fase 2: Criar View Livewire (40 min)

**Arquivo**: `resources/views/livewire/box-list.blade.php`

```blade
<div>
    <!-- Filtros -->
    <div class="p-4 mb-4 bg-white rounded-lg shadow dark:bg-gray-800">
        <div class="grid items-end grid-cols-1 gap-4 md:grid-cols-6">
            <div class="md:col-span-2">
                <x-input-label for="search" value="Buscar Nº/Local/Proj./Conf." />
                <x-text-input wire:model.live.debounce.300ms="search" class="w-full mt-1" 
                    placeholder="Digite para buscar..." />
            </div>

            <div>
                <x-input-label value="Status" />
                <x-select-input wire:model.live="filter_status" class="w-full mt-1">
                    <option value="">Todos</option>
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </x-select-input>
            </div>

            <div>
                <x-input-label value="Projeto" />
                <x-select-input wire:model.live="project_id" class="w-full mt-1">
                    <option value="">Todos</option>
                    @foreach($projects as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </x-select-input>
            </div>

            <div>
                <x-input-label value="Conferente" />
                <x-select-input wire:model.live="commission_member_id" class="w-full mt-1">
                    <option value="">Todos</option>
                    @foreach($activeMembers as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </x-select-input>
            </div>

            <div>
                <x-ui.button wire:click="$set('search', '')" variant="secondary" size="sm">
                    Limpar
                </x-ui.button>
            </div>
        </div>
    </div>

    <!-- Seletor por página -->
    <div class="flex flex-col items-center justify-between gap-4 mb-4 sm:flex-row">
        <div class="flex items-center gap-2">
            <label class="text-sm text-gray-700 dark:text-gray-300">Itens por página:</label>
            <x-select-input wire:model.live="per_page" class="text-sm">
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </x-select-input>
        </div>
        
        @can('boxes.create')
            <a href="{{ route('boxes.create') }}" wire:navigate>
                <x-ui.button variant="primary" icon="fas fa-plus">
                    Adicionar Caixa
                </x-ui.button>
            </a>
        @endcan
    </div>

    <!-- Mensagens -->
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 rounded-xl">
            {{ session('success') }}
        </div>
    @endif
    
    @if (session()->has('warning'))
        <div class="mb-4 p-4 bg-yellow-500/10 border border-yellow-500/20 text-yellow-600 rounded-xl">
            {{ session('warning') }}
        </div>
    @endif

    <!-- Tabela com Alpine.js para seleção -->
    <div x-data="{
        selected: @entangle('selectedBoxes').live,
        allIds: {{ json_encode($boxes->pluck('id')->toArray()) }},
        get isAllSelected() {
            return this.selected.length > 0 && this.selected.length === this.allIds.length;
        },
        toggleAll() {
            this.selected = this.isAllSelected ? [] : [...this.allIds];
        }
    }">
        
        <!-- Botões de ação em massa -->
        @can('boxes.delete')
            <div x-show="selected.length > 0" x-cloak class="mb-4 p-4 bg-gray-50 rounded-xl">
                <div class="flex items-center space-x-2">
                    <x-ui.button 
                        variant="danger" 
                        size="sm"
                        icon="fas fa-trash-alt"
                        x-bind:disabled="selected.length === 0"
                        @click="$store.confirmDelete.open({
                            title: 'Excluir Selecionados',
                            message: 'Tem certeza que deseja processar ' + selected.length + ' caixa(s)?',
                            onConfirm: () => { $wire.batchDelete() }
                        })"
                    >
                        Excluir (<span x-text="selected.length"></span>)
                    </x-ui.button>
                    
                    <x-ui.button 
                        variant="secondary" 
                        size="sm"
                        icon="fas fa-check-square"
                        @click="toggleAll()"
                    >
                        <span x-text="isAllSelected ? 'Desmarcar Todos' : 'Selecionar Todos'"></span>
                    </x-ui.button>
                </div>
            </div>
        @endcan

        <!-- Tabela -->
        <div class="overflow-hidden bg-white rounded-lg shadow dark:bg-gray-800">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            @can('boxes.delete')
                                <th class="w-12 px-6 py-3">
                                    <input type="checkbox" @click="toggleAll()" :checked="isAllSelected">
                                </th>
                            @endcan
                            <th class="px-6 py-3 text-left">Número</th>
                            <th class="px-6 py-3 text-left">Local</th>
                            <th class="px-6 py-3 text-left">Projeto</th>
                            <th class="px-6 py-3 text-left">Conferente</th>
                            <th class="px-6 py-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($boxes as $box)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750/50">
                                @can('boxes.delete')
                                    <td class="px-6 py-4">
                                        <input type="checkbox" :value="{{ $box->id }}" x-model="selected">
                                    </td>
                                @endcan
                                <td class="px-6 py-4">{{ $box->number }}</td>
                                <td class="px-6 py-4">{{ $box->physical_location ?? '--' }}</td>
                                <td class="px-6 py-4">{{ $box->project?->name ?? '--' }}</td>
                                <td class="px-6 py-4">{{ $box->commissionMember?->user?->name ?? '--' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        @can('boxes.view')
                                            <a href="{{ route('boxes.show', $box) }}" wire:navigate>
                                                <x-ui.button variant="ghost-primary" size="sm" icon="fas fa-eye" />
                                            </a>
                                        @endcan
                                        
                                        @can('boxes.edit')
                                            <a href="{{ route('boxes.edit', $box) }}" wire:navigate>
                                                <x-ui.button variant="ghost-warning" size="sm" icon="fas fa-edit" />
                                            </a>
                                        @endcan
                                        
                                        @can('boxes.delete')
                                            <x-ui.button 
                                                variant="ghost-danger" 
                                                size="sm" 
                                                icon="fas fa-trash-alt"
                                                @click="$store.confirmDelete.open({
                                                    title: 'Excluir Caixa',
                                                    message: 'Tem certeza que deseja excluir {{ $box->number }}?',
                                                    onConfirm: () => { $wire.deleteBox({{ $box->id }}) }
                                                })"
                                            />
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                    Nenhuma caixa encontrada.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $boxes->links() }}
            </div>
        </div>
    </div>
</div>
```

### Fase 3: Simplificar View Principal (10 min)

**Arquivo**: `resources/views/boxes/index.blade.php`

```blade
<x-app-layout>
    @section('title', 'Caixas')
    @section('header-title', 'Gerenciamento de Caixas')

    <div class="py-6">
        <livewire:box-list />
    </div>
</x-app-layout>
```

### Fase 4: Atualizar Controller (Opcional - 5 min)

Manter `BoxController::index()` simples:

```php
public function index()
{
    return view('boxes.index');
}
```

---

## ⏱️ Estimativa de Tempo

| Fase | Tempo Estimado |
|------|----------------|
| Fase 1: Criar Componente PHP | 30 min |
| Fase 2: Criar View Blade | 40 min |
| Fase 3: Simplificar View Principal | 10 min |
| Fase 4: Testes e Ajustes | 20 min |
| **Total** | **~1h 40min** |

---

## ✅ Checklist de Validação

- [ ] Filtros funcionam em tempo real
- [ ] Ordenação funciona
- [ ] Paginação funciona
- [ ] Seleção individual funciona
- [ ] Seleção "Todos" funciona
- [ ] Deleção individual funciona
- [ ] Deleção em massa funciona
- [ ] Mensagens flash aparecem
- [ ] Permissões respeitadas
- [ ] URLs mantêm filtros (history)

---

## 🎨 Benefícios da Nova Abordagem

1. **Código mais limpo** - Separação de responsabilidades
2. **Reatividade** - Filtros atualizam sem reload
3. **Performance** - Apenas dados necessários via AJAX
4. **Manutenibilidade** - Padrão consistente com Projetos
5. **Testabilidade** - Lógica testável no PHP
6. **UX melhor** - Feedback instantâneo

---

## ⚠️ Considerações Importantes

1. **Deleção em Massa**: Funciona igual, mas via `$wire.batchDelete()` em vez de form submission
2. **Seleção**: Continua usando Alpine.js com `@entangle` para sincronizar com Livewire
3. **Confirmação**: Usa o mesmo `$store.confirmDelete` global
4. **Rotas**: Não precisa alterar rotas existentes
5. **Controller**: `batchDestroy()` pode ser removido ou mantido para compatibilidade

**Quer prosseguir com a implementação?**