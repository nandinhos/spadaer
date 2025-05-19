<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  public function run(): void
  {
    // 1. Criar Permissões (base para o sistema de autorização)
    $this->call(PermissionSeeder::class);

    // 2. Criar Papéis e atribuir permissões
    $this->call(RoleSeeder::class);

    // 3. Criar Usuários
    $this->call(UserSeeder::class);

    // 4. Criar Projetos
    $this->call(ProjectSeeder::class);

    // 5. Criar Caixas (precisa de Users e Projects)
    $this->call(BoxSeeder::class);

    // 6. Criar Documentos (precisa de Boxes e Projects)
    $this->call(DocumentSeeder::class);
  }
}
