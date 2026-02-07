<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\DocumentService;
use App\Models\Document;
use App\Models\Box;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DocumentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DocumentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DocumentService();
    }

    public function test_it_can_list_documents_with_filters(): void
    {
        // Setup scenarios
        $project = Project::factory()->create(['name' => 'Projeto A']);
        $box = Box::factory()->create(['number' => 'CX-001']);
        
        Document::factory()->create([
            'title' => 'Documento Alvo',
            'project_id' => $project->id,
            'box_id' => $box->id,
            'document_date' => '01/2023'
        ]);

        Document::factory()->create(['title' => 'Outro']);

        // Test filtering
        $results = $this->service->listDocuments([
            'search' => 'Alvo',
            'filter_project_id' => $project->id,
            'filter_year' => '2023'
        ])->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Documento Alvo', $results->first()->title);
    }

    public function test_it_calculates_correct_statistics(): void
    {
        $project = Project::factory()->create();
        Document::factory()->count(3)->create([
            'project_id' => $project->id,
            'document_date' => '05/2022'
        ]);
        Document::factory()->create(['document_date' => '01/2020']);

        $query = Document::query()->where('project_id', $project->id);
        $stats = $this->service->getStatistics($query);

        $this->assertEquals(3, $stats['filteredDocumentsCount']);
        $this->assertEquals('2022', $stats['yearRange']);
    }
}
