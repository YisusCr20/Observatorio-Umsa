<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpecialGuestReservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'invitados_reserva';

    protected $fillable = [
        'nombre',
        'cargo',
        'institucion',
        'pais',
        'correo',
        'telefono',
        'tipo_visita',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'cantidad_personas',
        'motivo',
        'observacion',
        'estado',
        'creado_por',
    ];

    protected $casts = [
        'fecha' => 'date',
        'cantidad_personas' => 'integer',
    ];

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }
}
