<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Access\AuthorizationException;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            throw new AuthorizationException('Você precisa estar autenticado para acessar este recurso.');
        }

        if (!auth()->user()->hasRole($role)) {
            throw new AuthorizationException('Você não tem o papel necessário para acessar este recurso.');
        }

        return $next($request);
    }
}