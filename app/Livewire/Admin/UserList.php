<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class UserList extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $sort_by = 'name';

    #[Url(history: true)]
    public $sort_dir = 'asc';

    public $per_page = 10;

    // Form fields
    public $userId = null;

    public $name = '';

    public $full_name = '';

    public $rank = '';

    public $order_number = '';

    public $email = '';

    public $password = '';

    public $selectedRoles = [];

    public $selectedPermissions = [];

    public $isEditMode = false;

    public $showUserModal = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'full_name' => 'required|string|max:255',
        'rank' => 'required|string|max:50',
        'order_number' => 'required|string|max:20',
        'email' => 'required|email|max:255|unique:users,email',
        'password' => 'required|min:6',
        'selectedRoles' => 'required|array|min:1',
        'selectedPermissions' => 'nullable|array',
    ];

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

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->resetForm();
        $this->isEditMode = false;
        $this->showUserModal = true;
    }

    public function openEditModal($id)
    {
        $this->resetValidation();
        $this->resetForm();

        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->full_name = $user->full_name;
        $this->rank = $user->rank;
        $this->order_number = $user->order_number;
        $this->email = $user->email;
        $this->password = ''; // Don't show password
        $this->selectedRoles = $user->roles->pluck('name')->toArray();
        $this->selectedPermissions = $user->getDirectPermissions()->pluck('name')->toArray();

        $this->isEditMode = true;
        $this->showUserModal = true;
    }

    public function resetForm()
    {
        $this->userId = null;
        $this->name = '';
        $this->full_name = '';
        $this->rank = '';
        $this->order_number = '';
        $this->email = '';
        $this->password = '';
        $this->selectedRoles = [];
        $this->selectedPermissions = [];
    }

    public function saveUser()
    {
        $rules = $this->rules;

        if ($this->isEditMode) {
            $rules['email'] = 'required|email|max:255|unique:users,email,'.$this->userId;
            $rules['password'] = 'nullable|min:6';
        }

        $validatedData = $this->validate($rules);

        if ($this->isEditMode) {
            $user = User::findOrFail($this->userId);
            $data = [
                'name' => $this->name,
                'full_name' => $this->full_name,
                'rank' => $this->rank,
                'order_number' => $this->order_number,
                'email' => $this->email,
            ];

            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }

            $oldRoles = $user->getRoleNames()->toArray();
            $oldPermissions = $user->getDirectPermissions()->pluck('name')->toArray();

            $user->update($data);
            $user->syncRoles($this->selectedRoles);
            $user->syncPermissions($this->selectedPermissions);

            // Auditoria manual para pivots (que não disparam eventos Eloquent)
            if ($oldRoles !== $this->selectedRoles) {
                $user->auditManual('roles_synced', ['roles' => $oldRoles], ['roles' => $this->selectedRoles]);
            }
            if ($oldPermissions !== $this->selectedPermissions) {
                $user->auditManual('permissions_synced', ['permissions' => $oldPermissions], ['permissions' => $this->selectedPermissions]);
            }

            session()->flash('success', 'Usuário atualizado com sucesso.');
        } else {
            $user = User::create([
                'name' => $this->name,
                'full_name' => $this->full_name,
                'rank' => $this->rank,
                'order_number' => $this->order_number,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);

            $user->assignRole($this->selectedRoles);
            $user->syncPermissions($this->selectedPermissions);

            // Audit manual de atribuição inicial
            $user->auditManual('roles_assigned', [], ['roles' => $this->selectedRoles]);
            if (! empty($this->selectedPermissions)) {
                $user->auditManual('permissions_assigned', [], ['permissions' => $this->selectedPermissions]);
            }

            session()->flash('success', 'Usuário criado com sucesso.');
        }

        $this->showUserModal = false;
        $this->resetForm();
    }

    public function deleteUser($id)
    {
        if ($id === auth()->id()) {
            session()->flash('error', 'Você não pode excluir a si mesmo.');

            return;
        }

        $user = User::findOrFail($id);
        $user->delete();

        session()->flash('success', 'Usuário excluído com sucesso.');
    }

    public function render()
    {
        $users = User::query()
            ->with(['roles', 'permissions'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
                    ->orWhere('full_name', 'like', '%'.$this->search.'%')
                    ->orWhere('rank', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sort_by, $this->sort_dir)
            ->paginate($this->per_page);

        // Agrupar permissões por categoria para facilitar a visualização
        $permissions = \Spatie\Permission\Models\Permission::all()->groupBy(function ($perm) {
            return explode('.', $perm->name)[0] ?? 'sistema';
        });

        return view('livewire.admin.user-list', [
            'users' => $users,
            'roles' => Role::all(),
            'permissionsByCategory' => $permissions,
        ]);
    }
}
