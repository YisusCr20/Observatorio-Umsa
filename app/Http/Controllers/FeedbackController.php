<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\User;
use App\Models\VisitFeedback;
use App\Notifications\FeedbackRecibidoNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reserva_id' => 'nullable|exists:reservas,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:700',
        ]);

        $user = $request->user();
        $reservaId = $validated['reserva_id'] ?? null;

        if ($reservaId) {
            Reserva::where('id', $reservaId)
                ->where('user_id', $user->id)
                ->where('estado', 'Confirmado')
                ->firstOrFail();
        }

        $feedback = VisitFeedback::updateOrCreate(
            [
                'user_id' => $user->id,
                'reserva_id' => $reservaId,
            ],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ]
        );

        $feedback->load(['user', 'reserva.horario']);

        User::where('role', 'secretaria')->get()
            ->each(function (User $recipient) use ($feedback) {
                $recipient->notify(new FeedbackRecibidoNotification($feedback));
                $this->mailFeedbackSecretaria($recipient, $feedback);
            });

        return redirect()
            ->route('user.dashboard', ['panel' => 'dashboard'])
            ->with('success', 'Gracias por calificar tu experiencia. Tu opinión fue enviada a secretaría.');
    }

    private function mailFeedbackSecretaria(User $secretaria, VisitFeedback $feedback): void
    {
        if (!filter_var($secretaria->email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        try {
            $feedback->loadMissing(['user', 'reserva.horario']);
            $reserva = $feedback->reserva;
            $fecha = $reserva?->fecha ? $reserva->fecha->format('d/m/Y') : 'Opinión general';
            $hora = $reserva?->horario ? substr($reserva->horario->hora_inicio, 0, 5) : 'Sin horario';

            $message = "Nueva calificación del recorrido.\n\n"
                . 'Visitante: ' . ($feedback->user->name ?? 'Usuario') . "\n"
                . 'Calificación: ' . $feedback->rating . "/5 estrellas\n"
                . 'Reserva: ' . $fecha . ' - ' . $hora . "\n"
                . 'Comentario: ' . ($feedback->comment ?: 'Sin comentario adicional.') . "\n\n"
                . 'Revisa las opiniones recientes en el dashboard de secretaría.';

            Mail::raw($message, function ($mail) use ($secretaria) {
                $mail->to($secretaria->email)
                    ->subject('Nueva calificación del recorrido - Observatorio Max Schreier');
            });
        } catch (\Throwable $e) {
            Log::warning('No se pudo enviar correo de feedback a secretaría.', [
                'secretaria_id' => $secretaria->id,
                'feedback_id' => $feedback->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
