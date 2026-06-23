<?php

namespace App\Notifications;

use App\Models\VisitFeedback;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FeedbackRecibidoNotification extends Notification
{
    use Queueable;

    public function __construct(private VisitFeedback $feedback)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $feedback = $this->feedback->loadMissing(['user']);

        return [
            'feedback_id' => $feedback->id,
            'mensaje' => 'Nueva calificación de ' . ($feedback->user->name ?? 'un visitante') . ': ' . $feedback->rating . '/5 estrellas.',
            'rating' => $feedback->rating,
            'comentario' => $feedback->comment,
        ];
    }
}
