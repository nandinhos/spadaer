<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\CommissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CommissionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CommissionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(CommissionService::class);
    }

    public function test_it_creates_commission_with_file_and_members(): void
    {
        Storage::fake('public');
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $file = UploadedFile::fake()->create('portaria_123.pdf', 150, 'application/pdf');

        $commission = $this->service->create([
            'name' => 'Comissão de Descarte 2026',
            'description' => 'Descrição da comissão',
            'ordinance_number' => 'PORT-2026-01',
            'ordinance_date' => '2026-03-01',
            'members' => [$user1->id, $user2->id],
        ], $file);

        $this->assertDatabaseHas('commissions', [
            'id' => $commission->id,
            'name' => 'Comissão de Descarte 2026',
            'ordinance_number' => 'PORT-2026-01',
        ]);

        $this->assertNotNull($commission->ordinance_file);
        Storage::disk('public')->assertExists($commission->ordinance_file);

        $this->assertDatabaseHas('commission_members', [
            'commission_id' => $commission->id,
            'user_id' => $user1->id,
        ]);
        $this->assertDatabaseHas('commission_members', [
            'commission_id' => $commission->id,
            'user_id' => $user2->id,
        ]);
    }

    public function test_it_updates_commission_and_syncs_members_and_replaces_file(): void
    {
        Storage::fake('public');
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        $oldFile = UploadedFile::fake()->create('portaria_antiga.pdf', 100, 'application/pdf');
        $commission = $this->service->create([
            'name' => 'Comissão Antiga',
            'description' => 'Desc',
            'ordinance_number' => 'PORT-01',
            'ordinance_date' => '2026-01-01',
            'members' => [$user1->id, $user2->id],
        ], $oldFile);

        $oldFilePath = $commission->ordinance_file;
        Storage::disk('public')->assertExists($oldFilePath);

        $newFile = UploadedFile::fake()->create('portaria_nova.pdf', 200, 'application/pdf');

        // Atualiza: remove user2, mantém user1, adiciona user3
        $updated = $this->service->update($commission, [
            'name' => 'Comissão Atualizada',
            'description' => 'Nova Desc',
            'ordinance_number' => 'PORT-02',
            'ordinance_date' => '2026-02-01',
            'members' => [$user1->id, $user3->id],
        ], $newFile);

        $this->assertSame('Comissão Atualizada', $updated->name);
        $this->assertNotSame($oldFilePath, $updated->ordinance_file);
        Storage::disk('public')->assertMissing($oldFilePath);
        Storage::disk('public')->assertExists($updated->ordinance_file);

        $this->assertDatabaseHas('commission_members', [
            'commission_id' => $commission->id,
            'user_id' => $user1->id,
        ]);
        $this->assertDatabaseMissing('commission_members', [
            'commission_id' => $commission->id,
            'user_id' => $user2->id,
        ]);
        $this->assertDatabaseHas('commission_members', [
            'commission_id' => $commission->id,
            'user_id' => $user3->id,
        ]);
    }

    public function test_it_deletes_commission_and_its_ordinance_file(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('portaria.pdf', 100, 'application/pdf');

        $commission = $this->service->create([
            'name' => 'Comissão Para Deletar',
            'ordinance_number' => 'PORT-DEL',
            'ordinance_date' => '2026-01-01',
            'members' => [$user->id],
        ], $file);

        $filePath = $commission->ordinance_file;
        Storage::disk('public')->assertExists($filePath);

        $deleted = $this->service->delete($commission);

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('commissions', ['id' => $commission->id]);
        Storage::disk('public')->assertMissing($filePath);
    }
}
