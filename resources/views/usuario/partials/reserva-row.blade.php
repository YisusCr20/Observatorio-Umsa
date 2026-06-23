@php
    $fecha = $reserva->fecha instanceof \Carbon\Carbon ? $reserva->fecha : \Carbon\Carbon::parse($reserva->fecha);
    $estado = $reserva->estado;
    $estadoVisible = $estado === 'Pendiente' ? 'Pendiente a confirmación de pago' : $estado;
    $badge = match ($estado) {
        'Confirmado' => 'bg-emerald-50 text-emerald-600 border-emerald-200 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400',
        'Pendiente' => 'bg-yellow-50 text-yellow-600 border-yellow-200 dark:bg-yellow-900/20 dark:border-yellow-800 dark:text-yellow-400',
        'Cancelada', 'Cancelado', 'Rechazado' => 'bg-red-50 text-red-600 border-red-200 dark:bg-red-900/20 dark:border-red-800 dark:text-red-400',
        default => 'bg-gray-50 text-gray-600 border-gray-200 dark:bg-[#3A3B3C] dark:border-gray-700 dark:text-gray-400',
    };
    $icon = match ($estado) {
        'Confirmado' => 'check-circle',
        'Pendiente' => 'clock',
        'Cancelada', 'Cancelado', 'Rechazado' => 'x-circle',
        default => 'circle',
    };
    $rowIcon = function (string $name, string $class = 'w-4 h-4') {
        $paths = [
            'calendar-days' => '<path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/>',
            'check-circle' => '<path d="M22 11.1V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/>',
            'clock' => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
            'x-circle' => '<circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/>',
            'eye' => '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>',
            'edit-3' => '<path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
            'lock' => '<rect width="18" height="11" x="3" y="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
            'circle' => '<circle cx="12" cy="12" r="9"/>',
        ];
        $path = $paths[$name] ?? $paths['circle'];

        return '<svg xmlns="http://www.w3.org/2000/svg" class="' . e($class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $path . '</svg>';
    };
    $horaInicio = $reserva->horario?->hora_inicio ? substr($reserva->horario->hora_inicio, 0, 5) : 'Sin hora';
    $horaFin = $reserva->horario?->hora_fin ? substr($reserva->horario->hora_fin, 0, 5) : null;
    $fechaTurno = $reserva->horario?->hora_inicio
        ? \Carbon\Carbon::parse($fecha->format('Y-m-d') . ' ' . $reserva->horario->hora_inicio)
        : null;
    $horasRestantes = $fechaTurno ? now()->diffInHours($fechaTurno, false) : null;
    $puedeEditar = $fechaTurno && $horasRestantes >= 0 && $horasRestantes <= 24 && ! in_array($estado, ['Cancelada', 'Cancelado', 'Rechazado']);
    $mensajeBloqueo = in_array($estado, ['Cancelada', 'Cancelado', 'Rechazado'])
        ? 'No puedes editar una reserva cancelada o rechazada.'
        : 'Podrás editar esta reserva únicamente dentro de las 24 horas previas a la visita.';
    $seguimiento = match ($estado) {
        'Confirmado' => 'Secretaría validó el pago y confirmó tu visita. Llega 10 minutos antes con tu documento.',
        'Pendiente' => 'Tu solicitud está pendiente. Secretaría revisará pago, cupos y disponibilidad.',
        'Cancelada', 'Cancelado' => 'La reserva fue cancelada. Puedes crear una nueva solicitud si deseas reprogramar.',
        'Rechazado' => 'La reserva fue rechazada. Revisa tu correo o comunícate con secretaría para más información.',
        default => 'Secretaría hará seguimiento a esta solicitud.',
    };
@endphp

<div class="p-4 sm:p-5 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 hover:bg-gray-50 dark:hover:bg-[#1E2124] transition">
    <div class="flex gap-4 items-start min-w-0">
        <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-blue-50 dark:bg-blue-900/20 text-[#1877F2] shrink-0">
            {!! $rowIcon('calendar-days', 'w-5 h-5') !!}
        </div>
        <div class="min-w-0">
            <p class="font-black text-gray-800 dark:text-gray-200">
                Reserva para {{ $reserva->cantidad_personas }} persona(s)
            </p>
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mt-1">
                {{ $fecha->format('d/m/Y') }} · {{ $horaInicio }}
                @if($horaFin)
                    - {{ $horaFin }}
                @endif
            </p>
            @if(!($compact ?? false) && $reserva->descripcion)
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $reserva->descripcion }}</p>
            @endif
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-2 lg:justify-end">
        <span class="{{ $badge }} inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest border">
            {!! $rowIcon($icon, 'w-3.5 h-3.5') !!}
            {{ $estadoVisible }}
        </span>
        <button type="button"
            @click="openReservaDetail({
                codigo: @js('RES-' . str_pad((string) $reserva->id, 4, '0', STR_PAD_LEFT)),
                fecha: @js($fecha->format('d/m/Y')),
                hora: @js($horaFin ? $horaInicio . ' - ' . $horaFin : $horaInicio),
                personas: @js($reserva->cantidad_personas),
                estado: @js($estadoVisible),
                descripcion: @js($reserva->descripcion ?: 'Sin observación registrada.'),
                seguimiento: @js($seguimiento),
                titular: @js($reserva->nombre ?? optional($reserva->user)->name ?? 'Visitante'),
                correo: @js($reserva->correo ?? optional($reserva->user)->email ?? 'Sin correo'),
                telefono: @js($reserva->telefono ?? optional($reserva->user)->telefono ?? 'Sin teléfono')
            })"
            class="inline-flex items-center gap-1.5 rounded-xl bg-gray-100 dark:bg-[#18191A] text-gray-600 dark:text-gray-300 px-3 py-2 text-[10px] font-black uppercase">
            {!! $rowIcon('eye', 'w-3.5 h-3.5') !!}
            Ver
        </button>
        <button type="button"
            @click="@js($puedeEditar) ? openReservaFrame(@js(route('reservas.edit', $reserva) . '?embedded=1')) : showPanelMessage(@js($mensajeBloqueo), 'error')"
            class="inline-flex items-center gap-1.5 rounded-xl {{ $puedeEditar ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-300' : 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-300' }} px-3 py-2 text-[10px] font-black uppercase">
            {!! $rowIcon($puedeEditar ? 'edit-3' : 'lock', 'w-3.5 h-3.5') !!}
            Editar
        </button>
    </div>
</div>
