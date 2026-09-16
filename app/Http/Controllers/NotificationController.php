<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Histórico de notificações do usuário autenticado (sempre escopado ao próprio usuário).
     */
    public function index(Request $request): View
    {
        $notifications = $request->user()->notifications()->latest()->paginate(15);

        return view('notifications.index', compact('notifications'));
    }
}
