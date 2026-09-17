<?php

namespace Tests\Unit\Services;

use App\Models\Box;
use App\Models\Document;
use App\Models\User;
use App\Services\BoxService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoxServiceTest extends TestCase
{
    use RefreshDatabase;

    protected BoxService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(BoxService::class);
    }

    public function test_it_can_create_batch_of_sequential_boxes(): void
    {
        $count = $this->service->createBatch([
            'number' => 'CX-001',
            'physical_location' => 'Setor A',
        ], 3);

        $this->assertSame(3, $count);
        $this->assertDatabaseHas('boxes', ['number' => 'CX-001']);
        $this->assertDatabaseHas('boxes', ['number' => 'CX-002']);
        $this->assertDatabaseHas('boxes', ['number' => 'CX-003']);
    }

    public function test_it_destroys_empty_boxes_and_disassociates_occupied_ones(): void
    {
        $user = User::factory()->create();
        $emptyBox = Box::factory()->create(['number' => 'CX-VAZIA']);
        $occupiedBox = Box::factory()->create(['number' => 'CX-OCUPADA']);

        $doc = Document::factory()->create(['box_id' => $occupiedBox->id]);

        $result = $this->service->destroyBatch([$emptyBox->id, $occupiedBox->id], $user);

        $this->assertSame(1, $result['deleted']);
        $this->assertSame(1, $result['orphaned']);
        $this->assertDatabaseMissing('boxes', ['id' => $emptyBox->id]);
        $this->assertDatabaseHas('boxes', ['id' => $occupiedBox->id]);
        $this->assertNull($doc->fresh()->box_id);
    }

    public function test_it_generates_correct_sequential_number_patterns(): void
    {
        $this->assertSame('AD-002', $this->service->generateSequentialNumber('AD-001', 1));
        $this->assertSame('BOX-10', $this->service->generateSequentialNumber('BOX-09', 1));
        $this->assertSame('DOC-2', $this->service->generateSequentialNumber('DOC', 1));
    }
}
