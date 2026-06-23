<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $fillable = [
        'reserva_id',
        'registrado_por',
        'monto',
        'metodo_pago',
        'nro_comprobante',
        'estado_pago',
        'observacion',
        'pagado_en',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'pagado_en' => 'datetime',
    ];

    public function reserva()
    {
        return $this->belongsTo(Reserva::class);
    }

    public function secretaria()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
