<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        // Generamos la URL segura que Laravel espera para resetear la clave
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Recuperación de Contraseña - Observatorio Max Schreier')
            ->greeting('¡Hola, ' . $notifiable->name . '!')
            ->line('Has recibido este correo porque solicitaste restablecer la contraseña de tu cuenta en el Sistema de Reservas.')
            ->action('Restablecer Contraseña', $url)
            ->line('Este enlace de recuperación expirará en 60 minutos.')
            ->line('Si no realizaste esta solicitud, puedes ignorar este correo con seguridad.')
            ->salutation('Saludos, Equipo del Observatorio Astronómico Max Schreier.');
    }
}