<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reserva extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', // <--- ESTO DEBE ESTAR AQUÍ
        'nombre',
        'correo',
        'telefono',
        'cantidad_personas',
        'fecha',
        'turno_id',
        'horario_id',
        'descripcion',
        'estado',
    ];
    /**
     * Casts de atributos.
     * Importante: 'fecha' como date permite usar ->format() en la vista.
     */
    protected $casts = [
        'fecha' => 'date',
        'notificacion_leida' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function horario()
    {
        // Relación con las horas específicas (ej. 14:00 - 15:00)
        return $this->belongsTo(Horario::class, 'horario_id');
    }

    public function turno()
    {
        // Relación con el bloque (Mañana, Tarde, Noche)
        return $this->belongsTo(Turno::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    public function pago()
    {
        return $this->hasOne(Pago::class)->latestOfMany();
    }

    public function feedback()
    {
        return $this->hasMany(VisitFeedback::class);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS (Utilidades para tu Dashboard)
    |--------------------------------------------------------------------------
    */

    /**
     * Retorna un color de Tailwind según el estado para las insignias (badges)
     */
    public function getEstadoColorAttribute()
    {
        return match ($this->estado) {
            'Confirmado' => 'bg-green-100 text-green-700',
            'Pendiente' => 'bg-amber-100 text-amber-700',
            'Cancelada' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }
}
