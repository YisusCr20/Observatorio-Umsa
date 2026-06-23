<?php

namespace App\Notifications;

use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NuevaReservaNotification extends Notification
{
    use Queueable;

    protected $reserva;

    public function __construct(Reserva $reserva)
    {
        $this->reserva = $reserva;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toMail($notifiable)
    {
        $fecha = \Carbon\Carbon::parse($this->reserva->fecha)->format('d/m/Y');
        $hora = $this->reserva->horario ? $this->reserva->horario->hora_inicio : 'Sin hora asignada';
        $nombre = $this->reserva->nombre ?? optional($this->reserva->user)->name ?? 'Visitante';

        return (new MailMessage)
            ->subject('Nueva reserva pendiente - Observatorio Max Schreier')
            ->greeting('Hola, ' . $notifiable->name)
            ->line('Tienes una nueva solicitud de reserva pendiente de validación.')
            ->line('Visitante: ' . $nombre)
            ->line('Fecha: ' . $fecha)
            ->line('Hora: ' . $hora)
            ->line('Personas: ' . $this->reserva->cantidad_personas)
            ->action('Revisar en Secretaría', route('secretaria.dashboard'))
            ->line('La solicitud también queda registrada en la campanita del sistema.');
    }

    // Datos estructurados que leerá tu Blade de la secretaría
    public function toArray($notifiable)
    {
        // Formateamos la fecha de la reserva para que se vea amigable en la campanita
        $fechaAmigable = \Carbon\Carbon::parse($this->reserva->fecha)->format('d M');

        return [
            'reserva_id' => $this->reserva->id,
            'mensaje' => 'Nueva reserva pendiente de confirmación de pago: ' . ($this->reserva->nombre ?? $this->reserva->user->name),
            'pax' => $this->reserva->cantidad_personas, // Sincronizado con el campo de tu controlador
            'hora' => $this->reserva->horario ? $this->reserva->horario->hora_inicio : 'Sin hora',
            'fecha' => $fechaAmigable,
        ];
    }
}
