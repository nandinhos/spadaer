<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Execute as seeds do banco de dados.
     */
    public function run(): void
    {
        // Delete existing roles to prevent duplicates
        Role::query()->delete();

        // Criar papel de Administrador
        $adminRole = Role::create([
            'name' => 'admin',
            'guard_name' => 'web',
            'display_name' => 'Administrador'
        ]);
        // Atribuir todas as permissões ao administrador
        $adminRole->givePermissionTo(Permission::all());

        // Criar papel de Usuário Padrão
        $userRole = Role::create([
            'name' => 'user',
            'guard_name' => 'web',
            'display_name' => 'Usuário'
        ]);
        // Atribuir permissões básicas ao usuário padrão
        $userRole->givePermissionTo([
            'documents.view',
            'commissions.view',
            'boxes.view',
            'boxes.create',
            'boxes.edit',
            'boxes.delete'
        ]);

        // Criar papel de Presidente de Comissão
        $presidentRole = Role::create([
            'name' => 'commission_president',
            'guard_name' => 'web',
            'display_name' => 'Presidente de Comissão'
        ]);
        // Atribuir permissões ao presidente de comissão
        $presidentRole->givePermissionTo([
            'documents.view',
            'documents.create',
            'documents.edit',
            'documents.export.excel',
            'documents.export.pdf',
            'commissions.view',
            'commissions.edit'
        ]);

        // Criar papel de Membro de Comissão
        $memberRole = Role::create([
            'name' => 'commission_member',
            'guard_name' => 'web',
            'display_name' => 'Membro de Comissão'
        ]);
        // Atribuir permissões ao membro de comissão
        $memberRole->givePermissionTo([
            'documents.view',
            'documents.create',
            'documents.export.excel',
            'documents.export.pdf',
            'commissions.view'
        ]);
    }
}