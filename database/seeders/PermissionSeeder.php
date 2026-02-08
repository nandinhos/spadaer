<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Execute as seeds do banco de dados.
     */
    public function run(): void
    {

        // Limpa o cache para evitar problemas com permissões existentes

        // Limpa as permissões existentes antes de recriá-las
        Permission::query()->delete();
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
        Permission::create(['name' => 'documents.view.secret', 'guard_name' => 'web']);

        // Permissões para gerenciamento de comissões
        Permission::create(['name' => 'commissions.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'commissions.create', 'guard_name' => 'web']);
        Permission::create(['name' => 'commissions.edit', 'guard_name' => 'web']);
        Permission::create(['name' => 'commissions.delete', 'guard_name' => 'web']);

        // Permissões para gerenciamento de caixas
        Permission::create(['name' => 'boxes.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'boxes.create', 'guard_name' => 'web']);
        Permission::create(['name' => 'boxes.edit', 'guard_name' => 'web']);
        Permission::create(['name' => 'boxes.delete', 'guard_name' => 'web']);

    }
}
