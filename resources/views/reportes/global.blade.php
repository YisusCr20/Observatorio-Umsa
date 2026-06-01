<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #1a202c; color: white; }
        .resumen { margin-bottom: 20px; background: #f4f4f4; padding: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>OBSERVATORIO UMSA</h1>
        <h2>Reporte Mensual de Reservas y Visitas</h2>
        <p>Fecha de generación: {{ $fecha }}</p>
    </div>

    <div class="resumen">
        <strong>Resumen Ejecutivo:</strong> Total de Reservas: {{ $reservas->count() }}
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Visitante</th>
                <th>Fecha</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reservas as $reserva)
            <tr>
                <td>{{ $reserva->id }}</td>
                <td>{{ $reserva->nombre_visitante }}</td>
                <td>{{ $reserva->fecha }}</td>
                <td>{{ $reserva->estado }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>