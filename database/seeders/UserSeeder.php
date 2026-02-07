<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Criar usuários de teste padrão para verificação
        $testUsers = [
            [
                'name' => 'Admin',
                'rank' => 'Cel',
                'full_name' => 'Administrador do Sistema',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin'
            ],
            [
                'name' => 'Presidente',
                'rank' => 'Ten Cel',
                'full_name' => 'Presidente da Comissão',
                'email' => 'presidente@example.com',
                'password' => Hash::make('password'),
                'role' => 'commission_president'
            ],
            [
                'name' => 'Membro',
                'rank' => 'Maj',
                'full_name' => 'Membro da Comissão',
                'email' => 'membro@example.com',
                'password' => Hash::make('password'),
                'role' => 'commission_member'
            ],
            [
                'name' => 'Usuario',
                'rank' => 'Cap',
                'full_name' => 'Usuário Padrão',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'role' => 'user'
            ],
        ];

        foreach ($testUsers as $u) {
            $user = User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'rank' => $u['rank'],
                    'full_name' => $u['full_name'],
                    'password' => $u['password'],
                    'order_number' => rand(1000000, 9999999),
                ]
            );
            $user->assignRole($u['role']);
        }

        // 2. Importar lista de militares (opcional - mantendo original)
        $usuarios = [
            ['idx' => 1, 'rank' => "Cel Av", 'name' => "Diogo Silva CASTILHO", 'login' => "castilhodsc@fab.mil.br", 'order_number' => "3047512"],
            ['idx' => 2, 'rank' => "Ten Cel Eng", 'name' => "PAULO CÉSAR da Silva Guimarães", 'login' => "paulocesarpcsg@fab.mil.br", 'order_number' => "3432831"],
            ['idx' => 3, 'rank' => "Ten Cel Eng", 'name' => "Francisco de MATTOS BRITO Junior", 'login' => "mattosbritofmbj@fab.mil.br", 'order_number' => "3686515"],
            ['idx' => 4, 'rank' => "Maj QOAV NTE", 'name' => "Thiago Romeiro CAPUCHINHO", 'login' => "capuchinhotrc@fab.mil.br", 'order_number' => "3490351"],
            ['idx' => 5, 'rank' => "Cap Int", 'name' => "Renan de LACERDA Lima Gonçalves", 'login' => "lacerdarllg@fab.mil.br", 'order_number' => "4111281"],
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
                    'password' => Hash::make((string)$userData['order_number']),
                ]
            );

            $user->assignRole('user');
        }
    }
}
