<x-app-layout>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    {{-- 🌟 SOLUCIÓN: Fijamos la altura de la pantalla a h-screen y evitamos desbordamiento global con overflow-hidden
    --}}
    <div x-data="{ 
            darkMode: false, {{-- Forzado a light por ahora para igualar el estilo de la imagen --}}
            sidebarOpen: false 
        }"
        class="h-screen w-screen flex flex-col md:flex-row bg-[#F4F7FE] text-slate-900 font-sans antialiased overflow-hidden">

        {{-- SIDEBAR ESTILO USUARIO (Azul Vibrante) --}}
        <aside :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-blue-600 text-white transition-transform duration-300 md:relative md:translate-x-0 flex flex-col h-screen shadow-2xl shrink-0">

            <div class="p-6">
                {{-- Logo Estilo Imagen --}}
                <div class="flex items-center gap-3 mb-10">
                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg">
                        <span class="text-blue-600 font-black text-xs">MS</span>
                    </div>
                    <div class="leading-tight">
                        <h1 class="font-black text-xs uppercase tracking-tight">Max Schreier</h1>
                        <p class="text-[9px] text-blue-100 uppercase opacity-80">Observatorio Online</p>
                    </div>
                </div>

                {{-- PERFIL SECRETARÍA --}}
                <div class="flex items-center gap-3 mb-8 p-3 bg-white/10 rounded-2xl border border-white/20">
                    <div
                        class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-blue-600 font-bold shadow-sm">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-bold text-[11px] truncate uppercase">{{ auth()->user()->name }}</p>
                        <p class="text-[9px] text-emerald-300 font-bold flex items-center gap-1 uppercase">
                            <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span> Activo ahora
                        </p>
                    </div>
                </div>

                <p class="text-[9px] font-black text-blue-200 uppercase tracking-widest mb-4 px-2 opacity-60">Menú
                    Principal</p>

                <nav class="space-y-1">
                    <a href="#"
                        class="flex items-center gap-3 bg-white/20 text-white px-4 py-3 rounded-xl font-bold text-[10px] uppercase tracking-wide transition-all shadow-lg border border-white/10">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('secretaria.reservas.index') }}"
                        class="flex items-center gap-3 text-blue-100 hover:bg-white/10 px-4 py-3 rounded-xl font-bold text-[10px] uppercase tracking-wide transition-all">
                        <i data-lucide="users" class="w-4 h-4"></i>
                        Reservas de Usuarios
                    </a>
                    <a href="{{ route('secretaria.reportes.index') }}"
                        class="flex items-center gap-3 text-blue-100 hover:bg-white/10 px-4 py-3 rounded-xl font-bold text-[10px] uppercase tracking-wide transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Reportes
                    </a>
                    <a href="#"
                        class="flex items-center gap-3 text-blue-100 hover:bg-white/10 px-4 py-3 rounded-xl font-bold text-[10px] uppercase tracking-wide transition-all">
                        <i data-lucide="banknote" class="w-4 h-4"></i>
                        Historial de Pagos
                    </a>
                </nav>
            </div>

            {{-- BOTÓN CERRAR SESIÓN AL FINAL --}}
            <div class="mt-auto p-6">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        class="w-full bg-[#3b5998] hover:bg-[#2d4373] text-white py-3 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all shadow-lg border border-white/10">
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </aside>

        {{-- CONTENIDO PRINCIPAL --}}
        {{-- 🌟 SOLUCIÓN: Forzamos a que el main ocupe el alto restante de la pantalla de forma estricta (h-full flex
        flex-col) --}}
        <main class="flex-1 h-full flex flex-col p-4 md:p-8 space-y-6 overflow-hidden">

            {{-- HEADER SUPERIOR --}}
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 shrink-0">
                <h2 class="text-2xl font-black text-blue-700 tracking-tight">Bienvenido,
                    {{ explode(' ', auth()->user()->name)[0] }}
                </h2>

                <div class="flex items-center gap-3">
                    {{-- 🔔 COMPONENTE DE NOTIFICACIONES --}}
                    <div x-data="{ openNotification: false }" class="relative">
                        <button @click="openNotification = !openNotification"
                            class="p-2.5 bg-white rounded-xl shadow-sm text-slate-400 hover:text-blue-600 transition-colors relative focus:outline-none">
                            <i data-lucide="bell" class="w-5 h-5"></i>

                            @if($notificacionesUnread->count() > 0)
                                <span
                                    class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[9px] font-black rounded-full flex items-center justify-center animate-bounce">
                                    {{ $notificacionesUnread->count() }}
                                </span>
                            @endif
                        </button>

                        {{-- Menú desplegable flotante --}}
                        <div x-show="openNotification" @click.away="openNotification = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-slate-100 z-50 py-2 overflow-hidden"
                            style="display: none;">

                            <div
                                class="px-4 py-2 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
                                <span
                                    class="text-[9px] font-black uppercase text-slate-500 tracking-wider">Notificaciones
                                    de Control</span>
                                @if($notificacionesUnread->count() > 0)
                                    <a href="{{ route('notifications.markRead') }}"
                                        class="text-[8px] font-black text-blue-600 hover:underline">Marcar como leídas</a>
                                @endif
                            </div>

                            <div class="max-h-64 overflow-y-auto divide-y divide-slate-50">
                                @forelse($notificacionesUnread as $notification)
                                    <div class="p-3 hover:bg-slate-50 transition-colors flex items-start gap-2.5">
                                        <div class="w-2 h-2 bg-blue-500 rounded-full mt-1.5 shrink-0"></div>
                                        <div class="flex-1">
                                            <p class="text-[10px] text-slate-700 font-bold leading-tight">
                                                {{ $notification->data['mensaje'] ?? 'Nueva actividad registrada en el sistema.' }}
                                            </p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-6 text-center text-slate-400 opacity-70">
                                        <i data-lucide="bell-off" class="w-5 h-5 mx-auto mb-1 text-slate-300"></i>
                                        <p class="text-[9px] font-black uppercase tracking-wider">Sin novedades por revisar
                                        </p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('reservas.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-black uppercase text-[10px] tracking-widest shadow-xl shadow-blue-600/30 transition-all flex items-center gap-2">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        Nueva Reserva
                    </a>
                </div>
            </div>

            {{-- ESTADÍSTICAS (Se mantienen fijas arriba) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 shrink-0">
                <div
                    class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between group hover:shadow-md transition-all">
                    <div>
                        <p class="text-[9px] font-black text-blue-500 uppercase tracking-widest mb-1">Total</p>
                        <h3 class="text-2xl font-black text-slate-800">{{ $totalAsistentes }}</h3>
                    </div>
                    <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-500">
                        <i data-lucide="users" class="w-5 h-5"></i>
                    </div>
                </div>

                <div
                    class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between group">
                    <div>
                        <p class="text-[9px] font-black text-emerald-500 uppercase tracking-widest mb-1">Confirmadas</p>
                        <h3 class="text-2xl font-black text-slate-800">{{ $reservasConfirmadas }}</h3>
                    </div>
                    <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-500">
                        <i data-lucide="check-circle" class="w-5 h-5"></i>
                    </div>
                </div>

                <div
                    class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between group">
                    <div>
                        <p class="text-[9px] font-black text-amber-500 uppercase tracking-widest mb-1">Pendientes</p>
                        <h3 class="text-2xl font-black text-slate-800">{{ $reservasPendientesCuenta }}</h3>
                    </div>
                    <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center text-amber-500">
                        <i data-lucide="clock" class="w-5 h-5"></i>
                    </div>
                </div>

                <div
                    class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between group">
                    <div>
                        <p class="text-[9px] font-black text-red-500 uppercase tracking-widest mb-1">Canceladas</p>
                        <h3 class="text-2xl font-black text-slate-800">{{ $reservasCanceladasCuenta }}</h3>
                    </div>
                    <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center text-red-500">
                        <i data-lucide="x-circle" class="w-5 h-5"></i>
                    </div>
                </div>
            </div>

            {{-- SECCIÓN DE LA TABLA Y EL SIDEBAR DE PENDIENTES --}}
            {{-- 🌟 SOLUCIÓN: flex-1 y overflow-hidden obligan a este contenedor a ajustarse dinámicamente al tamaño que
            sobra abajo --}}
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 flex-1 min-h-0 overflow-hidden">

                {{-- TABLA DE ASISTENTES (HISTORIAL RECIENTE) --}}
                {{-- 🌟 SOLUCIÓN: Al añadir flex flex-col y overflow-hidden, la tarjeta se vuelve responsiva
                internamente --}}
                <div
                    class="xl:col-span-2 bg-white rounded-3xl shadow-sm border border-slate-100 flex flex-col overflow-hidden h-full">
                    <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/50 shrink-0">
                        <h3
                            class="text-[11px] font-black uppercase tracking-widest text-slate-700 flex items-center gap-2">
                            <i data-lucide="calendar-check" class="w-4 h-4 text-blue-600"></i> Historial Reciente
                        </h3>
                        <a href="#" class="text-[10px] font-bold text-blue-600 hover:underline">Ver todas</a>
                    </div>

                    {{-- 🌟 SOLUCIÓN: Agregamos overflow-y-auto aquí. Las cabeceras se quedan fijas y solo los registros
                    hacen scroll --}}
                    <div class="flex-1 overflow-y-auto pattern-scroll">
                        <table class="w-full text-left border-collapse">
                            <thead class="sticky top-0 bg-white z-10 shadow-sm">
                                <tr
                                    class="text-[9px] font-black text-slate-400 uppercase tracking-tighter border-b border-slate-50">
                                    <th class="px-6 py-4">Hora</th>
                                    <th class="px-6 py-4">Usuario</th>
                                    <th class="px-6 py-4 text-center">Personas</th>
                                    <th class="px-6 py-4 text-right">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($asistentesHoy as $reserva)
                                    <tr class="hover:bg-blue-50/30 transition-colors">
                                        <td class="px-6 py-4 text-[10px] font-bold text-slate-500 whitespace-nowrap">
                                            {{ $reserva->horario ? $reserva->horario->hora_inicio : 'Sin hora' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div
                                                class="text-[11px] font-black text-slate-800 uppercase tracking-tight truncate max-w-[200px]">
                                                {{ $reserva->nombre ?? ($reserva->user ? $reserva->user->name : 'Invitado') }}
                                            </div>
                                            <div class="text-[9px] text-slate-400 truncate max-w-[200px]">
                                                {{ $reserva->correo ?? ($reserva->user ? $reserva->user->email : '') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span
                                                class="text-[10px] font-black text-blue-600 bg-blue-50 px-2 py-1 rounded-lg">
                                                {{ $reserva->cantidad_personas }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span
                                                class="text-[8px] font-black px-2 py-1 rounded-full uppercase bg-emerald-100 text-emerald-600">
                                                {{ $reserva->estado }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-12 text-center opacity-40">
                                            <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2"></i>
                                            <p class="text-[10px] font-bold uppercase">No hay visitas confirmadas para el
                                                día de hoy</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- SIDEBAR DE PENDIENTES --}}
                {{-- 🌟 SOLUCIÓN: flex flex-col y overflow-hidden para que mantenga su scroll interno aislado si llegan
                muchas solicitudes --}}
                <div class="flex flex-col h-full overflow-hidden space-y-4">
                    <h3
                        class="text-[10px] font-black uppercase tracking-widest text-slate-400 flex items-center gap-2 shrink-0">
                        <span class="w-2 h-2 bg-amber-500 rounded-full animate-pulse"></span> Por Validar
                    </h3>

                    <div class="flex-1 overflow-y-auto space-y-3 pb-4">
                        @forelse($reservasPendientes as $reserva)
                            <div
                                class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 hover:border-blue-200 transition-all group">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="min-w-0">
                                        <p class="font-black text-[10px] uppercase text-slate-800 tracking-tight truncate">
                                            {{ $reserva->nombre ?? ($reserva->user ? $reserva->user->name : 'Invitado') }}
                                        </p>
                                        <p class="text-[8px] text-blue-500 font-bold uppercase mt-0.5">
                                            {{ \Carbon\Carbon::parse($reserva->fecha)->format('d M') }} •
                                            {{ $reserva->cantidad_personas }} Pax
                                        </p>
                                    </div>
                                    <div
                                        class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center text-amber-500 shrink-0">
                                        <i data-lucide="hourglass" class="w-4 h-4"></i>
                                    </div>
                                </div>

                                <div class="flex gap-2">
                                    <form action="{{ route('secretaria.reservas.status', $reserva->id) }}" method="POST"
                                        class="flex-1">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="estado" value="Confirmado">
                                        <button type="submit"
                                            class="w-full bg-blue-600 hover:bg-blue-700 text-white text-[8px] font-black uppercase py-2 rounded-lg transition shadow-md">
                                            Validar
                                        </button>
                                    </form>

                                    <form action="{{ route('secretaria.reservas.status', $reserva->id) }}" method="POST"
                                        class="flex-1">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="estado" value="Cancelado">
                                        <button type="submit"
                                            class="w-full bg-slate-50 hover:bg-red-50 text-slate-400 hover:text-red-500 text-[8px] font-black uppercase py-2 rounded-lg border border-slate-100 transition">
                                            Negar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div
                                class="bg-slate-100/50 border-2 border-dashed border-slate-200 p-8 rounded-3xl text-center">
                                <p class="text-[9px] font-black text-slate-400 uppercase">Sin solicitudes</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script> lucide.createIcons(); </script>
</x-app-layout>