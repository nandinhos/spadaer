<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleManager extends Component
{
    // Removendo lógica de modal de edição
    public $roleName = '';

    public $showCreateModal = false;

    public function openCreateModal()
    {
        $this->roleName = '';
        $this->showCreateModal = true;
    }

    public function saveNewRole()
    {
        $this->validate([
            'roleName' => 'required|string|max:255|unique:roles,name',
        ]);

        $role = Role::create(['name' => $this->roleName]);

        session()->flash('success', 'Papel criado com sucesso! Agora defina as permissões.');

        return $this->redirect(route('admin.roles.edit', $role->id), navigate: true);
    }

    public function deleteRole($id)
    {
        $role = Role::findOrFail($id);

        if (in_array($role->name, ['admin', 'user'])) {
            session()->flash('error', 'Papéis básicos do sistema não podem ser excluídos.');

            return;
        }

        $role->delete();
        session()->flash('success', 'Papel excluído com sucesso.');
    }

    public function render()
    {
        return view('livewire.admin.role-manager', [
            'roles' => Role::with('permissions')->get(),
            'permissionsByCategory' => Permission::all()->groupBy(function ($perm) {
                return explode('.', $perm->name)[0] ?? 'sistema';
            }),
        ]);
    }
}
