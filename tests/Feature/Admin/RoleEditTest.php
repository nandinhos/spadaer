<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\RoleEdit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Models\Role;
use Tests\TestCase;

class RoleEditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_only_admins_can_access_role_edit_page()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create();
        $user->assignRole('user');

        $role = Role::findByName('commission_president');

        $this->actingAs($admin)
            ->get(route('admin.roles.edit', $role->id))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('admin.roles.edit', $role->id))
            ->assertForbidden();
    }

    public function test_can_update_role_permissions_on_page()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $role = Role::create(['name' => 'editor']);

        Livewire::actingAs($admin)
            ->test(RoleEdit::class, ['role' => $role])
            ->set('rolePermissions', ['documents.view', 'documents.edit'])
            ->call('save')
            ->assertRedirect(route('admin.roles.index'));

        $this->assertTrue($role->fresh()->hasPermissionTo('documents.view'));
        $this->assertTrue($role->fresh()->hasPermissionTo('documents.edit'));
    }

    public function test_cannot_rename_system_roles_on_page()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $adminRole = Role::findByName('admin');

        Livewire::actingAs($admin)
            ->test(RoleEdit::class, ['role' => $adminRole])
            ->set('roleName', 'root')
            ->call('save');

        $this->assertEquals('admin', $adminRole->fresh()->name);
    }
}
