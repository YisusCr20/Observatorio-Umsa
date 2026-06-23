<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        try {
            $status = Password::sendResetLink(
                $request->only('email')
            );
        } catch (Throwable $exception) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'No se pudo enviar el correo de recuperación. Revisa la configuración SMTP o intenta nuevamente.',
                ]);
        }

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', 'Te enviamos un enlace de recuperación. Revisa tu bandeja de entrada o spam.')
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => 'No encontramos una cuenta registrada con ese correo.']);
    }
}
