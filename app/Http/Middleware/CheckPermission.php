<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!Auth::check()) {
            throw new AuthorizationException('Você precisa estar autenticado para acessar este recurso.');
        }

        if (!Auth::user()->hasPermissionTo($permission)) {
            throw new AuthorizationException('Você não tem permissão para acessar este recurso.');
        }

        return $next($request);
    }
}