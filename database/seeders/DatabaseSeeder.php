<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  public function run(): void
  {
    //User::factory()->create([
    //  'name' => 'Nando Dev',
    //'email' => 'nandinhos@gmail.com',
    //]);
    //$this->call(DocumentSeeder::class);

    // 1. Criar Usuários
    $this->call(UserSeeder::class); // Adiciona o seeder de usuários

    // 2. Criar Projetos
    $this->call(ProjectSeeder::class);

    // 3. Criar Caixas (precisa de Users e Projects)
    $this->call(BoxSeeder::class);

    // 4. Criar Documentos (precisa de Boxes e Projects)
    $this->call(DocumentSeeder::class);
  }
}
