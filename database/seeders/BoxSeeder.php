<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Box;
use App\Models\Project;
use App\Models\CommissionMember;
use App\Models\User; // Necessário para encontrar o membro da comissão via usuário

class BoxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buscar os Projetos pelos códigos para obter seus IDs
        //    Usamos first() assumindo que os códigos são únicos e os projetos existem (o ProjectSeeder rodou antes)
        $projectAdarter = Project::where('code', 'A-DARTER')->first();
        $projectMar1    = Project::where('code', 'MAR-1')->first();
        $projectFx39    = Project::where('code', 'FX-39')->first();
        $projectE99m    = Project::where('code', 'E-99M')->first();
        $projectF5br    = Project::where('code', 'F5-BR')->first();
        $projectAmx     = Project::where('code', 'AM-X')->first();
        $projectKc390   = Project::where('code', 'KC-390')->first();
        $projectKcx     = Project::where('code', 'KC-X')->first(); // Adicionei caso precise
        $projectHxbr    = Project::where('code', 'HX-BR')->first(); // Adicionei caso precise
        // ... continue buscando outros projetos se for associá-los a caixas específicas

        // 2. Buscar um Membro da Comissão exemplo para usar como conferente
        //    Vamos assumir que o 'admin@example.com' é um membro da comissão ativo
        $adminUser = User::where('email', 'admin@example.com')->first();
        $checkerMember = null;
        if ($adminUser) {
            // Busca o registro CommissionMember associado ao usuário admin
            $checkerMember = CommissionMember::where('user_id', $adminUser->id)->first();
        }

        // 3. Definir os dados das Caixas a serem criadas
        $boxesData = [
            // Caixa 1: Projeto A-DARTER, conferida pelo admin
            [
                'number' => 'AD001', // Use um padrão de número de caixa
                'physical_location' => 'Prateleira A-1 / Nível 1',
                'project_id' => $projectAdarter?->id, // Usa o ID do projeto A-DARTER
                'commission_member_id' => $checkerMember?->id, // Usa o ID do membro conferente
                'conference_date' => $checkerMember ? now()->subDays(20) : null, // Define data só se houver conferente
            ],
            // Caixa 2: Projeto MAR-1, não conferida
            [
                'number' => 'MR001',
                'physical_location' => 'Prateleira B-3 / Nível 2',
                'project_id' => $projectMar1?->id,
                'commission_member_id' => null,
                'conference_date' => null,
            ],
            // Caixa 3: Projeto FX-39, conferida
            [
                'number' => 'FX001',
                'physical_location' => 'Armário C-1 / Gaveta 1',
                'project_id' => $projectFx39?->id,
                'commission_member_id' => $checkerMember?->id,
                'conference_date' => $checkerMember ? now()->subDays(5) : null,
            ],
            // Caixa 4: Projeto E-99M, não conferida
            [
                'number' => 'E9901',
                'physical_location' => 'Arquivo Deslizante 1 / Fila A',
                'project_id' => $projectE99m?->id,
                'commission_member_id' => null,
                'conference_date' => null,
            ],
            // Caixa 5: Projeto F5-BR, conferida
            [
                'number' => 'F5001',
                'physical_location' => 'Prateleira A-1 / Nível 2',
                'project_id' => $projectF5br?->id,
                'commission_member_id' => $checkerMember?->id,
                'conference_date' => $checkerMember ? now()->subDays(2) : null,
            ],
            // Caixa 6: Sem projeto específico (Administrativo), não conferida
            [
                'number' => 'ADM01',
                'physical_location' => 'Sala Administração / Arquivo Corrente',
                'project_id' => null, // Sem projeto associado
                'commission_member_id' => null,
                'conference_date' => null,
            ],
            // Caixa 7: Projeto KC-390, não conferida
            [
                'number' => 'KC390-A',
                'physical_location' => 'Hangar 2 / Setor K',
                'project_id' => $projectKc390?->id,
                'commission_member_id' => null,
                'conference_date' => null,
            ],
            // Caixa 8: Projeto KC-390, conferida
            [
                'number' => 'KC390-B',
                'physical_location' => 'Hangar 2 / Setor L',
                'project_id' => $projectKc390?->id,
                'commission_member_id' => $checkerMember?->id,
                'conference_date' => $checkerMember ? now()->subDays(30) : null,
            ],
            // Caixa 9: Projeto HX-BR, não conferida
            [
                'number' => 'HXBR-001',
                'physical_location' => 'Prateleira D-5 / Nível 3',
                'project_id' => $projectHxbr?->id,
                'commission_member_id' => null,
                'conference_date' => null,
            ],
        ];

        // 4. Criar as Caixas no Banco de Dados
        foreach ($boxesData as $boxData) {
            // Verifica se o projeto foi encontrado antes de tentar usar o ID (opcional, mas seguro)
            // Se project_id for null no array $boxesData, ele simplesmente será inserido como null.
            // A verificação é mais útil se você *não* quisesse criar a caixa se o projeto não existisse.
            // if (isset($boxData['project_id']) || $boxData['project_id'] === null) {
            Box::create($boxData);
            // } else {
            // Pode logar um aviso se um projeto esperado não foi encontrado
            // Log::warning("Project not found for box data: " . json_encode($boxData));
            // }
        }

        // Opcional: Mensagem de confirmação no console
        $this->command->info('BoxSeeder executado com sucesso!');
    }
}
