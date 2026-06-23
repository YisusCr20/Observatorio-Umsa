<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de pagos</title>
    <style>
        @page { margin: 1.4cm; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 10px; color: #111827; }
        .header-table { width: 100%; border-collapse: collapse; border-bottom: 3px solid #1d4ed8; padding-bottom: 10px; margin-bottom: 18px; }
        .header-table td { vertical-align: middle; }
        .logo-img { width: 68px; height: 68px; object-fit: cover; border-radius: 8px; }
        .logo-fallback { width: 64px; height: 64px; border: 2px solid #1d4ed8; text-align: center; line-height: 64px; font-weight: bold; color: #1d4ed8; }
        .brand { font-size: 16px; font-weight: bold; text-transform: uppercase; color: #1e3a8a; letter-spacing: 1px; }
        .meta { color: #64748b; margin-top: 4px; }
        .title { font-size: 18px; font-weight: bold; text-align: center; text-transform: uppercase; margin: 18px 0 4px; }
        .subtitle { text-align: center; color: #64748b; margin-bottom: 18px; }
        .stats { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        .stats td { width: 50%; background: #eff6ff; border: 1px solid #bfdbfe; padding: 12px; text-align: center; }
        .value { font-size: 18px; font-weight: bold; color: #1d4ed8; }
        .label { font-size: 8px; text-transform: uppercase; color: #64748b; font-weight: bold; }
        table.data { width: 100%; border-collapse: collapse; }
        .data th { background: #1e3a8a; color: white; padding: 7px; text-align: left; text-transform: uppercase; font-size: 8px; }
        .data td { border-bottom: 1px solid #e5e7eb; padding: 7px; vertical-align: top; }
        .data tr:nth-child(even) { background: #f8fafc; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; color: #94a3b8; font-size: 8px; border-top: 1px solid #e5e7eb; padding-top: 5px; }
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
            <td>
                <div class="brand">Observatorio Max Schreier</div>
                <div class="meta">Carrera de Física - Facultad de Ciencias Puras y Naturales - UMSA</div>
            </td>
            <td style="width: 150px; text-align: right;">
                <div class="meta"><strong>Generado:</strong><br>{{ $fechaGeneracion }}</div>
                <div class="meta"><strong>Área:</strong><br>Secretaría</div>
            </td>
        </tr>
    </table>

    <div class="title">Reporte de pagos</div>
    <div class="subtitle">Periodo: {{ $fechaInicio->format('d/m/Y') }} al {{ $fechaFin->format('d/m/Y') }}</div>

    <table class="stats">
        <tr>
            <td>
                <div class="label">Pagos registrados</div>
                <div class="value">{{ $totalPagos }}</div>
            </td>
            <td>
                <div class="label">Monto total</div>
                <div class="value">Bs. {{ number_format((float) $totalMonto, 2) }}</div>
            </td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th>Fecha pago</th>
                <th>Reserva</th>
                <th>Visitante</th>
                <th>Método</th>
                <th>Comprobante</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pagos as $pago)
                <tr>
                    <td>{{ optional($pago->pagado_en ?? $pago->created_at)->format('d/m/Y H:i') }}</td>
                    <td>
                        {{ optional($pago->reserva->fecha)->format('d/m/Y') }}<br>
                        {{ $pago->reserva->horario->hora_inicio ?? 'Sin hora' }}
                    </td>
                    <td>
                        <strong>{{ $pago->reserva->nombre ?? optional($pago->reserva->user)->name }}</strong><br>
                        {{ $pago->reserva->correo ?? optional($pago->reserva->user)->email }}
                    </td>
                    <td>{{ $pago->metodo_pago }}</td>
                    <td>{{ $pago->nro_comprobante ?: 'N/A' }}</td>
                    <td><strong>Bs. {{ number_format((float) $pago->monto, 2) }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center; color:#64748b; padding:18px;">Sin pagos registrados en este periodo.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Sistema de Reservas del Observatorio Max Schreier</div>
</body>
</html>
