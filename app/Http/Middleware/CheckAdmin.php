<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle($request, Closure $next)
    {
        // 1. Verificamos si el usuario está autenticado
        // 2. Verificamos si tiene el rol 'Admin' usando la relación que creamos en el Modelo
        if (Auth::check() && Auth::user()->roles->contains('nombre', 'Admin')) {
            return $next($request); // ¡Adelante, puedes pasar!
        }

        // Si no es admin, lo mandamos al home con un mensaje de error
        return redirect('/dashboard')->with('error', 'Acceso denegado. No eres Administrador.');
    }
}
