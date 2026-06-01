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
use App\Notifications\WelcomeNotification;

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
        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'apellido' => ['required', 'string', 'min:3', 'max:255'],
            'ci' => ['required', 'string', 'min:7', 'max:20', 'unique:users,ci'],
            'telefono' => ['required', 'string', 'min:7', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                'confirmed',
                Rules\Password::min(8)->letters()->numbers(),
            ],
        ], [
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

        event(new Registered($user));

        $user->notify(new WelcomeNotification($user));

        Auth::login($user);

        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'secretaria' => redirect()->route('secretaria.dashboard'),
            default => redirect()->route('user.dashboard'),
        };
    }
}