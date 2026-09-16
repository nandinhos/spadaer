<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\SystemNotification;

/**
 * Ponto único de emissão de notificações do sistema.
 *
 * Centraliza para não espalhar `new SystemNotification` pelos controllers
 * e permitir evoluções (mail, broadcast) num só lugar.
 */
class NotificationService
{
    public function send(User $user, string $title, string $message, string $icon = 'fa-info-circle'): void
    {
        $user->notify(new SystemNotification($title, $message, $icon));
    }
}
