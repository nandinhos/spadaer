<?php

namespace Tests\Feature\Admin;

use App\Livewire\DocumentList;
use App\Models\Document;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DocumentDeletionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_can_delete_document_with_observation()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $document = Document::factory()->create([
            'document_number' => 'DOC-123',
            'title' => 'Test Document'
        ]);

        Livewire::actingAs($admin)
            ->test(DocumentList::class)
            ->call('deleteDocument', $document->id, 'Deleção de teste')
            ->assertSet('selectedDocuments', []);

        $this->assertDatabaseMissing('documents', ['id' => $document->id]);
        
        // Verificar se o log de auditoria foi criado
        $this->assertDatabaseHas('audit_logs', [
            'event' => 'document_deleted',
            'auditable_type' => Document::class,
            'auditable_id' => $document->id,
        ]);
        
        $log = AuditLog::where('event', 'document_deleted')->first();
        $this->assertEquals('Deleção de teste', $log->new_values['reason']);
    }

    public function test_can_bulk_delete_documents_with_observation()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $docs = Document::factory()->count(3)->create();
        $ids = $docs->pluck('id')->map(fn($id) => (string)$id)->toArray();

        Livewire::actingAs($admin)
            ->test(DocumentList::class)
            ->set('selectedDocuments', $ids)
            ->call('batchDelete', 'Deleção em massa de teste')
            ->assertSet('selectedDocuments', []);

        foreach ($docs as $doc) {
            $this->assertDatabaseMissing('documents', ['id' => $doc->id]);
        }
    }
}
