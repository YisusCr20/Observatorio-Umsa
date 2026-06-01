<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    protected $fillable = [
        'nombre',
        'hora_inicio', // Representa el inicio del bloque (ej. 14:00)
        'hora_fin',    // Representa el fin del bloque (ej. 18:00)
        'capacidad_maxima',
        'activo'
    ];

    /**
     * RELACIÓN CLAVE PARA LOS HORARIOS DINÁMICOS
     * Un turno (Bloque) tiene muchos horarios específicos.
     */
    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }

    /**
     * Calcula los espacios disponibles para una fecha específica.
     */
    public function spotsDisponibles($fecha)
    {
        $ocupados = $this->reservas()
            ->where('fecha', $fecha)
            ->where('estado', '!=', 'Cancelada') // Solo sumamos las no canceladas
            ->sum('cantidad_personas');

        // Nota: Asegúrate de que el nombre de la columna en la DB sea 'capacidad_maxima' 
        // tal como está en el $fillable.
        return $this->capacidad_maxima - $ocupados;
    }
}