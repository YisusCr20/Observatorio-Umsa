<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification; // Importante para el Punto 3

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
protected $fillable = [
    'name',
    'apellido',
    'ci',
    'telefono',
    'email',
    'password',
    'role',
    'is_blacklisted',
    'blacklist_reason',
    'blacklisted_at',
    'id_acceso',
    'departamento', // <--- Debe estar aquí
];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'blacklisted_at' => 'datetime',
            'is_blacklisted' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * PUNTO 3: Enviar notificación personalizada de restablecimiento de contraseña.
     * Esto enviará el correo usando el SMTP de Gmail que configuramos.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    // RELACIONES
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }

    public function visitFeedback()
    {
        return $this->hasMany(VisitFeedback::class);
    }

    // CHEQUEO DE ROLES
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSecretaria(): bool
    {
        return $this->role === 'secretaria';
    }

    public function isUsuario(): bool
    {
        return $this->role === 'usuario';
    }
}
