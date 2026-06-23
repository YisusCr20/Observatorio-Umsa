<x-app-layout>
    <script>
        (function () {
            const saved = localStorage.getItem('theme');
            const dark = saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.classList.toggle('dark', dark);
            document.body.style.colorScheme = dark ? 'dark' : 'light';
        })();
    </script>
    <style>
        [x-cloak] { display: none !important; }
        html.dark body { background: #18191A; }
        @media (max-width: 1023px) {
            .user-sidebar {
                height: 100dvh;
                max-height: 100dvh;
            }
        }
        @media (max-width: 640px) {
            .visitor-hero-title {
                font-size: 1.15rem;
                line-height: 1.18;
            }
            .dashboard-content {
                padding: 0.75rem;
            }
            .reservation-iframe {
                min-height: 640px;
                height: calc(100dvh - 94px);
            }
        }
        @media (min-width: 641px) and (max-width: 1023px) {
            .reservation-iframe {
                min-height: 680px;
                height: calc(100dvh - 104px);
            }
        }
        @media (min-width: 1024px) {
            .reservation-iframe {
                min-height: 560px;
                height: calc(100dvh - 112px);
            }
        }
    </style>

    @php
        $user = auth()->user();
        $reservasUsuario = $reservas ?? $misReservas ?? collect();
        $turnosReserva = $turnos ?? collect();
        $activePanel = request('panel', 'dashboard');
        $activePanel = $activePanel === 'create' ? 'crear' : $activePanel;
        $initialFrameUrl = request('frame');
        if ($activePanel === 'reserva-frame' && blank($initialFrameUrl)) {
            $activePanel = 'reservas';
        }
        $proximaReserva = $reservasUsuario
            ->filter(fn ($reserva) => $reserva->estado === 'Confirmado' && $reserva->fecha && \Carbon\Carbon::parse($reserva->fecha)->gte(now()->subDay()->startOfDay()))
            ->sortBy('fecha')
            ->first();
        $secretariaContacto = $secretariaContacto ?? \App\Models\User::where('role', 'secretaria')->first();
        $feedbackReservas = $feedbackReservas ?? $reservasUsuario->where('estado', 'Confirmado')->sortByDesc('fecha')->values();
        $uiIcon = function (string $name, string $class = 'w-5 h-5') {
            $paths = [
                'layout-dashboard' => '<rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/>',
                'calendar-plus' => '<path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/><path d="M12 14v4"/><path d="M10 16h4"/>',
                'calendar-days' => '<path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/>',
                'user-cog' => '<circle cx="10" cy="8" r="5"/><path d="M2 21a8 8 0 0 1 10.4-7.6"/><circle cx="18" cy="18" r="3"/><path d="M18 14.5v1"/><path d="M18 20.5v1"/><path d="M21.5 18h-1"/><path d="M15.5 18h-1"/>',
                'bell' => '<path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>',
                'star' => '<path d="m12 2 3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/>',
                'bar-chart-3' => '<path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/>',
                'check-circle' => '<path d="M22 11.1V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/>',
                'clock' => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
                'x-circle' => '<circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/>',
                'history' => '<path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 3v6h6"/><path d="M12 7v5l4 2"/>',
                'shield-check' => '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.68 0C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.5 3.8 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/>',
                'empty' => '<path d="M21 15V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v9"/><path d="M3 15h6l2 3h2l2-3h6"/><path d="M3 15v2a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-2"/>',
            ];
            $path = $paths[$name] ?? '<circle cx="12" cy="12" r="9"/>';

            return '<svg xmlns="http://www.w3.org/2000/svg" class="' . e($class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $path . '</svg>';
        };
    @endphp

    <div x-data="userDashboard()" x-init="init()"
         :class="darkMode ? 'dark bg-[#18191A] text-white' : 'bg-[#F0F2F5] text-gray-900'"
         class="min-h-screen lg:h-screen flex transition-colors duration-300 font-sans antialiased overflow-x-hidden">

        <aside x-show="sidebarOpen"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               x-transition
               x-cloak
               class="user-sidebar fixed inset-y-0 left-0 z-[200] w-[250px] sm:w-[260px] 2xl:w-[280px] bg-gradient-to-b from-[#1877F2] to-[#0E5BCF] dark:from-[#242526] dark:to-[#18191A] shadow-2xl flex flex-col transition-transform duration-300 lg:static lg:translate-x-0">
            <div class="p-4 2xl:p-5 border-b border-white/20 dark:border-gray-700 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center font-black text-[#1877F2] shadow-lg">MS</div>
                    <div>
                        <h2 class="text-sm font-black tracking-wide text-white">Max Schreier</h2>
                        <p class="text-[9px] font-bold text-blue-100 uppercase">Observatorio Online</p>
                    </div>
                </div>
                <button @click="sidebarOpen = false" class="lg:hidden text-white hover:bg-white/20 p-2 rounded-xl">
                    <span class="text-lg leading-none">×</span>
                </button>
            </div>

            <div class="p-4 2xl:p-5 border-b border-white/20 dark:border-gray-700 shrink-0">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center text-white font-black text-xl ring-2 ring-white">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-bold text-white truncate">{{ $user->name }}</p>
                        <p class="text-[10px] text-green-300 font-semibold flex items-center gap-1">
                            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span> Activo ahora
                        </p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 p-3 sm:p-4 space-y-1.5 overflow-y-auto min-h-0">
                <p class="text-[10px] font-bold text-white/60 uppercase tracking-wider px-4">Menú principal</p>

                <button type="button" @click="setPanel('dashboard')" :class="navClass('dashboard')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-semibold text-sm transition-all">
                    <span class="w-5 flex justify-center">{!! $uiIcon('layout-dashboard', 'w-5 h-5') !!}</span> Dashboard
                </button>
                <button type="button" @click="setPanel('crear')" :class="navClass('crear')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-semibold text-sm transition-all">
                    <span class="w-5 flex justify-center">{!! $uiIcon('calendar-plus', 'w-5 h-5') !!}</span> Crear reserva
                </button>
                <button type="button" @click="setPanel('reservas')" :class="navClass('reservas')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-semibold text-sm transition-all">
                    <span class="w-5 flex justify-center">{!! $uiIcon('calendar-days', 'w-5 h-5') !!}</span> Mis reservas
                </button>
                <button type="button" @click="setPanel('perfil')" :class="navClass('perfil')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-semibold text-sm transition-all">
                    <span class="w-5 flex justify-center">{!! $uiIcon('user-cog', 'w-5 h-5') !!}</span> Perfil
                </button>
            </nav>

            <div class="p-3 sm:p-4 2xl:p-5 space-y-2 border-t border-white/20 dark:border-gray-700 shrink-0">
                <div class="bg-black/20 rounded-xl p-1 flex items-center cursor-pointer relative h-10" @click="toggleTheme()">
                    <div class="absolute w-[calc(50%-4px)] h-[32px] bg-white rounded-lg transition-all duration-300" :style="{ transform: darkMode ? 'translateX(calc(100% + 4px))' : 'translateX(0)' }"></div>
                    <div class="relative z-10 w-1/2 text-center text-[10px] font-bold" :class="!darkMode ? 'text-[#1877F2]' : 'text-white/50'">Día</div>
                    <div class="relative z-10 w-1/2 text-center text-[10px] font-bold" :class="darkMode ? 'text-[#1877F2]' : 'text-white/50'">Noche</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full py-2.5 rounded-xl bg-red-500/20 text-red-100 font-bold text-xs uppercase hover:bg-red-500 hover:text-white transition-all">
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </aside>

        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[180] lg:hidden" x-transition.opacity x-cloak></div>

        <main class="flex-1 min-w-0 min-h-screen lg:h-screen flex flex-col bg-[#F0F2F5] dark:bg-[#18191A] overflow-hidden">
            <header class="sticky top-0 z-[150] p-3 sm:p-4 lg:px-6 lg:py-4 flex justify-between items-center backdrop-blur-md bg-white/85 dark:bg-[#242526]/90 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-4 min-w-0">
                    <button @click="sidebarOpen = true" class="lg:hidden w-10 h-10 rounded-xl bg-[#1877F2] text-white flex items-center justify-center shadow-md">
                        <span class="text-xl leading-none">≡</span>
                    </button>
                    <div class="min-w-0">
                        <h1 class="text-xl sm:text-2xl lg:text-[1.7rem] 2xl:text-3xl font-black tracking-tight bg-gradient-to-r from-[#1877F2] to-[#0E5BCF] bg-clip-text text-transparent truncate">
                            Bienvenido, {{ $user->name }}
                        </h1>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 hidden sm:block">Gestiona tu perfil y tus visitas desde un solo lugar.</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <div class="relative">
                        <button @click="feedbackOpen = !feedbackOpen; notificationsOpen = false"
                            class="relative w-11 h-11 rounded-2xl bg-white dark:bg-[#18191A] text-amber-500 border border-gray-100 dark:border-gray-700 shadow-sm flex items-center justify-center hover:bg-amber-50 dark:hover:bg-amber-900/20 transition"
                            title="Calificar recorrido">
                            {!! $uiIcon('star', 'w-5 h-5') !!}
                        </button>

                        <div x-show="feedbackOpen" @click.away="feedbackOpen = false" x-transition x-cloak
                            class="fixed left-1/2 top-20 w-[calc(100vw-1.5rem)] max-w-sm -translate-x-1/2 md:absolute md:left-auto md:right-0 md:top-auto md:mt-3 md:w-[24rem] md:max-w-none md:translate-x-0 bg-white dark:bg-[#242526] border border-gray-100 dark:border-gray-700 rounded-2xl shadow-2xl overflow-hidden z-[180]">
                            <form method="POST" action="{{ route('feedback.store') }}" class="p-3 sm:p-4 space-y-3 sm:space-y-4">
                                @csrf
                                <input type="hidden" name="rating" :value="feedbackRating">
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-amber-500">Califica tu recorrido</p>
                                    <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white mt-1">¿Qué tal estuvo tu visita?</h3>
                                </div>

                                <div class="flex items-center gap-1">
                                    <template x-for="star in 5" :key="star">
                                        <button type="button"
                                            @click="feedbackRating = star"
                                            @mouseenter="feedbackHover = star"
                                            @mouseleave="feedbackHover = 0"
                                            class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center transition"
                                            :class="star <= (feedbackHover || feedbackRating) ? 'bg-amber-100 text-amber-500' : 'bg-gray-100 dark:bg-[#18191A] text-gray-300'">
                                            {!! $uiIcon('star', 'w-5 h-5') !!}
                                        </button>
                                    </template>
                                    <span class="ml-2 text-xs font-black text-gray-500" x-text="feedbackRating + '/5'"></span>
                                </div>

                                <label class="block">
                                    <span class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">Reserva relacionada</span>
                                    <select name="reserva_id" class="w-full rounded-2xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#18191A] text-sm font-bold dark:text-white">
                                        <option value="">Opinión general</option>
                                        @foreach($feedbackReservas as $reservaFeedback)
                                            <option value="{{ $reservaFeedback->id }}">
                                                {{ $reservaFeedback->fecha?->format('d/m/Y') }} - {{ $reservaFeedback->horario?->hora_inicio ? substr($reservaFeedback->horario->hora_inicio, 0, 5) : 'Sin hora' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </label>

                                <label class="block">
                                    <span class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">Comentario</span>
                                    <textarea name="comment" rows="2" maxlength="700"
                                        class="w-full rounded-2xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#18191A] text-sm font-bold dark:text-white"
                                        placeholder="Cuéntanos qué te gustó, qué mejorarías o cómo fue la atención."></textarea>
                                </label>

                                <button class="w-full rounded-2xl bg-amber-500 hover:bg-amber-600 text-white py-2.5 sm:py-3 text-xs font-black uppercase tracking-widest">
                                    Enviar calificación
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="relative">
                        <button @click="notificationsOpen = !notificationsOpen; feedbackOpen = false"
                            class="relative w-11 h-11 rounded-2xl bg-white dark:bg-[#18191A] text-gray-500 dark:text-gray-300 border border-gray-100 dark:border-gray-700 shadow-sm flex items-center justify-center hover:text-[#1877F2] transition">
                            {!! $uiIcon('bell', 'w-5 h-5') !!}
                            @if($user->unreadNotifications->count() > 0)
                                <span class="absolute -top-1 -right-1 min-w-5 h-5 px-1 rounded-full bg-red-500 text-white text-[10px] font-black flex items-center justify-center">
                                    {{ $user->unreadNotifications->count() }}
                                </span>
                            @endif
                        </button>

                        <div x-show="notificationsOpen" @click.away="notificationsOpen = false" x-transition x-cloak
                            class="fixed left-1/2 top-20 w-[calc(100vw-1.5rem)] max-w-sm -translate-x-1/2 md:absolute md:left-auto md:right-0 md:top-auto md:mt-3 md:w-[22rem] md:max-w-none md:translate-x-0 bg-white dark:bg-[#242526] border border-gray-100 dark:border-gray-700 rounded-2xl shadow-2xl overflow-hidden z-[180]">
                            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-500">Notificaciones</span>
                                @if($user->unreadNotifications->count() > 0)
                                    <a href="{{ route('notifications.markRead') }}" class="text-[10px] font-black text-[#1877F2] uppercase">Marcar leídas</a>
                                @endif
                            </div>
                            <div class="max-h-80 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($user->unreadNotifications as $notification)
                                    <div class="p-4 flex gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-900/20 text-[#1877F2] flex items-center justify-center shrink-0">
                                            <span class="text-sm leading-none">•</span>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-black text-gray-800 dark:text-gray-100">
                                                {{ $notification->data['mensaje'] ?? 'Tu reserva tuvo una actualización.' }}
                                            </p>
                                            <p class="text-[10px] text-gray-400 font-bold mt-1">También se envía por correo cuando secretaría confirma o rechaza.</p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-8 text-center text-gray-400">
                                        <span class="flex justify-center mb-2">{!! $uiIcon('empty', 'w-8 h-8') !!}</span>
                                        <p class="text-xs font-black uppercase">Sin notificaciones nuevas</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="dashboard-content flex-1 overflow-y-auto p-3 sm:p-4 lg:p-5 2xl:p-6 space-y-4">
                @if(session('success') || in_array(session('status'), ['profile-updated', 'password-updated'], true))
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700">
                        {{ session('success') ?? (session('status') === 'password-updated' ? 'Contraseña actualizada correctamente.' : 'Perfil actualizado correctamente.') }}
                    </div>
                @endif

                <div x-show="flashMessage" x-cloak class="rounded-2xl border px-4 py-3 text-sm font-bold"
                     :class="flashType === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-red-50 border-red-200 text-red-700'"
                     x-text="flashMessage"></div>

                <section x-show="activePanel === 'dashboard'" x-cloak class="space-y-4">
                    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#1877F2] via-[#0E5BCF] to-[#0f172a] text-white shadow-xl shadow-blue-900/15">
                        <div class="absolute inset-y-0 right-0 w-1/2 opacity-20 bg-[radial-gradient(circle_at_center,_white,_transparent_55%)]"></div>
                        <div class="relative p-3 sm:p-4 lg:p-5 grid grid-cols-1 xl:grid-cols-[1fr_230px] 2xl:grid-cols-[1fr_270px] gap-3 items-center">
                            <div>
                                <p class="text-[9px] sm:text-[10px] font-black uppercase tracking-[0.28em] text-blue-100 mb-1.5">Panel del visitante</p>
                                <h2 class="visitor-hero-title text-lg sm:text-xl 2xl:text-2xl font-black leading-tight">Tu visita al observatorio, lista y ordenada.</h2>
                                <p class="text-[11px] sm:text-xs font-semibold text-blue-100 mt-1.5 max-w-2xl">Reserva, revisa estados y mantén tus datos actualizados para que secretaría valide tu visita.</p>
                            </div>

                            <div class="rounded-2xl bg-white/12 border border-white/20 p-3 backdrop-blur">
                                <p class="text-[10px] font-black uppercase tracking-widest text-blue-100">Próxima visita</p>
                                @if($proximaReserva)
                                    <p class="text-lg font-black mt-1.5">{{ \Carbon\Carbon::parse($proximaReserva->fecha)->format('d/m/Y') }}</p>
                                    <p class="text-xs font-bold text-blue-100 mt-1">
                                        {{ $proximaReserva->horario->hora_inicio ?? 'Sin hora' }}
                                        @if($proximaReserva->horario?->hora_fin)
                                            - {{ $proximaReserva->horario->hora_fin }}
                                        @endif
                                    </p>
                                    <span class="inline-flex mt-3 rounded-xl bg-white text-[#1877F2] px-3 py-1.5 text-[10px] font-black uppercase">
                                        {{ $proximaReserva->estado }}
                                    </span>
                                @else
                                    <p class="text-base font-black mt-2">Sin visitas próximas</p>
                                    <p class="text-xs font-bold text-blue-100 mt-1">Crea tu primera reserva desde el menú lateral.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4">
                        @foreach([
                            ['Total', $totalReservas ?? $reservasUsuario->count(), 'bar-chart-3', 'text-[#1877F2]', 'bg-blue-100'],
                            ['Confirmadas', $confirmadas ?? 0, 'check-circle', 'text-green-500', 'bg-green-100'],
                            ['Pendientes', $pendientes ?? 0, 'clock', 'text-yellow-500', 'bg-yellow-100'],
                            ['Canceladas', $canceladas ?? 0, 'x-circle', 'text-red-500', 'bg-red-100'],
                        ] as [$label, $value, $icon, $color, $bg])
                            <div class="bg-white dark:bg-[#242526] p-4 sm:p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                                <div class="flex justify-between items-start gap-3">
                                    <div>
                                        <p class="text-[10px] font-bold {{ $color }} uppercase tracking-wider">{{ $label }}</p>
                                        <p class="text-2xl sm:text-3xl font-black mt-1">{{ $value }}</p>
                                    </div>
                                    <div class="w-10 h-10 rounded-xl {{ $bg }} dark:bg-white/10 flex items-center justify-center {{ $color }}">
                                        {!! $uiIcon($icon, 'w-5 h-5') !!}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                            <h3 class="font-bold text-lg flex items-center gap-2"><span class="text-[#1877F2]">{!! $uiIcon('history', 'w-5 h-5') !!}</span> Historial reciente</h3>
                            <button @click="setPanel('reservas')" class="text-sm font-bold text-[#1877F2] hover:underline">Ver todas</button>
                        </div>

                        <div class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse($reservasUsuario->take(3) as $reserva)
                                @include('usuario.partials.reserva-row', ['reserva' => $reserva, 'compact' => true])
                            @empty
                                <div class="p-10 text-center text-gray-500 font-bold">Aún no tienes reservas registradas.</div>
                            @endforelse
                        </div>
                    </div>
                </section>

                <section x-show="activePanel === 'crear'" x-cloak class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden -mt-1">
                    <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700">
                        <div>
                            <h2 class="text-lg font-black">Crear reserva</h2>
                            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 font-semibold">
                                Flujo completo de reservas con calendario, colegios, costos, términos y pago.
                            </p>
                        </div>
                    </div>

                    <div class="relative">
                        <div x-show="createFrameLoading" x-cloak
                             class="absolute inset-0 z-10 flex flex-col items-center justify-center gap-3 bg-[#F0F2F5] dark:bg-[#18191A] text-gray-500 dark:text-gray-300">
                            <div class="w-10 h-10 rounded-full border-4 border-blue-100 border-t-[#1877F2] animate-spin"></div>
                            <p class="text-xs font-black uppercase tracking-widest">Cargando formulario...</p>
                        </div>
                        <iframe
                            title="Crear reserva"
                            :src="createFrameUrl || 'about:blank'"
                            @load="createFrameReady = true; createFrameLoading = false"
                            class="reservation-iframe w-full bg-[#F0F2F5] dark:bg-[#18191A]"
                            loading="eager"></iframe>
                    </div>
                </section>

                <section x-show="activePanel === 'reservas'" x-cloak class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h2 class="text-xl font-black">Mis reservas</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 font-semibold">Consulta el estado de todas tus solicitudes.</p>
                        </div>
                        <button @click="setPanel('crear')" class="rounded-xl bg-[#1877F2] text-white px-4 py-2.5 text-xs font-black uppercase tracking-widest">Nueva reserva</button>
                    </div>

                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($reservasUsuario as $reserva)
                            @include('usuario.partials.reserva-row', ['reserva' => $reserva, 'compact' => false])
                        @empty
                            <div class="p-12 text-center">
                                <span class="flex justify-center text-gray-300 mb-3">{!! $uiIcon('empty', 'w-10 h-10') !!}</span>
                                <p class="font-black text-gray-700 dark:text-gray-200">Aún no tienes reservas.</p>
                            </div>
                        @endforelse
                    </div>
                </section>

                <section x-show="activePanel === 'perfil'" x-cloak class="grid grid-cols-1 xl:grid-cols-12 gap-5">
                    <div class="xl:col-span-5 space-y-5">
                    <div class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/20 text-[#1877F2] flex items-center justify-center">
                                {!! $uiIcon('user-cog', 'w-6 h-6') !!}
                            </div>
                            <div>
                                <h2 class="text-xl font-black">Perfil</h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400 font-semibold">Datos principales de tu cuenta.</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="return_to_dashboard" value="1">

                            <label class="block">
                                <span class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">Nombre</span>
                                <input name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-2xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#18191A] text-base font-bold px-4 py-3 dark:text-white">
                            </label>

                            <label class="block">
                                <span class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">Correo</span>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-2xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#18191A] text-base font-bold px-4 py-3 dark:text-white">
                            </label>

                            <button class="w-full rounded-2xl bg-[#1877F2] hover:bg-[#0E5BCF] text-white py-3 text-xs font-black uppercase tracking-widest">
                                Guardar perfil
                            </button>
                        </form>
                    </div>

                    <div class="bg-white dark:bg-[#242526] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 flex items-center justify-center">
                                {!! $uiIcon('shield-check', 'w-6 h-6') !!}
                            </div>
                            <div>
                                <h2 class="text-xl font-black">Seguridad</h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400 font-semibold">Actualiza tu contraseña.</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="return_to_dashboard" value="1">

                            <label class="block">
                                <span class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">Contraseña actual</span>
                                <input type="password" name="current_password" autocomplete="current-password"
                                    class="w-full rounded-2xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#18191A] text-base font-bold px-4 py-3 dark:text-white">
                            </label>

                            <label class="block">
                                <span class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">Nueva contraseña</span>
                                <input type="password" name="password" autocomplete="new-password"
                                    class="w-full rounded-2xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#18191A] text-base font-bold px-4 py-3 dark:text-white">
                            </label>

                            <label class="block">
                                <span class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">Confirmar contraseña</span>
                                <input type="password" name="password_confirmation" autocomplete="new-password"
                                    class="w-full rounded-2xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#18191A] text-base font-bold px-4 py-3 dark:text-white">
                            </label>

                            <button class="w-full rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white py-3 text-xs font-black uppercase tracking-widest">
                                Cambiar contraseña
                            </button>
                        </form>
                    </div>
                    </div>

                    <div class="xl:col-span-7 bg-white dark:bg-[#242526] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                        <h3 class="text-sm font-black uppercase tracking-widest mb-4">Resumen de cuenta</h3>
                        <div class="rounded-3xl bg-gradient-to-br from-[#1877F2] to-[#0E5BCF] p-5 text-white mb-5 shadow-lg shadow-blue-600/20">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center text-2xl font-black ring-1 ring-white/30">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-2xl font-black truncate">{{ $user->name }} {{ $user->apellido }}</p>
                                    <p class="text-xs font-bold text-blue-100 break-all">{{ $user->email }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="rounded-2xl bg-gray-50 dark:bg-[#18191A] p-4">
                                <p class="text-[10px] uppercase font-black text-gray-400">CI</p>
                                <p class="font-black mt-1">{{ $user->ci ?: 'No registrado' }}</p>
                            </div>
                            <div class="rounded-2xl bg-gray-50 dark:bg-[#18191A] p-4">
                                <p class="text-[10px] uppercase font-black text-gray-400">Teléfono</p>
                                <p class="font-black mt-1">{{ $user->telefono ?: 'No registrado' }}</p>
                            </div>
                            <div class="rounded-2xl bg-gray-50 dark:bg-[#18191A] p-4">
                                <p class="text-[10px] uppercase font-black text-gray-400">Rol</p>
                                <p class="font-black mt-1 uppercase">{{ $user->role }}</p>
                            </div>
                            <div class="rounded-2xl bg-gray-50 dark:bg-[#18191A] p-4">
                                <p class="text-[10px] uppercase font-black text-gray-400">Reservas</p>
                                <p class="font-black mt-1">{{ $totalReservas ?? $reservasUsuario->count() }}</p>
                            </div>
                        </div>
                        <div class="mt-5 rounded-2xl bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800 p-4">
                            <p class="text-[10px] uppercase font-black text-amber-600 mb-1">Recordatorio</p>
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-300">
                                Mantén tu correo actualizado porque las confirmaciones de secretaría llegan por correo y por la campanita del sistema.
                            </p>
                        </div>
                    </div>
                </section>

                <section x-show="activePanel === 'detalle'" x-cloak class="grid grid-cols-1 xl:grid-cols-12 gap-5">
                    <div class="xl:col-span-5 bg-white dark:bg-[#242526] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                        <div class="flex items-start justify-between gap-3 mb-5">
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-[#1877F2]" x-text="selectedReserva.codigo"></p>
                                <h2 class="text-2xl font-black mt-1">Detalle de reserva</h2>
                            </div>
                            <button @click="setPanel('reservas')" class="rounded-xl bg-gray-100 dark:bg-[#18191A] text-gray-700 dark:text-gray-200 px-3 py-2 text-[10px] font-black uppercase">
                                Volver
                            </button>
                        </div>

                        <div class="space-y-3">
                            <div class="rounded-2xl bg-gray-50 dark:bg-[#18191A] p-4">
                                <p class="text-[10px] uppercase font-black text-gray-400">Fecha y hora</p>
                                <p class="font-black mt-1"><span x-text="selectedReserva.fecha"></span> · <span x-text="selectedReserva.hora"></span></p>
                            </div>
                            <div class="rounded-2xl bg-gray-50 dark:bg-[#18191A] p-4">
                                <p class="text-[10px] uppercase font-black text-gray-400">Estado</p>
                                <p class="font-black mt-1 text-[#1877F2]" x-text="selectedReserva.estado"></p>
                            </div>
                            <div class="rounded-2xl bg-gray-50 dark:bg-[#18191A] p-4">
                                <p class="text-[10px] uppercase font-black text-gray-400">Asistentes</p>
                                <p class="font-black mt-1"><span x-text="selectedReserva.personas"></span> persona(s)</p>
                            </div>
                        </div>
                    </div>

                    <div class="xl:col-span-7 bg-white dark:bg-[#242526] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                        <h3 class="text-sm font-black uppercase tracking-widest mb-4">Datos registrados</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="rounded-2xl bg-gray-50 dark:bg-[#18191A] p-4">
                                <p class="text-[10px] uppercase font-black text-gray-400">Titular</p>
                                <p class="font-black mt-1" x-text="selectedReserva.titular"></p>
                            </div>
                            <div class="rounded-2xl bg-gray-50 dark:bg-[#18191A] p-4">
                                <p class="text-[10px] uppercase font-black text-gray-400">Correo</p>
                                <p class="font-black mt-1 break-all" x-text="selectedReserva.correo"></p>
                            </div>
                            <div class="rounded-2xl bg-gray-50 dark:bg-[#18191A] p-4">
                                <p class="text-[10px] uppercase font-black text-gray-400">Teléfono</p>
                                <p class="font-black mt-1" x-text="selectedReserva.telefono"></p>
                            </div>
                            <div class="rounded-2xl bg-gray-50 dark:bg-[#18191A] p-4">
                                <p class="text-[10px] uppercase font-black text-gray-400">Seguimiento</p>
                                <p class="font-black mt-1" x-text="selectedReserva.seguimiento"></p>
                            </div>
                        </div>
                        <div class="rounded-2xl bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 p-4 mt-4">
                            <p class="text-[10px] uppercase font-black text-blue-500 mb-1">Observación</p>
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-300" x-text="selectedReserva.descripcion"></p>
                        </div>
                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="rounded-2xl bg-gray-50 dark:bg-[#18191A] p-4">
                                <p class="text-[10px] uppercase font-black text-gray-400">Solicitud</p>
                                <p class="text-sm font-black mt-1">Registrada</p>
                            </div>
                            <div class="rounded-2xl bg-gray-50 dark:bg-[#18191A] p-4">
                                <p class="text-[10px] uppercase font-black text-gray-400">Pago</p>
                                <p class="text-sm font-black mt-1" x-text="selectedReserva.estado === 'Confirmado' ? 'Validado' : 'En revisión'"></p>
                            </div>
                            <div class="rounded-2xl bg-gray-50 dark:bg-[#18191A] p-4">
                                <p class="text-[10px] uppercase font-black text-gray-400">Confirmación</p>
                                <p class="text-sm font-black mt-1" x-text="selectedReserva.estado"></p>
                            </div>
                        </div>
                    </div>
                </section>

                <section x-show="activePanel === 'reserva-frame'" x-cloak class="relative bg-white dark:bg-[#242526] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-xl font-black">Editar reserva</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 font-semibold">Formulario original cargado dentro del dashboard.</p>
                        </div>
                        <button @click="setPanel('reservas')" class="rounded-xl bg-gray-100 dark:bg-[#18191A] text-gray-700 dark:text-gray-200 px-4 py-2.5 text-xs font-black uppercase">
                            Volver
                        </button>
                    </div>
                    <iframe
                        title="Editar reserva"
                        :src="frameUrl"
                        @load="frameLoading = false"
                        class="w-full h-[calc(100vh-210px)] min-h-[720px] bg-[#F0F2F5] dark:bg-[#18191A]"
                        loading="eager"></iframe>
                    <div x-show="frameLoading" x-cloak
                         class="absolute inset-x-0 top-[88px] bottom-0 z-10 flex flex-col items-center justify-center gap-3 bg-[#F0F2F5] dark:bg-[#18191A] text-gray-500 dark:text-gray-300">
                        <div class="w-10 h-10 rounded-full border-4 border-blue-100 border-t-[#1877F2] animate-spin"></div>
                        <p class="text-xs font-black uppercase tracking-widest">Cargando edición...</p>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <script>
        function userDashboard() {
            return {
                sidebarOpen: window.innerWidth >= 1024,
                activePanel: @js($activePanel),
                darkMode: localStorage.getItem('theme') === 'dark' || (window.matchMedia('(prefers-color-scheme: dark)').matches && localStorage.getItem('theme') !== 'light'),
                notificationsOpen: false,
                feedbackOpen: false,
                feedbackRating: 5,
                feedbackHover: 0,
                submitting: false,
                flashMessage: '',
                flashType: 'success',
                frameUrl: @js($initialFrameUrl ? urldecode($initialFrameUrl) : ''),
                frameLoading: @js($initialFrameUrl ? true : false),
                createFrameUrl: '',
                createFrameLoading: false,
                createFrameReady: false,
                selectedReserva: {
                    codigo: '',
                    fecha: '',
                    hora: '',
                    personas: 0,
                    estado: '',
                    descripcion: '',
                    seguimiento: '',
                    titular: '',
                    correo: '',
                    telefono: ''
                },
                horarios: @js($turnosReserva->flatMap(fn ($turno) => $turno->horarios->map(fn ($horario) => substr($horario->hora_inicio, 0, 5) . ' - ' . substr($horario->hora_fin, 0, 5)))->unique()->values()->all() ?: ['08:30 - 10:00', '10:00 - 11:30', '11:30 - 13:00', '13:00 - 14:30', '14:30 - 16:00', '16:00 - 17:30', '17:30 - 19:00']),
                form: {
                    fecha: '',
                    hora_inicio: '',
                    cantidad_personas: 1,
                    descripcion: '',
                    nombre: @js($user->name),
                    correo: @js($user->email),
                    telefono: @js($user->telefono ?? ''),
                },
                init() {
                    this.applyTheme();
                    if (this.activePanel === 'crear') {
                        this.createFrameLoading = true;
                        this.createFrameUrl = @js(route('reservas.create', ['embedded' => 1]));
                    } else {
                        window.setTimeout(() => {
                            if (!this.createFrameUrl) {
                                this.createFrameReady = false;
                                this.createFrameUrl = @js(route('reservas.create', ['embedded' => 1]));
                            }
                        }, 700);
                    }
                    const pendingMessage = localStorage.getItem('user-dashboard-message');
                    if (pendingMessage) {
                        this.flashType = 'success';
                        this.flashMessage = pendingMessage;
                        localStorage.removeItem('user-dashboard-message');
                    }
                    window.addEventListener('resize', () => {
                        this.sidebarOpen = window.innerWidth >= 1024;
                    });
                    window.addEventListener('message', (event) => {
                        if (event.origin !== window.location.origin) return;
                        if (event.data?.type === 'reserva-panel') {
                            this.setPanel(event.data.panel || 'reservas');
                        }
                        if (event.data?.type === 'reserva-created') {
                            localStorage.setItem('user-dashboard-message', event.data.message || 'Reserva registrada correctamente. Ponte en contacto con secretaría para confirmar el pago.');
                            window.location.href = @js(route('dashboard')) + '?panel=dashboard';
                        }
                        if (event.data?.type === 'reserva-updated') {
                            localStorage.setItem('user-dashboard-message', event.data.message || 'Reserva modificada correctamente. Quedó pendiente de revisión por secretaría.');
                            window.location.href = @js(route('dashboard')) + '?panel=dashboard';
                        }
                    });
                    this.$watch('darkMode', () => this.applyTheme());
                },
                navClass(panel) {
                    return this.activePanel === panel
                        ? 'bg-white/20 text-white shadow-lg border border-white/10'
                        : 'text-white/70 hover:bg-white/10';
                },
                setPanel(panel) {
                    this.activePanel = panel;
                    if (panel === 'crear') {
                        if (!this.createFrameUrl) {
                            this.createFrameReady = false;
                            this.createFrameLoading = true;
                            this.createFrameUrl = @js(route('reservas.create', ['embedded' => 1]));
                        } else {
                            this.createFrameLoading = !this.createFrameReady;
                        }
                    }
                    if (window.innerWidth < 1024) this.sidebarOpen = false;
                    const url = new URL(window.location.href);
                    url.searchParams.set('panel', panel);
                    if (panel === 'reserva-frame' && this.frameUrl) {
                        url.searchParams.set('frame', this.frameUrl);
                    } else {
                        url.searchParams.delete('frame');
                    }
                    window.history.replaceState({}, '', url);
                    this.$nextTick(() => {
                        const scroller = document.querySelector('.dashboard-content');
                        if (scroller) scroller.scrollTo({ top: 0, behavior: 'smooth' });
                    });
                },
                goPanel(panel) {
                    this.setPanel(panel);
                },
                showPanelMessage(message, type = 'success') {
                    this.flashType = type;
                    this.flashMessage = message;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    clearTimeout(this.flashTimeout);
                    this.flashTimeout = setTimeout(() => {
                        this.flashMessage = '';
                    }, 5500);
                },
                openReservaDetail(reserva) {
                    this.selectedReserva = reserva;
                    this.setPanel('detalle');
                },
                openReservaFrame(url) {
                    this.frameUrl = url;
                    this.frameLoading = true;
                    this.setPanel('reserva-frame');
                },
                applyTheme() {
                    document.documentElement.classList.toggle('dark', this.darkMode);
                    localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
                },
                toggleTheme() {
                    this.darkMode = !this.darkMode;
                }
            }
        }
    </script>
</x-app-layout>
