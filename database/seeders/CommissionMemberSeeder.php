<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\CommissionMember;

class CommissionMemberSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@example.com')->first();
        if ($adminUser) {
            CommissionMember::create([
                'user_id' => $adminUser->id,
                'role' => 'Presidente',
                'start_date' => now()->subYear(),
                'is_active' => true,
            ]);
        }
        // Criar outros membros se houver mais usuários...
    }
}
