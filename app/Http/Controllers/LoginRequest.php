<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class LoginRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:8'], // Validación de 8 caracteres
        ];
    }

    public function authenticate(): void
    {
        $user = User::where('email', $this->email)->first();

        // 1. Bloqueo por seguridad
        if ($user && $user->estado_bloqueo) {
            throw ValidationException::withMessages([
                'email' => 'Cuenta bloqueada. Contacte al administrador.',
            ]);
        }

        // 2. Intento de autenticación
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            
            // Si el usuario existe, sumamos intentos
            if ($user) {
                $user->increment('intentos_fallidos');
                if ($user->intentos_fallidos >= 3) {
                    $user->update(['estado_bloqueo' => true]);
                }
            }

            throw ValidationException::withMessages([
                'email' => 'Credenciales incorrectas.',
            ]);
        }

        // 3. Si es exitoso, reiniciamos intentos
        if ($user) {
            $user->update(['intentos_fallidos' => 0]);
        }
    }
}