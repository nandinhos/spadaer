<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    /**
     * Exibe o painel de gerenciamento de permissões.
     */
    public function index(): View
    {
        $users = User::with(['roles', 'roles.permissions'])->get();
        $roles = Role::with('permissions')->get();

        return view('admin.permissions', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    /**
     * Atualiza as permissões de um usuário.
     */
    public function updateUserRoles(Request $request, User $user)
    {
        $validated = $request->validate([
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:'.config('permission.table_names.roles').',id'],
        ]);

        $user->roles()->sync($validated['roles']);

        return back()->with('success', 'Permissões atualizadas com sucesso.');
    }
}