<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
// Importante para la notificación
use Illuminate\Support\Facades\Notification;
use App\Notifications\WelcomeNotification; // Asegúrate de crear esta notificación

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. VALIDACIÓN
        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'apellido' => ['required', 'string', 'min:3', 'max:255'],
            'ci' => ['required', 'string', 'min:7', 'max:20', 'unique:users,ci'], // Especificar columna ci
            'telefono' => ['required', 'string', 'min:7', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::min(8)->letters()->numbers()],
        ], [
            // Tus mensajes personalizados están perfectos
            'ci.unique' => 'Esta Cédula de Identidad ya está registrada.',
            'email.unique' => 'Este correo ya está en uso.',
        ]);


        $user = User::create([
            'name' => $request->name,
            'apellido' => $request->apellido,
            'ci' => $request->ci,
            'telefono' => $request->telefono,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Enviamos la notificación
        $user->notify(new WelcomeNotification($user));

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard', absolute: false));

        // 3. EVENTOS
        event(new Registered($user));

        // Solo si ya creaste la clase WelcomeNotification
        // $user->notify(new WelcomeNotification($user));

        // 4. LOGIN
        Auth::login($user);

        // 5. REDIRECCIÓN (Verifica que estas rutas existan en web.php)
        return match ($user->role) {
            'admin' => redirect()->intended(route('admin.dashboard')),
            'secretaria' => redirect()->intended(route('secretaria.dashboard')),
            default => redirect()->intended(route('user.dashboard')),
        };
    }


}
