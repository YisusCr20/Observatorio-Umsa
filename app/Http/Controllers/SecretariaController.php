<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\Turno;
use App\Models\Pago;
use Carbon\Carbon;
use App\Notifications\ReservaConfirmadaNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class SecretariaController extends Controller
{
    /**
     * 🖥️ Dashboard Principal de la Secretaría
     */
    public function index()
    {
        $user = auth()->user();
        if (!$user || (!$user->isSecretaria() && !$user->isAdmin())) {
            abort(403, 'No autorizado');
        }

        $hoy = Carbon::today();

        $asistentesHoy = Reserva::with(['user', 'turno', 'horario'])
            ->where('estado', 'Confirmado')
            ->orWhere('estado', 'Cancelado')
            ->latest()
            ->get();

        $reservasPendientes = Reserva::with(['user', 'turno', 'horario'])
            ->where('estado', 'Pendiente')
            ->orderBy('fecha', 'asc')
            ->get();

        $totalAsistentes = Reserva::where('estado', 'Confirmado')->count();
        $reservasConfirmadas = $asistentesHoy->where('estado', 'Confirmado')->count();
        $reservasPendientesCuenta = $reservasPendientes->count();
        $reservasCanceladasCuenta = Reserva::whereIn('estado', ['Cancelado', 'Rechazado', 'Cancelada'])->count();

        $turnosCupos = Turno::with(['reservas' => function ($q) use ($hoy) {
            $q->whereDate('fecha', $hoy)->whereIn('estado', ['Confirmado', 'Pendiente']);
        }])->get();

        return view('secretaria.dashboard', [
            'asistentesHoy' => $asistentesHoy,
            'reservasPendientes' => $reservasPendientes,
            'totalAsistentes' => $totalAsistentes,
            'reservasConfirmadas' => $reservasConfirmadas,
            'reservasPendientesCuenta' => $reservasPendientesCuenta,
            'reservasCanceladasCuenta' => $reservasCanceladasCuenta,
            'turnosCupos' => $turnosCupos,
            'notificacionesUnread' => $user->unreadNotifications,
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
            'reservas' => Reserva::with(['user', 'horario'])->whereBetween('fecha', [$inicio, $fin])->orderBy('fecha', 'asc')->get()
        ];

        return Pdf::loadView('secretaria.reportes.mensual-pdf', $data)
            ->download('Reporte_'.date('Y_m_d').'.pdf');
    }

    /**
     * 💳 Historial de pagos
     */
    public function historialPagos()
    {
        $pagos = Pago::with(['reserva.user'])->orderBy('created_at', 'desc')->paginate(15);
        $reservasSinPago = Reserva::where('estado', 'Pendiente')->orderBy('fecha', 'desc')->get();
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
            'metodo_pago' => 'required|in:Efectivo,Transferencia,Depósito'
        ]);

        $pago = Pago::create([
            'reserva_id' => $request->reserva_id,
            'monto' => $request->monto,
            'metodo_pago' => $request->metodo_pago,
            'nro_comprobante' => $request->nro_comprobante ?? 'MANUAL-'.time(),
            'estado_pago' => 'Completado'
        ]);

        $reserva = Reserva::with('user')->findOrFail($request->reserva_id);
        $reserva->update(['estado' => 'Confirmado']);

        if ($reserva->user) {
            $reserva->user->notify(new ReservaConfirmadaNotification($reserva));
        }

        return redirect()->route('secretaria.pagos.verificar')->with('success', 'Pago registrado y reserva confirmada.');
    }

    public function reservasIndex()
    {
        $reservas = Reserva::with(['user', 'horario'])->latest()->paginate(10);
        return view('secretaria.reservas_index', compact('reservas'));
    }
}