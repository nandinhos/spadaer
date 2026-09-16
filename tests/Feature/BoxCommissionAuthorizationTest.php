<?php

namespace Tests\Feature;

use App\Models\Box;
use App\Models\Commission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoxCommissionAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_fresh_user_without_roles_cannot_open_box_create(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('boxes.create'));

        $response->assertForbidden();
    }

    public function test_fresh_user_without_roles_cannot_delete_box(): void
    {
        $user = User::factory()->create();
        $box = Box::factory()->create();

        $response = $this->actingAs($user)->delete(route('boxes.destroy', $box));

        $response->assertForbidden();
        $this->assertDatabaseHas('boxes', ['id' => $box->id]);
    }

    public function test_fresh_user_without_roles_cannot_batch_destroy_boxes(): void
    {
        $user = User::factory()->create();
        $box = Box::factory()->create();

        $response = $this->actingAs($user)->delete(route('boxes.batch-destroy'), [
            'selected_boxes' => [$box->id],
        ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('boxes', ['id' => $box->id]);
    }

    public function test_fresh_user_without_roles_cannot_delete_commission(): void
    {
        $user = User::factory()->create();
        $commission = Commission::factory()->create();

        $response = $this->actingAs($user)->delete(route('commissions.destroy', $commission));

        $response->assertForbidden();
        $this->assertDatabaseHas('commissions', ['id' => $commission->id]);
    }

    public function test_user_role_keeps_box_create_access(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->get(route('boxes.create'));

        $response->assertOk();
    }

    public function test_president_without_box_delete_permission_is_denied(): void
    {
        $user = User::factory()->create();
        $user->assignRole('commission_president');
        $box = Box::factory()->create();

        $response = $this->actingAs($user)->delete(route('boxes.destroy', $box));

        $response->assertForbidden();
        $this->assertDatabaseHas('boxes', ['id' => $box->id]);
    }

    public function test_fresh_user_cannot_delete_commission_via_livewire(): void
    {
        $user = User::factory()->create();
        $commission = Commission::factory()->create();

        \Livewire\Livewire::actingAs($user)
            ->test(\App\Livewire\CommissionList::class)
            ->call('deleteCommission', $commission->id)
            ->assertForbidden();

        $this->assertDatabaseHas('commissions', ['id' => $commission->id]);
    }

    public function test_fresh_user_cannot_delete_project_via_livewire(): void
    {
        $user = User::factory()->create();
        $project = \App\Models\Project::factory()->create();

        \Livewire\Livewire::actingAs($user)
            ->test(\App\Livewire\ProjectList::class)
            ->call('deleteProject', $project->id)
            ->assertForbidden();

        $this->assertDatabaseHas('projects', ['id' => $project->id]);
    }

    public function test_box_search_matches_physical_location(): void
    {
        // Caminho real da busca: componente Livewire (a view ignora a query do controller).
        $user = User::factory()->create();
        Box::factory()->create(['number' => 'CX-0001', 'physical_location' => 'Galpao Norte Prateleira 7']);
        Box::factory()->create(['number' => 'CX-0002', 'physical_location' => 'Galpao Sul']);

        \Livewire\Livewire::actingAs($user)
            ->test(\App\Livewire\BoxList::class)
            ->set('search', 'Prateleira 7')
            ->assertSee('CX-0001')
            ->assertDontSee('CX-0002');
    }

    public function test_batch_destroy_documents_rejects_ids_from_another_box(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $box = Box::factory()->create();
        $otherBox = Box::factory()->create();
        $foreignDoc = \App\Models\Document::factory()->create(['box_id' => $otherBox->id]);

        $response = $this->actingAs($user)->delete(
            route('boxes.documents.batchDestroy', $box),
            ['document_ids' => [$foreignDoc->id]]
        );

        $response->assertRedirect(route('boxes.show', $box));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('documents', ['id' => $foreignDoc->id]);
    }
}
