<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\UserList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup initial roles and permissions
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_only_admins_can_access_user_management()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_can_list_users()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        User::factory()->count(5)->create();

        Livewire::actingAs($admin)
            ->test(UserList::class)
            ->assertViewHas('users', function ($users) {
                return $users->count() === 6; // admin + 5 users
            });
    }

    public function test_can_create_user()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Livewire::actingAs($admin)
            ->test(UserList::class)
            ->set('full_name', 'Test User Full')
            ->set('name', 'TESTUSER')
            ->set('rank', 'Cap')
            ->set('order_number', '1234567')
            ->set('email', 'test@fab.mil.br')
            ->set('password', 'password123')
            ->set('selectedRoles', ['user'])
            ->call('saveUser')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'email' => 'test@fab.mil.br',
            'full_name' => 'Test User Full',
        ]);

        $newUser = User::where('email', 'test@fab.mil.br')->first();
        $this->assertTrue($newUser->hasRole('user'));
    }

    public function test_can_create_user_with_direct_permissions()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Livewire::actingAs($admin)
            ->test(UserList::class)
            ->set('full_name', 'User with Perms')
            ->set('name', 'USERPERMS')
            ->set('rank', 'Cap')
            ->set('order_number', '7654321')
            ->set('email', 'perms@fab.mil.br')
            ->set('password', 'password123')
            ->set('selectedRoles', ['user'])
            ->set('selectedPermissions', ['documents.view', 'documents.create'])
            ->call('saveUser')
            ->assertHasNoErrors();

        $user = User::where('email', 'perms@fab.mil.br')->first();
        $this->assertTrue($user->hasDirectPermission('documents.view'));
        $this->assertTrue($user->hasDirectPermission('documents.create'));
    }

    public function test_can_edit_user()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create([
            'email' => 'old@fab.mil.br',
        ]);

        Livewire::actingAs($admin)
            ->test(UserList::class)
            ->call('openEditModal', $user->id)
            ->set('full_name', 'Updated Name')
            ->set('email', 'new@fab.mil.br')
            ->set('selectedRoles', ['admin'])
            ->call('saveUser')
            ->assertHasNoErrors();

        $user->refresh();
        $this->assertEquals('Updated Name', $user->full_name);
        $this->assertEquals('new@fab.mil.br', $user->email);
        $this->assertTrue($user->hasRole('admin'));
    }

    public function test_cannot_delete_self()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Livewire::actingAs($admin)
            ->test(UserList::class)
            ->call('deleteUser', $admin->id)
            ->assertSee('Você não pode excluir a si mesmo.');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_can_delete_other_user()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create();

        Livewire::actingAs($admin)
            ->test(UserList::class)
            ->call('deleteUser', $user->id);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
