<?php

namespace App\Traits;

use Spatie\Permission\Models\{Permission, Role};
use Illuminate\Support\Facades\Auth;

trait HasAuthorization
{
    /**
     * Verifica se o usuário autenticado tem um papel específico.
     *
     * @param string|Role $role
     * @return bool
     */
    protected function userHasRole(string|Role $role): bool
    {
        return Auth::user()?->hasRole($role) ?? false;
    }

    /**
     * Verifica se o usuário autenticado tem uma permissão específica.
     *
     * @param string|Permission $permission
     * @return bool
     */
    protected function userHasPermission(string|Permission $permission): bool
    {
        return Auth::user()?->hasPermissionTo($permission) ?? false;
    }

    /**
     * Verifica se o usuário autenticado tem qualquer um dos papéis especificados.
     *
     * @param array<string|Role> $roles
     * @return bool
     */
    protected function userHasAnyRole(array $roles): bool
    {
        return Auth::user()?->hasAnyRole($roles) ?? false;
    }

    /**
     * Verifica se o usuário autenticado tem qualquer uma das permissões especificadas.
     *
     * @param array<string|Permission> $permissions
     * @return bool
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