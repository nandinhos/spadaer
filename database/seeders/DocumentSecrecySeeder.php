<?php

namespace Database\Seeders;

use App\Models\Box;
use App\Models\Document;
use App\Models\Project;
use Illuminate\Database\Seeder;

class DocumentSecrecySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Garante que temos pelo menos uma caixa e um projeto
        $box = Box::first() ?? Box::create([
            'number' => 'BOX-S7-001',
            'rack' => 'EST-01',
            'shelf' => 'PRAT-01',
        ]);

        $project = Project::first() ?? Project::create([
            'name' => 'PROJETO SIGILO SPRINT 7',
            'code' => 'SIGILO-S7',
        ]);

        $secrecyLevels = [
            'OSTENSIVO' => 'Documento de caráter público sobre o projeto.',
            'RESTRITO' => 'Manual de operação técnica interna.',
            'CONFIDENCIAL' => 'Relatório financeiro estratégico do trimestre.',
            'RESERVADO' => 'Dados de infraestrutura sensível.',
            'SECRETO' => 'Planos táticos de segurança perimetral.',
            'ULTRASSECRETO' => 'Protocolos de resposta a incidentes críticos.',
        ];

        foreach ($secrecyLevels as $level => $title) {
            Document::create([
                'box_id' => $box->id,
                'project_id' => $project->id,
                'document_number' => 'DOC-'.$level.'-'.rand(100, 999),
                'title' => $title,
                'document_date' => now()->format('d/m/Y'),
                'confidentiality' => $level,
                'item_number' => rand(1, 100),
                'code' => 'SPR7-'.substr($level, 0, 3),
            ]);
        }
    }
}
