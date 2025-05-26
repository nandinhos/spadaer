<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            throw new AuthorizationException('Você precisa estar autenticado para acessar este recurso.');
        }

        // Separa os papéis por vírgula se fornecidos como string única
        if (count($roles) === 1 && str_contains($roles[0], ',')) {
            $roles = explode(',', $roles[0]);
        }

        $hasRole = false;
        foreach ($roles as $role) {
            if (Auth::user()->hasRole(trim($role))) {
                $hasRole = true;
                break;
            }
        }

        if (!$hasRole) {
            throw new AuthorizationException('Você não tem os papéis necessários para acessar este recurso.');
        }

        return $next($request);
    }
}