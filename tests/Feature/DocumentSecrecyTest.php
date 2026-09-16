<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentSecrecyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_fresh_user_without_roles_cannot_list_documents(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('documents.index'));

        $response->assertForbidden();
    }

    public function test_user_without_secret_permission_does_not_see_secret_documents(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        Document::factory()->create(['title' => 'Doc Publico Visivel', 'confidentiality' => 'PÚBLICO']);
        Document::factory()->create(['title' => 'Doc Sigiloso Oculto', 'confidentiality' => 'RESERVADO']);

        $response = $this->actingAs($user)->get(route('documents.index'));

        $response->assertOk();
        $response->assertSee('Doc Publico Visivel');
        $response->assertDontSee('Doc Sigiloso Oculto');
    }

    public function test_admin_with_secret_permission_sees_all_documents(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Document::factory()->create(['title' => 'Doc Publico Visivel', 'confidentiality' => 'PÚBLICO']);
        Document::factory()->create(['title' => 'Doc Sigiloso Oculto', 'confidentiality' => 'RESERVADO']);

        $response = $this->actingAs($admin)->get(route('documents.index'));

        $response->assertOk();
        $response->assertSee('Doc Publico Visivel');
        $response->assertSee('Doc Sigiloso Oculto');
    }
}
