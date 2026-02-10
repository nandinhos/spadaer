<?php

namespace Tests\Feature;

use App\Models\Box;
use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class ImportDocumentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'admin']);
    }

    public function test_import_documents_with_valid_csv()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $box = Box::create(['number' => 'BOX-001']);
        $project = Project::create(['name' => 'Project 1', 'code' => 'P1']);

        $csvContent = "box_id,project_id,item_number,code,descriptor,document_number,title,document_date,confidentiality,version,is_copy\n";
        $csvContent .= "{$box->id},{$project->id},001,441,\"TEST DESCRIPTOR\",\"DOC-123\",\"Document Title\",01/2026,Público,1.0,Não";

        $file = UploadedFile::fake()->createWithContent('import.csv', $csvContent);

        $response = $this->actingAs($admin)->post(route('documents.import'), [
            'csv_file' => $file,
        ]);

        $response->assertRedirect(route('documents.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('documents', [
            'title' => 'Document Title',
            'document_number' => 'DOC-123',
            'box_id' => $box->id,
            'document_date' => '01/2026',
        ]);
    }

    public function test_import_documents_with_invalid_date_format()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $box = Box::create(['number' => 'BOX-001']);

        // Invalid date format (YYYY-MM-DD)
        $csvContent = "box_id,project_id,item_number,code,descriptor,document_number,title,document_date,confidentiality,version,is_copy\n";
        $csvContent .= "{$box->id},,001,441,\"DESC\",\"DOC-INVALID\",\"Invalid Date\",2026-01-01,Público,1.0,Não";

        $file = UploadedFile::fake()->createWithContent('import_invalid.csv', $csvContent);

        $response = $this->actingAs($admin)->post(route('documents.import'), [
            'csv_file' => $file,
        ]);

        $response->assertRedirect(route('documents.index'));
        $response->assertSessionHas('import_errors');
    }

    public function test_import_documents_standard_behavior()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $box = Box::create(['number' => 'BOX-001']);

        // Standard comma delimiter
        $csvContent = "box_id,project_id,item_number,code,descriptor,document_number,title,document_date,confidentiality,version,is_copy\n";
        $csvContent .= "{$box->id},,001,441,\"DESC\",\"DOC-STD\",\"Standard Title\",01/2026,Público,1.0,Não";

        $file = UploadedFile::fake()->createWithContent('import_std.csv', $csvContent);

        $response = $this->actingAs($admin)->post(route('documents.import'), [
            'csv_file' => $file,
        ]);

        $response->assertRedirect(route('documents.index'));
        $response->assertSessionHas('success');
    }

    public function test_import_documents_with_short_date_format()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $box = Box::create(['number' => 'BOX-001']);

        // M/YYYY format
        $csvContent = "box_id,project_id,item_number,code,descriptor,document_number,title,document_date,confidentiality,version,is_copy\n";
        $csvContent .= "{$box->id},,005,441,\"DESC\",\"DOC-SHORT\",\"Short Date\",1/2026,Público,1.0,Não";

        $file = UploadedFile::fake()->createWithContent('import_short.csv', $csvContent);

        $response = $this->actingAs($admin)->post(route('documents.import'), [
            'csv_file' => $file,
        ]);

        $response->assertRedirect(route('documents.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('documents', [
            'title' => 'Short Date',
            'document_date' => '01/2026', // Normalized
        ]);
    }

    public function test_import_by_commission_president()
    {
        Role::create(['name' => 'commission_president']);
        $president = User::factory()->create();
        $president->assignRole('commission_president');

        $box = Box::create(['number' => 'BOX-PRES']);

        $csvContent = "box_id,project_id,item_number,code,descriptor,document_number,title,document_date,confidentiality,version,is_copy\n";
        $csvContent .= "{$box->id},,001,441,\"AUTH\",\"DOC-PRES\",\"President Title\",01/2026,Público,1.0,Não";

        $file = UploadedFile::fake()->createWithContent('import_pres.csv', $csvContent);

        $response = $this->actingAs($president)->post(route('documents.import'), [
            'csv_file' => $file,
        ]);

        $response->assertRedirect(route('documents.index'));
        $response->assertSessionHas('success');
    }
}
