<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    use Queueable;

    protected $user;

    /**
     * Recibimos la instancia del usuario recién creado.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Canal de envío: Mail.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Diseño del correo electrónico.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🔭 ¡Bienvenido al Sistema - Observatorio Max Schreier!')
            ->greeting('¡Hola, ' . $this->user->name . '!')
            ->line('Tu cuenta ha sido creada exitosamente en nuestro sistema de gestión de visitas.')
            ->line('Ahora puedes acceder para realizar tus reservas y consultas.')
            ->action('Ingresar al Sistema', url('/login'))
            ->line('Estamos emocionados de tenerte con nosotros explorando el cosmos.')
            ->salutation('Atentamente, El equipo del Observatorio Astronómico Max Schreier.');
    }
}