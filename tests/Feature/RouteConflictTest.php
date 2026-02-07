<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class RouteConflictTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Desabilitar o Vite para evitar erro de manifest nos testes
        $this->withoutVite();

        // Criar todas as permissões de documentos
        $permissions = [
            'documents.create',
            'documents.view',
            'documents.edit',
            'documents.delete',
            'documents.export',
            'documents.import',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Criar papel de admin e atribuir todas as permissões
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo($permissions);
    }

    /**
     * Testa se a rota de criação de documentos está acessível.
     */
    public function test_document_create_route_is_accessible(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $response = $this->actingAs($user)->get(route('documents.create'));

        $response->assertStatus(200);
    }

    /**
     * Testa se a rota de exportação de documentos está acessível.
     */
    public function test_document_export_route_is_accessible(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $response = $this->actingAs($user)->get(route('documents.export'));

        $response->assertSuccessful();
    }

    /**
     * Testa se a rota de exibição de um documento real ainda funciona.
     */
    public function test_document_show_route_works_with_real_id(): void
    {
        $document = \App\Models\Document::factory()->create();

        $user = User::factory()->create();
        $user->assignRole('admin');

        $response = $this->actingAs($user)->get(route('documents.show', $document));

        $response->assertStatus(200);
    }
}
