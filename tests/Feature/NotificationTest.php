<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        // O sidebar verifica gates de permissão em páginas full (padrão dos testes Feature).
        $this->seed(\Database\Seeders\PermissionSeeder::class);
    }

    public function test_service_dispatch_creates_unread_notification(): void
    {
        $user = User::factory()->create();

        app(NotificationService::class)->send($user, 'Titulo Teste', 'Mensagem teste', 'fa-bell');

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'type' => \App\Notifications\SystemNotification::class,
        ]);
        $this->assertSame(1, $user->fresh()->unreadNotifications()->count());
    }

    public function test_header_dropdown_marks_notification_as_read(): void
    {
        $user = User::factory()->create();
        app(NotificationService::class)->send($user, 'Titulo Teste', 'Mensagem teste');
        $id = $user->fresh()->unreadNotifications()->first()->id;

        Livewire::actingAs($user)
            ->test(\App\Livewire\Admin\HeaderNotifications::class)
            ->call('markAsRead', $id);

        $this->assertSame(0, $user->fresh()->unreadNotifications()->count());
    }

    public function test_history_shows_only_own_notifications(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        app(NotificationService::class)->send($userA, 'Segredo de A', 'só A vê');

        $response = $this->actingAs($userB)->get(route('notifications.index'));

        $response->assertOk();
        $response->assertDontSee('Segredo de A');
    }

    public function test_history_lists_own_notifications(): void
    {
        $user = User::factory()->create();
        app(NotificationService::class)->send($user, 'Minha Noticia', 'conteúdo próprio');

        $response = $this->actingAs($user)->get(route('notifications.index'));

        $response->assertOk();
        $response->assertSee('Minha Noticia');
    }

    public function test_app_name_is_spadaer(): void
    {
        $this->assertSame('SPADAER', config('app.name'));
    }
}
