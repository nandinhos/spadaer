<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Execute as seeds do banco de dados.
     */
    public function run(): void
    {
        // Criar papel de Administrador
        $adminRole = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrador',
            'description' => 'Administrador do sistema com acesso total',
            'is_active' => true
        ]);

        // Atribuir todas as permissões ao administrador
        $adminRole->permissions()->attach(
            Permission::where('is_active', true)->pluck('id')
        );

        // Criar papel de Usuário Padrão
        $userRole = Role::create([
            'name' => 'user',
            'display_name' => 'Usuário',
            'description' => 'Usuário padrão do sistema',
            'is_active' => true
        ]);

        // Atribuir permissões básicas ao usuário padrão
        $userRole->permissions()->attach(
            Permission::whereIn('name', [
                'documents.view',
                'commissions.view'
            ])->pluck('id')
        );

        // Criar papel de Presidente de Comissão
        $presidentRole = Role::create([
            'name' => 'commission_president',
            'display_name' => 'Presidente de Comissão',
            'description' => 'Presidente de comissão com permissões específicas',
            'is_active' => true
        ]);

        // Atribuir permissões ao presidente de comissão
        $presidentRole->permissions()->attach(
            Permission::whereIn('name', [
                'documents.view',
                'documents.create',
                'documents.edit',
                'commissions.view',
                'commissions.edit'
            ])->pluck('id')
        );

        // Criar papel de Membro de Comissão
        $memberRole = Role::create([
            'name' => 'commission_member',
            'display_name' => 'Membro de Comissão',
            'description' => 'Membro regular de comissão',
            'is_active' => true
        ]);

        // Atribuir permissões ao membro de comissão
        $memberRole->permissions()->attach(
            Permission::whereIn('name', [
                'documents.view',
                'documents.create',
                'commissions.view'
            ])->pluck('id')
        );
    }
}