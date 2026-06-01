<?php

namespace App\Notifications;

use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NuevaReservaNotification extends Notification
{
    use Queueable;

    protected $reserva;

    public function __construct(Reserva $reserva)
    {
        $this->reserva = $reserva;
    }

    // Guardaremos esta notificación en la base de datos para la campanita
    public function via($notifiable)
    {
        return ['database'];
    }

    // Datos estructurados que leerá tu Blade de la secretaría
    public function toArray($notifiable)
    {
        // Formateamos la fecha de la reserva para que se vea amigable en la campanita
        $fechaAmigable = \Carbon\Carbon::parse($this->reserva->fecha)->format('d M');

        return [
            'reserva_id' => $this->reserva->id,
            'mensaje' => 'Nueva solicitud de ' . ($this->reserva->nombre ?? $this->reserva->user->name),
            'pax' => $this->reserva->cantidad_personas, // Sincronizado con el campo de tu controlador
            'hora' => $this->reserva->horario ? $this->reserva->horario->hora_inicio : 'Sin hora',
            'fecha' => $fechaAmigable,
        ];
    }
}