<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Document;
use App\Models\Box;
use App\Models\Project;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log; // Para logging opcional

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buscar as Caixas pelos seus números para obter os IDs
        $boxAD001 = Box::where('number', 'AD001')->first();
        $boxMR001 = Box::where('number', 'MR001')->first();
        $boxFX001 = Box::where('number', 'FX001')->first();
        $boxE9901 = Box::where('number', 'E9901')->first();
        $boxF5001 = Box::where('number', 'F5001')->first();
        $boxADM01 = Box::where('number', 'ADM01')->first(); // Caixa sem projeto
        $boxKC390A = Box::where('number', 'KC390-A')->first();
        $boxKC390B = Box::where('number', 'KC390-B')->first();
        $boxHXBR001 = Box::where('number', 'HXBR-001')->first();
        // Adicione outras caixas se necessário

        // 2. Buscar os Projetos pelos seus códigos para obter os IDs
        $projectAdarter = Project::where('code', 'A-DARTER')->first();
        $projectMar1    = Project::where('code', 'MAR-1')->first();
        $projectFx39    = Project::where('code', 'FX-39')->first();
        $projectE99m    = Project::where('code', 'E-99M')->first();
        $projectF5br    = Project::where('code', 'F5-BR')->first();
        $projectKc390   = Project::where('code', 'KC-390')->first();
        $projectHxbr    = Project::where('code', 'HX-BR')->first();
        // Adicione outros projetos se necessário

        // 3. Definir os dados dos Documentos a serem criados, usando os IDs encontrados
        $documentsData = [
            // --- Documentos Projeto A-DARTER (Caixa AD001) ---
            [
                'box_id' => $boxAD001?->id,
                'item_number' => '001', // Sequencial dentro da caixa
                'code' => 'ADRT-REQ-001', // Código do documento
                'descriptor' => 'Requisitos Técnicos', // Descritor/Tipo
                'document_number' => 'DART-24-REQ-001', // Número único do documento
                'title' => 'Especificação de Requisitos de Sistema A-Darter Bloco II',
                'document_date' => '2024-01-15',
                'project_id' => $projectAdarter?->id, // ID do projeto
                'confidentiality' => 'Restrito',
                'version' => '1.0',
                'is_copy' => false,
            ],
            [
                'box_id' => $boxAD001?->id,
                'item_number' => '002',
                'code' => 'ADRT-TEST-005',
                'descriptor' => 'Relatório de Teste',
                'document_number' => 'DART-24-TST-005',
                'title' => 'Resultados dos Testes de Voo - Ensaio #5',
                'document_date' => '2024-03-20',
                'project_id' => $projectAdarter?->id,
                'confidentiality' => 'Confidencial',
                'version' => '1.1',
                'is_copy' => false,
            ],

            // --- Documentos Projeto MAR-1 (Caixa MR001) ---
            [
                'box_id' => $boxMR001?->id,
                'item_number' => '001',
                'code' => 'MAR1-SPEC-001',
                'descriptor' => 'Especificação Técnica',
                'document_number' => 'MAR1-23-SPEC-001',
                'title' => 'Especificação Detalhada do Sistema de Guiagem MAR-1',
                'document_date' => '2023-05-10',
                'project_id' => $projectMar1?->id,
                'confidentiality' => 'Restrito',
                'version' => '2.1',
                'is_copy' => false,
            ],
            [
                'box_id' => $boxMR001?->id,
                'item_number' => '002',
                'code' => 'MAR1-PLAN-001',
                'descriptor' => 'Plano de Projeto',
                'document_number' => 'MAR1-23-PLAN-001',
                'title' => 'Plano de Gerenciamento do Projeto MAR-1',
                'document_date' => '2023-02-01',
                'project_id' => $projectMar1?->id,
                'confidentiality' => 'Restrito',
                'version' => '1.0',
                'is_copy' => true,
            ],


            // --- Documentos Projeto FX-39 (Caixa FX001) ---
            [
                'box_id' => $boxFX001?->id,
                'item_number' => '001',
                'code' => 'FX39-CTR-001',
                'descriptor' => 'Contrato',
                'document_number' => 'FX39-23-CTR-001',
                'title' => 'Contrato Principal de Aquisição das Aeronaves Gripen E/F',
                'document_date' => '2023-04-15',
                'project_id' => $projectFx39?->id,
                'confidentiality' => 'Confidencial',
                'version' => '1.0 Rev A',
                'is_copy' => false,
            ],
            [
                'box_id' => $boxFX001?->id,
                'item_number' => '002',
                'code' => 'FX39-RELTEC-001',
                'descriptor' => 'Relatório Técnico',
                'document_number' => 'FX39-24-REL-008',
                'title' => 'Relatório de Acompanhamento Técnico - Trimestre 1/2024',
                'document_date' => '2024-04-30',
                'project_id' => $projectFx39?->id,
                'confidentiality' => 'Restrito',
                'version' => '1.0',
                'is_copy' => false,
            ],

            // --- Documentos Projeto E-99M (Caixa E9901) ---
            [
                'box_id' => $boxE9901?->id,
                'item_number' => '001',
                'code' => 'E99M-MOD-005',
                'descriptor' => 'Proposta de Modificação',
                'document_number' => 'E99M-22-MOD-005',
                'title' => 'Documentação Técnica da Modernização do Radar Erieye',
                'document_date' => '2022-11-01',
                'project_id' => $projectE99m?->id,
                'confidentiality' => 'Restrito',
                'version' => '1.2',
                'is_copy' => false,
            ],

            // --- Documentos Projeto F5-BR (Caixa F5001) ---
            [
                'box_id' => $boxF5001?->id,
                'item_number' => '001',
                'code' => 'F5BR-MAN-001',
                'descriptor' => 'Manual Técnico',
                'document_number' => 'F5BR-21-MAN-001',
                'title' => 'Manual de Operação e Manutenção da Aeronave F-5BR (Modernizado)',
                'document_date' => '2021-08-10',
                'project_id' => $projectF5br?->id,
                'confidentiality' => 'Público',
                'version' => '3.0',
                'is_copy' => false,
            ],

            // --- Documentos Administrativos (Caixa ADM01 - Sem Projeto) ---
            [
                'box_id' => $boxADM01?->id,
                'item_number' => '001',
                'code' => 'ADM-CIRC-015',
                'descriptor' => 'Circular Interna',
                'document_number' => 'ADM-24-CIRC-015',
                'title' => 'Normas para Utilização do Refeitório',
                'document_date' => '2024-06-01',
                'project_id' => null, // Sem projeto associado
                'confidentiality' => 'Público',
                'version' => '1.0',
                'is_copy' => false,
            ],
            [
                'box_id' => $boxADM01?->id,
                'item_number' => '002',
                'code' => 'ADM-MEMO-102',
                'descriptor' => 'Memorando',
                'document_number' => 'ADM-24-MEMO-102',
                'title' => 'Solicitação de Compra de Material de Escritório',
                'document_date' => '2024-05-28',
                'project_id' => null,
                'confidentiality' => 'Público',
                'version' => '1.0',
                'is_copy' => true,
            ],

            // --- Documentos Projeto KC-390 (Caixas KC390-A e KC390-B) ---
            [
                'box_id' => $boxKC390A?->id,
                'item_number' => '001',
                'code' => 'KC390-CERT-010',
                'descriptor' => 'Certificação',
                'document_number' => 'KC390-22-CERT-010',
                'title' => 'Relatório Final de Certificação de Tipo ANAC',
                'document_date' => '2022-10-20',
                'project_id' => $projectKc390?->id,
                'confidentiality' => 'Público',
                'version' => 'Final',
                'is_copy' => false,
            ],
            [
                'box_id' => $boxKC390B?->id,
                'item_number' => '001',
                'code' => 'KC390-OPER-005',
                'descriptor' => 'Relatório Operacional',
                'document_number' => 'KC390-23-OPER-005',
                'title' => 'Avaliação Operacional - Lançamento de Cargas',
                'document_date' => '2023-09-12',
                'project_id' => $projectKc390?->id,
                'confidentiality' => 'Restrito',
                'version' => '1.0',
                'is_copy' => false,
            ],
            [
                'box_id' => $boxKC390B?->id,
                'item_number' => '002',
                'code' => 'KC390-MANUT-015',
                'descriptor' => 'Procedimento Manutenção',
                'document_number' => 'KC390-23-MANUT-015',
                'title' => 'Procedimento de Inspeção Estrutural - 500 horas',
                'document_date' => '2023-11-05',
                'project_id' => $projectKc390?->id,
                'confidentiality' => 'Restrito',
                'version' => '2.0',
                'is_copy' => false,
            ],

            // --- Documentos Projeto HX-BR (Caixa HXBR-001) ---
            [
                'box_id' => $boxHXBR001?->id,
                'item_number' => '001',
                'code' => 'HXBR-REQ-001',
                'descriptor' => 'Requisito Logístico',
                'document_number' => 'HXBR-20-REQ-001',
                'title' => 'Requisitos de Suporte Logístico Integrado (SLI) H225M',
                'document_date' => '2020-11-15',
                'project_id' => $projectHxbr?->id,
                'confidentiality' => 'Confidencial',
                'version' => '1.0',
                'is_copy' => false,
            ],
        ];

        // 4. Preparar e Inserir os dados no Banco
        $insertData = [];
        $count = 0;
        foreach ($documentsData as $doc) {
            // Validação Mínima: Garante que a caixa associada existe no banco
            if (empty($doc['box_id'])) {
                Log::warning("DocumentSeeder: Caixa não encontrada para o documento (pulando): " . ($doc['document_number'] ?? json_encode($doc)));
                continue; // Pula este documento se a caixa não foi encontrada
            }
            // Validação Mínima: Garante que o projeto associado existe (se não for nulo)
            if (isset($doc['project_id']) && $doc['project_id'] !== null && Project::find($doc['project_id']) === null) {
                Log::warning("DocumentSeeder: Projeto ID {$doc['project_id']} não encontrado para o documento (definindo como null): " . ($doc['document_number'] ?? json_encode($doc)));
                $doc['project_id'] = null; // Define como null se o projeto não existe
            }


            // Converte data e adiciona timestamps
            try {
                $doc['document_date'] = Carbon::parse($doc['document_date']);
            } catch (\Exception $e) {
                Log::error("DocumentSeeder: Data inválida para o documento (definindo como null): " . ($doc['document_number'] ?? json_encode($doc)) . " Data: " . $doc['document_date']);
                $doc['document_date'] = null; // Define como null se a data for inválida
            }
            $doc['created_at'] = now();
            $doc['updated_at'] = now();

            $insertData[] = $doc; // Adiciona ao array para inserção em lote
            $count++;
        }

        // Inserir os dados em lotes (chunks) para melhor performance se houver muitos dados
        foreach (array_chunk($insertData, 200) as $chunk) {
            Document::insert($chunk); // Usa insert para performance (não dispara eventos de model)
            // Alternativa: usar create() dentro do loop (dispara eventos, mais lento para muitos dados)
            // foreach ($chunk as $docToCreate) {
            //    Document::create($docToCreate);
            // }
        }

        $this->command->info("DocumentSeeder: {$count} documentos criados com sucesso!");
    }
}
