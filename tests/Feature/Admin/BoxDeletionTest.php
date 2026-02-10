<?php

namespace Tests\Feature\Admin;

use App\Livewire\BoxList;
use App\Models\Box;
use App\Models\User;
use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BoxDeletionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_can_delete_empty_box()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $box = Box::create(['number' => 'BOX-001']);

        Livewire::actingAs($admin)
            ->test(BoxList::class)
            ->call('deleteBox', $box->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('boxes', ['id' => $box->id]);
    }

    public function test_deleting_box_with_documents_orphans_them()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $box = Box::create(['number' => 'BOX-001']);
        $doc = Document::factory()->create(['box_id' => $box->id]);

        Livewire::actingAs($admin)
            ->test(BoxList::class)
            ->call('deleteBox', $box->id)
            ->assertHasNoErrors();

        // Caixa não deve ser deletada no fluxo atual do BoxList se tem documentos (ela apenas orfã)
        // Na verdade, no BoxList.php:99 ele orfana e mantem a caixa? 
        // Vamos checar a logica: se documents > 0, update box_id null e flash warning.
        
        $this->assertDatabaseHas('boxes', ['id' => $box->id]);
        $this->assertNull($doc->fresh()->box_id);
    }

    public function test_can_bulk_delete_boxes()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $boxes = Box::factory()->count(2)->create();
        $ids = $boxes->pluck('id')->map(fn($id) => (string)$id)->toArray();

        Livewire::actingAs($admin)
            ->test(BoxList::class)
            ->set('selectedBoxes', $ids)
            ->call('batchDelete', 'Teste mass deletion')
            ->assertSet('selectedBoxes', []);

        foreach ($boxes as $box) {
            $this->assertDatabaseMissing('boxes', ['id' => $box->id]);
        }
    }
}
