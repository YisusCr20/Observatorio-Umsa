<x-app-layout>
    <script src="https://unpkg.com/lucide@0.468.0/dist/umd/lucide.min.js" defer></script>
    <script>
        (function () {
            const saved = localStorage.getItem('theme');
            const dark = saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.classList.toggle('dark', dark);
        })();
    </script>
    <style>[x-cloak] { display: none !important; } html.dark body { background: #18191A; }</style>

    {{-- 🌟 SOLUCIÓN: Fijamos la altura de la pantalla a h-screen y evitamos desbordamiento global con overflow-hidden
    --}}
    <div x-data="{ 
            darkMode: localStorage.getItem('theme') === 'dark' || (window.matchMedia('(prefers-color-scheme: dark)').matches && localStorage.getItem('theme') !== 'light'),
            sidebarOpen: false,
            activePanel: @js($activePanel ?? 'dashboard'),
            reservaActionOpen: true,
            frameUrl: '',
            flashMessage: '',
            selectedReserva: {
                id: null,
                codigo: '',
                fecha: '',
                fechaIso: '',
                hora: '',
                horaInicio: '',
                horaFin: '',
                visitante: '',
                correo: '',
                telefono: '',
                personas: '',
                pago: '',
                estado: '',
                observacion: '',
                editUrl: '',
                deleteUrl: ''
            },
            setPanel(panel) {
                this.activePanel = panel;
                if (window.innerWidth < 768) this.sidebarOpen = false;
                this.$nextTick(() => window.lucide?.createIcons());
            },
            selectReserva(reserva) {
                this.selectedReserva = reserva;
                this.reservaActionOpen = true;
                this.activePanel = 'reservas';
                this.$nextTick(() => window.lucide?.createIcons());
            },
            openReservaDetail() {
                if (!this.selectedReserva.id) return;
                this.setPanel('detalle-reserva');
            },
            openReservaEditor() {
                if (!this.selectedReserva.id) return;
                this.frameUrl = this.selectedReserva.editUrl;
                this.setPanel('editar-reserva');
            },
            openReservaCreator() {
                this.frameUrl = @js(route('reservas.create') . '?embedded=1');
                this.setPanel('nueva-reserva');
            },
            submitDeleteSelected() {
                if (!this.selectedReserva.id) return;
                if (confirm('¿Eliminar esta reserva definitivamente?')) {
                    this.$refs.deleteReservaForm.submit();
                }
            },
            toggleTheme() {
                this.darkMode = !this.darkMode;
                document.documentElement.classList.toggle('dark', this.darkMode);
                localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
                this.$nextTick(() => window.lucide?.createIcons());
            }
        }"
        x-init="
            document.documentElement.classList.toggle('dark', darkMode);
            flashMessage = localStorage.getItem('secretaria-dashboard-message') || '';
            if (flashMessage) localStorage.removeItem('secretaria-dashboard-message');
            window.addEventListener('message', (event) => {
                if (event.origin !== window.location.origin) return;
                if (event.data?.type === 'reserva-updated') {
                    localStorage.setItem('secretaria-dashboard-message', event.data.message || 'Reserva actualizada correctamente.');
                    window.location.href = @js(route('secretaria.dashboard'));
                }
                if (event.data?.type === 'reserva-created') {
                    localStorage.setItem('secretaria-dashboard-message', event.data.message || 'Reserva registrada correctamente.');
                    window.location.href = @js(route('secretaria.dashboard'));
                }
                if (event.data?.type === 'reserva-panel') {
                    this.setPanel(event.data.panel || 'reservas');
                }
            });
        "
        :class="darkMode ? 'dark bg-[#18191A] text-white' : 'bg-[#F4F7FE] text-slate-900'"
        class="min-h-screen w-full flex flex-col md:flex-row font-sans antialiased overflow-x-hidden md:overflow-hidden transition-colors">

        {{-- SIDEBAR ESTILO USUARIO (Azul Vibrante) --}}
        <aside :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-blue-600 dark:bg-[#242526] text-white transition-transform duration-300 md:relative md:translate-x-0 flex flex-col h-[100dvh] shadow-2xl shrink-0">

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
                    <button type="button" @click="setPanel('dashboard')"
                        :class="activePanel === 'dashboard' ? 'bg-white/20 text-white shadow-lg border border-white/10' : 'text-blue-100 hover:bg-white/10'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-[10px] uppercase tracking-wide transition-all">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        Dashboard
                    </button>
                    <button type="button" @click="setPanel('reservas')"
                        :class="['reservas', 'detalle-reserva', 'editar-reserva', 'nueva-reserva'].includes(activePanel) ? 'bg-white/20 text-white shadow-lg border border-white/10' : 'text-blue-100 hover:bg-white/10'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-[10px] uppercase tracking-wide transition-all">
                        <i data-lucide="users" class="w-4 h-4"></i>
                        Reservas de Usuarios
                    </button>
                    <div x-show="activePanel === 'reservas' || activePanel === 'detalle-reserva' || activePanel === 'editar-reserva' || activePanel === 'nueva-reserva'"
                         x-transition
                         class="ml-5 mt-2 mb-2 space-y-1 border-l border-white/20 pl-3">
                        <p class="text-[8px] font-black uppercase tracking-widest text-blue-100/70">
                            Acciones de reserva
                        </p>
                        <button type="button"
                                @click="openReservaDetail()"
                                :disabled="!selectedReserva.id"
                                :class="selectedReserva.id ? 'text-blue-50 hover:bg-white/10' : 'text-blue-100/35 cursor-not-allowed'"
                                class="w-full flex items-center gap-2 px-3 py-2 rounded-xl text-[9px] font-black uppercase transition">
                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                            Ver seleccionada
                        </button>
                        <button type="button"
                                @click="openReservaEditor()"
                                :disabled="!selectedReserva.id"
                                :class="selectedReserva.id ? 'text-blue-50 hover:bg-white/10' : 'text-blue-100/35 cursor-not-allowed'"
                                class="w-full flex items-center gap-2 px-3 py-2 rounded-xl text-[9px] font-black uppercase transition">
                            <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                            Editar seleccionada
                        </button>
                        <button type="button"
                                @click="submitDeleteSelected()"
                                :disabled="!selectedReserva.id"
                                :class="selectedReserva.id ? 'text-red-100 hover:bg-red-500/20' : 'text-blue-100/35 cursor-not-allowed'"
                                class="w-full flex items-center gap-2 px-3 py-2 rounded-xl text-[9px] font-black uppercase transition">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            Eliminar seleccionada
                        </button>
                        <p class="text-[8px] font-bold text-blue-100/60 truncate" x-text="selectedReserva.id ? selectedReserva.codigo + ' · ' + selectedReserva.visitante : 'Selecciona una fila'"></p>
                    </div>
                    <button type="button" @click="setPanel('reportes')"
                        :class="activePanel === 'reportes' ? 'bg-white/20 text-white shadow-lg border border-white/10' : 'text-blue-100 hover:bg-white/10'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-[10px] uppercase tracking-wide transition-all">
                        <i data-lucide="file-bar-chart" class="w-4 h-4"></i>
                        Reportes
                    </button>
                    <button type="button" @click="setPanel('pagos')"
                        :class="activePanel === 'pagos' ? 'bg-white/20 text-white shadow-lg border border-white/10' : 'text-blue-100 hover:bg-white/10'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-[10px] uppercase tracking-wide transition-all">
                        <i data-lucide="banknote" class="w-4 h-4"></i>
                        Historial de Pagos
                    </button>
                </nav>
            </div>

            {{-- BOTÓN CERRAR SESIÓN AL FINAL --}}
            <div class="mt-auto p-6 space-y-3 border-t border-white/15">
                <div class="bg-black/20 rounded-xl p-1 flex items-center cursor-pointer relative h-10" @click="toggleTheme()">
                    <div class="absolute w-[calc(50%-4px)] h-[32px] bg-white rounded-lg transition-all duration-300" :style="{ transform: darkMode ? 'translateX(calc(100% + 4px))' : 'translateX(0)' }"></div>
                    <div class="relative z-10 w-1/2 text-center text-[10px] font-black uppercase" :class="!darkMode ? 'text-blue-600' : 'text-white/50'">Claro</div>
                    <div class="relative z-10 w-1/2 text-center text-[10px] font-black uppercase" :class="darkMode ? 'text-blue-600' : 'text-white/50'">Oscuro</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        class="w-full bg-red-500 hover:bg-red-600 text-white py-3 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all shadow-lg border border-red-400/40">
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </aside>

        <form x-ref="deleteReservaForm"
              method="POST"
              :action="selectedReserva.deleteUrl || '#'"
              class="hidden">
            @csrf
            @method('DELETE')
        </form>

        <div x-show="sidebarOpen"
             @click="sidebarOpen = false"
             x-transition.opacity
             x-cloak
             class="fixed inset-0 z-40 bg-black/50 md:hidden"></div>

        {{-- CONTENIDO PRINCIPAL --}}
        {{-- 🌟 SOLUCIÓN: Forzamos a que el main ocupe el alto restante de la pantalla de forma estricta (h-full flex
        flex-col) --}}
        <main class="flex-1 min-h-screen md:h-screen flex flex-col p-4 md:p-8 space-y-5 md:space-y-6 overflow-y-auto md:overflow-hidden bg-[#F4F7FE] dark:bg-[#18191A]">

            {{-- HEADER SUPERIOR --}}
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 sm:gap-4 shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    <button type="button"
                            @click="sidebarOpen = true"
                            class="md:hidden w-10 h-10 rounded-xl bg-blue-600 text-white shadow-lg flex items-center justify-center font-black text-xl">
                        ≡
                    </button>
                    <h2 class="text-2xl font-black text-blue-700 tracking-tight truncate">Bienvenido,
                        {{ explode(' ', auth()->user()->name)[0] }}
                    </h2>
                </div>

                <div class="flex items-center justify-end gap-3 w-full sm:w-auto">
                    {{-- 🔔 COMPONENTE DE NOTIFICACIONES --}}
                    <div x-data="{ openNotification: false }" class="relative">
                        <button @click="openNotification = !openNotification"
                            class="relative w-11 h-11 rounded-2xl bg-white dark:bg-[#242526] text-slate-500 dark:text-slate-300 border border-slate-100 dark:border-slate-700 shadow-sm flex items-center justify-center hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition focus:outline-none">
                            <i data-lucide="bell" class="w-5 h-5"></i>

                            @if($notificacionesUnread->count() > 0)
                                <span
                                    class="absolute -top-1 -right-1 min-w-5 h-5 px-1 bg-red-500 text-white text-[10px] font-black rounded-full flex items-center justify-center shadow-sm shadow-red-500/30">
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
                            class="fixed left-1/2 top-20 w-[calc(100vw-1.5rem)] max-w-sm -translate-x-1/2 sm:absolute sm:left-auto sm:right-0 sm:top-auto sm:mt-3 sm:w-[22rem] sm:max-w-none sm:translate-x-0 bg-white dark:bg-[#242526] rounded-2xl shadow-2xl border border-slate-100 dark:border-slate-700 z-50 overflow-hidden"
                            style="display: none;">

                            <div
                                class="px-4 py-3 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/70 dark:bg-[#18191A]/70">
                                <span
                                    class="text-[10px] font-black uppercase text-slate-500 dark:text-slate-300 tracking-widest">Notificaciones
                                    de Control</span>
                                @if($notificacionesUnread->count() > 0)
                                    <a href="{{ route('notifications.markRead') }}"
                                        class="text-[10px] font-black text-blue-600 hover:underline uppercase">Marcar leídas</a>
                                @endif
                            </div>

                            <div class="max-h-80 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-700">
                                @forelse($notificacionesUnread as $notification)
                                    <div class="p-4 hover:bg-slate-50 dark:hover:bg-[#18191A] transition-colors flex items-start gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 flex items-center justify-center shrink-0">
                                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs text-slate-800 dark:text-slate-100 font-black leading-snug">
                                                {{ $notification->data['mensaje'] ?? 'Nueva actividad registrada en el sistema.' }}
                                            </p>
                                            <p class="text-[10px] text-slate-400 font-bold mt-1">
                                                Revisa la reserva o marca la notificación como leída.
                                            </p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-8 text-center text-slate-400 opacity-80">
                                        <i data-lucide="bell-off" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                                        <p class="text-xs font-black uppercase tracking-wider">Sin novedades por revisar
                                        </p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            @if(session('success'))
                <div class="shrink-0 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('status'))
                <div class="shrink-0 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-bold text-amber-700">
                    {{ session('status') }}
                </div>
            @endif
            <div x-show="flashMessage" x-transition x-cloak
                class="shrink-0 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700">
                <span x-text="flashMessage"></span>
            </div>

            <section x-show="activePanel === 'dashboard'" x-cloak class="flex-1 min-h-0 flex flex-col space-y-5 md:space-y-6 md:overflow-hidden">
            {{-- ESTADÍSTICAS (Se mantienen fijas arriba) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 shrink-0">
                <div
                    class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between group hover:shadow-md transition-all">
                    <div>
                        <p class="text-[9px] font-black text-blue-500 uppercase tracking-widest mb-1">Total</p>
                        <h3 class="text-2xl font-black text-slate-800">{{ $totalReservas ?? $totalAsistentes }}</h3>
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
                        <p class="text-[9px] font-black text-red-500 uppercase tracking-widest mb-1">Pagos pendientes</p>
                        <h3 class="text-2xl font-black text-slate-800">{{ $pagosPendientes ?? $reservasCanceladasCuenta }}</h3>
                    </div>
                    <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center text-red-500">
                        <i data-lucide="wallet-cards" class="w-5 h-5"></i>
                    </div>
                </div>
            </div>

            {{-- SECCIÓN DE LA TABLA Y EL SIDEBAR DE PENDIENTES --}}
            {{-- 🌟 SOLUCIÓN: flex-1 y overflow-hidden obligan a este contenedor a ajustarse dinámicamente al tamaño que
            sobra abajo --}}
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 md:gap-6 flex-1 min-h-0 md:overflow-hidden">

                {{-- TABLA DE ASISTENTES (HISTORIAL RECIENTE) --}}
                {{-- 🌟 SOLUCIÓN: Al añadir flex flex-col y overflow-hidden, la tarjeta se vuelve responsiva
                internamente --}}
                <div
                    class="xl:col-span-2 bg-white rounded-3xl shadow-sm border border-slate-100 flex flex-col overflow-hidden h-full min-h-[260px]">
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
                <div class="flex flex-col h-full md:overflow-hidden space-y-4 min-h-[220px]">
                    <h3
                        class="text-[10px] font-black uppercase tracking-widest text-slate-400 flex items-center gap-2 shrink-0">
                        <span class="w-2 h-2 bg-amber-500 rounded-full animate-pulse"></span> Por Validar
                    </h3>

                    <div class="flex-1 overflow-y-auto space-y-3 pb-4 max-h-[420px] md:max-h-none">
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
                                            {{ $reserva->horario->hora_inicio ?? 'Sin hora' }} •
                                            {{ $reserva->cantidad_personas }} Pax
                                        </p>
                                    </div>
                                    <div
                                        class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center text-amber-500 shrink-0">
                                        <i data-lucide="hourglass" class="w-4 h-4"></i>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-2 mb-3 text-[9px] font-bold text-slate-500">
                                    <div class="rounded-xl bg-slate-50 border border-slate-100 px-3 py-2 truncate">
                                        Correo: {{ $reserva->correo ?? optional($reserva->user)->email ?? 'Sin correo' }}
                                    </div>
                                    <div class="rounded-xl bg-slate-50 border border-slate-100 px-3 py-2 truncate">
                                        Teléfono: {{ $reserva->telefono ?? optional($reserva->user)->telefono ?? 'Sin teléfono' }}
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        <span class="rounded-xl bg-blue-50 text-blue-600 px-2.5 py-1 font-black uppercase">
                                            {{ $reserva->turno->nombre ?? 'Turno sin nombre' }}
                                        </span>
                                        <span class="rounded-xl {{ $reserva->pago ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }} px-2.5 py-1 font-black uppercase">
                                            {{ $reserva->pago ? 'Pago registrado' : 'Pago por validar' }}
                                        </span>
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

            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-5 shrink-0">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
                    <div>
                        <h3 class="text-[11px] font-black uppercase tracking-widest text-slate-700 flex items-center gap-2">
                            <i data-lucide="star" class="w-4 h-4 text-amber-500"></i> Opiniones recientes
                        </h3>
                        <p class="text-xs text-slate-400 font-bold mt-1">Calificaciones enviadas por visitantes después del recorrido.</p>
                    </div>
                    @if(($feedbackReciente ?? collect())->count() > 0)
                        <span class="rounded-2xl bg-amber-50 text-amber-600 px-3 py-1.5 text-[10px] font-black uppercase">
                            {{ number_format(($feedbackReciente ?? collect())->avg('rating'), 1) }}/5 promedio
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">
                    @forelse(($feedbackReciente ?? collect()) as $feedback)
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <p class="font-black text-[11px] uppercase text-slate-800 truncate">
                                    {{ $feedback->user->name ?? 'Visitante' }}
                                </p>
                                <div class="flex items-center gap-0.5 text-amber-500">
                                    @for($star = 1; $star <= 5; $star++)
                                        <i data-lucide="star" class="w-3.5 h-3.5 {{ $star <= $feedback->rating ? 'fill-amber-400' : 'opacity-25' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-[10px] font-bold text-blue-500 mt-1">
                                @if($feedback->reserva)
                                    {{ $feedback->reserva->fecha?->format('d/m/Y') }} · {{ $feedback->reserva->horario?->hora_inicio ? substr($feedback->reserva->horario->hora_inicio, 0, 5) : 'Sin hora' }}
                                @else
                                    Opinión general
                                @endif
                            </p>
                            <p class="text-xs font-semibold text-slate-500 mt-3 line-clamp-3">
                                {{ $feedback->comment ?: 'Sin comentario adicional.' }}
                            </p>
                        </div>
                    @empty
                        <div class="md:col-span-2 xl:col-span-4 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/60 p-6 text-center">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Todavía no hay opiniones registradas.</p>
                        </div>
                    @endforelse
                </div>
            </div>
            </section>

            <section x-show="activePanel === 'reservas'" x-cloak class="flex-1 min-h-0 bg-white rounded-3xl shadow-sm border border-slate-100 flex flex-col overflow-hidden min-h-[520px]">
                <div class="p-6 border-b border-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-slate-50/50 shrink-0">
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-widest text-slate-800 flex items-center gap-2">
                            <i data-lucide="users" class="w-4 h-4 text-blue-600"></i> Reservas de usuarios
                        </h3>
                        <p class="text-xs text-slate-400 font-bold mt-1">Vista interna sin salir del panel de secretaria.</p>
                    </div>
                    <button type="button"
                        @click="openReservaCreator()"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl font-black uppercase text-[10px] tracking-widest shadow-lg shadow-blue-600/20 inline-flex items-center gap-2">
                        <i data-lucide="plus" class="w-4 h-4"></i> Nueva reserva
                    </button>
                </div>

                <div class="flex-1 overflow-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="sticky top-0 bg-white z-10 shadow-sm">
                            <tr class="text-[9px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                                <th class="px-6 py-4">Fecha</th>
                                <th class="px-6 py-4">Hora</th>
                                <th class="px-6 py-4">Visitante</th>
                                <th class="px-6 py-4 text-center">Pax</th>
                                <th class="px-6 py-4">Pago</th>
                                <th class="px-6 py-4 text-right">Estado y acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse(($reservasPanel ?? collect()) as $reserva)
                                <tr class="hover:bg-blue-50/30 transition-colors">
                                    <td class="px-6 py-4 text-[11px] font-black text-slate-700 whitespace-nowrap">
                                        {{ $reserva->fecha?->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-[10px] font-bold text-slate-500 whitespace-nowrap">
                                        {{ $reserva->horario->hora_inicio ?? 'Sin hora' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-[11px] font-black text-slate-800 uppercase truncate max-w-[240px]">
                                            {{ $reserva->nombre ?? optional($reserva->user)->name ?? 'Invitado' }}
                                        </div>
                                        <div class="text-[9px] text-slate-400 truncate max-w-[240px]">
                                            {{ $reserva->correo ?? optional($reserva->user)->email ?? 'Sin correo' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-[10px] font-black text-blue-600 bg-blue-50 px-2 py-1 rounded-lg">
                                            {{ $reserva->cantidad_personas }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-[10px] font-bold {{ $reserva->pago ? 'text-emerald-600' : 'text-amber-600' }}">
                                        {{ $reserva->pago ? 'Bs. ' . number_format((float) $reserva->pago->monto, 2) : 'Sin pago' }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex flex-col items-end gap-2">
                                            <span class="text-[8px] font-black px-3 py-1 rounded-full uppercase
                                                {{ $reserva->estado === 'Confirmado' ? 'bg-emerald-100 text-emerald-600' : '' }}
                                                {{ $reserva->estado === 'Pendiente' ? 'bg-amber-100 text-amber-600' : '' }}
                                                {{ in_array($reserva->estado, ['Cancelado', 'Cancelada', 'Rechazado']) ? 'bg-red-100 text-red-600' : '' }}">
                                                {{ $reserva->estado }}
                                            </span>
                                            <div class="flex flex-wrap justify-end gap-2">
                                                <button type="button"
                                                    @click="selectReserva({
                                                        id: @js($reserva->id),
                                                        codigo: @js('RES-' . str_pad((string) $reserva->id, 4, '0', STR_PAD_LEFT)),
                                                        fecha: @js($reserva->fecha?->format('d/m/Y') ?? 'Sin fecha'),
                                                        fechaIso: @js($reserva->fecha?->format('Y-m-d') ?? ''),
                                                        hora: @js($reserva->horario ? substr($reserva->horario->hora_inicio, 0, 5) . ($reserva->horario->hora_fin ? ' - ' . substr($reserva->horario->hora_fin, 0, 5) : '') : 'Sin hora'),
                                                        horaInicio: @js($reserva->horario?->hora_inicio ? substr($reserva->horario->hora_inicio, 0, 5) : ''),
                                                        horaFin: @js($reserva->horario?->hora_fin ? substr($reserva->horario->hora_fin, 0, 5) : ''),
                                                        visitante: @js($reserva->nombre ?? optional($reserva->user)->name ?? 'Invitado'),
                                                        correo: @js($reserva->correo ?? optional($reserva->user)->email ?? 'Sin correo'),
                                                        telefono: @js($reserva->telefono ?? optional($reserva->user)->telefono ?? 'Sin teléfono'),
                                                        personas: @js($reserva->cantidad_personas),
                                                        pago: @js($reserva->pago ? 'Bs. ' . number_format((float) $reserva->pago->monto, 2) : 'Sin pago'),
                                                        estado: @js($reserva->estado),
                                                        observacion: @js($reserva->descripcion ?: 'Sin observación registrada.'),
                                                        editUrl: @js(route('secretaria.reservas.edit', $reserva) . '?embedded=1'),
                                                        deleteUrl: @js(route('secretaria.reservas.destroy', $reserva))
                                                    })"
                                                    class="rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 text-[9px] font-black uppercase shadow-sm">
                                                    Gestionar
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-400 font-bold">No hay reservas registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section x-show="activePanel === 'nueva-reserva'" x-cloak class="flex-1 min-h-0 bg-white rounded-3xl shadow-sm border border-slate-100 flex flex-col overflow-hidden min-h-[640px]">
                <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-slate-50/70 shrink-0">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-blue-600">Secretaría</p>
                        <h3 class="text-xl font-black text-slate-800">Nueva reserva</h3>
                        <p class="text-xs text-slate-400 font-bold mt-1">Crea una reserva sin salir del panel de atención.</p>
                    </div>
                    <button type="button" @click="setPanel('reservas')"
                        class="rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2.5 text-[10px] font-black uppercase">
                        Volver
                    </button>
                </div>
                <iframe title="Crear reserva"
                    :src="activePanel === 'nueva-reserva' ? frameUrl : 'about:blank'"
                    class="w-full flex-1 min-h-[580px] bg-[#F0F2F5]"
                    loading="lazy"></iframe>
            </section>

            <section x-show="activePanel === 'detalle-reserva'" x-cloak class="flex-1 min-h-0 grid grid-cols-1 xl:grid-cols-12 gap-5 overflow-y-auto md:overflow-hidden">
                <div class="xl:col-span-5 bg-white rounded-3xl shadow-sm border border-slate-100 p-5 h-fit">
                    <div class="flex items-start justify-between gap-3 mb-5">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-blue-600" x-text="selectedReserva.codigo"></p>
                            <h3 class="text-2xl font-black text-slate-800 mt-1">Detalle de reserva</h3>
                            <p class="text-xs text-slate-400 font-bold mt-1">Consulta rápida sin salir del panel.</p>
                        </div>
                        <button type="button" @click="setPanel('reservas')"
                            class="rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 px-3 py-2 text-[10px] font-black uppercase">
                            Volver
                        </button>
                    </div>

                    <div class="space-y-3">
                        <div class="rounded-2xl bg-slate-50 border border-slate-100 p-4">
                            <p class="text-[10px] uppercase font-black text-slate-400">Fecha y hora</p>
                            <p class="text-sm font-black mt-1"><span x-text="selectedReserva.fecha"></span> · <span x-text="selectedReserva.hora"></span></p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 border border-slate-100 p-4">
                            <p class="text-[10px] uppercase font-black text-slate-400">Estado</p>
                            <p class="text-sm font-black mt-1 text-blue-600" x-text="selectedReserva.estado"></p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 border border-slate-100 p-4">
                            <p class="text-[10px] uppercase font-black text-slate-400">Asistentes</p>
                            <p class="text-sm font-black mt-1"><span x-text="selectedReserva.personas"></span> persona(s)</p>
                        </div>
                    </div>
                </div>

                <div class="xl:col-span-7 bg-white rounded-3xl shadow-sm border border-slate-100 p-5">
                    <h3 class="text-sm font-black uppercase tracking-widest text-slate-800 mb-4">Datos registrados</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="rounded-2xl bg-slate-50 border border-slate-100 p-4">
                            <p class="text-[10px] uppercase font-black text-slate-400">Visitante</p>
                            <p class="font-black mt-1" x-text="selectedReserva.visitante"></p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 border border-slate-100 p-4">
                            <p class="text-[10px] uppercase font-black text-slate-400">Correo</p>
                            <p class="font-black mt-1 break-all" x-text="selectedReserva.correo"></p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 border border-slate-100 p-4">
                            <p class="text-[10px] uppercase font-black text-slate-400">Teléfono</p>
                            <p class="font-black mt-1" x-text="selectedReserva.telefono"></p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 border border-slate-100 p-4">
                            <p class="text-[10px] uppercase font-black text-slate-400">Pago</p>
                            <p class="font-black mt-1" x-text="selectedReserva.pago"></p>
                        </div>
                    </div>

                    <div class="mt-4 rounded-2xl bg-blue-50 border border-blue-100 p-4">
                        <p class="text-[10px] uppercase font-black text-blue-600 mb-1">Observación</p>
                        <p class="text-sm font-semibold text-slate-600" x-text="selectedReserva.observacion"></p>
                    </div>

                    <div class="mt-5 flex flex-col sm:flex-row gap-3">
                        <button type="button" @click="openReservaEditor()"
                            class="rounded-2xl bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 text-xs font-black uppercase tracking-widest">
                            Editar reserva
                        </button>
                        <button type="button" @click="submitDeleteSelected()"
                            class="rounded-2xl bg-red-50 hover:bg-red-100 text-red-600 px-5 py-3 text-xs font-black uppercase tracking-widest">
                            Eliminar reserva
                        </button>
                    </div>
                </div>
            </section>

            <section x-show="activePanel === 'editar-reserva'" x-cloak class="flex-1 min-h-0 bg-white rounded-3xl shadow-sm border border-slate-100 flex flex-col overflow-hidden min-h-[640px]">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between gap-3 bg-slate-50/70 shrink-0">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-blue-600" x-text="selectedReserva.codigo"></p>
                        <h3 class="text-xl font-black text-slate-800">Editar reserva</h3>
                        <p class="text-xs text-slate-400 font-bold mt-1">Formulario cargado dentro del panel de secretaría.</p>
                    </div>
                    <button type="button" @click="setPanel('reservas')"
                        class="rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2.5 text-[10px] font-black uppercase">
                        Volver
                    </button>
                </div>
                <iframe title="Editar reserva"
                    :src="activePanel === 'editar-reserva' ? frameUrl : 'about:blank'"
                    class="w-full flex-1 min-h-[580px] bg-[#F0F2F5]"
                    loading="lazy"></iframe>
            </section>

            <section x-show="activePanel === 'reportes'" x-cloak class="flex-1 min-h-0 flex flex-col space-y-5 md:overflow-hidden">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-5 shrink-0">
                    <div class="flex flex-col lg:flex-row lg:items-end gap-3">
                        <form method="GET" action="{{ route('secretaria.dashboard') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 flex-1">
                            <input type="hidden" name="panel" value="reportes">
                            <select name="preset" class="rounded-2xl border-slate-200 bg-slate-50 text-sm">
                                <option value="mensual" @selected(($reportPreset ?? 'mensual') === 'mensual')>Mensual</option>
                                <option value="semanal" @selected(($reportPreset ?? 'mensual') === 'semanal')>Semanal</option>
                                <option value="anual" @selected(($reportPreset ?? 'mensual') === 'anual')>Anual</option>
                                <option value="personalizado" @selected(($reportPreset ?? 'mensual') === 'personalizado')>Personalizado</option>
                            </select>
                            <input type="date" name="fecha_inicio" value="{{ ($reportFechaInicio ?? now())->format('Y-m-d') }}" class="rounded-2xl border-slate-200 bg-slate-50 text-sm">
                            <input type="date" name="fecha_fin" value="{{ ($reportFechaFin ?? now())->format('Y-m-d') }}" class="rounded-2xl border-slate-200 bg-slate-50 text-sm">
                            <button class="rounded-2xl bg-blue-600 px-5 py-3 text-xs font-black uppercase tracking-widest text-white">Aplicar</button>
                        </form>

                        <form method="POST" action="{{ route('secretaria.reportes.pdf') }}">
                            @csrf
                            <input type="hidden" name="fecha_inicio" value="{{ ($reportFechaInicio ?? now())->format('Y-m-d') }}">
                            <input type="hidden" name="fecha_fin" value="{{ ($reportFechaFin ?? now())->format('Y-m-d') }}">
                            <input type="hidden" name="titulo" value="Reporte de reservas de secretaría">
                            <input type="hidden" name="firmado_por" value="{{ auth()->user()->name }}">
                            <button class="rounded-2xl bg-emerald-600 px-5 py-3 text-xs font-black uppercase tracking-widest text-white inline-flex items-center gap-2">
                                <i data-lucide="download" class="w-4 h-4"></i> PDF reservas
                            </button>
                        </form>
                    </div>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 shrink-0">
                    @foreach([
                        ['Total', $reportStats['total'] ?? 0, 'text-blue-600'],
                        ['Confirmadas', $reportStats['confirmadas'] ?? 0, 'text-emerald-600'],
                        ['Pendientes', $reportStats['pendientes'] ?? 0, 'text-amber-600'],
                        ['Canceladas', $reportStats['canceladas'] ?? 0, 'text-red-600'],
                        ['Visitantes', $reportStats['visitantes'] ?? 0, 'text-slate-800'],
                    ] as [$label, $value, $color])
                        <div class="bg-white rounded-2xl border border-slate-100 p-4 text-center shadow-sm">
                            <p class="text-2xl font-black {{ $color }}">{{ $value }}</p>
                            <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">{{ $label }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 flex-1 min-h-0 overflow-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="sticky top-0 bg-slate-50 text-[10px] uppercase tracking-widest text-slate-500">
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
                            @forelse(($reportReservas ?? collect()) as $reserva)
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

            <section x-show="activePanel === 'pagos'" x-cloak class="flex-1 min-h-0 grid grid-cols-1 xl:grid-cols-12 gap-6 md:overflow-hidden">
                <div class="xl:col-span-5 bg-white rounded-3xl shadow-sm border border-slate-100 p-5 overflow-y-auto">
                    <h3 class="text-sm font-black uppercase tracking-widest border-b border-slate-100 pb-3 mb-5">
                        Registrar pago
                    </h3>

                    <form method="POST" action="{{ route('secretaria.pagos.manual.store') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="return_to_dashboard" value="1">

                        <label class="block">
                            <span class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2">Reserva sin pago</span>
                            <select name="reserva_id" required class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm">
                                <option value="">Selecciona una reserva</option>
                                @foreach(($reservasSinPago ?? collect()) as $reserva)
                                    <option value="{{ $reserva->id }}">
                                        {{ $reserva->fecha?->format('d/m/Y') }} - {{ $reserva->horario->hora_inicio ?? 'Sin hora' }} - {{ $reserva->nombre ?? optional($reserva->user)->name }} - {{ $reserva->cantidad_personas }} pax
                                    </option>
                                @endforeach
                            </select>
                        </label>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="block">
                                <span class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2">Monto Bs.</span>
                                <input type="number" step="0.01" min="0" name="monto" required class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm" placeholder="0.00">
                            </label>
                            <label class="block">
                                <span class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2">Método</span>
                                <select name="metodo_pago" required class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm">
                                    <option value="Efectivo">Efectivo</option>
                                    <option value="QR">QR</option>
                                    <option value="Transferencia">Transferencia</option>
                                    <option value="Depósito">Depósito</option>
                                </select>
                            </label>
                        </div>

                        <label class="block">
                            <span class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2">Comprobante</span>
                            <input type="text" name="nro_comprobante" class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm" placeholder="Ej. QR-2026-001">
                        </label>

                        <label class="block">
                            <span class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2">OBSERVACIÓN</span>
                            <textarea name="observacion" rows="3" class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm" placeholder="Detalle interno del pago."></textarea>
                        </label>

                        <button class="w-full rounded-2xl bg-blue-600 hover:bg-blue-700 text-white py-3 text-xs font-black uppercase tracking-widest shadow-lg shadow-blue-600/20">
                            Guardar pago
                        </button>
                    </form>
                </div>

                <div class="xl:col-span-7 bg-white rounded-3xl shadow-sm border border-slate-100 flex flex-col overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-black uppercase tracking-widest">Pagos registrados</h3>
                            <p class="text-xs text-slate-400 font-bold mt-1">Últimos pagos validados por secretaría.</p>
                        </div>
                        <a href="{{ route('secretaria.pagos.pdf') }}"
                            class="rounded-2xl bg-emerald-600 px-4 py-2.5 text-[10px] font-black uppercase tracking-widest text-white inline-flex items-center gap-2">
                            <i data-lucide="download" class="w-4 h-4"></i> PDF pagos
                        </a>
                    </div>

                    <div class="flex-1 overflow-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="sticky top-0 bg-slate-50 text-[10px] uppercase tracking-widest text-slate-500">
                                <tr>
                                    <th class="p-4">Fecha</th>
                                    <th class="p-4">Visitante</th>
                                    <th class="p-4">Método</th>
                                    <th class="p-4">Monto</th>
                                    <th class="p-4">Comprobante</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse(($pagosDashboard ?? collect()) as $pago)
                                    <tr>
                                        <td class="p-4 font-bold text-slate-600">{{ optional($pago->pagado_en ?? $pago->created_at)->format('d/m/Y H:i') }}</td>
                                        <td class="p-4">
                                            <p class="font-black text-slate-800">{{ $pago->reserva->nombre ?? optional($pago->reserva->user)->name ?? 'Visitante' }}</p>
                                            <p class="text-xs text-slate-400">{{ $pago->reserva->correo ?? optional($pago->reserva->user)->email }}</p>
                                        </td>
                                        <td class="p-4 font-bold">{{ $pago->metodo_pago }}</td>
                                        <td class="p-4 font-black text-emerald-600">Bs. {{ number_format((float) $pago->monto, 2) }}</td>
                                        <td class="p-4 text-slate-500">{{ $pago->nro_comprobante ?: 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-10 text-center text-slate-400 font-bold">Todavía no hay pagos registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.lucide?.createIcons();
        });
    </script>
</x-app-layout>
