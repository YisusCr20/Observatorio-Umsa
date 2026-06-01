<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Allow request only for users with one of the expected roles.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Acceso denegado.');
        }

        if (! in_array($user->role, $roles, true)) {
            abort(403, 'No tienes permisos para acceder a este modulo.');
        }

        return $next($request);
    }
}
