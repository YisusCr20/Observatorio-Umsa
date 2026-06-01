<x-app-layout>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <div x-data="dashboard()" x-init="init()"
        :class="darkMode ? 'bg-[#18191A] text-white' : 'bg-[#F0F2F5] text-gray-900'"
        class="min-h-screen flex transition-all duration-500 overflow-x-hidden font-sans antialiased">

        {{-- SIDEBAR MODERNO --}}
        <aside x-show="sidebarOpen"
            class="fixed inset-y-0 left-0 z-[200] w-[280px] bg-gradient-to-b from-[#1877F2] to-[#0E5BCF] dark:from-[#242526] dark:to-[#18191A] shadow-2xl flex flex-col transition-transform duration-300 lg:static lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" x-transition:enter="transition duration-300"
            x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition duration-300" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full" x-cloak>

            <div class="flex items-center justify-between p-6 border-b border-white/20 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center font-black text-[#1877F2] shadow-lg">
                        MS
                    </div>
                    <div>
                        <h2 class="text-sm font-black tracking-wide text-white">Max Schreier</h2>
                        <p class="text-[9px] font-bold text-blue-100 uppercase">Observatorio Online</p>
                    </div>
                </div>
                <button @click="sidebarOpen = false" class="lg:hidden text-white hover:bg-white/20 p-2 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>

            <div class="p-6 border-b border-white/20 dark:border-gray-700">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center text-white font-black text-xl ring-2 ring-white">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-bold text-white">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-green-300 font-semibold flex items-center gap-1">
                            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span> Activo ahora
                        </p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 p-4 space-y-2">
                <p class="text-[10px] font-bold text-white/60 uppercase tracking-wider px-4">MENÚ PRINCIPAL</p>
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-white/20 text-white font-semibold text-sm transition-all hover:bg-white/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/70 hover:bg-white/10 text-sm transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Perfil
                </a>
                <a href="{{ route('reservas.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/70 hover:bg-white/10 text-sm transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Mis Reservas
                </a>
            </nav>

            <div class="p-6 space-y-4 border-t border-white/20 dark:border-gray-700">
                <div class="bg-black/20 rounded-xl p-1 flex items-center cursor-pointer relative h-10" @click="toggleTheme()">
                    <div class="absolute w-[calc(50%-4px)] h-[32px] bg-white rounded-lg transition-all duration-300" :style="{ transform: darkMode ? 'translateX(calc(100% + 4px))' : 'translateX(0)' }"></div>
                    <div class="relative z-10 w-1/2 text-center text-[10px] font-bold" :class="!darkMode ? 'text-[#1877F2]' : 'text-white/50'">🌞 DÍA</div>
                    <div class="relative z-10 w-1/2 text-center text-[10px] font-bold" :class="darkMode ? 'text-white' : 'text-white/50'">🌙 NOCHE</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full py-3 rounded-xl bg-red-500/20 text-red-200 font-bold text-xs uppercase hover:bg-red-500 hover:text-white transition-all">
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </aside>

        {{-- CONTENIDO PRINCIPAL --}}
        <main class="flex-1 min-w-0 flex flex-col overflow-y-auto">
            <header class="sticky top-0 z-[150] p-4 lg:px-8 lg:py-5 flex justify-between items-center backdrop-blur-md bg-white/80 dark:bg-[#242526]/80 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden w-10 h-10 rounded-xl bg-[#1877F2] text-white flex items-center justify-center shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                        </svg>
                    </button>
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-black tracking-tight bg-gradient-to-r from-[#1877F2] to-[#0E5BCF] bg-clip-text text-transparent">
                            Bienvenido, {{ Auth::user()->name }}
                        </h1>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <button class="relative p-2 text-gray-500 hover:text-[#1877F2] dark:text-gray-300 transition-colors bg-white dark:bg-[#242526] rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 border-2 border-white dark:border-[#242526] rounded-full"></span>
                        <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full animate-ping opacity-75"></span>
                    </button>

                    <a href="{{ route('reservas.create') }}" class="px-5 py-2 rounded-xl bg-[#1877F2] text-white text-xs font-bold uppercase shadow-md flex items-center gap-2 hover:bg-[#0E5BCF] transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="3" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="hidden md:inline">Nueva Reserva</span>
                    </a>
                </div>
            </header>

            <div class="p-4 lg:p-8 space-y-8">
                {{-- Estadísticas con Animación Alpine --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white dark:bg-[#242526] p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[10px] font-bold text-[#1877F2] uppercase tracking-wider">Total</p>
                                <p class="text-3xl font-black mt-1" x-text="animatedStats.total"></p>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-[#1877F2]">📊</div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-[#242526] p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[10px] font-bold text-green-500 uppercase">Confirmadas</p>
                                <p class="text-3xl font-black mt-1" x-text="animatedStats.confirmed"></p>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-500">✅</div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-[#242526] p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[10px] font-bold text-yellow-500 uppercase">Pendientes</p>
                                <p class="text-3xl font-black mt-1" x-text="animatedStats.pending"></p>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center text-yellow-500">⏳</div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-[#242526] p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[10px] font-bold text-red-500 uppercase">Canceladas</p>
                                <p class="text-3xl font-black mt-1" x-text="animatedStats.cancelled"></p>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center text-red-500">❌</div>
                        </div>
                    </div>
                </div>

                {{-- Lista de reservas (Historial) --}}
                <div class="grid grid-cols-1 gap-6 mt-6">
                    <div class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                            <h3 class="font-bold text-lg">📋 Historial Reciente</h3>
                            <a href="{{ route('reservas.index') }}" class="text-sm font-bold text-[#1877F2] hover:underline">Ver todas</a>
                        </div>

                        <div class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse($reservas as $reserva)
                                <div class="p-5 flex justify-between items-center hover:bg-gray-50 dark:hover:bg-[#1E2124] transition">
                                    <div class="flex gap-4 items-center">
                                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl bg-gray-50 dark:bg-[#212427]">
                                            {{ $reserva->estado == 'Confirmado' ? '✅' : ($reserva->estado == 'Pendiente' ? '⏳' : '❌') }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-800 dark:text-gray-200">
                                                Reserva para {{ $reserva->cantidad_personas }} persona(s)
                                            </p>
                                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mt-0.5">
                                                {{ $reserva->fecha->format('d/m/Y') }} | Turno:
                                                {{ $reserva->turno->nombre ?? 'N/A' }}
                                                ({{ $reserva->horario->hora_inicio ?? '' }})
                                            </p>
                                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-md mt-2 inline-block {{ $reserva->estado_color ?? 'bg-gray-100 text-gray-600' }}">
                                                {{ strtoupper($reserva->estado) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-12 flex flex-col items-center justify-center text-center">
                                    <div class="w-16 h-16 bg-gray-50 dark:bg-[#212427] rounded-full flex items-center justify-center text-2xl mb-4">
                                        📭
                                    </div>
                                    <h4 class="font-black text-gray-800 dark:text-gray-200">Aún no hay reservas</h4>
                                    <p class="text-sm text-gray-500 mt-1">Cuando realices tu primera visita al observatorio, aparecerá aquí.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <div x-show="sidebarOpen" @click="sidebarOpen = false"
            class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[180] lg:hidden" x-transition.opacity x-cloak></div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: #1877F2; border-radius: 10px; }
        .dark ::-webkit-scrollbar-track { background: #3a3b3c; }
        .dark ::-webkit-scrollbar-thumb { background: #1877F2; }
    </style>

    <script>
        function dashboard() {
            return {
                sidebarOpen: false,
                darkMode: localStorage.getItem('theme') === 'dark' || (window.matchMedia('(prefers-color-scheme: dark)').matches && localStorage.getItem('theme') !== 'light'),
                bookings: [],
                animatedStats: { total: 0, confirmed: 0, pending: 0, cancelled: 0 },
                // ¡AQUÍ ESTÁ LA MAGIA! Inyectamos las variables de Laravel para que Alpine las anime.
                finalStats: { 
                    total: Number('{{ $totalReservas ?? 0 }}'), 
                    confirmed: Number('{{ $confirmadas ?? 0 }}'), 
                    pending: Number('{{ $pendientes ?? 0 }}'), 
                    cancelled: Number('{{ $canceladas ?? 0 }}') 
                },
                init() {
                    if (window.innerWidth >= 1024) this.sidebarOpen = true;
                    window.addEventListener('resize', () => {
                        if (window.innerWidth >= 1024) this.sidebarOpen = true;
                        else this.sidebarOpen = false;
                    });
                    this.$watch('darkMode', val => {
                        if (val) { document.documentElement.classList.add('dark'); localStorage.setItem('theme', 'dark'); }
                        else { document.documentElement.classList.remove('dark'); localStorage.setItem('theme', 'light'); }
                    });
                    this.darkMode ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark');
                    this.animateStats();
                },
                animateStats() {
                    const duration = 800;
                    const startTime = performance.now();
                    const animate = (now) => {
                        let elapsed = now - startTime;
                        let progress = Math.min(elapsed / duration, 1);
                        this.animatedStats.total = Math.floor(progress * this.finalStats.total);
                        this.animatedStats.confirmed = Math.floor(progress * this.finalStats.confirmed);
                        this.animatedStats.pending = Math.floor(progress * this.finalStats.pending);
                        this.animatedStats.cancelled = Math.floor(progress * this.finalStats.cancelled);
                        if (progress < 1) requestAnimationFrame(animate);
                    };
                    requestAnimationFrame(animate);
                },
                toggleTheme() { this.darkMode = !this.darkMode; }
            }
        }
        
    </script>
</x-app-layout>