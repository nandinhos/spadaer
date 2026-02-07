<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UIStandardizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()->setLocale('pt_BR');
        
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        
        // Add missing projects.view permission if it's not in the seeder
        if (Permission::where('name', 'projects.view')->doesntExist()) {
            Permission::create(['name' => 'projects.view', 'guard_name' => 'web']);
        }

        // Add missing projects.edit permission for show page buttons
        if (Permission::where('name', 'projects.edit')->doesntExist()) {
            Permission::create(['name' => 'projects.edit', 'guard_name' => 'web']);
        }

        // Add missing boxes.edit and boxes.delete for index page buttons
        if (Permission::where('name', 'boxes.edit')->doesntExist()) {
            Permission::create(['name' => 'boxes.edit', 'guard_name' => 'web']);
        }
        if (Permission::where('name', 'boxes.delete')->doesntExist()) {
            Permission::create(['name' => 'boxes.delete', 'guard_name' => 'web']);
        }

        // Create admin role and give all permissions
        if (Role::where('name', 'admin')->doesntExist()) {
            $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
            $role->givePermissionTo(Permission::all());
        } else {
            Role::findByName('admin')->givePermissionTo(Permission::all());
        }
    }

    public function test_boxes_index_uses_new_button_component(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $response = $this->actingAs($user)->get(route('boxes.index'));
        $response->assertStatus(200);

        // In boxes.index, we replaced <x-primary-button> which had 'tracking-widest' 
        // with <x-ui.button> which has 'justify-center'.
        // Since 'tracking-widest' is in the sidebar, we can't assertDontSee it globally.
        // Instead, we check for a specific combination that should exist now.
        $response->assertSee('Gerenciamento de Caixas');
        $response->assertSee('Filtrar');
        $response->assertSee('justify-center');
        $response->assertSee('bg-primary');
    }

    public function test_projects_show_uses_new_button_component(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        
        $project = Project::factory()->create();

        $response = $this->actingAs($user)->get(route('projects.show', $project));
        $response->assertStatus(200);

        // Check that the old manual classes are gone
        $response->assertDontSee('bg-blue-500 hover:bg-blue-700');
        
        // Check that the new buttons are there
        $response->assertSee('Editar');
        $response->assertSee('Voltar');
        $response->assertSee('bg-primary');
        $response->assertSee('bg-gray-100'); // for variant="secondary" in x-ui.button
    }

    public function test_boxes_show_uses_new_button_component(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        
        $box = \App\Models\Box::factory()->create();

        $response = $this->actingAs($user)->get(route('boxes.show', $box));
        $response->assertStatus(200);

        $response->assertSee('Informações da Caixa');
        $response->assertSee('Documentos na Caixa');
        $response->assertSee('Editar'); // Button in info card
        $response->assertSee('Importar Documentos');
        $response->assertSee('bg-gray-100'); // variant="secondary"
        $response->assertSee('bg-rose-500'); // variant="danger" (rendered class)
    }

    public function test_commissions_show_uses_new_button_component(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        
        $commission = \App\Models\Commission::factory()->create();

        $response = $this->actingAs($user)->get(route('commissions.show', $commission));
        $response->assertStatus(200);

        $response->assertSee('Editar');
        $response->assertSee('Excluir');
        $response->assertSee('bg-primary');
        $response->assertSee('bg-rose-500');
    }

    public function test_documents_show_uses_new_button_component(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        
        $document = \App\Models\Document::factory()->create();

        $response = $this->actingAs($user)->get(route('documents.show', $document));
        $response->assertStatus(200);

        $response->assertSee('Editar');
        $response->assertSee('Voltar');
        $response->assertSee('Excluir Documento');
        $response->assertSee('bg-primary');
        $response->assertSee('bg-gray-100');
        $response->assertSee('bg-rose-500');
    }

    public function test_boxes_create_uses_new_button_component(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $response = $this->actingAs($user)->get(route('boxes.create'));
        $response->assertStatus(200);

        $response->assertSee('Salvar Caixa');
        $response->assertSee('Cancelar');
        $response->assertSee('bg-primary');
        $response->assertSee('justify-center');
    }
}
