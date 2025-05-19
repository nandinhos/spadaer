<?php

namespace App\Traits;

use App\Models\{Permission, Role};

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
        return auth()->user()?->hasRole($role) ?? false;
    }

    /**
     * Verifica se o usuário autenticado tem uma permissão específica.
     *
     * @param string|Permission $permission
     * @return bool
     */
    protected function userHasPermission(string|Permission $permission): bool
    {
        return auth()->user()?->hasPermission($permission) ?? false;
    }

    /**
     * Verifica se o usuário autenticado tem qualquer um dos papéis especificados.
     *
     * @param array<string|Role> $roles
     * @return bool
     */
    protected function userHasAnyRole(array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->userHasRole($role)) {
                return true;
            }
        }
        return false;
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