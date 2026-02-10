<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\UserList;
use App\Models\AuditLog;
use App\Models\Commission;
use App\Models\CommissionMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Models\Role;
use Tests\TestCase;

class GlobalAuditTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup admin user with permissions
        $this->admin = User::factory()->create();
        $role = Role::create(['name' => 'admin']);
        $this->admin->assignRole($role);
    }

    /** @test */
    public function user_creation_is_audited(): void
    {
        $userData = [
            'name' => 'New User',
            'full_name' => 'New Full Name',
            'rank' => 'Soldado',
            'order_number' => '12345',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'selectedRoles' => ['user'],
        ];

        // Ensure role exists
        Role::create(['name' => 'user']);

        Livewire::actingAs($this->admin)
            ->test(UserList::class)
            ->set('name', $userData['name'])
            ->set('full_name', $userData['full_name'])
            ->set('rank', $userData['rank'])
            ->set('order_number', $userData['order_number'])
            ->set('email', $userData['email'])
            ->set('password', $userData['password'])
            ->set('selectedRoles', $userData['selectedRoles'])
            ->call('saveUser');

        $this->assertDatabaseHas('audit_logs', [
            'event' => 'created',
            'auditable_type' => User::class,
        ]);
    }

    /** @test */
    public function user_update_is_audited(): void
    {
        $user = User::factory()->create(['name' => 'Old Name']);
        $user->assignRole(Role::create(['name' => 'editor_update']));

        Livewire::actingAs($this->admin)
            ->test(UserList::class)
            ->call('openEditModal', $user->id)
            ->set('name', 'Updated Name')
            ->call('saveUser')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('audit_logs', [
            'event' => 'updated',
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
        ]);

        // Opcional: Verificar conteúdo específico do JSON se necessário
        $log = AuditLog::where('auditable_id', $user->id)->where('event', 'updated')->first();
        $this->assertEquals('Updated Name', $log->new_values['name']);
    }

    /** @test */
    public function role_assignment_changes_are_audited(): void
    {
        $user = User::factory()->create();
        Role::create(['name' => 'editor']);

        Livewire::actingAs($this->admin)
            ->test(UserList::class)
            ->call('openEditModal', $user->id)
            ->set('selectedRoles', ['editor', 'admin'])
            ->call('saveUser');

        // Deve existir log manual de 'roles_synced' (conforme implementado no UserList)
        $this->assertDatabaseHas('audit_logs', [
            'event' => 'roles_synced',
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
        ]);
    }

    /** @test */
    public function commission_member_actions_are_audited(): void
    {
        $commission = Commission::factory()->create();
        $user = User::factory()->create();

        // Criando membro via Eloquent diretamente para testar o trait
        CommissionMember::create([
            'user_id' => $user->id,
            'commission_id' => $commission->id,
            'role' => 'member',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'event' => 'created',
            'auditable_type' => CommissionMember::class,
        ]);
    }
}
