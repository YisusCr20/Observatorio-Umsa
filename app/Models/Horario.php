<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    protected $fillable = ['turno_id', 'hora_inicio', 'hora_fin'];

    public function turno()
    {
        return $this->belongsTo(Turno::class);
    }
}