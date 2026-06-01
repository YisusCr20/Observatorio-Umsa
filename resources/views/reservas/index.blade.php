<x-app-layout>
    {{-- Dependencias --}}
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .scrollbar-hide::-webkit-scrollbar {
            height: 6px;
            width: 6px;
        }

        .scrollbar-hide::-webkit-scrollbar-track {
            background: transparent;
        }

        .scrollbar-hide::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 10px;
        }
    </style>

    <div x-data="reservasIndex()" x-init="init()"
        :class="darkMode ? 'dark bg-[#18191A] text-gray-100' : 'bg-[#F0F2F5] text-gray-900'"
        class="min-h-screen transition-colors duration-500 font-sans antialiased pb-12" x-cloak>

        {{-- Header Superior --}}
        <header
            class="sticky top-0 z-[150] p-4 lg:px-8 backdrop-blur-xl bg-white/70 dark:bg-[#242526]/80 border-b border-gray-200 dark:border-gray-800">
            <div class="max-w-6xl mx-auto flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <a href="{{ route('dashboard') }}"
                        class="w-11 h-11 rounded-2xl bg-white dark:bg-[#3A3B3C] shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all group">
                        <i data-lucide="arrow-left" class="w-5 h-5 group-hover:-translate-x-1 transition-transform"></i>
                    </a>
                    <div>
                        <h1 class="text-lg md:text-xl font-black tracking-tight uppercase">Panel de Usuario</h1>
                        <p class="text-[10px] md:text-[11px] font-bold text-gray-500 dark:text-gray-400">Observatorio
                            Astronómico Max Schreier</p>
                    </div>
                </div>
                <button @click="toggleTheme()"
                    class="w-11 h-11 flex items-center justify-center rounded-2xl bg-white dark:bg-[#3A3B3C] shadow-sm border border-gray-100 dark:border-gray-700 text-yellow-500 hover:rotate-12 transition-all">
                    <i :data-lucide="darkMode ? 'sun' : 'moon'" class="w-5 h-5"></i>
                </button>
            </div>
        </header>

        <div class="max-w-6xl mx-auto mt-8 px-4">

            {{-- Notificación de Éxito o Errores --}}
            @if(session('success'))
                <div
                    class="mb-6 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-2xl p-4 flex items-center gap-3 animate__animated animate__fadeInDown">
                    <div class="w-8 h-8 bg-emerald-500 text-white rounded-full flex items-center justify-center shrink-0">
                        <i data-lucide="check" class="w-5 h-5"></i>
                    </div>
                    <p class="text-sm font-bold text-emerald-700 dark:text-emerald-400">{{ session('success') }}</p>
                </div>
            @endif
            @if(session('error'))
                <div
                    class="mb-6 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-2xl p-4 flex items-center gap-3 animate__animated animate__fadeInDown">
                    <div class="w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center shrink-0">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </div>
                    <p class="text-sm font-bold text-red-700 dark:text-red-400">{{ session('error') }}</p>
                </div>
            @endif

            {{-- Título --}}
            <div class="mb-8">
                <h2 class="text-2xl md:text-3xl font-black text-gray-800 dark:text-white tracking-tight">Mis Reservas</h2>
                <p class="text-sm text-gray-500 font-bold mt-1">Consulta y edita tus visitas programadas al observatorio.</p>
            </div>

            {{-- Contenedor de la Tabla --}}
            <div
                class="bg-white dark:bg-[#242526] rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">

                @if($reservas->count() > 0)
                    <div class="overflow-x-auto scrollbar-hide">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50 dark:bg-[#18191A]/50 border-b border-gray-100 dark:border-gray-800">
                                    <th class="py-4 px-6 text-gray-400 dark:text-gray-500 font-black uppercase text-[10px] tracking-widest whitespace-nowrap">
                                        Fecha y Turno</th>
                                    <th class="py-4 px-6 text-gray-400 dark:text-gray-500 font-black uppercase text-[10px] tracking-widest text-center whitespace-nowrap">
                                        Asistentes</th>
                                    <th class="py-4 px-6 text-gray-400 dark:text-gray-500 font-black uppercase text-[10px] tracking-widest text-center whitespace-nowrap">
                                        Estado</th>
                                    <th class="py-4 px-6 text-gray-400 dark:text-gray-500 font-black uppercase text-[10px] tracking-widest text-right whitespace-nowrap">
                                        Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @foreach($reservas as $reserva)
                                    @php
                                        // 1. Convertimos y unimos la fecha y hora de inicio de la reserva
                                        $fechaString = is_a($reserva->fecha, 'Carbon\Carbon') ? $reserva->fecha->format('Y-m-d') : \Carbon\Carbon::parse($reserva->fecha)->format('Y-m-d');
                                        $fechaTurno = \Carbon\Carbon::parse($fechaString . ' ' . $reserva->horario->hora_inicio);

                                        // 2. Calculamos la diferencia en horas reales desde "ahora" hasta el turno
                                        $horasRestantes = now()->diffInHours($fechaTurno, false);

                                        // 3. Condición de ventana exclusiva: Permitido solo entre 0 y 24 horas antes del evento.
                                        // Administradores y Secretarias se saltan esta restricción por jerarquía.
                                        $puedeEditar = (auth()->user()->isAdmin() || auth()->user()->isSecretaria()) || 
                                                       ($horasRestantes >= 0 && $horasRestantes <= 24 && $reserva->estado !== 'Cancelada');

                                        // 4. Estilos dinámicos para las insignias de estado
                                        $estilo = match ($reserva->estado) {
                                            'Confirmado' => 'bg-emerald-50 text-emerald-600 border-emerald-200 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400',
                                            'Pendiente' => 'bg-yellow-50 text-yellow-600 border-yellow-200 dark:bg-yellow-900/20 dark:border-yellow-800 dark:text-yellow-400',
                                            'Cancelada' => 'bg-red-50 text-red-600 border-red-200 dark:bg-red-900/20 dark:border-red-800 dark:text-red-400',
                                            default => 'bg-gray-50 text-gray-600 border-gray-200 dark:bg-[#3A3B3C] dark:border-gray-700 dark:text-gray-400'
                                        };

                                        $icono = match ($reserva->estado) {
                                            'Confirmado' => 'check-circle',
                                            'Pendiente' => 'clock',
                                            'Cancelada' => 'x-circle',
                                            default => 'circle'
                                        };
                                    @endphp

                                    <tr class="hover:bg-gray-50/80 dark:hover:bg-[#1C1D1E] transition-colors group">
                                        {{-- Fecha y Hora --}}
                                        <td class="py-4 px-6">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 flex items-center justify-center shrink-0">
                                                    <i data-lucide="calendar" class="w-5 h-5"></i>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-black text-gray-900 dark:text-white">
                                                        {{ is_a($reserva->fecha, 'Carbon\Carbon') ? $reserva->fecha->format('d/m/Y') : \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y') }}
                                                    </div>
                                                    <div class="text-[10px] text-gray-500 font-bold uppercase mt-0.5 flex items-center gap-1">
                                                        <i data-lucide="clock" class="w-3 h-3"></i>
                                                        {{ \Carbon\Carbon::parse($reserva->horario->hora_inicio)->format('H:i') }}
                                                        @if($reserva->horario->hora_fin)
                                                            - {{ \Carbon\Carbon::parse($reserva->horario->hora_fin)->format('H:i') }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Cantidad Personas --}}
                                        <td class="py-4 px-6 text-center align-middle">
                                            <div class="inline-flex items-center justify-center gap-1.5 text-xs font-black text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-[#3A3B3C] px-3 py-1.5 rounded-lg">
                                                <i data-lucide="users" class="w-4 h-4"></i>
                                                {{ $reserva->cantidad_personas }} PAX
                                            </div>
                                        </td>

                                        {{-- Estado Dinámico --}}
                                        <td class="py-4 px-6 text-center align-middle">
                                            <span class="{{ $estilo }} inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest border">
                                                <i data-lucide="{{ $icono }}" class="w-3 h-3"></i>
                                                {{ $reserva->estado }}
                                            </span>
                                        </td>

                                        {{-- Botones de Acción (Ver y Editar Condicional) --}}
                                        <td class="py-4 px-6 text-right align-middle">
                                            <div class="flex justify-end gap-2">

                                                {{-- Botón Ver (Siempre disponible) --}}
                                                <a href="{{ route('reservas.show', $reserva) }}"
                                                    class="w-9 h-9 flex items-center justify-center bg-gray-50 hover:bg-blue-50 dark:bg-[#18191A] dark:hover:bg-blue-900/30 text-gray-400 hover:text-blue-600 rounded-xl transition-all border border-gray-100 dark:border-gray-800"
                                                    title="Ver Detalles">
                                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                                </a>

                                                {{-- Lógica de Ventana Exclusiva de 24h --}}
                                                @if($puedeEditar)
                                                    {{-- CASO ACTIVO: Está dentro de las últimas 24 horas --}}
                                                    <a href="{{ route('reservas.edit', $reserva) }}"
                                                        class="w-9 h-9 flex items-center justify-center bg-gray-50 hover:bg-yellow-50 dark:bg-[#18191A] dark:hover:bg-yellow-900/30 text-gray-400 hover:text-yellow-600 rounded-xl transition-all border border-gray-100 dark:border-gray-800"
                                                        title="Editar Reserva (Ventana de tiempo activa)">
                                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                                    </a>
                                                @else
                                                    {{-- CASO BLOQUEADO: Fuera de la ventana (Muy temprano o ya venció) --}}
                                                    <button type="button"
                                                        class="w-9 h-9 flex items-center justify-center bg-gray-100 dark:bg-[#18191A]/40 text-gray-300 dark:text-gray-600 rounded-xl cursor-not-allowed border border-gray-200/50 dark:border-gray-800"
                                                        @if($reserva->estado === 'Cancelada')
                                                            title="La reserva está cancelada"
                                                        @elseif($horasRestantes > 24)
                                                            title="Bloqueado: Se habilitará únicamente las 24 horas previas al turno"
                                                        @else
                                                            title="Bloqueado: El tiempo de modificación de esta reserva ya expiró"
                                                        @endif>
                                                        <i data-lucide="lock" class="w-4 h-4"></i>
                                                    </button>
                                                @endif

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    {{-- Estado Vacío --}}
                    <div class="py-16 px-6 text-center flex flex-col items-center justify-center">
                        <div class="w-20 h-20 bg-gray-50 dark:bg-[#18191A] rounded-full flex items-center justify-center mb-4 text-gray-300 dark:text-gray-600">
                            <i data-lucide="calendar-x" class="w-10 h-10"></i>
                        </div>
                        <h3 class="text-xl font-black text-gray-800 dark:text-white mb-2">Aún no tienes reservas</h3>
                        <p class="text-sm font-bold text-gray-500 max-w-md mx-auto">No hay ninguna visita programada en tu historial.</p>
                    </div>
                @endif
            </div>

            {{-- Paginación --}}
            @if(method_exists($reservas, 'links'))
                <div class="mt-6">
                    {{ $reservas->links() }}
                </div>
            @endif

        </div>
    </div>

    {{-- Lógica de Alpine para Dark Mode e Iconos --}}
    <script>
        function reservasIndex() {
            return {
                darkMode: false,
                init() {
                    const savedTheme = localStorage.getItem('theme');
                    if (savedTheme !== null) {
                        this.darkMode = savedTheme === 'dark';
                    } else {
                        this.darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    }
                    this.applyTheme();

                    this.$watch('darkMode', () => {
                        this.applyTheme();
                    });
                },
                applyTheme() {
                    document.documentElement.classList.toggle('dark', this.darkMode);
                    localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
                    this.$nextTick(() => {
                        if (typeof lucide !== 'undefined') {
                            lucide.createIcons();
                        }
                    });
                },
                toggleTheme() {
                    this.darkMode = !this.darkMode;
                }
            }
        }
    </script>
</x-app-layout>