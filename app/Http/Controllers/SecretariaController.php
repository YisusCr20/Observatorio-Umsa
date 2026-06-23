<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\Turno;
use App\Models\Pago;
use App\Models\VisitFeedback;
use Carbon\Carbon;
use App\Notifications\ReservaConfirmadaNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SecretariaController extends Controller
{
    /**
     * 🖥️ Dashboard Principal de la Secretaría
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user || (!$user->isSecretaria() && !$user->isAdmin())) {
            abort(403, 'No autorizado');
        }

        $hoy = Carbon::today();

        $asistentesHoy = Reserva::with(['user', 'turno', 'horario', 'pago'])
            ->whereDate('fecha', $hoy)
            ->whereIn('estado', ['Confirmado', 'Cancelado', 'Cancelada', 'Rechazado'])
            ->latest()
            ->get();

        $reservasPendientes = Reserva::with(['user', 'turno', 'horario', 'pago'])
            ->where('estado', 'Pendiente')
            ->orderBy('fecha', 'asc')
            ->get();

        $totalReservas = Reserva::count();
        $totalAsistentes = (int) Reserva::where('estado', 'Confirmado')->sum('cantidad_personas');
        $reservasConfirmadas = $asistentesHoy->where('estado', 'Confirmado')->count();
        $reservasPendientesCuenta = $reservasPendientes->count();
        $reservasCanceladasCuenta = Reserva::whereIn('estado', ['Cancelado', 'Rechazado', 'Cancelada'])->count();
        $pagosPendientes = Schema::hasTable('pagos')
            ? Reserva::where('estado', 'Pendiente')->doesntHave('pagos')->count()
            : $reservasPendientesCuenta;

        $turnosCupos = Turno::with(['reservas' => function ($q) use ($hoy) {
            $q->whereDate('fecha', $hoy)->whereIn('estado', ['Confirmado', 'Pendiente']);
        }])->get();

        [$reportFechaInicio, $reportFechaFin] = $this->resolveDateRange($request);
        $reportReservas = $this->reservasForRange($reportFechaInicio, $reportFechaFin);
        $reservasPanel = Reserva::with(['user', 'horario', 'turno', 'pago'])
            ->latest()
            ->limit(40)
            ->get();
        $pagosDashboard = Pago::with(['reserva.user', 'reserva.horario', 'secretaria'])
            ->orderByDesc('pagado_en')
            ->orderByDesc('created_at')
            ->limit(25)
            ->get();
        $reservasSinPago = Reserva::with(['user', 'horario'])
            ->whereIn('estado', ['Pendiente', 'Confirmado'])
            ->whereDoesntHave('pagos')
            ->orderBy('fecha', 'desc')
            ->get();
        $feedbackReciente = Schema::hasTable('visit_feedback')
            ? VisitFeedback::with(['user', 'reserva.horario'])
                ->latest()
                ->limit(8)
                ->get()
            : collect();

        return view('secretaria.dashboard', [
            'asistentesHoy' => $asistentesHoy,
            'reservasPendientes' => $reservasPendientes,
            'totalReservas' => $totalReservas,
            'totalAsistentes' => $totalAsistentes,
            'reservasConfirmadas' => $reservasConfirmadas,
            'reservasPendientesCuenta' => $reservasPendientesCuenta,
            'reservasCanceladasCuenta' => $reservasCanceladasCuenta,
            'pagosPendientes' => $pagosPendientes,
            'turnosCupos' => $turnosCupos,
            'notificacionesUnread' => $user->unreadNotifications,
            'activePanel' => in_array($request->get('panel'), ['reportes', 'pagos'], true)
                ? $request->get('panel')
                : 'dashboard',
            'reservasPanel' => $reservasPanel,
            'pagosDashboard' => $pagosDashboard,
            'reservasSinPago' => $reservasSinPago,
            'feedbackReciente' => $feedbackReciente,
            'reportFechaInicio' => $reportFechaInicio,
            'reportFechaFin' => $reportFechaFin,
            'reportPreset' => $request->get('preset', 'mensual'),
            'reportReservas' => $reportReservas,
            'reportStats' => [
                'total' => $reportReservas->count(),
                'confirmadas' => $reportReservas->where('estado', 'Confirmado')->count(),
                'pendientes' => $reportReservas->where('estado', 'Pendiente')->count(),
                'canceladas' => $reportReservas->whereIn('estado', ['Cancelado', 'Rechazado', 'Cancelada'])->count(),
                'visitantes' => (int) $reportReservas->where('estado', 'Confirmado')->sum('cantidad_personas'),
            ],
        ]);
    }

    /**
     * 🔄 Actualizar estado y notificar automáticamente
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['estado' => 'required|in:Confirmado,Pendiente,Cancelado,Rechazado,Cancelada']);

        $reserva = Reserva::with('user')->findOrFail($id);
        $reserva->estado = $request->estado;
        $reserva->save();

        if (in_array($reserva->estado, ['Confirmado', 'Cancelado', 'Rechazado', 'Cancelada'])) {
            if ($reserva->user) {
                $reserva->user->notify(new ReservaConfirmadaNotification($reserva));
                Log::info("Notificación enviada a {$reserva->user->email} por cambio de estado a {$reserva->estado}");
            }
        }

        return back()->with('success', 'Estado actualizado y notificación enviada.');
    }

    /**
     * 📂 Listado histórico de reservas
     */
    public function historialReservas()
    {
        $todasLasReservas = Reserva::with(['user', 'turno', 'horario'])
            ->orderBy('fecha', 'desc')
            ->paginate(15);
        return view('secretaria.historial_reservas', compact('todasLasReservas'));
    }

    public function indexPendientes()
    {
        return redirect()
            ->route('secretaria.dashboard')
            ->with('success', 'Las reservas pendientes se muestran en el panel Por validar.');
    }

    /**
     * 📊 Exportar Reporte PDF
     */
    public function exportarReportePdf(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'titulo' => 'required|string|max:100',
        ]);

        $inicio = Carbon::parse($request->fecha_inicio)->startOfDay();
        $fin = Carbon::parse($request->fecha_fin)->endOfDay();

        $data = [
            'titulo' => $request->titulo,
            'observaciones' => $request->observaciones,
            'firmado_por' => $request->firmado_por,
            'rango_fechas' => 'Del ' . $inicio->format('d/m/Y') . ' al ' . $fin->format('d/m/Y'),
            'fechaGeneracion' => Carbon::now()->format('d/m/Y H:i'),
            'totalReservas' => Reserva::whereBetween('fecha', [$inicio, $fin])->count(),
            'confirmadas' => Reserva::whereBetween('fecha', [$inicio, $fin])->where('estado', 'Confirmado')->count(),
            'pendientes' => Reserva::whereBetween('fecha', [$inicio, $fin])->where('estado', 'Pendiente')->count(),
            'canceladas' => Reserva::whereIn('estado', ['Cancelado', 'Rechazado', 'Cancelada'])->whereBetween('fecha', [$inicio, $fin])->count(),
            'totalAsistentes' => Reserva::whereBetween('fecha', [$inicio, $fin])->where('estado', 'Confirmado')->sum('cantidad_personas'),
            'reservas' => Reserva::with(['user', 'horario'])->whereBetween('fecha', [$inicio, $fin])->orderBy('fecha', 'asc')->get(),
            'logoBase64' => $this->logoBase64(),
        ];

        return Pdf::loadView('secretaria.mensual-pdf', $data)
            ->download('Reporte_'.date('Y_m_d').'.pdf');
    }

    public function panelReportes(Request $request)
    {
        [$inicio, $fin] = $this->resolveDateRange($request);

        $reservas = $this->reservasForRange($inicio, $fin);

        return view('secretaria.reportes.index', [
            'fechaInicio' => $inicio,
            'fechaFin' => $fin,
            'preset' => $request->get('preset', 'mensual'),
            'reservas' => $reservas,
            'totalReservas' => $reservas->count(),
            'confirmadas' => $reservas->where('estado', 'Confirmado')->count(),
            'pendientes' => $reservas->where('estado', 'Pendiente')->count(),
            'canceladas' => $reservas->whereIn('estado', ['Cancelado', 'Rechazado', 'Cancelada'])->count(),
            'totalAsistentes' => (int) $reservas->where('estado', 'Confirmado')->sum('cantidad_personas'),
        ]);
    }

    /**
     * 💳 Historial de pagos
     */
    public function historialPagos()
    {
        $pagos = Pago::with(['reserva.user', 'reserva.horario', 'secretaria'])
            ->orderByDesc('pagado_en')
            ->orderByDesc('created_at')
            ->paginate(15);

        $reservasSinPago = Reserva::with(['user', 'horario'])
            ->whereIn('estado', ['Pendiente', 'Confirmado'])
            ->whereDoesntHave('pagos')
            ->orderBy('fecha', 'desc')
            ->get();

        return view('secretaria.historial_pagos', compact('pagos', 'reservasSinPago'));
    }

    /**
     * ➕ Registrar Pago manual
     */
    public function registrarPagoManual(Request $request)
    {
        $request->validate([
            'reserva_id' => 'required|exists:reservas,id',
            'monto' => 'required|numeric|min:0',
            'metodo_pago' => 'required|in:Efectivo,Transferencia,Depósito,QR',
            'nro_comprobante' => 'nullable|string|max:120',
            'observacion' => 'nullable|string|max:500',
        ]);

        $pago = Pago::create([
            'reserva_id' => $request->reserva_id,
            'registrado_por' => auth()->id(),
            'monto' => $request->monto,
            'metodo_pago' => $request->metodo_pago,
            'nro_comprobante' => $request->nro_comprobante ?? 'MANUAL-'.time(),
            'estado_pago' => 'Completado',
            'observacion' => $request->observacion,
            'pagado_en' => now(),
        ]);

        $reserva = Reserva::with('user')->findOrFail($request->reserva_id);
        $reserva->update(['estado' => 'Confirmado']);

        if ($reserva->user) {
            $reserva->user->notify(new ReservaConfirmadaNotification($reserva));
        }

        if ($request->boolean('return_to_dashboard')) {
            return redirect()
                ->route('secretaria.dashboard', ['panel' => 'pagos'])
                ->with('success', 'Pago registrado y reserva confirmada.');
        }

        return redirect()->route('secretaria.pagos.verificar')->with('success', 'Pago registrado y reserva confirmada.');
    }

    public function pagosPdf(Request $request)
    {
        [$inicio, $fin] = $this->resolveDateRange($request);

        $pagos = Pago::with(['reserva.user', 'reserva.horario', 'secretaria'])
            ->whereBetween('pagado_en', [$inicio->copy()->startOfDay(), $fin->copy()->endOfDay()])
            ->orderBy('pagado_en')
            ->get();

        return Pdf::loadView('secretaria.reportes.pagos-pdf', [
            'pagos' => $pagos,
            'fechaInicio' => $inicio,
            'fechaFin' => $fin,
            'fechaGeneracion' => now()->format('d/m/Y H:i'),
            'totalPagos' => $pagos->count(),
            'totalMonto' => $pagos->sum('monto'),
            'logoBase64' => $this->logoBase64(),
        ])->download('reporte_pagos_' . now()->format('Ymd_His') . '.pdf');
    }

    public function reservasIndex()
    {
        $reservas = Reserva::with(['user', 'horario', 'pago'])->latest()->paginate(10);
        return view('secretaria.reservas_index', compact('reservas'));
    }

    private function resolveDateRange(Request $request): array
    {
        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            return [
                Carbon::parse($request->fecha_inicio)->startOfDay(),
                Carbon::parse($request->fecha_fin)->endOfDay(),
            ];
        }

        return match ($request->get('preset', 'mensual')) {
            'semanal' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'anual' => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            default => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        };
    }

    private function logoBase64(): ?string
    {
        if (! extension_loaded('gd')) {
            return null;
        }

        $path = public_path('images/observatorio-logo.png');

        if (! file_exists($path)) {
            return null;
        }

        $mime = mime_content_type($path) ?: 'image/png';

        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
    }

    private function reservasForRange(Carbon $inicio, Carbon $fin)
    {
        return Reserva::with(['user', 'turno', 'horario', 'pago'])
            ->whereBetween('fecha', [$inicio->toDateString(), $fin->toDateString()])
            ->orderBy('fecha')
            ->orderBy('horario_id')
            ->get();
    }
}
