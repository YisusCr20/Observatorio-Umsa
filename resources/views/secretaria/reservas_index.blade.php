<x-app-layout>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <div x-data="{ sidebarOpen: false }"
        class="h-screen w-screen flex flex-col md:flex-row bg-[#F4F7FE] text-slate-900 font-sans antialiased overflow-hidden">

        {{-- SIDEBAR ESTILO USUARIO (Azul Vibrante) --}}
        <aside :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-blue-600 text-white transition-transform duration-300 md:relative md:translate-x-0 flex flex-col h-screen shadow-2xl shrink-0">

            <div class="p-6">
                {{-- Logo --}}
                <div class="flex items-center gap-3 mb-10">
                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg">
                        <span class="text-blue-600 font-black text-xs">MS</span>
                    </div>
                    <div class="leading-tight">
                        <h1 class="font-black text-xs uppercase tracking-tight">Max Schreier</h1>
                        <p class="text-[9px] text-blue-100 uppercase opacity-80">Observatorio Online</p>
                    </div>
                </div>

                {{-- Perfil Secretaría --}}
                <div class="flex items-center gap-3 mb-8 p-3 bg-white/10 rounded-2xl border border-white/20">
                    <div
                        class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-blue-600 font-bold shadow-sm">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-bold text-[11px] truncate uppercase">{{ auth()->user()->name }}</p>
                        <p class="text-[9px] text-emerald-300 font-bold flex items-center gap-1 uppercase">
                            <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full"></span> Activo ahora
                        </p>
                    </div>
                </div>

                <p class="text-[9px] font-black text-blue-200 uppercase tracking-widest mb-4 px-2 opacity-60">Menú
                    Principal</p>

                <nav class="space-y-1">
                    {{-- 🔄 RUTA ACTUALIZADA AL DASHBOARD DE LA SECRETARIA --}}
                    <a href="{{ route('secretaria.dashboard') }}"
                        class="flex items-center gap-3 text-blue-100 hover:bg-white/10 px-4 py-3 rounded-xl font-bold text-[10px] uppercase tracking-wide transition-all">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        Dashboard
                    </a>

                    {{-- 🔄 RUTA INTERNA ACTUALIZADA CON PREFIJO PARA ESTA PANTALLA ACTIVA --}}
                    <a href="{{ route('secretaria.reservas.index') }}"
                        class="flex items-center gap-3 bg-white/20 text-white px-4 py-3 rounded-xl font-bold text-[10px] uppercase tracking-wide transition-all shadow-lg border border-white/10">
                        <i data-lucide="users" class="w-4 h-4"></i>
                        Reservas de Usuarios
                    </a>
                    <a href="{{ route('secretaria.reportes.pdf') }}"
                        class="flex items-center gap-3 text-blue-100 hover:bg-white/10 px-4 py-3 rounded-xl font-bold text-[10px] uppercase tracking-wide transition-all">
                        <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                        Reportes
                    </a>
                    <a href="#"
                        class="flex items-center gap-3 text-blue-100 hover:bg-white/10 px-4 py-3 rounded-xl font-bold text-[10px] uppercase tracking-wide transition-all">
                        <i data-lucide="banknote" class="w-4 h-4"></i>
                        Historial de Pagos
                    </a>
                </nav>
            </div>

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

        {{-- CONTENIDO DE LA TABLA GLOBAL --}}
        <main class="flex-1 h-full flex flex-col p-4 md:p-8 space-y-6 overflow-hidden">

            {{-- HEADER SUPERIOR --}}
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 shrink-0">
                <div>
                    <h2 class="text-2xl font-black text-blue-700 tracking-tight">Reservas de Usuarios</h2>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wide mt-0.5">Historial y Registro
                        General del Observatorio</p>
                </div>

                <a href="{{ route('reservas.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-black uppercase text-[10px] tracking-widest shadow-xl shadow-blue-600/30 transition-all flex items-center gap-2 self-start sm:self-auto">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Nueva Reserva
                </a>
            </div>

            {{-- CONTENEDOR DE LA TABLA MAESTRA --}}
            <div
                class="bg-white rounded-3xl shadow-sm border border-slate-100 flex flex-col flex-1 min-h-0 overflow-hidden">

                <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/50 shrink-0">
                    <h3 class="text-[11px] font-black uppercase tracking-widest text-slate-700 flex items-center gap-2">
                        <i data-lucide="layers" class="w-4 h-4 text-blue-600"></i> Listado Global de Solicitudes
                    </h3>
                    <span class="text-[10px] font-black bg-blue-50 text-blue-600 px-3 py-1 rounded-full">
                        {{ $reservas->total() }} Registros en total
                    </span>
                </div>

                {{-- CUERPO DE LA TABLA CON SCROLL AISLADO --}}
                <div class="flex-1 overflow-y-auto pattern-scroll">
                    <table class="w-full text-left border-collapse">
                        <thead class="sticky top-0 bg-white z-10 shadow-sm">
                            <tr
                                class="text-[9px] font-black text-slate-400 uppercase tracking-tighter border-b border-slate-50">
                                <th class="px-6 py-4">Fecha Planificada</th>
                                <th class="px-6 py-4">Hora</th>
                                <th class="px-6 py-4">Datos del Visitante</th>
                                <th class="px-6 py-4 text-center">Cupos (Pax)</th>
                                <th class="px-6 py-4 text-center">Estado Actual</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($reservas as $reserva)
                                <tr class="hover:bg-blue-50/30 transition-colors">
                                    {{-- Fecha formateada --}}
                                    <td class="px-6 py-4 text-[11px] font-black text-slate-700 whitespace-nowrap uppercase">
                                        {{ \Carbon\Carbon::parse($reserva->fecha)->translatedFormat('d \d\e F, Y') }}
                                    </td>

                                    {{-- Hora --}}
                                    <td class="px-6 py-4 text-[10px] font-bold text-slate-500 whitespace-nowrap">
                                        {{ $reserva->horario ? $reserva->horario->hora_inicio : 'No asignada' }}
                                    </td>

                                    {{-- Usuario --}}
                                    <td class="px-6 py-4">
                                        <div
                                            class="text-[11px] font-black text-slate-800 uppercase tracking-tight truncate max-w-[240px]">
                                            {{ $reserva->nombre ?? ($reserva->user ? $reserva->user->name : 'Invitado') }}
                                        </div>
                                        <div class="text-[9px] text-slate-400 truncate max-w-[240px]">
                                            {{ $reserva->correo ?? ($reserva->user ? $reserva->user->email : 'Sin correo electrónico') }}
                                        </div>
                                    </td>

                                    {{-- Cantidad de personas --}}
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            class="text-[10px] font-black text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg">
                                            {{ $reserva->cantidad_personas }} Pax
                                        </span>
                                    </td>

                                    {{-- Estado Dinámico --}}
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-[8px] font-black px-3 py-1 rounded-full uppercase inline-block min-w-[85px] text-center
                                                {{ $reserva->estado === 'Confirmado' ? 'bg-emerald-100 text-emerald-600' : '' }}
                                                {{ $reserva->estado === 'Pendiente' ? 'bg-amber-100 text-amber-600' : '' }}
                                                {{ $reserva->estado === 'Cancelado' ? 'bg-red-100 text-red-600' : '' }}">
                                            {{ $reserva->estado }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-16 text-center opacity-40">
                                        <i data-lucide="folder-open" class="w-10 h-10 mx-auto mb-2 text-slate-300"></i>
                                        <p class="text-[10px] font-bold uppercase">No se encontraron reservas registradas en
                                            el sistema</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PIE DE PAGINA CON ENLACES DE PAGINACIÓN --}}
                <div class="p-4 border-t border-slate-50 bg-slate-50/30 shrink-0">
                    {{ $reservas->links() }}
                </div>

            </div>
        </main>
    </div>
    <script> lucide.createIcons(); </script>
</x-app-layout>