<?php

namespace Tests\Feature\Admin;

use App\Models\AuditLog;
use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function document_update_records_old_and_new_values(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        $document = Document::factory()->create(['title' => 'Titulo Antes']);
        AuditLog::query()->delete();

        $document->update(['title' => 'Titulo Depois']);

        $log = AuditLog::where('event', 'updated')
            ->where('auditable_type', Document::class)
            ->where('auditable_id', $document->id)
            ->first();

        $this->assertNotNull($log, 'Nenhum audit_log de updated foi registrado.');
        $this->assertSame('Titulo Antes', $log->old_values['title'] ?? null);
        $this->assertSame('Titulo Depois', $log->new_values['title'] ?? null);
    }

    /** @test */
    public function batch_document_delete_audits_each_document(): void
    {
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $box = \App\Models\Box::factory()->create();
        $docA = Document::factory()->create(['box_id' => $box->id]);
        $docB = Document::factory()->create(['box_id' => $box->id]);

        $this->actingAs($admin)->delete(
            route('boxes.documents.batchDestroy', $box),
            ['document_ids' => [$docA->id, $docB->id]]
        );

        foreach ([$docA->id, $docB->id] as $docId) {
            $this->assertDatabaseHas('audit_logs', [
                'event' => 'deleted',
                'auditable_type' => Document::class,
                'auditable_id' => $docId,
            ]);
        }
    }

    /** @test */
    public function box_batch_destroy_audits_disassociated_documents(): void
    {
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $box = \App\Models\Box::factory()->create();
        $doc = Document::factory()->create(['box_id' => $box->id]);
        AuditLog::query()->delete();

        $this->actingAs($admin)->delete(
            route('boxes.batch-destroy'),
            ['selected_boxes' => [$box->id]]
        );

        $this->assertDatabaseHas('documents', ['id' => $doc->id, 'box_id' => null]);
        $this->assertDatabaseHas('audit_logs', [
            'event' => 'documents_disassociated',
            'auditable_type' => \App\Models\Box::class,
            'auditable_id' => $box->id,
        ]);
    }
}
