<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Turno;
use App\Models\Horario;
use App\Models\User; // <-- IMPORTANTE: Añadimos el modelo User para buscar a las secretarias
use App\Notifications\NuevaReservaNotification; // <-- IMPORTANTE: Añadimos la nueva notificación
use App\Notifications\ReservaModificadaSecretariaNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class ReservaController extends Controller
{
    /**
     * DASHBOARD: Motor principal del panel de usuario.
     */
    public function dashboard()
    {
        $user = auth()->user();

        $baseQuery = Reserva::query();
        if (!$user->isAdmin() && !$user->isSecretaria()) {
            $baseQuery->where('user_id', $user->id);
        }

        $hiddenForVisitors = ['Cancelada', 'Cancelado', 'Rechazado'];
        $visibleQuery = clone $baseQuery;

        if (!$user->isAdmin() && !$user->isSecretaria()) {
            $visibleQuery->whereNotIn('estado', $hiddenForVisitors);
        }

        $reservas = (clone $baseQuery)->with(['turno', 'horario'])
            ->orderBy('fecha')
            ->orderBy('horario_id')
            ->get();

        $reservasVisibles = (clone $visibleQuery)->with(['turno', 'horario'])
            ->orderBy('fecha')
            ->orderBy('horario_id')
            ->get();

        $totalReservas = (clone $visibleQuery)->count();
        $confirmadas = (clone $visibleQuery)->where('estado', 'Confirmado')->count();
        $pendientes = (clone $visibleQuery)->where('estado', 'Pendiente')->count();
        $canceladas = $user->isAdmin() || $user->isSecretaria()
            ? (clone $baseQuery)->whereIn('estado', ['Cancelada', 'Cancelado', 'Rechazado'])->count()
            : 0;

        $vista = view()->exists('usuario.dashboard') ? 'usuario.dashboard' : 'dashboard';

        $turnos = Turno::with('horarios')->where('activo', true)->get();
        $reservas = $reservasVisibles;
        $misReservas = $reservasVisibles;
        $secretariaContacto = User::where('role', 'secretaria')->first();
        $feedbackReservas = $reservasVisibles
            ->filter(fn (Reserva $reserva) => $reserva->estado === 'Confirmado')
            ->sortByDesc('fecha')
            ->values();

        return view($vista, compact(
            'reservas',
            'misReservas',
            'totalReservas',
            'confirmadas',
            'pendientes',
            'canceladas',
            'turnos',
            'secretariaContacto',
            'feedbackReservas'
        ));
    }

    /**
     * INDEX: Listado completo de "Mis Reservas"
     */
    public function index()
    {
        $user = auth()->user();
        $query = Reserva::with(['user', 'turno', 'horario']);

        if (!$user->isAdmin() && !$user->isSecretaria()) {
            $query->where('user_id', $user->id)
                ->whereNotIn('estado', ['Cancelada', 'Cancelado', 'Rechazado']);
        }

        $reservas = $query->orderBy('fecha')->orderBy('horario_id')->paginate(15);
        return view('reservas.index', compact('reservas'));
    }
    public function reservasIndex(Request $request)
    {
        // Traemos todas las reservas de la base de datos con sus relaciones cargadas
        $reservas = Reserva::with(['user', 'horario', 'turno'])
            ->latest()
            ->paginate(10);

        // Renderizamos la vista premium de administración que creamos
        return view('secretaria.reservas-index', compact('reservas'));
    }
    
    protected function authorizeAccess(Reserva $reserva)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->isSecretaria() && $reserva->user_id !== $user->id) {
            abort(403, 'No tienes permiso para acceder a esta reserva.');
        }
    }

    public function create()
    {
        $secretariaContacto = User::query()
            ->select('id', 'name', 'email', 'telefono', 'role')
            ->where('role', 'secretaria')
            ->first();
        $reservedSlots = [];

        return view('reservas.create', compact('secretariaContacto', 'reservedSlots'));
    }

    public function disponibilidad()
    {
        return response()->json([
            'slots' => $this->reservedSlotsForCalendar(),
        ]);
    }

    public function store(Request $request)
    {
        // Validamos lo que nos manda el fetch de JS
        $validated = $request->validate([
            'fecha' => 'required|date|after_or_equal:today',
            'cantidad_personas' => 'required|integer|min:1',
            'hora_inicio' => 'required|string',
            'descripcion' => 'nullable|string|max:500',
            'nombre' => 'nullable|string|max:255',
            'correo' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
        ]);

        $user = auth()->user();
        $fechaReserva = Carbon::parse($validated['fecha']);

        if ($fechaReserva->isWeekend()) {
            $message = 'No se pueden realizar reservas en sábados o domingos.';

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return back()->withInput()->with('error', $message);
        }

        // BUSCAMOS EL TURNO Y EL HORARIO BASADO EN LA HORA QUE ELIGIÓ
        $horaInicio = substr($validated['hora_inicio'], 0, 5);
        $horaFin = str_contains($validated['hora_inicio'], '-')
            ? trim(substr($validated['hora_inicio'], strpos($validated['hora_inicio'], '-') + 1, 5))
            : Carbon::parse($horaInicio)->addMinutes(90)->format('H:i');

        $horario = Horario::where('hora_inicio', $horaInicio)->first();

        if (!$horario) {
            $turno = Turno::firstOrCreate(
                ['nombre' => 'Sesiones guiadas'],
                [
                    'hora_inicio' => '08:30',
                    'hora_fin' => '19:00',
                    'capacidad_maxima' => 30,
                    'activo' => true,
                ]
            );

            $horario = Horario::create([
                'turno_id' => $turno->id,
                'hora_inicio' => $horaInicio,
                'hora_fin' => $horaFin,
            ]);
        }

        // Si no encuentra el horario en la BD, creamos la reserva igual como respaldo.
        $turno_id = $horario->turno_id;
        $horario_id = $horario->id;

        $slotOcupado = Reserva::whereDate('fecha', $fechaReserva->toDateString())
            ->where('horario_id', $horario_id)
            ->whereNotIn('estado', ['Cancelada', 'Cancelado', 'Rechazado'])
            ->exists();

        if ($slotOcupado) {
            return response()->json([
                'success' => false,
                'message' => 'Ese horario ya está ocupado. Selecciona otra hora disponible para la misma fecha.',
            ], 422);
        }

        $estado = ($user->isAdmin() || $user->isSecretaria()) ? 'Confirmado' : 'Pendiente';

        $reserva = Reserva::create([
            'user_id' => $user->id,
            'nombre' => $validated['nombre'] ?? $user->name,
            'correo' => $validated['correo'] ?? $user->email,
            'telefono' => $validated['telefono'] ?? 'No especificado',
            'cantidad_personas' => $validated['cantidad_personas'],
            'fecha' => $validated['fecha'],
            'turno_id' => $turno_id,
            'horario_id' => $horario_id,
            'descripcion' => $validated['descripcion'],
            'estado' => $estado,
        ]);

        // 🔔 ADICIONADO: DISPARAR LA NOTIFICACIÓN PARA LA SECRETARÍA
        // Solo enviamos la notificación si el que reserva es un usuario común (estado pendiente)
        if ($estado === 'Pendiente') {
            $secretarias = $this->secretariaRecipients();
            foreach ($secretarias as $secretaria) {
                $secretaria->notify(new NuevaReservaNotification($reserva));
            }

            $this->programarCorreosNuevaReservaSecretaria(
                $secretarias->pluck('id')->all(),
                $reserva->id
            );
        }

        $reserva->load(['horario', 'turno']);
        $secretariaContacto = User::where('role', 'secretaria')->first();
        $whatsappUrl = $this->secretariaWhatsappUrl($reserva, $secretariaContacto);

        // Como la petición viene de JS (fetch), devolvemos JSON en lugar de redirigir.
        return response()->json([
            'success' => true,
            'message' => 'Reserva registrada correctamente. Espera la confirmación de secretaría.',
            'codigo' => 'RES-' . str_pad((string) $reserva->id, 4, '0', STR_PAD_LEFT),
            'whatsapp_url' => $whatsappUrl,
            'reserva' => [
                'id' => $reserva->id,
                'fecha' => $reserva->fecha?->format('d/m/Y') ?? Carbon::parse($reserva->fecha)->format('d/m/Y'),
                'hora' => $reserva->horario
                    ? substr($reserva->horario->hora_inicio, 0, 5) . ' - ' . substr($reserva->horario->hora_fin, 0, 5)
                    : $horaInicio . ' - ' . $horaFin,
                'estado' => $reserva->estado,
                'personas' => $reserva->cantidad_personas,
                'nombre' => $reserva->nombre,
                'correo' => $reserva->correo,
                'telefono' => $reserva->telefono,
            ],
        ]);
    }

    private function secretariaWhatsappUrl(Reserva $reserva, ?User $secretariaContacto): ?string
    {
        $phone = preg_replace('/\D+/', '', (string) ($secretariaContacto?->telefono ?: '78837658'));

        if (!$phone) {
            return null;
        }

        if (!str_starts_with($phone, '591')) {
            $phone = '591' . $phone;
        }

        $hora = $reserva->horario
            ? substr($reserva->horario->hora_inicio, 0, 5) . ' - ' . substr($reserva->horario->hora_fin, 0, 5)
            : 'Horario registrado';

        $message = "Hola secretaria, acabo de registrar mi reserva en el Observatorio Max Schreier.\n\n"
            . 'Código: RES-' . str_pad((string) $reserva->id, 4, '0', STR_PAD_LEFT) . "\n"
            . 'Nombre: ' . $reserva->nombre . "\n"
            . 'Fecha: ' . Carbon::parse($reserva->fecha)->format('d/m/Y') . "\n"
            . 'Hora: ' . $hora . "\n"
            . 'Personas: ' . $reserva->cantidad_personas . "\n\n"
            . 'Quedo atento a la confirmación de la reserva.';

        return 'https://wa.me/' . $phone . '?text=' . rawurlencode($message);
    }

    private function secretariaRecipients()
    {
        return User::query()
            ->whereIn('role', ['secretaria', 'Secretaria', 'SECRETARIA', 'secretaría', 'Secretaría'])
            ->orWhere('role', 'admin')
            ->get();
    }

    private function programarCorreosNuevaReservaSecretaria(array $secretariaIds, int $reservaId): void
    {
        if (empty($secretariaIds)) {
            return;
        }

        dispatch(function () use ($secretariaIds, $reservaId) {
            $reserva = Reserva::with(['horario', 'turno', 'user'])->find($reservaId);

            if (! $reserva) {
                return;
            }

            User::whereIn('id', $secretariaIds)
                ->get()
                ->each(fn (User $secretaria) => self::mailNuevaReservaSecretaria($secretaria, $reserva));
        })->afterResponse();
    }

    private static function mailNuevaReservaSecretaria(User $secretaria, Reserva $reserva): void
    {
        if (!filter_var($secretaria->email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        try {
            $reserva->loadMissing(['horario', 'turno', 'user']);
            $hora = $reserva->horario
                ? substr($reserva->horario->hora_inicio, 0, 5) . ' - ' . substr($reserva->horario->hora_fin, 0, 5)
                : 'Sin hora';

            $message = "Nueva reserva pendiente de confirmación de pago.\n\n"
                . 'Código: RES-' . str_pad((string) $reserva->id, 4, '0', STR_PAD_LEFT) . "\n"
                . 'Visitante: ' . $reserva->nombre . "\n"
                . 'Correo: ' . $reserva->correo . "\n"
                . 'Teléfono: ' . $reserva->telefono . "\n"
                . 'Fecha: ' . Carbon::parse($reserva->fecha)->format('d/m/Y') . "\n"
                . 'Hora: ' . $hora . "\n"
                . 'Personas: ' . $reserva->cantidad_personas . "\n\n"
                . 'Ingresa al panel de secretaría para validar el pago y confirmar la reserva.';

            Mail::raw($message, function ($mail) use ($secretaria) {
                $mail->to($secretaria->email)
                    ->subject('Nueva reserva pendiente de pago - Observatorio Max Schreier');
            });
        } catch (\Throwable $e) {
            Log::warning('No se pudo enviar correo de nueva reserva a secretaría.', [
                'secretaria_id' => $secretaria->id,
                'reserva_id' => $reserva->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function notifySecretariasReservaModificada(Reserva $reserva): void
    {
        foreach ($this->secretariaRecipients() as $secretaria) {
            $secretaria->notify(new ReservaModificadaSecretariaNotification($reserva));
            $this->mailReservaModificadaSecretaria($secretaria, $reserva);
        }
    }

    private function mailReservaModificadaSecretaria(User $secretaria, Reserva $reserva): void
    {
        if (!filter_var($secretaria->email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        try {
            $hora = $reserva->horario
                ? substr($reserva->horario->hora_inicio, 0, 5) . ' - ' . substr($reserva->horario->hora_fin, 0, 5)
                : 'Sin hora';

            $message = "Un usuario modificó su reserva y requiere nueva revisión.\n\n"
                . 'Código: RES-' . str_pad((string) $reserva->id, 4, '0', STR_PAD_LEFT) . "\n"
                . 'Visitante: ' . $reserva->nombre . "\n"
                . 'Correo: ' . $reserva->correo . "\n"
                . 'Teléfono: ' . $reserva->telefono . "\n"
                . 'Nueva fecha: ' . Carbon::parse($reserva->fecha)->format('d/m/Y') . "\n"
                . 'Nuevo horario: ' . $hora . "\n"
                . 'Personas: ' . $reserva->cantidad_personas . "\n"
                . 'Motivo/observación: ' . ($reserva->descripcion ?: 'Sin observación') . "\n\n"
                . 'La reserva volvió a estado Pendiente para validación de secretaría.';

            Mail::raw($message, function ($mail) use ($secretaria) {
                $mail->to($secretaria->email)
                    ->subject('Reserva modificada pendiente de revisión - Observatorio Max Schreier');
            });
        } catch (\Throwable $e) {
            Log::warning('No se pudo enviar correo de reserva modificada a secretaría.', [
                'secretaria_id' => $secretaria->id,
                'reserva_id' => $reserva->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function reservedSlotsForCalendar(?int $excludeReservaId = null): array
    {
        $query = Reserva::query()
            ->leftJoin('horarios', 'reservas.horario_id', '=', 'horarios.id')
            ->select([
                'reservas.id',
                'reservas.fecha',
                'reservas.cantidad_personas',
                'reservas.estado',
                'reservas.nombre',
                'horarios.hora_inicio',
            ])
            ->whereDate('fecha', '>=', now()->toDateString())
            ->whereDate('fecha', '<=', now()->addMonths(6)->toDateString())
            ->whereNotIn('estado', ['Cancelada', 'Cancelado', 'Rechazado']);

        if ($excludeReservaId) {
            $query->where('reservas.id', '!=', $excludeReservaId);
        }

        return $query
            ->get()
            ->groupBy(fn ($reserva) => Carbon::parse($reserva->fecha)->format('Y-m-d'))
            ->map(function ($reservas) {
                return $reservas
                    ->filter(fn ($reserva) => $reserva->hora_inicio)
                    ->mapWithKeys(function ($reserva) {
                        $inicio = substr($reserva->hora_inicio, 0, 5);

                        return [$inicio => [
                            'ocupado' => true,
                            'personas' => (int) $reserva->cantidad_personas,
                            'estado' => $reserva->estado,
                            'nombre' => $reserva->nombre,
                        ]];
                    })
                    ->all();
            })
            ->all();
    }

    private function isHoliday(Carbon $date): bool
    {
        $fixedHolidays = [
            '01-01',
            '01-22',
            '05-01',
            '08-06',
            '11-02',
            '12-25',
        ];

        return in_array($date->format('m-d'), $fixedHolidays, true);
    }

    public function show(Reserva $reserva)
    {
        $this->authorizeAccess($reserva);
        $reserva->load(['turno', 'horario', 'user']);
        return view('reservas.show', compact('reserva'));
    }

    public function edit(Reserva $reserva)
    {
        $this->authorizeAccess($reserva);
        $user = auth()->user();

        // LÓGICA DE VENTANA EXCLUSIVA DE 24 HORAS PARA USUARIOS COMUNES
        if (!$user->isAdmin() && !$user->isSecretaria()) {
            $fechaString = is_a($reserva->fecha, 'Carbon\Carbon') ? $reserva->fecha->format('Y-m-d') : Carbon::parse($reserva->fecha)->format('Y-m-d');
            $fechaTurno = Carbon::parse($fechaString . ' ' . $reserva->horario->hora_inicio);

            $horasRestantes = now()->diffInHours($fechaTurno, false);

            // BLOQUEO: Si faltan más de 24 horas O si ya pasó la hora O si está cancelada
            if ($horasRestantes > 24 || $horasRestantes < 0 || $reserva->estado === 'Cancelada') {
                return redirect()->route('reservas.index')
                    ->with('error', 'Solo puedes modificar tu reserva dentro de las 24 horas previas al turno.');
            }
        }

        $turnos = Turno::with('horarios')->where('activo', true)->get();
        $horariosDisponibles = Horario::where('turno_id', $reserva->turno_id)->get();
        $reservedSlots = $this->reservedSlotsForCalendar($reserva->id);

        return view('reservas.edit', compact('reserva', 'turnos', 'horariosDisponibles', 'reservedSlots'));
    }

    public function update(Request $request, Reserva $reserva)
    {
        $this->authorizeAccess($reserva);
        $user = auth()->user();

        if (!$user->isAdmin() && !$user->isSecretaria()) {
            $fechaString = $reserva->fecha instanceof Carbon
                ? $reserva->fecha->format('Y-m-d')
                : Carbon::parse($reserva->fecha)->format('Y-m-d');
            $fechaTurno = Carbon::parse($fechaString . ' ' . ($reserva->horario?->hora_inicio ?? '00:00'));
            $horasRestantes = now()->diffInHours($fechaTurno, false);

            if ($horasRestantes > 24 || $horasRestantes < 0 || in_array($reserva->estado, ['Cancelada', 'Cancelado', 'Rechazado'])) {
                $message = 'Solo puedes modificar tu reserva dentro de las 24 horas previas al turno.';

                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 403);
                }

                return redirect()->route('reservas.index')->with('error', $message);
            }
        }

        $validated = $request->validate([
            'fecha' => 'required|date',
            'cantidad_personas' => 'required|integer|min:1',
            'hora_inicio' => 'nullable|string',
            'turno_id' => 'nullable|exists:turnos,id',
            'horario_id' => 'nullable|exists:horarios,id',
            'descripcion' => $user->isAdmin() || $user->isSecretaria() ? 'nullable|string|max:700' : 'required|string|min:10|max:700',
            'estado' => 'nullable|string|in:Pendiente,Confirmado,Cancelada', // 🌟 ADICIONADO: Validar el estado
        ]);

        $fechaAjustada = Carbon::parse($validated['fecha']);

        if ($fechaAjustada->isWeekend()) {
            $message = 'No se pueden modificar reservas para sábados o domingos.';

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return back()->withInput()->with('error', $message);
        }

        if (!empty($validated['hora_inicio'])) {
            $horaInicio = substr($validated['hora_inicio'], 0, 5);
            $horaFin = str_contains($validated['hora_inicio'], '-')
                ? trim(substr($validated['hora_inicio'], strpos($validated['hora_inicio'], '-') + 1, 5))
                : Carbon::parse($horaInicio)->addMinutes(90)->format('H:i');

            $horario = Horario::where('hora_inicio', $horaInicio)->first();

            if (!$horario) {
                $turno = Turno::firstOrCreate(
                    ['nombre' => 'Sesiones guiadas'],
                    [
                        'hora_inicio' => '08:30',
                        'hora_fin' => '19:00',
                        'capacidad_maxima' => 30,
                        'activo' => true,
                    ]
                );

                $horario = Horario::create([
                    'turno_id' => $turno->id,
                    'hora_inicio' => $horaInicio,
                    'hora_fin' => $horaFin,
                ]);
            }

            $validated['turno_id'] = $horario->turno_id;
            $validated['horario_id'] = $horario->id;
        }

        $slotOcupado = Reserva::whereDate('fecha', $fechaAjustada->toDateString())
            ->where('horario_id', $validated['horario_id'] ?? $reserva->horario_id)
            ->where('id', '!=', $reserva->id)
            ->whereNotIn('estado', ['Cancelada', 'Cancelado', 'Rechazado'])
            ->exists();

        if ($slotOcupado) {
            $message = 'Ese horario ya está ocupado por otra reserva.';

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return back()->withInput()->with('error', $message);
        }

        if (!$user->isAdmin() && !$user->isSecretaria()) {
            $validated['estado'] = 'Pendiente';
        } else {
            unset($validated['estado']);
        }

        if (($user->isAdmin() || $user->isSecretaria()) && $request->has('estado')) {
            $validated['estado'] = $request->estado;
        }

        $reserva->update($validated);
        $reserva->load(['user', 'horario', 'turno']);

        if (!$user->isAdmin() && !$user->isSecretaria()) {
            $this->notifySecretariasReservaModificada($reserva);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $user->isSecretaria()
                    ? 'Reserva actualizada por la Secretaría.'
                    : 'Reserva modificada correctamente. Quedó pendiente de revisión por secretaría.',
                'reserva' => [
                    'id' => $reserva->id,
                    'estado' => $reserva->estado,
                    'fecha' => Carbon::parse($reserva->fecha)->format('d/m/Y'),
                    'hora' => $reserva->horario
                        ? substr($reserva->horario->hora_inicio, 0, 5) . ' - ' . substr($reserva->horario->hora_fin, 0, 5)
                        : 'Sin hora',
                ],
            ]);
        }

        // 🌟 REDIRECCIÓN INTELIGENTE: Si edita la secretaria, la devolvemos a su panel premium
        if ($user->isSecretaria()) {
            return redirect()
                ->route('secretaria.dashboard')
                ->with('success', 'Reserva actualizada por la Secretaría.');
        }

        return redirect()->route('reservas.index')->with('success', 'Reserva actualizada correctamente.');
    }

    public function destroy(Reserva $reserva)
    {
        $this->authorizeAccess($reserva);
        $user = auth()->user();
        $reserva->delete();

        if ($user->isSecretaria()) {
            return redirect()
                ->route('secretaria.dashboard')
                ->with('success', 'Reserva eliminada correctamente.');
        }

        return redirect()->route('reservas.index')->with('success', 'Reserva eliminada correctamente.');
    }
}
