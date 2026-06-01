<?php

namespace App\Notifications;

use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ReservaConfirmadaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $reserva;

    // Recibimos la instancia de la reserva modificada
    public function __construct(Reserva $reserva)
    {
        $this->reserva = $reserva;
    }

    // Activamos los canales de Correo y Base de Datos (Campanita)
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    // Diseño del correo que recibirá el usuario en su móvil/computadora
    public function toMail($notifiable)
    {
        $estado = $this->reserva->estado; // "Confirmado" o "Cancelado"
        $esConfirmado = ($estado === 'Confirmado');

        $message = (new MailMessage)
            ->subject('Actualización de tu Reserva - Observatorio Max Schreier')
            ->greeting('¡Hola, ' . $this->reserva->user->name . '!');

        if ($esConfirmado) {
            $message->line('🎉 ¡Buenas noticias! Tu solicitud de reserva ha sido **APROBADA** con éxito.')
                    ->line('El personal de secretaría validó tu comprobante de pago correctamente.')
                    ->line('**Detalles de tu visita:**')
                    ->line('• **Fecha:** ' . \Carbon\Carbon::parse($this->reserva->fecha)->format('d/m/Y'))
                    ->line('• **Cupos:** ' . $this->reserva->cantidad_personas . ' personas')
                    ->line('Por favor, llega 10 minutos antes de la hora programada y presenta tu documento de identidad.');
        } else {
            $message->line('🔴 Tu solicitud de reserva ha sido **RECHAZADA o CANCELADA**.')
                    ->line('Esto puede deberse a un problema con la validación del comprobante de pago o falta de disponibilidad.')
                    ->line('Te recomendamos ponerte en contacto con la administración del observatorio para subsanar el inconveniente.');
        }

        return $message->action('Ver mi Dashboard', url('/usuario/dashboard'))
                       ->line('Gracias por tu interés en el Observatorio Astronómico Max Schreier.');
    }

    // Datos que se guardarán en la BD para la campanita del usuario
    public function toArray($notifiable)
    {
        return [
            'reserva_id' => $this->reserva->id,
            'estado' => $this->reserva->estado,
            'mensaje' => 'Tu reserva para el ' . \Carbon\Carbon::parse($this->reserva->fecha)->format('d/m/Y') . ' ha sido ' . strtolower($this->reserva->estado) . '.'
        ];
    }
}