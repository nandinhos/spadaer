<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Base de Autorização
        $this->call(PermissionSeeder::class);
        $this->call(RoleSeeder::class);

        // 2. Base de Usuários e Estrutura Organizacional
        $this->call(UserSeeder::class);
        $this->call(CommissionMemberSeeder::class); // Necessário para as caixas conferidas

        // 3. Estrutura de Negócio
        $this->call(ProjectSeeder::class);
        $this->call(BoxSeeder::class);

        // 4. Dados Transacionais
        $this->call(DocumentSeeder::class);
        $this->call(DocumentSecrecySeeder::class);
    }
}
