<?php

namespace Tests\Unit\Services;

use App\Models\Box;
use App\Models\Document;
use App\Models\Project;
use App\Services\DocumentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DocumentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DocumentService;
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
            'document_date' => '01/2023',
        ]);

        Document::factory()->create(['title' => 'Outro']);

        // Test filtering
        $results = $this->service->listDocuments([
            'search' => 'Alvo',
            'filter_project_id' => $project->id,
            'filter_year' => '2023',
        ])->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Documento Alvo', $results->first()->title);
    }

    public function test_it_calculates_correct_statistics(): void
    {
        $project = Project::factory()->create();
        Document::factory()->count(3)->create([
            'project_id' => $project->id,
            'document_date' => '05/2022',
        ]);
        Document::factory()->create(['document_date' => '01/2020']);

        $query = Document::query()->where('project_id', $project->id);
        $stats = $this->service->getStatistics($query);

        $this->assertEquals(3, $stats['filteredDocumentsCount']);
        $this->assertEquals('2022', $stats['yearRange']);
    }

    public function test_it_sorts_documents_chronologically_by_date(): void
    {
        // Criar documentos com datas em formatos variados e fora de ordem
        Document::factory()->create(['title' => 'Doc 2023', 'document_date' => '01/2023']);
        Document::factory()->create(['title' => 'Doc 2022', 'document_date' => '12/2022']);
        Document::factory()->create(['title' => 'Doc 2024 ISO', 'document_date' => '2024-05-10']);
        Document::factory()->create(['title' => 'Doc 2021 Full', 'document_date' => '15/06/2021']);

        // Testar ASC
        $resultsAsc = $this->service->listDocuments([
            'sort_by' => 'documents.document_date',
            'sort_dir' => 'asc',
        ])->get();

        $this->assertEquals('Doc 2021 Full', $resultsAsc[0]->title);
        $this->assertEquals('Doc 2022', $resultsAsc[1]->title);
        $this->assertEquals('Doc 2023', $resultsAsc[2]->title);
        $this->assertEquals('Doc 2024 ISO', $resultsAsc[3]->title);

        // Testar DESC
        $resultsDesc = $this->service->listDocuments([
            'sort_by' => 'documents.document_date',
            'sort_dir' => 'desc',
        ])->get();

        $this->assertEquals('Doc 2024 ISO', $resultsDesc[0]->title);
        $this->assertEquals('Doc 2021 Full', $resultsDesc[3]->title);
    }
}
