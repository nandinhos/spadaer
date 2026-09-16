<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

trait HasAuthorization
{
    /**
     * Verifica se o usuário autenticado tem um papel específico.
     */
    protected function userHasRole(string|Role $role): bool
    {
        return Auth::user()?->hasRole($role) ?? false;
    }

    /**
     * Verifica se o usuário autenticado tem uma permissão específica.
     */
    protected function userHasPermission(string|Permission $permission): bool
    {
        return Auth::user()?->hasPermissionTo($permission) ?? false;
    }

    /**
     * Verifica se o usuário autenticado tem qualquer um dos papéis especificados.
     *
     * @param  array<string|Role>  $roles
     */
    protected function userHasAnyRole(array $roles): bool
    {
        return Auth::user()?->hasAnyRole($roles) ?? false;
    }

    /**
     * Verifica se o usuário autenticado tem qualquer uma das permissões especificadas.
     *
     * @param  array<string|Permission>  $permissions
     */
    protected function userHasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->userHasPermission($permission)) {
                return true;
            }
        }

        return false;
    }
}
