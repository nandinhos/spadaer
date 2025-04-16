<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Document;
use Illuminate\Support\Carbon;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        $documents = [
            ['box_number' => '001', 'item_number' => '01', 'code' => 'ADM-001', 'descriptor' => 'Administrativo', 'document_number' => 'DOC2023-001', 'title' => 'Relatório Anual de Atividades', 'document_date' => '2023-12-15', 'project' => 'Gestão Corporativa', 'confidentiality' => 'Público', 'version' => '1.0', 'is_copy' => false ],
            ['box_number' => '001', 'item_number' => '02', 'code' => 'ADM-002', 'descriptor' => 'Administrativo', 'document_number' => 'DOC2023-002', 'title' => 'Plano Estratégico 2023', 'document_date' => '2023-01-10', 'project' => 'Gestão Corporativa', 'confidentiality' => 'Restrito', 'version' => '2.1', 'is_copy' => false ],
            ['box_number' => '001', 'item_number' => '03', 'code' => 'FIN-001', 'descriptor' => 'Financeiro', 'document_number' => 'DOC2023-010', 'title' => 'Balancete Mensal - Janeiro', 'document_date' => '2023-02-05', 'project' => 'Finanças', 'confidentiality' => 'Restrito', 'version' => '1.0', 'is_copy' => false ],
            ['box_number' => '002', 'item_number' => '01', 'code' => 'RH-001', 'descriptor' => 'Recursos Humanos', 'document_number' => 'DOC2023-015', 'title' => 'Política de Contratação', 'document_date' => '2023-03-20', 'project' => 'RH', 'confidentiality' => 'Público', 'version' => '3.0', 'is_copy' => true ],
            ['box_number' => '002', 'item_number' => '02', 'code' => 'RH-002', 'descriptor' => 'Recursos Humanos', 'document_number' => 'DOC2023-016', 'title' => 'Manual do Funcionário', 'document_date' => '2023-04-12', 'project' => 'RH', 'confidentiality' => 'Público', 'version' => '2.5', 'is_copy' => false ],
            ['box_number' => '002', 'item_number' => '03', 'code' => 'RH-003', 'descriptor' => 'Recursos Humanos', 'document_number' => 'DOC2022-050', 'title' => 'Relatório de Desempenho Anual', 'document_date' => '2022-12-10', 'project' => 'RH', 'confidentiality' => 'Confidencial', 'version' => '1.0', 'is_copy' => false ],
            ['box_number' => '003', 'item_number' => '01', 'code' => 'PROJ-001', 'descriptor' => 'Projetos', 'document_number' => 'DOC2022-100', 'title' => 'Proposta Técnica - Expansão', 'document_date' => '2022-06-15', 'project' => 'Expansão Norte', 'confidentiality' => 'Confidencial', 'version' => '1.0', 'is_copy' => false ],
            ['box_number' => '003', 'item_number' => '02', 'code' => 'PROJ-002', 'descriptor' => 'Projetos', 'document_number' => 'DOC2022-101', 'title' => 'Cronograma de Implementação', 'document_date' => '2022-07-01', 'project' => 'Expansão Norte', 'confidentiality' => 'Restrito', 'version' => '2.0', 'is_copy' => true ],
            ['box_number' => '003', 'item_number' => '03', 'code' => 'PROJ-003', 'descriptor' => 'Projetos', 'document_number' => 'DOC2022-102', 'title' => 'Análise de Riscos', 'document_date' => '2022-07-15', 'project' => 'Expansão Norte', 'confidentiality' => 'Restrito', 'version' => '1.5', 'is_copy' => false ],
            ['box_number' => '004', 'item_number' => '01', 'code' => 'TEC-001', 'descriptor' => 'Tecnologia', 'document_number' => 'DOC2021-200', 'title' => 'Especificações Técnicas - Sistema ERP', 'document_date' => '2021-11-10', 'project' => 'Modernização TI', 'confidentiality' => 'Público', 'version' => '1.0', 'is_copy' => false ],
            ['box_number' => '004', 'item_number' => '02', 'code' => 'TEC-002', 'descriptor' => 'Tecnologia', 'document_number' => 'DOC2021-201', 'title' => 'Plano de Migração de Dados', 'document_date' => '2021-12-05', 'project' => 'Modernização TI', 'confidentiality' => 'Restrito', 'version' => '1.2', 'is_copy' => false ],
            ['box_number' => '005', 'item_number' => '01', 'code' => 'JUR-001', 'descriptor' => 'Jurídico', 'document_number' => 'DOC2020-300', 'title' => 'Contrato de Prestação de Serviços', 'document_date' => '2020-05-20', 'project' => 'Jurídico', 'confidentiality' => 'Confidencial', 'version' => '1.0', 'is_copy' => true ],
            ['box_number' => '005', 'item_number' => '02', 'code' => 'JUR-002', 'descriptor' => 'Jurídico', 'document_number' => 'DOC2020-301', 'title' => 'Parecer Legal - Propriedade Intelectual', 'document_date' => '2020-06-15', 'project' => 'Jurídico', 'confidentiality' => 'Confidencial', 'version' => '1.0', 'is_copy' => false ],
            ['box_number' => '005', 'item_number' => '03', 'code' => 'JUR-003', 'descriptor' => 'Jurídico', 'document_number' => 'DOC2020-302', 'title' => 'Política de Compliance', 'document_date' => '2020-07-01', 'project' => 'Jurídico', 'confidentiality' => 'Público', 'version' => '2.0', 'is_copy' => false ],
            ['box_number' => '006', 'item_number' => '01', 'code' => 'MKT-001', 'descriptor' => 'Marketing', 'document_number' => 'DOC2022-400', 'title' => 'Plano de Marketing 2022', 'document_date' => '2022-01-15', 'project' => 'Marketing Digital', 'confidentiality' => 'Restrito', 'version' => '1.0', 'is_copy' => false ],
            ['box_number' => '006', 'item_number' => '02', 'code' => 'MKT-002', 'descriptor' => 'Marketing', 'document_number' => 'DOC2022-401', 'title' => 'Análise de Mercado', 'document_date' => '2022-02-10', 'project' => 'Marketing Digital', 'confidentiality' => 'Público', 'version' => '1.0', 'is_copy' => true ],
            ['box_number' => '006', 'item_number' => '03', 'code' => 'MKT-003', 'descriptor' => 'Marketing', 'document_number' => 'DOC2022-402', 'title' => 'Estratégia de Mídias Sociais', 'document_date' => '2022-03-05', 'project' => 'Marketing Digital', 'confidentiality' => 'Público', 'version' => '2.1', 'is_copy' => false ],
            ['box_number' => '007', 'item_number' => '01', 'code' => 'OPS-001', 'descriptor' => 'Operações', 'document_number' => 'DOC2021-500', 'title' => 'Manual de Processos', 'document_date' => '2021-05-10', 'project' => 'Otimização', 'confidentiality' => 'Público', 'version' => '3.0', 'is_copy' => false ],
            ['box_number' => '007', 'item_number' => '02', 'code' => 'OPS-002', 'descriptor' => 'Operações', 'document_number' => 'DOC2021-501', 'title' => 'Protocolo de Segurança', 'document_date' => '2021-06-15', 'project' => 'Otimização', 'confidentiality' => 'Restrito', 'version' => '1.5', 'is_copy' => false ],
            ['box_number' => '007', 'item_number' => '03', 'code' => 'OPS-003', 'descriptor' => 'Operações', 'document_number' => 'DOC2021-502', 'title' => 'Relatório de Produtividade', 'document_date' => '2021-07-01', 'project' => 'Otimização', 'confidentiality' => 'Confidencial', 'version' => '1.0', 'is_copy' => true ],
         ];

         foreach ($documents as $doc) {
             // Converte data e adiciona timestamps
             $doc['document_date'] = Carbon::parse($doc['document_date']);
             $doc['created_at'] = now();
             $doc['updated_at'] = now();
             Document::insert($doc);
         }
    }
}