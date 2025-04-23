<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        Project::create(['name' => 'PROJETO A-DARTER', 'code' => 'A-DARTER']);
        Project::create(['name' => 'PROJETO MAR-1', 'code' => 'MAR-1']);
        Project::create(['name' => 'PROJETO FX-39', 'code' => 'FX-39']);
        Project::create(['name' => 'PROJETO E-99M', 'code' => 'E-99M']);
        Project::create(['name' => 'PROJETO F5-BR', 'code' => 'F5-BR']);
        Project::create(['name' => 'PROJETO AM-X', 'code' => 'AM-X']);
        Project::create(['name' => 'PROJETO KC-390', 'code' => 'KC-390']);
        Project::create(['name' => 'PROJETO KC-X', 'code' => 'KC-X']);
        Project::create(['name' => 'PROJETO HX-BR', 'code' => 'HX-BR']);
        Project::create(['name' => 'PROJETO I-X', 'code' => 'I-X']);
        Project::create(['name' => 'PROJETO LINK-BR2', 'code' => 'LINK-BR2']);
        Project::create(['name' => 'PROJETO TH-X', 'code' => 'TH-X']);
    }
}
