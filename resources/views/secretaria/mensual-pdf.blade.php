<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Reservas</title>
    <style>
        @page { margin: 1.5cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; font-size: 11px; line-height: 1.5; }
        
        /* Encabezado Institucional */
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; border-bottom: 3px solid #1d4ed8; padding-bottom: 10px; }
        .header-table td { vertical-align: middle; }
        .logo-img { width: 68px; height: 68px; object-fit: cover; border-radius: 8px; }
        .logo-fallback { width: 64px; height: 64px; border: 2px solid #1d4ed8; text-align: center; line-height: 64px; font-weight: bold; color: #1d4ed8; }
        .header-logo { width: 60%; font-size: 15px; font-weight: bold; color: #1e3a8a; text-transform: uppercase; line-height: 1.2; }
        .header-meta { width: 40%; text-align: right; color: #4b5563; font-size: 10px; }
        
        .title { text-align: center; font-size: 16px; font-weight: bold; color: #111827; margin-bottom: 5px; text-transform: uppercase; }
        .subtitle { text-align: center; font-size: 11px; color: #4b5563; margin-bottom: 25px; }
        
        /* Bloque de Observaciones Escritas por la Secretaria */
        .observaciones-box { background-color: #f9fafb; padding: 12px; border-radius: 6px; border: 1px solid #e5e7eb; margin-bottom: 20px; }
        .observaciones-title { font-size: 10px; font-weight: bold; color: #1e3a8a; text-transform: uppercase; margin-bottom: 4px; }
        
        /* Tarjetas de Resumen Estadístico */
        .stats-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .stat-card { width: 18%; padding: 10px; background: #f3f4f6; border-radius: 6px; text-align: center; border: 1px solid #e5e7eb; }
        .stat-value { font-size: 14px; font-weight: bold; color: #1f2937; margin-top: 2px; }
        .stat-label { font-size: 8px; color: #6b7280; font-weight: bold; text-transform: uppercase; }
        
        /* Tabla Principal de Datos */
        .data-table { width: 100%; border-collapse: collapse; margin-top: 5px; margin-bottom: 30px; }
        .data-table th { background-color: #1e3a8a; color: white; text-align: left; padding: 7px 10px; font-size: 10px; text-transform: uppercase; }
        .data-table td { padding: 8px 10px; border-bottom: 1px solid #e5e7eb; font-size: 10px; vertical-align: middle; }
        .data-table tr:nth-child(even) { background-color: #f9fafb; }
        
        /* Badges de Estado adaptados para DomPDF */
        .badge { padding: 2px 6px; border-radius: 4px; font-size: 8px; font-weight: bold; text-transform: uppercase; display: inline-block; }
        .badge-confirmado { background-color: #def7ec; color: #03543f; }
        .badge-pendiente { background-color: #fef3c7; color: #92400e; }
        .badge-cancelado { background-color: #fde8e8; color: #9b1c1c; }
        
        /* Sección de Firmas */
        .signature-table { width: 100%; border-collapse: collapse; margin-top: 40px; page-break-inside: avoid; }
        .signature-line { border-top: 1px solid #9ca3af; width: 200px; margin: 0 auto; }
        .signature-text { font-size: 10px; font-weight: bold; color: #374151; margin-top: 4px; margin-bottom: 0; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 5px; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td style="width: 80px;">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" class="logo-img" alt="Observatorio Max Schreier">
                @else
                    <div class="logo-fallback">MS</div>
                @endif
            </td>
            <td class="header-logo">
                OBSERVATORIO MAX SCHREIER<br>
                <span style="font-size: 10px; color: #4b5563; font-weight: normal; text-transform: uppercase;">Carrera de Física - Facultad de Ciencias Puras y Naturales - UMSA</span>
            </td>
            <td class="header-meta">
                <strong>Generado el:</strong> {{ $fechaGeneracion }}<br>
                <strong>Área:</strong> Secretaría General
            </td>
        </tr>
    </table>

    <div class="title">{{ $titulo }}</div>
    <div class="subtitle">Periodo evaluado: <strong>{{ $rango_fechas }}</strong></div>

    @if(!empty($observaciones))
        <div class="observaciones-box">
            <div class="observaciones-title">Notas e Incidentes del Periodo:</div>
            <div style="color: #4b5563; white-space: pre-line;">{{ $observaciones }}</div>
        </div>
    @endif

    <table class="stats-table">
        <tr>
            <td class="stat-card">
                <div class="stat-label">Total Solicitudes</div>
                <div class="stat-value">{{ $totalReservas }}</div>
            </td>
            <td style="width: 2%;"></td>
            <td class="stat-card" style="border-left: 3px solid #0e9f6e;">
                <div class="stat-label">Confirmadas</div>
                <div class="stat-value">{{ $confirmadas }}</div>
            </td>
            <td style="width: 2%;"></td>
            <td class="stat-card" style="border-left: 3px solid #b45309;">
                <div class="stat-label">Pendientes</div>
                <div class="stat-value">{{ $pendientes }}</div>
            </td>
            <td style="width: 2%;"></td>
            <td class="stat-card" style="border-left: 3px solid #c81e1e;">
                <div class="stat-label">Canceladas</div>
                <div class="stat-value">{{ $canceladas }}</div>
            </td>
            <td style="width: 2%;"></td>
            <td class="stat-card" style="background-color: #eff6ff; border: 1px solid #bfdbfe;">
                <div class="stat-label">Total Visitantes</div>
                <div class="stat-value">{{ $totalAsistentes }}</div>
            </td>
        </tr>
    </table>

    <h3 style="color: #1f2937; margin-bottom: 8px; font-size: 11px; text-transform: uppercase;">Historial Detallado del Periodo</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 15%;">Fecha</th>
                <th style="width: 15%;">Horario</th>
                <th style="width: 35%;">Usuario / Visitante</th>
                <th style="width: 15%; text-align: center;">Asistentes</th>
                <th style="width: 20%; text-align: center;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reservas as $reserva)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y') }}</td>
                    <td>{{ $reserva->horario->hora_inicio ?? 'N/A' }}</td>
                    <td>
                        <strong style="color: #111827;">{{ $reserva->nombre }}</strong><br>
                        <span style="font-size: 8px; color: #6b7280;">{{ $reserva->correo }}</span>
                    </td>
                    <td style="text-align: center;"><strong>{{ $reserva->cantidad_personas }} PAX</strong></td>
                    <td style="text-align: center;">
                        @if($reserva->estado === 'Confirmado')
                            <span class="badge badge-confirmado">Confirmado</span>
                        @elseif($reserva->estado === 'Pendiente')
                            <span class="badge badge-pendiente">Pendiente</span>
                        @else
                            <span class="badge badge-cancelado">Cancelado</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #6b7280; padding: 20px;">
                        No se encontraron registros de reservas en el rango de fechas seleccionado.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if(!empty($firmado_por))
        <table class="signature-table">
            <tr>
                <td style="width: 50%;"></td>
                <td style="width: 50%; text-align: center;">
                    <div class="signature-line"></div>
                    <p class="signature-text">{{ $firmado_por }}</p>
                    <p style="margin: 0; font-size: 8px; color: #6b7280; text-transform: uppercase;">Responsable de Emisión</p>
                </td>
            </tr>
        </table>
    @endif

    <div class="footer">
        Sistema Automatizado de Reservas - UMSA Max Schreier
    </div>

</body>
</html>
