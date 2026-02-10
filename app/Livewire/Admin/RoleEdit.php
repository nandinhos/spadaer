<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleEdit extends Component
{
    public Role $role;

    public $roleName = '';

    public $rolePermissions = [];

    public function mount(Role $role)
    {
        $this->role = $role;
        $this->roleName = $role->name;
        $this->rolePermissions = $role->permissions->pluck('name')->toArray();
    }

    public function save()
    {
        $this->validate([
            'roleName' => 'required|string|max:255|unique:roles,name,'.$this->role->id,
            'rolePermissions' => 'nullable|array',
        ]);

        if (! in_array($this->role->name, ['admin', 'user'])) {
            $this->role->update(['name' => $this->roleName]);
        }

        $oldPermissions = $this->role->permissions->pluck('name')->toArray();
        $this->role->syncPermissions($this->rolePermissions);

        if ($oldPermissions !== $this->rolePermissions) {
            $this->role->auditManual('role_permissions_synced', ['permissions' => $oldPermissions], ['permissions' => $this->rolePermissions]);
        }

        session()->flash('success', 'Papel e permissões atualizados com sucesso.');

        return $this->redirect(route('admin.roles.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.role-edit', [
            'permissionsByCategory' => Permission::all()->groupBy(function ($perm) {
                return explode('.', $perm->name)[0] ?? 'sistema';
            }),
        ]);
    }
}
