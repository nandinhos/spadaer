<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
         User::factory()->create([
             'name' => 'Nando Dev',
             'email' => 'nandinhos@gmail.com',
         ]);
         $this->call(DocumentSeeder::class); // Adicione esta linha
    }
}