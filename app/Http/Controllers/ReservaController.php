<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Turno;
use App\Models\Horario;
use App\Models\User; // <-- IMPORTANTE: Añadimos el modelo User para buscar a las secretarias
use App\Notifications\NuevaReservaNotification; // <-- IMPORTANTE: Añadimos la nueva notificación
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $reservas = (clone $baseQuery)->with(['turno', 'horario'])
            ->latest()
            ->take(5)
            ->get();

        $totalReservas = (clone $baseQuery)->count();
        $confirmadas = (clone $baseQuery)->where('estado', 'Confirmado')->count();
        $pendientes = (clone $baseQuery)->where('estado', 'Pendiente')->count();
        $canceladas = (clone $baseQuery)->where('estado', 'Cancelada')->count();

        $vista = view()->exists('usuario.dashboard') ? 'usuario.dashboard' : 'dashboard';

        return view($vista, compact(
            'reservas',
            'totalReservas',
            'confirmadas',
            'pendientes',
            'canceladas'
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
            $query->where('user_id', $user->id);
        }

        $reservas = $query->latest()->paginate(15);
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
        $turnos = Turno::with('horarios')->where('activo', true)->get();
        return view('reservas.create', compact('turnos'));
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

        // BUSCAMOS EL TURNO Y EL HORARIO BASADO EN LA HORA QUE ELIGIÓ
        $horario = Horario::where('hora_inicio', $validated['hora_inicio'])->first();

        // Si no encuentra el horario en la BD, creamos la reserva igual como respaldo.
        $turno_id = $horario ? $horario->turno_id : Turno::first()->id;
        $horario_id = $horario ? $horario->id : Horario::first()->id;

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
            $secretarias = User::where('role', 'secretaria')->get();
            foreach ($secretarias as $secretaria) {
                $secretaria->notify(new NuevaReservaNotification($reserva));
            }
        }

        // Como la petición viene de JS (fetch), devolvemos JSON en lugar de redirigir
        return response()->json(['success' => true, 'reserva' => $reserva]);
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

        return view('reservas.edit', compact('reserva', 'turnos', 'horariosDisponibles'));
    }

    public function update(Request $request, Reserva $reserva)
    {
        $this->authorizeAccess($reserva);
        $user = auth()->user();

        // ... (Mantén tu lógica de validación de 24 horas intacta) ...

        $validated = $request->validate([
            'fecha' => 'required|date',
            'cantidad_personas' => 'required|integer|min:1',
            'turno_id' => 'required|exists:turnos,id',
            'horario_id' => 'required|exists:horarios,id',
            'descripcion' => 'nullable|string|max:500',
            'estado' => 'nullable|string|in:Pendiente,Confirmado,Cancelada', // 🌟 ADICIONADO: Validar el estado
        ]);

        // Si es un usuario común, siempre se sobreescribe a Pendiente por seguridad
        if (!$user->isAdmin() && !$user->isSecretaria()) {
            $validated['estado'] = 'Pendiente';
        }
        // Si es admin o secretaria y no envió estado en el formulario, mantenemos el que ya tenía
        unset($validated['estado']);
        if (($user->isAdmin() || $user->isSecretaria()) && $request->has('estado')) {
            $validated['estado'] = $request->estado;
        }

        $reserva->update($validated);

        // 🌟 REDIRECCIÓN INTELIGENTE: Si edita la secretaria, la devolvemos a su panel premium
        if ($user->isSecretaria()) {
            return redirect()->route('secretaria.reservas.index')->with('success', 'Reserva actualizada por la Secretaría.');
        }

        return redirect()->route('reservas.index')->with('success', 'Reserva actualizada correctamente.');
    }

    public function destroy(Reserva $reserva)
    {
        $this->authorizeAccess($reserva);
        $reserva->delete();

        return view('secretaria.reservas-index', compact('reservas'));
    }
}