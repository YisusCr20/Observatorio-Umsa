<?php

namespace App\Notifications;

use App\Models\Reserva;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReservaModificadaSecretariaNotification extends Notification
{
    use Queueable;

    public function __construct(private Reserva $reserva)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'reserva_id' => $this->reserva->id,
            'mensaje' => 'Reserva modificada pendiente de revisión: ' . ($this->reserva->nombre ?? optional($this->reserva->user)->name ?? 'Visitante'),
            'pax' => $this->reserva->cantidad_personas,
            'hora' => $this->reserva->horario ? substr($this->reserva->horario->hora_inicio, 0, 5) : 'Sin hora',
            'fecha' => Carbon::parse($this->reserva->fecha)->format('d M'),
            'motivo' => $this->reserva->descripcion,
        ];
    }
}
