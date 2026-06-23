<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Turno;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    public function reservas(Request $request): JsonResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:600'],
            'language' => ['nullable', 'string', 'in:es,en,ay'],
        ]);

        $message = trim($data['message']);
        $normalized = Str::of($message)
            ->lower()
            ->ascii()
            ->replaceMatches('/\s+/', ' ')
            ->toString();

        $language = $this->resolveLanguage($data['language'] ?? null, $normalized);
        $date = $this->extractDate($normalized);

        if ($this->isGreeting($normalized)) {
            return response()->json([
                'reply' => $this->translate($language,
                    "Hola. Puedo ayudarte con reservas, días disponibles, horarios de visita, cupos y cómo agendar tu visita.",
                    "Hi! I can help you with reservations, available days, visit hours, capacity and how to book your visit.",
                    "Kamisaki. Nayaxa reservas tuqita yanapt'asmawa: urunaka, visita horas, cupos ukhamaraki kunjamsa reserva luraña."
                ),
                'suggestions' => $this->suggestions($language),
            ]);
        }

        if ($this->containsAny($normalized, ['reservar', 'reserva', 'agendar', 'booking', 'book', 'reserve'])) {
            return response()->json([
                'reply' => $this->reservationGuide($language),
                'suggestions' => $this->suggestions($language),
            ]);
        }

        if ($this->containsAny($normalized, ['horario', 'hora', 'turno', 'hours', 'schedule', 'time'])) {
            return response()->json([
                'reply' => $this->scheduleAnswer($language),
                'suggestions' => $this->suggestions($language),
            ]);
        }

        if ($this->containsAny($normalized, ['duracion', 'dura', 'tiempo', 'duration', 'last', 'long'])) {
            return response()->json([
                'reply' => $this->translate($language,
                    "Cada sesión de visita guiada dura 1 hora y 30 minutos. La primera sesión inicia a las 08:00 y la última sesión configurada termina a las 18:30.",
                    "Each guided visit session lasts 1 hour and 30 minutes. The first session starts at 08:00 and the last configured session ends at 18:30.",
                    "Sapa visita guiadaxa 1 hora 30 minutos ukha pachawa. Nayriri sesionaxa 08:00 qallti, tukuyiri sesionaxa 18:30 tukuyi."
                ),
                'suggestions' => $this->suggestions($language),
            ]);
        }

        if ($date || $this->containsAny($normalized, ['disponible', 'cupos', 'fecha', 'available', 'availability', 'spots', 'capacity', 'tomorrow', 'today', 'manana', 'hoy'])) {
            return response()->json([
                'reply' => $this->availabilityAnswer($date ?: Carbon::today(), $language),
                'suggestions' => $this->suggestions($language),
            ]);
        }

        if ($this->containsAny($normalized, ['dia', 'dias', 'lunes', 'viernes', 'week', 'days', 'weekday'])) {
            return response()->json([
                'reply' => $this->translate($language,
                    "Las visitas normalmente están disponibles de lunes a viernes. Los fines de semana y fechas pasadas no están disponibles en el formulario de reservas.",
                    "Visits are normally available Monday to Friday. Weekends and past dates are not available in the booking form.",
                    "Visitanakaxa lunes ukat viernes urukama utji. Sabado, domingo ukhamaraki nayra urunakaxa janiwa reserva lurañatakixa utjkiti."
                ),
                'suggestions' => $this->suggestions($language),
            ]);
        }

        if ($this->containsAny($normalized, ['precio', 'costo', 'monto', 'pagar', 'price', 'cost', 'payment'])) {
            return response()->json([
                'reply' => $this->translate($language,
                    "Los costos de referencia son: estudiantes Bs. 5 por persona; profesores, padres de familia y público general Bs. 10 por persona. El formulario calcula el total automáticamente según el tipo de visita y la cantidad de asistentes.",
                    "Current reference prices are: students Bs. 5 per person; teachers, parents and general public Bs. 10 per person. The booking form calculates the total automatically according to the type of visit and number of attendees.",
                    "Qullqi chanipaxa akhamawa: yatiqirinaka Bs. 5 sapa maynitaki; yatichirinaka, awk taykanaka ukhamaraki taqi jaqinaka Bs. 10 sapa maynitaki. Formularioxa jakhuwi automático lurani."
                ),
                'suggestions' => $this->suggestions($language),
            ]);
        }

        return response()->json([
            'reply' => $this->translate($language,
                "Puedo responder sobre reservas, fechas disponibles, horarios y cupos. Prueba preguntando: “¿Qué horarios hay?” o “¿Hay cupos mañana?”.",
                "I can answer questions about reservations, available dates, visit hours and capacity. Try asking: “What hours are available?” or “Is tomorrow available?”",
                "Reservas, urunaka, horas ukhamaraki cupos tuqita jaysasmawa. Akhama jiskt'asma: “Kuna horas utji?” jan ukaxa “Qharuru cupos utjiti?”."
            ),
            'suggestions' => $this->suggestions($language),
        ]);
    }

    private function reservationGuide(string $language): string
    {
        $url = route('reservas.create');

        return $this->translate($language,
            "Para reservar una visita, inicia sesión o crea una cuenta y abre el formulario: $url. Elige un día hábil, selecciona un horario, agrega los datos de asistentes y confirma la solicitud.",
            "To book a visit, sign in or create an account, then open the booking form: $url. Choose an available weekday, select a session, add attendee details and confirm the request.",
            "Visita reserva lurañatakixa, cuenta ukampi mantaña jan ukaxa machaq cuenta luraña. Ukat formulario jist'araña: $url. Uru habil ajlliña, hora ajlliña, asistentes datos phuqaña ukat solicitud confirmar luraña."
        );
    }

    private function scheduleAnswer(string $language): string
    {
        $lines = collect($this->bookingSessions())
            ->map(fn ($session) => $session['label'])
            ->implode("\n");

        return $this->translate($language,
            "Cada visita guiada dura 1 hora y 30 minutos. Horarios disponibles:\n$lines\nLas visitas normalmente están disponibles de lunes a viernes.",
            "Each guided visit lasts 1 hour and 30 minutes. Available sessions:\n$lines\nVisits are normally available Monday to Friday.",
            "Sapa visita guiadaxa 1 hora 30 minutos ukha pachawa. Horas utjiri:\n$lines\nVisitanakaxa lunes ukat viernes urukama utji."
        );
    }

    private function availabilityAnswer(Carbon $date, string $language): string
    {
        if ($date->isPast() && ! $date->isToday()) {
            return $this->translate($language,
                "Esa fecha ya pasó. Elige hoy o una fecha futura en día hábil.",
                "That date has already passed. Please choose today or a future weekday.",
                "Uka uruxa nayraqata pasawayxiwa. Jichha uru jan ukaxa jutiri uru habil ajllima."
            );
        }

        if ($date->isWeekend()) {
            return $this->translate($language,
                "Para el {$date->format('d/m/Y')} no hay reservas porque es fin de semana. Elige una fecha de lunes a viernes.",
                "For {$date->format('d/m/Y')}, bookings are not available because it is a weekend. Please choose Monday to Friday.",
                "{$date->format('d/m/Y')} urutakixa janiwa reserva utjkiti, fin de semana satawa. Lunes ukat viernes taypina uru ajllima."
            );
        }

        $turnos = Turno::with(['reservas' => function ($query) use ($date) {
            $query->whereDate('fecha', $date)
                ->whereNotIn('estado', ['Cancelada', 'Cancelado', 'Rechazado']);
        }])->where('activo', true)->orderBy('hora_inicio')->get();

        if ($turnos->isEmpty()) {
            return $this->translate($language,
                "No hay turnos activos configurados, por eso todavía no puedo calcular cupos.",
                "There are no active sessions configured, so I cannot calculate availability yet.",
                "Janiwa turnos activos configurados utjkiti, ukatwa cupos jakhthapiñaxa jani wakiskiti."
            );
        }

        $lines = $turnos->map(function (Turno $turno) {
            $reserved = $turno->reservas->sum('cantidad_personas');
            $capacity = (int) $turno->capacidad_maxima;
            $available = max($capacity - $reserved, 0);

            return "{$turno->nombre}: {$available}/{$capacity} cupos";
        })->implode("\n");

        return $this->translate($language,
            "Disponibilidad para el {$date->format('d/m/Y')}:\n$lines\nCada sesión dura 1 hora y 30 minutos. Puedes reservar desde el formulario después de iniciar sesión.",
            "Availability for {$date->format('d/m/Y')}:\n$lines\nEach session lasts 1 hour and 30 minutes. You can book from the reservation form after signing in.",
            "{$date->format('d/m/Y')} urutaki cupos:\n$lines\nSapa sesionaxa 1 hora 30 minutos ukha pachawa. Mantañataki formulario tuqita reserva lurasma."
        );
    }

    private function extractDate(string $text): ?Carbon
    {
        if (str_contains($text, 'pasado manana')) {
            return Carbon::today()->addDays(2);
        }

        if (str_contains($text, 'manana') || str_contains($text, 'tomorrow')) {
            return Carbon::tomorrow();
        }

        if (str_contains($text, 'hoy') || str_contains($text, 'today')) {
            return Carbon::today();
        }

        if (preg_match('/\b(\d{4})-(\d{2})-(\d{2})\b/', $text, $match)) {
            return Carbon::createFromDate((int) $match[1], (int) $match[2], (int) $match[3])->startOfDay();
        }

        if (preg_match('/\b(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})\b/', $text, $match)) {
            return Carbon::createFromDate((int) $match[3], (int) $match[2], (int) $match[1])->startOfDay();
        }

        return null;
    }

    private function isGreeting(string $text): bool
    {
        return $this->containsAny($text, ['hola', 'buenas', 'hello', 'hi', 'hey']);
    }

    private function looksEnglish(string $text): bool
    {
        if ($this->containsAny($text, ['hola', 'buenas', 'reserva', 'reservar', 'agendar', 'horario', 'hora', 'turno', 'disponible', 'cupos', 'fecha', 'hoy', 'manana', 'precio', 'costo', 'monto', 'pagar', 'dias', 'duracion', 'dura'])) {
            return false;
        }

        return preg_match('/\b(hello|hi|booking|book|reserve|available|hours|schedule|today|tomorrow|price|cost|payment|visit)\b/', $text) === 1;
    }

    private function resolveLanguage(?string $requested, string $text): string
    {
        if (in_array($requested, ['es', 'en', 'ay'], true)) {
            return $requested;
        }

        if ($this->looksAymara($text)) {
            return 'ay';
        }

        return $this->looksEnglish($text) ? 'en' : 'es';
    }

    private function looksAymara(string $text): bool
    {
        return $this->containsAny($text, [
            'aymara', 'aimara', 'aruskipawi', 'arunaka', 'kamisaki', 'kunjamsa',
            'qawqha', 'kunapacha', 'qharuru', 'jichha', 'utjiti', 'qullqi',
            'uru', 'horas utji', 'reserva lur',
        ]);
    }

    private function translate(string $language, string $spanish, string $english, string $aymara): string
    {
        return match ($language) {
            'en' => $english,
            'ay' => $aymara,
            default => $spanish,
        };
    }

    private function containsAny(string $text, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($text, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function suggestions(string $language): array
    {
        return match ($language) {
            'en' => ['What hours are available?', 'Is tomorrow available?', 'How do I book?', 'How much does it cost?'],
            'ay' => ['Kuna horas utji?', 'Qharuru cupos utjiti?', 'Kunjamsa reserva lurta?', 'Qawqha qullqisa?'],
            default => ['¿Qué horarios hay?', '¿Hay cupos mañana?', '¿Cómo hago una reserva?', '¿Cuál es el costo?'],
        };
    }

    private function bookingSessions(): array
    {
        return [
            ['start' => '08:00', 'end' => '09:30', 'label' => '08:00 - 09:30'],
            ['start' => '09:30', 'end' => '11:00', 'label' => '09:30 - 11:00'],
            ['start' => '11:00', 'end' => '12:30', 'label' => '11:00 - 12:30'],
            ['start' => '12:30', 'end' => '14:00', 'label' => '12:30 - 14:00'],
            ['start' => '14:00', 'end' => '15:30', 'label' => '14:00 - 15:30'],
            ['start' => '15:30', 'end' => '17:00', 'label' => '15:30 - 17:00'],
            ['start' => '17:00', 'end' => '18:30', 'label' => '17:00 - 18:30'],
        ];
    }
}
