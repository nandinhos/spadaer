<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Execute as seeds do banco de dados.
     */
    public function run(): void
    {
        // Permissões para gerenciamento de usuários
        Permission::create([
            'name' => 'users.view',
            'display_name' => 'Visualizar Usuários',
            'description' => 'Permite visualizar a lista de usuários',
            'group' => 'users',
            'is_active' => true
        ]);

        Permission::create([
            'name' => 'users.create',
            'display_name' => 'Criar Usuários',
            'description' => 'Permite criar novos usuários',
            'group' => 'users',
            'is_active' => true
        ]);

        Permission::create([
            'name' => 'users.edit',
            'display_name' => 'Editar Usuários',
            'description' => 'Permite editar usuários existentes',
            'group' => 'users',
            'is_active' => true
        ]);

        Permission::create([
            'name' => 'users.delete',
            'display_name' => 'Excluir Usuários',
            'description' => 'Permite excluir usuários',
            'group' => 'users',
            'is_active' => true
        ]);

        // Permissões para gerenciamento de documentos
        Permission::create([
            'name' => 'documents.view',
            'display_name' => 'Visualizar Documentos',
            'description' => 'Permite visualizar documentos',
            'group' => 'documents',
            'is_active' => true
        ]);

        Permission::create([
            'name' => 'documents.create',
            'display_name' => 'Criar Documentos',
            'description' => 'Permite criar novos documentos',
            'group' => 'documents',
            'is_active' => true
        ]);

        Permission::create([
            'name' => 'documents.edit',
            'display_name' => 'Editar Documentos',
            'description' => 'Permite editar documentos existentes',
            'group' => 'documents',
            'is_active' => true
        ]);

        Permission::create([
            'name' => 'documents.delete',
            'display_name' => 'Excluir Documentos',
            'description' => 'Permite excluir documentos',
            'group' => 'documents',
            'is_active' => true
        ]);

        // Permissões para gerenciamento de comissões
        Permission::create([
            'name' => 'commissions.view',
            'display_name' => 'Visualizar Comissões',
            'description' => 'Permite visualizar comissões',
            'group' => 'commissions',
            'is_active' => true
        ]);

        Permission::create([
            'name' => 'commissions.create',
            'display_name' => 'Criar Comissões',
            'description' => 'Permite criar novas comissões',
            'group' => 'commissions',
            'is_active' => true
        ]);

        Permission::create([
            'name' => 'commissions.edit',
            'display_name' => 'Editar Comissões',
            'description' => 'Permite editar comissões existentes',
            'group' => 'commissions',
            'is_active' => true
        ]);

        Permission::create([
            'name' => 'commissions.delete',
            'display_name' => 'Excluir Comissões',
            'description' => 'Permite excluir comissões',
            'group' => 'commissions',
            'is_active' => true
        ]);
    }
}