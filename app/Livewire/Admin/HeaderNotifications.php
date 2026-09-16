<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class HeaderNotifications extends Component
{
    public function getNotificationsProperty()
    {
        return Auth::user()->unreadNotifications;
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
    }

    public function render()
    {
        return view('livewire.admin.header-notifications', [
            'notifications' => $this->notifications,
        ]);
    }
}
