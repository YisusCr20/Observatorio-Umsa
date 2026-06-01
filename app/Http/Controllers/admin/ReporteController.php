<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function exportarReservasPDF(Request $request)
    {
        // Filtro opcional por rango de fechas si se requiere
        $fecha_inicio = $request->input('fecha_inicio');
        $fecha_fin = $request->input('fecha_fin');

        $query = Reserva::with(['usuario.persona', 'horario', 'pago']);

        if ($fecha_inicio && $fecha_fin) {
            $query->whereBetween('fecha_reserva', [$fecha_inicio, $fecha_fin]);
        }

        $reservas = $query->get();

        // Carga la vista blade diseñada exclusivamente para el formato del PDF
        $pdf = Pdf::loadView('admin.reportes.reservas_pdf', compact('reservas', 'fecha_inicio', 'fecha_fin'));
        
        // Retorna el archivo para descarga o visualización en el navegador
        return $pdf->download('Reporte_Reservas_Observatorio.pdf');
    }
}