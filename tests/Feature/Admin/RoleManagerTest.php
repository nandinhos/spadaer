<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\RoleManager;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Models\Role;
use Tests\TestCase;

class RoleManagerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_only_admins_can_access_role_manager()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($admin)
            ->get(route('admin.roles.index'))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('admin.roles.index'))
            ->assertForbidden();
    }

    public function test_role_manager_lists_roles()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Livewire::actingAs($admin)
            ->test(RoleManager::class)
            ->assertSee('admin')
            ->assertSee('commission_president')
            ->assertSee('commission_member');
    }

    public function test_can_initiate_new_role_creation()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Livewire::actingAs($admin)
            ->test(RoleManager::class)
            ->set('roleName', 'new_role')
            ->call('saveNewRole')
            ->assertRedirect(route('admin.roles.edit', Role::findByName('new_role')->id));

        $this->assertDatabaseHas('roles', ['name' => 'new_role']);
    }

    public function test_can_delete_role()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $role = Role::create(['name' => 'temporary_role']);

        Livewire::actingAs($admin)
            ->test(RoleManager::class)
            ->call('deleteRole', $role->id);

        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    public function test_cannot_delete_system_roles()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $adminRole = Role::findByName('admin');

        Livewire::actingAs($admin)
            ->test(RoleManager::class)
            ->call('deleteRole', $adminRole->id)
            ->assertSee('Papéis básicos do sistema não podem ser excluídos.');

        $this->assertDatabaseHas('roles', ['id' => $adminRole->id]);
    }
}
