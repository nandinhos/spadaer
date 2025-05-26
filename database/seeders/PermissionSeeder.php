<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Execute as seeds do banco de dados.
     */
    public function run(): void
    {
        // Permissões para gerenciamento de usuários
        Permission::create(['name' => 'users.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'users.create', 'guard_name' => 'web']);
        Permission::create(['name' => 'users.edit', 'guard_name' => 'web']);
        Permission::create(['name' => 'users.delete', 'guard_name' => 'web']);

        // Permissões para gerenciamento de documentos
        Permission::create(['name' => 'documents.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'documents.create', 'guard_name' => 'web']);
        Permission::create(['name' => 'documents.edit', 'guard_name' => 'web']);
        Permission::create(['name' => 'documents.delete', 'guard_name' => 'web']);
        Permission::create(['name' => 'documents.export.excel', 'guard_name' => 'web']);
        Permission::create(['name' => 'documents.export.pdf', 'guard_name' => 'web']);
        Permission::create(['name' => 'documents.import', 'guard_name' => 'web']);

        // Permissões para gerenciamento de comissões
        Permission::create(['name' => 'commissions.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'commissions.create', 'guard_name' => 'web']);
        Permission::create(['name' => 'commissions.edit', 'guard_name' => 'web']);
        Permission::create(['name' => 'commissions.delete', 'guard_name' => 'web']);

    }
}