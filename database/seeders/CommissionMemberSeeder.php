<?php

namespace Database\Seeders;

use App\Models\CommissionMember;
use App\Models\User;
use Illuminate\Database\Seeder;

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
