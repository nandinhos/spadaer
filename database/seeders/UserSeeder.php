<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Importar lista de militares para a nova fase
        $usuarios = [
            ['idx' => 1, 'rank' => 'Cel Av', 'name' => 'Diogo Silva CASTILHO', 'login' => 'castilhodsc@fab.mil.br', 'order_number' => '3047512'],
            ['idx' => 2, 'rank' => 'Ten Cel Eng', 'name' => 'PAULO CÉSAR da Silva Guimarães', 'login' => 'paulocesarpcsg@fab.mil.br', 'order_number' => '3432831'],
            ['idx' => 3, 'rank' => 'Ten Cel Eng', 'name' => 'Francisco de MATTOS BRITO Junior', 'login' => 'mattosbritofmbj@fab.mil.br', 'order_number' => '3686515'],
            ['idx' => 4, 'rank' => 'Maj QOAV NTE', 'name' => 'Thiago Romeiro CAPUCHINHO', 'login' => 'capuchinhotrc@fab.mil.br', 'order_number' => '3490351'],
            ['idx' => 5, 'rank' => 'Cap Int', 'name' => 'Renan de LACERDA Lima Gonçalves', 'login' => 'lacerdarllg@fab.mil.br', 'order_number' => '4111281'],
        ];

        foreach ($usuarios as $userData) {
            // Extrai o nome de guerra (última palavra ou texto em maiúsculas)
            $parts = explode(' ', $userData['name']);
            $warName = end($parts);

            $user = User::updateOrCreate(
                ['email' => $userData['login']],
                [
                    'name' => $warName,
                    'rank' => $userData['rank'],
                    'full_name' => $userData['name'],
                    'order_number' => $userData['order_number'],
                    'email' => $userData['login'],
                    'password' => Hash::make((string) $userData['order_number']),
                ]
            );

            $user->assignRole('user');
        }

        // 2. Criar Usuário Administrador
        $admin = User::firstOrCreate(
            ['email' => 'admin@fab.mil.br'],
            [
                'name' => 'ADMIN',
                'rank' => 'Cel Av',
                'full_name' => 'ADMINISTRADOR DO SISTEMA',
                'order_number' => '0000000',
                'password' => Hash::make('admin123'),
            ]
        );
        $admin->syncRoles(['admin']);
    }
}
