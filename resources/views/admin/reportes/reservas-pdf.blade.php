<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        @page { margin: 1.35cm; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10.5px; color: #172033; }
        .header { width: 100%; border-bottom: 3px solid #1d4ed8; padding-bottom: 12px; margin-bottom: 18px; }
        .header td { vertical-align: middle; }
        .logo { width: 72px; height: 72px; object-fit: contain; }
        .logo-fallback { width: 68px; height: 68px; border: 2px solid #1d4ed8; border-radius: 50%; text-align: center; line-height: 68px; font-size: 16px; font-weight: bold; color: #1d4ed8; }
        .institution { text-align: center; line-height: 1.25; }
        .institution .u { font-size: 12px; font-weight: bold; text-transform: uppercase; color: #1e3a8a; }
        .institution .f { font-size: 10px; text-transform: uppercase; color: #334155; }
        .institution .o { font-size: 16px; font-weight: bold; text-transform: uppercase; color: #0f172a; margin-top: 4px; }
        .meta { text-align: right; font-size: 9px; color: #64748b; }
        .title { text-align: center; font-size: 15px; font-weight: bold; text-transform: uppercase; margin: 16px 0 4px; }
        .subtitle { text-align: center; color: #64748b; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        .stats td { background: #eef4ff; border: 1px solid #c7d2fe; text-align: center; padding: 9px 5px; }
        .stat-value { font-size: 16px; font-weight: bold; color: #1d4ed8; }
        .stat-label { font-size: 7.5px; color: #475569; text-transform: uppercase; font-weight: bold; }
        .data th { background: #1e3a8a; color: white; padding: 7px; text-align: left; font-size: 8px; text-transform: uppercase; }
        .data td { padding: 7px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        .data tr:nth-child(even) { background: #f8fafc; }
        .badge { font-size: 7.5px; font-weight: bold; text-transform: uppercase; border-radius: 4px; padding: 2px 5px; }
        .ok { background: #dcfce7; color: #166534; }
        .wait { background: #fef3c7; color: #92400e; }
        .bad { background: #fee2e2; color: #991b1b; }
        .footer { position: fixed; bottom: -5px; left: 0; right: 0; text-align: center; color: #94a3b8; font-size: 8px; border-top: 1px solid #e2e8f0; padding-top: 5px; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td style="width: 85px;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" class="logo" alt="Observatorio Max Schreier">
                @else
                    <div class="logo-fallback">MS</div>
                @endif
            </td>
            <td class="institution">
                <div class="u">Universidad Mayor de San Andrés</div>
                <div class="f">Facultad de Ciencias Puras y Naturales - Carrera de Física</div>
                <div class="o">Observatorio Astronómico Max Schreier</div>
            </td>
            <td class="meta" style="width: 135px;">
                <strong>Generado:</strong><br>{{ $fechaGeneracion }}<br>
                <strong>Sistema:</strong><br>SISOBS
            </td>
        </tr>
    </table>

    <div class="title">{{ $titulo }}</div>
    <div class="subtitle">Periodo evaluado: <strong>{{ $rango }}</strong></div>

    <table class="stats" style="margin-bottom: 18px;">
        <tr>
            <td><div class="stat-label">Reservas</div><div class="stat-value">{{ $totalReservas }}</div></td>
            <td><div class="stat-label">Usuarios únicos</div><div class="stat-value">{{ $usuariosUnicos }}</div></td>
            <td><div class="stat-label">Visitantes confirmados</div><div class="stat-value">{{ $visitantes }}</div></td>
            <td><div class="stat-label">Confirmadas</div><div class="stat-value">{{ $confirmadas }}</div></td>
            <td><div class="stat-label">Pendientes</div><div class="stat-value">{{ $pendientes }}</div></td>
            <td><div class="stat-label">Canceladas</div><div class="stat-value">{{ $canceladas }}</div></td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Visitante / Usuario</th>
                <th style="text-align:center;">Personas</th>
                <th>Pago</th>
                <th>Estado</th>
                <th>Observación / Modificación</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reservas as $reserva)
                @php
                    $estadoClass = $reserva->estado === 'Confirmado' ? 'ok' : ($reserva->estado === 'Pendiente' ? 'wait' : 'bad');
                    $modificada = $reserva->updated_at && $reserva->created_at && $reserva->updated_at->gt($reserva->created_at->copy()->addMinute());
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y') }}</td>
                    <td>
                        {{ $reserva->horario?->hora_inicio ?? 'N/A' }}
                        @if($reserva->horario?->hora_fin)
                            - {{ $reserva->horario->hora_fin }}
                        @endif
                    </td>
                    <td>
                        <strong>{{ $reserva->nombre }}</strong><br>
                        <span style="color:#64748b;">{{ $reserva->correo }}</span>
                    </td>
                    <td style="text-align:center; font-weight:bold;">{{ $reserva->cantidad_personas }}</td>
                    <td>
                        @if($reserva->pago)
                            Bs. {{ number_format((float) $reserva->pago->monto, 2) }}<br>
                            <span style="color:#64748b;">{{ $reserva->pago->metodo_pago }}</span>
                        @else
                            Sin pago
                        @endif
                    </td>
                    <td><span class="badge {{ $estadoClass }}">{{ $reserva->estado }}</span></td>
                    <td>
                        {{ $reserva->descripcion ?: 'Sin observación registrada.' }}
                        @if($modificada)
                            <br><span style="color:#1d4ed8;">Modificada: {{ $reserva->updated_at->format('d/m/Y H:i') }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; color:#64748b; padding: 18px;">No hay reservas en este periodo.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Reporte generado automáticamente por el Sistema de Reservas del Observatorio Max Schreier - UMSA
    </div>
</body>
</html>
