<?php

namespace Tests\Feature\Security;

use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class DocumentSecrecyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Criar permissões necessárias
        Permission::create(['name' => 'documents.view.secret', 'guard_name' => 'web']);
        Permission::create(['name' => 'documents.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'documents.edit', 'guard_name' => 'web']);
        Permission::create(['name' => 'documents.delete', 'guard_name' => 'web']);
    }

    /** @test */
    public function any_user_can_view_ostensive_document()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('documents.view');

        $document = Document::factory()->create([
            'confidentiality' => 'OSTENSIVO',
        ]);

        $response = $this->actingAs($user)
            ->get(route('documents.show', $document));

        $response->assertStatus(200);
    }

    /** @test */
    public function user_without_permission_cannot_view_confidential_document()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('documents.view');

        $document = Document::factory()->create([
            'confidentiality' => 'CONFIDENCIAL',
        ]);

        $response = $this->actingAs($user)
            ->get(route('documents.show', $document));

        $response->assertStatus(403);
    }

    /** @test */
    public function user_with_permission_can_view_confidential_document()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('documents.view');
        $user->givePermissionTo('documents.view.secret');

        $document = Document::factory()->create([
            'confidentiality' => 'CONFIDENCIAL',
        ]);

        $response = $this->actingAs($user)
            ->get(route('documents.show', $document));

        $response->assertStatus(200);
    }

    /** @test */
    public function viewing_a_confidential_document_is_audited()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('documents.view');
        $user->givePermissionTo('documents.view.secret');

        $document = Document::factory()->create([
            'confidentiality' => 'CONFIDENCIAL',
        ]);

        $this->actingAs($user)
            ->get(route('documents.show', $document));

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'event' => 'viewed',
            'auditable_type' => Document::class,
            'auditable_id' => $document->id,
        ]);
    }
}
