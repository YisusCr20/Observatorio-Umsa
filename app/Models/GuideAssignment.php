<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GuideAssignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'cargo',
        'ci',
        'telefono',
        'email',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'observacion',
        'email_sent_at',
        'whatsapp_link_generated_at',
    ];

    protected $casts = [
        'fecha' => 'date',
        'email_sent_at' => 'datetime',
        'whatsapp_link_generated_at' => 'datetime',
    ];
}
