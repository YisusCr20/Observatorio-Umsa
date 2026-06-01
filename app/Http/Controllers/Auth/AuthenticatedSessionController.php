<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     * ESTO ES LO QUE TE FALTABA Y LO QUE ARREGLA TU PROBLEMA
     */
public function store(LoginRequest $request): RedirectResponse
{
    // Ejecuta la lógica del LoginRequest (incluye el bloqueo de 3 intentos)
    $request->authenticate();

    $request->session()->regenerate();

    // Redirección según rol (Asegúrate de que tus roles sean 'admin', 'secretaria', 'usuario')
    $role = Auth::user()->role;

    return match ($role) {
        'admin'      => redirect()->route('admin.dashboard'),
        'secretaria' => redirect()->route('secretaria.dashboard'),
        'usuario'    => redirect()->route('user.dashboard'),
        default      => redirect()->intended('dashboard'),
    };
}

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}