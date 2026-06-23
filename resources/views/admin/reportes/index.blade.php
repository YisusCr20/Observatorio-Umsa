<x-app-layout>
    <div class="min-h-screen bg-[#f4f7fb] dark:bg-[#07111f] text-slate-900 dark:text-slate-100 px-4 py-8">
        <div class="max-w-7xl mx-auto space-y-6">
            <header class="bg-white dark:bg-[#101b2d] border border-slate-200 dark:border-white/10 rounded-[28px] p-6 shadow-xl flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.4em] text-blue-600 dark:text-cyan-300">
                        Observatorio Max Schreier
                    </p>
                    <h1 class="text-3xl font-black mt-2">Vista previa de reportes</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Revisa datos, filtra periodos y descarga reportes institucionales en PDF.
                    </p>
                </div>

                <a href="{{ route('admin.dashboard') }}"
                   class="bg-slate-900 dark:bg-white dark:text-slate-950 text-white rounded-2xl px-5 py-3 text-xs font-black uppercase tracking-widest text-center">
                    Volver al dashboard
                </a>
            </header>

            <section class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-[#101b2d] border border-slate-200 dark:border-white/10 rounded-[28px] p-6 shadow-xl">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-5">
                        <div>
                            <h2 class="text-xl font-black uppercase tracking-widest">Reporte de reservas</h2>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                                Selecciona rango semanal, mensual, anual o personalizado.
                            </p>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('admin.reportes.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-5">
                        <select name="preset" class="rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-[#07111f] text-sm dark:text-white">
                            <option value="semanal" @selected($preset === 'semanal')>Semanal</option>
                            <option value="mensual" @selected($preset === 'mensual')>Mensual</option>
                            <option value="anual" @selected($preset === 'anual')>Anual</option>
                            <option value="personalizado" @selected($preset === 'personalizado')>Personalizado</option>
                        </select>

                        <input type="date" name="fecha_inicio" value="{{ $fechaInicio->format('Y-m-d') }}"
                               class="rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-[#07111f] text-sm dark:text-white">

                        <input type="date" name="fecha_fin" value="{{ $fechaFin->format('Y-m-d') }}"
                               class="rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-[#07111f] text-sm dark:text-white">

                        <input type="hidden" name="tipo_usuarios" value="{{ $tipoUsuarios }}">

                        <button class="bg-blue-600 hover:bg-blue-500 text-white rounded-2xl px-5 py-3 text-xs font-black uppercase tracking-widest">
                            Aplicar
                        </button>
                    </form>

                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-5">
                        @foreach ([
                            ['label' => 'Reservas', 'value' => $reservaStats['total']],
                            ['label' => 'Usuarios', 'value' => $reservaStats['usuarios']],
                            ['label' => 'Visitantes', 'value' => $reservaStats['visitantes']],
                            ['label' => 'Confirmadas', 'value' => $reservaStats['confirmadas']],
                            ['label' => 'Pendientes', 'value' => $reservaStats['pendientes']],
                        ] as $stat)
                            <div class="rounded-2xl bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 p-3 text-center">
                                <p class="text-2xl font-black">{{ $stat['value'] }}</p>
                                <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">{{ $stat['label'] }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-700 max-h-[420px]">
                        <table class="w-full text-left text-sm">
                            <thead class="sticky top-0 bg-slate-100 dark:bg-[#07111f] text-[10px] uppercase tracking-widest text-slate-500">
                                <tr>
                                    <th class="p-3">Fecha</th>
                                    <th class="p-3">Hora</th>
                                    <th class="p-3">Usuario</th>
                                    <th class="p-3 text-center">Personas</th>
                                    <th class="p-3">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @forelse ($reservas as $reserva)
                                    <tr>
                                        <td class="p-3 font-bold">{{ \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y') }}</td>
                                        <td class="p-3">{{ $reserva->horario?->hora_inicio ?? 'N/A' }}</td>
                                        <td class="p-3">
                                            <div class="font-bold">{{ $reserva->nombre }}</div>
                                            <div class="text-xs text-slate-500">{{ $reserva->correo }}</div>
                                        </td>
                                        <td class="p-3 text-center font-black">{{ $reserva->cantidad_personas }}</td>
                                        <td class="p-3">{{ $reserva->estado }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-6 text-center text-slate-400 font-bold">Sin reservas en este rango.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <a href="{{ route('admin.reportes.reservas.pdf', ['fecha_inicio' => $fechaInicio->format('Y-m-d'), 'fecha_fin' => $fechaFin->format('Y-m-d'), 'preset' => $preset]) }}"
                       class="mt-5 inline-flex bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl px-5 py-3 text-xs font-black uppercase tracking-widest">
                        Descargar PDF de reservas
                    </a>
                </div>

                <div class="bg-white dark:bg-[#101b2d] border border-slate-200 dark:border-white/10 rounded-[28px] p-6 shadow-xl">
                    <h2 class="text-xl font-black uppercase tracking-widest">Reporte de usuarios</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 mb-5">
                        Por defecto se muestran solo usuarios visitantes. También puedes incluir personal interno.
                    </p>

                    <form method="GET" action="{{ route('admin.reportes.index') }}" class="grid grid-cols-1 md:grid-cols-[1fr_auto] gap-3 mb-5">
                        <input type="hidden" name="fecha_inicio" value="{{ $fechaInicio->format('Y-m-d') }}">
                        <input type="hidden" name="fecha_fin" value="{{ $fechaFin->format('Y-m-d') }}">
                        <input type="hidden" name="preset" value="{{ $preset }}">

                        <select name="tipo_usuarios" class="rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-[#07111f] text-sm dark:text-white">
                            <option value="usuario" @selected($tipoUsuarios === 'usuario')>Solo usuarios visitantes</option>
                            <option value="internos" @selected($tipoUsuarios === 'internos')>Administradores y secretarias</option>
                            <option value="todos" @selected($tipoUsuarios === 'todos')>Todos los usuarios</option>
                        </select>

                        <button class="bg-blue-600 hover:bg-blue-500 text-white rounded-2xl px-5 py-3 text-xs font-black uppercase tracking-widest">
                            Aplicar
                        </button>
                    </form>

                    <div class="grid grid-cols-3 gap-3 mb-5">
                        <div class="rounded-2xl bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 p-3 text-center">
                            <p class="text-2xl font-black">{{ $usuarioStats['total'] }}</p>
                            <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">Total</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 p-3 text-center">
                            <p class="text-2xl font-black">{{ $usuarioStats['visitantes'] }}</p>
                            <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">Visitantes</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 p-3 text-center">
                            <p class="text-2xl font-black">{{ $usuarioStats['internos'] }}</p>
                            <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">Internos</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-700 max-h-[420px]">
                        <table class="w-full text-left text-sm">
                            <thead class="sticky top-0 bg-slate-100 dark:bg-[#07111f] text-[10px] uppercase tracking-widest text-slate-500">
                                <tr>
                                    <th class="p-3">Nombre completo</th>
                                    <th class="p-3">CI</th>
                                    <th class="p-3">Teléfono</th>
                                    <th class="p-3">Correo</th>
                                    <th class="p-3">Rol</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @forelse ($usuarios as $usuario)
                                    <tr>
                                        <td class="p-3 font-bold">{{ $usuario->name }} {{ $usuario->apellido }}</td>
                                        <td class="p-3">{{ $usuario->ci }}</td>
                                        <td class="p-3">{{ $usuario->telefono }}</td>
                                        <td class="p-3">{{ $usuario->email }}</td>
                                        <td class="p-3 uppercase text-xs font-black">{{ $usuario->role }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-6 text-center text-slate-400 font-bold">Sin usuarios para este filtro.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <a href="{{ route('admin.reportes.usuarios.pdf', ['tipo' => $tipoUsuarios]) }}"
                       class="mt-5 inline-flex bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl px-5 py-3 text-xs font-black uppercase tracking-widest">
                        Descargar PDF de usuarios
                    </a>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
