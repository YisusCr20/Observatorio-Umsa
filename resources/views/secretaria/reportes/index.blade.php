<x-app-layout>
    <script src="https://unpkg.com/lucide@latest"></script>

    <div class="min-h-screen bg-[#F4F7FE] text-slate-900 font-sans">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.35em] text-blue-500">Observatorio Max Schreier</p>
                    <h1 class="text-3xl font-black text-slate-900">Reportes de reservas</h1>
                    <p class="text-sm font-semibold text-slate-500 mt-1">Filtra por semana, mes, año o rango personalizado.</p>
                </div>

                <a href="{{ route('secretaria.dashboard') }}" class="inline-flex items-center gap-2 rounded-2xl bg-white px-4 py-3 text-xs font-black uppercase tracking-widest text-slate-700 shadow-sm border border-slate-200">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Volver
                </a>
            </div>

            <section class="bg-white rounded-[28px] border border-slate-200 shadow-sm p-5 mb-6">
                <form method="GET" action="{{ route('secretaria.reportes.index') }}" class="grid grid-cols-1 md:grid-cols-[1fr_1fr_1fr_auto] gap-3">
                    <select name="preset" class="rounded-2xl border-slate-200 bg-slate-50 text-sm">
                        <option value="mensual" @selected($preset === 'mensual')>Mensual</option>
                        <option value="semanal" @selected($preset === 'semanal')>Semanal</option>
                        <option value="anual" @selected($preset === 'anual')>Anual</option>
                        <option value="personalizado" @selected($preset === 'personalizado')>Personalizado</option>
                    </select>
                    <input type="date" name="fecha_inicio" value="{{ $fechaInicio->format('Y-m-d') }}" class="rounded-2xl border-slate-200 bg-slate-50 text-sm">
                    <input type="date" name="fecha_fin" value="{{ $fechaFin->format('Y-m-d') }}" class="rounded-2xl border-slate-200 bg-slate-50 text-sm">
                    <button class="rounded-2xl bg-blue-600 px-6 py-3 text-xs font-black uppercase tracking-widest text-white">
                        Aplicar
                    </button>
                </form>
            </section>

            <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-6">
                @foreach([
                    ['Total', $totalReservas, 'text-blue-600'],
                    ['Confirmadas', $confirmadas, 'text-emerald-600'],
                    ['Pendientes', $pendientes, 'text-amber-600'],
                    ['Canceladas', $canceladas, 'text-red-600'],
                    ['Visitantes', $totalAsistentes, 'text-slate-900'],
                ] as [$label, $value, $color])
                    <div class="bg-white rounded-2xl border border-slate-200 p-4 text-center shadow-sm">
                        <p class="text-2xl font-black {{ $color }}">{{ $value }}</p>
                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">{{ $label }}</p>
                    </div>
                @endforeach
            </div>

            <section class="bg-white rounded-[28px] border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-black uppercase tracking-widest">Vista previa</h2>
                        <p class="text-xs font-semibold text-slate-500 mt-1">{{ $fechaInicio->format('d/m/Y') }} al {{ $fechaFin->format('d/m/Y') }}</p>
                    </div>

                    <form method="POST" action="{{ route('secretaria.reportes.pdf') }}">
                        @csrf
                        <input type="hidden" name="fecha_inicio" value="{{ $fechaInicio->format('Y-m-d') }}">
                        <input type="hidden" name="fecha_fin" value="{{ $fechaFin->format('Y-m-d') }}">
                        <input type="hidden" name="titulo" value="Reporte de reservas de secretaría">
                        <input type="hidden" name="firmado_por" value="{{ auth()->user()->name }}">
                        <button class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-4 py-3 text-xs font-black uppercase tracking-widest text-white">
                            <i data-lucide="download" class="w-4 h-4"></i>
                            Descargar PDF
                        </button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-[10px] uppercase tracking-widest text-slate-500">
                            <tr>
                                <th class="p-4">Fecha</th>
                                <th class="p-4">Hora</th>
                                <th class="p-4">Visitante</th>
                                <th class="p-4">Personas</th>
                                <th class="p-4">Pago</th>
                                <th class="p-4">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($reservas as $reserva)
                                <tr>
                                    <td class="p-4 font-bold">{{ $reserva->fecha?->format('d/m/Y') }}</td>
                                    <td class="p-4">{{ $reserva->horario->hora_inicio ?? 'Sin hora' }}</td>
                                    <td class="p-4">
                                        <p class="font-black">{{ $reserva->nombre ?? optional($reserva->user)->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $reserva->correo ?? optional($reserva->user)->email }}</p>
                                    </td>
                                    <td class="p-4 font-black">{{ $reserva->cantidad_personas }}</td>
                                    <td class="p-4">{{ $reserva->pago ? 'Bs. ' . number_format((float) $reserva->pago->monto, 2) : 'Sin pago' }}</td>
                                    <td class="p-4 font-black uppercase text-xs">{{ $reserva->estado }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-10 text-center text-slate-400 font-bold">Sin reservas para el rango seleccionado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>

    <script>lucide.createIcons();</script>
</x-app-layout>
