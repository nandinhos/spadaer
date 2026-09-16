<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        Project::firstOrCreate(['code' => 'A-DARTER'], ['name' => 'PROJETO A-DARTER']);
        Project::firstOrCreate(['code' => 'MAR-1'], ['name' => 'PROJETO MAR-1']);
        Project::firstOrCreate(['code' => 'FX-39'], ['name' => 'PROJETO FX-39']);
        Project::firstOrCreate(['code' => 'E-99M'], ['name' => 'PROJETO E-99M']);
        Project::firstOrCreate(['code' => 'F5-BR'], ['name' => 'PROJETO F5-BR']);
        Project::firstOrCreate(['code' => 'AM-X'], ['name' => 'PROJETO AM-X']);
        Project::firstOrCreate(['code' => 'KC-390'], ['name' => 'PROJETO KC-390']);
        Project::firstOrCreate(['code' => 'KC-X'], ['name' => 'PROJETO KC-X']);
        Project::firstOrCreate(['code' => 'HX-BR'], ['name' => 'PROJETO HX-BR']);
        Project::firstOrCreate(['code' => 'I-X'], ['name' => 'PROJETO I-X']);
        Project::firstOrCreate(['code' => 'LINK-BR2'], ['name' => 'PROJETO LINK-BR2']);
        Project::firstOrCreate(['code' => 'TH-X'], ['name' => 'PROJETO TH-X']);
    }
}
