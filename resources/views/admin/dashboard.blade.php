<x-app-layout>
    @php
        $adminName = auth()->user()->name ?? 'Administrador';

        $stats = [
            [
                'label' => 'Usuarios registrados',
                'value' => $usuariosRegistrados ?? 0,
                'icon' => '👥',
                'color' => 'text-slate-900 dark:text-white',
                'bg' => 'bg-slate-100 dark:bg-slate-800',
            ],
            [
                'label' => 'Reservas hoy',
                'value' => $reservasHoy ?? 0,
                'icon' => '📅',
                'color' => 'text-blue-600 dark:text-blue-400',
                'bg' => 'bg-blue-50 dark:bg-blue-500/10',
            ],
            [
                'label' => 'Disponibilidad',
                'value' => $disponibilidadHoy ?? 'Disponible',
                'icon' => '⭐',
                'color' => 'text-amber-500 dark:text-amber-400',
                'bg' => 'bg-amber-50 dark:bg-amber-500/10',
            ],
            [
                'label' => 'Visitas confirmadas',
                'value' => $visitasConfirmadas ?? 0,
                'icon' => '✅',
                'color' => 'text-emerald-600 dark:text-emerald-400',
                'bg' => 'bg-emerald-50 dark:bg-emerald-500/10',
            ],
        ];

        $panelTitles = [
            'dashboard' => ['Panel principal', 'Administra usuarios, reservas y estadísticas generales.'],
            'usuarios' => ['Gestión de usuarios', 'Crea roles, registra personal interno y revisa las cuentas administrativas.'],
            'guias' => ['Asignación de guía', 'Programa guías por fecha y notifica su sesión por WhatsApp o correo.'],
            'invitados' => ['Invitados especiales', 'Crea reservas especiales para autoridades, extranjeros o delegaciones institucionales.'],
            'bienvenido' => ['Editar página Bienvenido', 'Administra slides, fondos y mensajes principales.'],
            'acerca' => ['Editar página Acerca de', 'Administra historia, misión, visión e información institucional.'],
            'eventos' => ['Editar página Eventos', 'Publica actividades, imágenes y descripciones para visitantes.'],
            'investigacion' => ['Editar página Investigación', 'Publica proyectos, resultados e investigaciones anteriores.'],
            'galeria' => ['Editar galería de imágenes', 'Administra fotos públicas del observatorio.'],
            'reportes' => ['Reportes administrativos', 'Filtra, revisa y descarga reportes institucionales sin salir del panel.'],
            'mantenimiento' => ['Backups y logs', 'Respalda la base de datos y revisa eventos técnicos del sistema.'],
        ];

        $requestedPanel = request('panel', 'dashboard');
        $activePanel = $requestedPanel === 'reportes' ? 'reportes' : 'dashboard';
        $activePanel = array_key_exists($activePanel, $panelTitles) ? $activePanel : 'dashboard';
        [$activePanelTitle, $activePanelSubtitle] = $panelTitles[$activePanel];
    @endphp

    <script>
        (function () {
            try {
                const savedTheme = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const isDark = savedTheme === 'dark' || (!savedTheme && prefersDark);

                document.documentElement.classList.toggle('dark', isDark);

                if (document.body) {
                    document.body.classList.toggle('dark', isDark);
                    document.body.style.colorScheme = isDark ? 'dark' : 'light';
                }
            } catch (error) {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    <div id="dashboard-wrapper"
         class="min-h-screen bg-[#f4f7fb] dark:bg-[#07111f] text-slate-900 dark:text-slate-100 transition-colors duration-500">

        <style>
            .admin-card {
                background: rgba(255, 255, 255, 0.92);
                border: 1px solid rgba(15, 23, 42, 0.10);
                box-shadow: 0 18px 45px rgba(15, 23, 42, 0.06);
            }

            .dark .admin-card {
                background: rgba(16, 27, 45, 0.92);
                border: 1px solid rgba(255, 255, 255, 0.09);
                box-shadow: 0 18px 45px rgba(0, 0, 0, 0.30);
            }

            .admin-glass {
                background: rgba(255, 255, 255, 0.78);
                border: 1px solid rgba(15, 23, 42, 0.10);
                backdrop-filter: blur(22px);
            }

            .dashboard-surface,
            .admin-card,
            .admin-glass,
            #sidebar,
            body,
            html {
                transition: background-color 220ms ease, color 220ms ease, border-color 220ms ease, box-shadow 220ms ease;
            }

            .dark .admin-glass {
                background: rgba(16, 27, 45, 0.78);
                border: 1px solid rgba(255, 255, 255, 0.09);
            }

            .sidebar-link.active {
                background: rgba(255, 255, 255, 0.18);
                color: #fff;
            }

            .custom-scrollbar::-webkit-scrollbar {
                width: 5px;
            }

            .custom-scrollbar::-webkit-scrollbar-track {
                background: transparent;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: rgba(255, 255, 255, 0.35);
                border-radius: 999px;
            }

            input[type="range"]::-webkit-slider-thumb {
                -webkit-appearance: none;
                width: 16px;
                height: 16px;
                background: #f59e0b;
                border-radius: 50%;
                cursor: pointer;
            }
        </style>

        {{-- Fondo decorativo --}}
        <div class="fixed inset-0 pointer-events-none overflow-hidden">
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-amber-400/10 rounded-full blur-3xl"></div>
        </div>

        {{-- Overlay móvil --}}
        <div id="sidebar-overlay"
             class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden"
             onclick="toggleSidebar()">
        </div>

        {{-- Sidebar --}}
        <aside id="sidebar"
               class="fixed inset-y-0 left-0 z-50 w-72 bg-blue-700 dark:bg-[#0f172a] text-white transform -translate-x-full lg:translate-x-0 transition-transform duration-300 shadow-2xl flex flex-col">

            {{-- Marca --}}
            <div class="p-6 border-b border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-white text-blue-700 flex items-center justify-center font-black shadow-lg">
                        MS
                    </div>

                    <div>
                        <h1 class="text-lg font-black leading-none">
                            Max Schreier
                        </h1>
                        <p class="text-[10px] text-blue-100 uppercase tracking-widest font-bold mt-1">
                            Observatorio online
                        </p>
                    </div>
                </div>
            </div>

            {{-- Usuario --}}
            <div class="p-6 border-b border-white/10">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full border-2 border-white/70 flex items-center justify-center text-xl font-black">
                        {{ strtoupper(substr($adminName, 0, 1)) }}
                    </div>

                    <div class="min-w-0">
                        <p class="font-black truncate">
                            {{ $adminName }}
                        </p>
                        <p class="text-[11px] text-green-300 font-bold flex items-center gap-1 mt-1">
                            <span class="w-2 h-2 rounded-full bg-green-400"></span>
                            Administrador activo
                        </p>
                    </div>
                </div>
            </div>

            {{-- Navegación --}}
            <nav class="flex-1 overflow-y-auto custom-scrollbar p-4 space-y-6">

                <div>
                    <p class="text-[10px] text-blue-100/70 font-black uppercase tracking-widest px-3 mb-3">
                        Menú principal
                    </p>

                    <div class="space-y-2">
                        <button type="button"
                                data-panel-button="dashboard"
                                data-panel-title="Panel principal"
                                data-panel-subtitle="Administra usuarios, reservas y estadísticas generales."
                                class="sidebar-link {{ $activePanel === 'dashboard' ? 'active' : '' }} w-full flex items-center gap-3 px-4 py-3 rounded-2xl hover:bg-white/10 text-blue-50 font-bold text-sm transition">
                            <span>📊</span>
                            Dashboard
                        </button>

                        <button type="button"
                                data-panel-button="usuarios"
                                data-panel-title="Gestión de usuarios"
                                data-panel-subtitle="Crea roles, registra personal interno y revisa las cuentas administrativas."
                                class="sidebar-link {{ $activePanel === 'usuarios' ? 'active' : '' }} w-full flex items-center gap-3 px-4 py-3 rounded-2xl hover:bg-white/10 text-blue-50 font-bold text-sm transition">
                            <span>👤</span>
                            Gestión de usuarios
                        </button>

                        <button type="button"
                                data-panel-button="guias"
                                data-panel-title="Asignación de guía"
                                data-panel-subtitle="Programa guías por fecha y notifica su sesión por WhatsApp o correo."
                                class="sidebar-link {{ $activePanel === 'guias' ? 'active' : '' }} w-full flex items-center gap-3 px-4 py-3 rounded-2xl hover:bg-white/10 text-blue-50 font-bold text-sm transition">
                            <span>🧭</span>
                            Asignación de guía
                        </button>

                        <button type="button"
                                data-panel-button="invitados"
                                data-panel-title="Invitados especiales"
                                data-panel-subtitle="Crea reservas especiales para autoridades, extranjeros o delegaciones institucionales."
                                class="sidebar-link {{ $activePanel === 'invitados' ? 'active' : '' }} w-full flex items-center gap-3 px-4 py-3 rounded-2xl hover:bg-white/10 text-blue-50 font-bold text-sm transition">
                            <span>⭐</span>
                            Invitados especiales
                        </button>

                        <a href="{{ route('bienvenido') }}" target="_blank"
                           class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl hover:bg-white/10 text-blue-50 font-bold text-sm transition">
                            <span>🌐</span>
                            Ver sitio público
                        </a>

                        <button type="button"
                                data-panel-button="reportes"
                                data-panel-title="Reportes administrativos"
                                data-panel-subtitle="Filtra, revisa y descarga reportes institucionales sin salir del panel."
                                class="sidebar-link {{ $activePanel === 'reportes' ? 'active' : '' }} w-full flex items-center gap-3 px-4 py-3 rounded-2xl hover:bg-white/10 text-blue-50 font-bold text-sm transition">
                            <span>📄</span>
                            Reportes
                        </button>

                        <button type="button"
                                data-panel-button="mantenimiento"
                                data-panel-title="Backups y logs"
                                data-panel-subtitle="Respalda la base de datos y revisa eventos técnicos del sistema."
                                class="sidebar-link {{ $activePanel === 'mantenimiento' ? 'active' : '' }} w-full flex items-center gap-3 px-4 py-3 rounded-2xl hover:bg-white/10 text-blue-50 font-bold text-sm transition">
                            <span>🛡️</span>
                            Backups y logs
                        </button>
                    </div>
                </div>

                <div>
                    <p class="text-[10px] text-blue-100/70 font-black uppercase tracking-widest px-3 mb-3">
                        Contenido público
                    </p>

                    <div class="space-y-2">
                        <button type="button"
                                data-panel-button="bienvenido"
                                data-panel-title="Editar página Bienvenido"
                                data-panel-subtitle="Cambia fondos, slides, imágenes y textos del inicio."
                                class="sidebar-link w-full flex items-center gap-3 px-4 py-3 rounded-2xl hover:bg-white/10 text-blue-50 font-bold text-sm transition">
                            <span>🏠</span>
                            Bienvenido
                        </button>

                        <button type="button"
                                data-panel-button="acerca"
                                data-panel-title="Editar página Acerca de"
                                data-panel-subtitle="Administra historia, misión, visión e información institucional."
                                class="sidebar-link w-full flex items-center gap-3 px-4 py-3 rounded-2xl hover:bg-white/10 text-blue-50 font-bold text-sm transition">
                            <span>ℹ️</span>
                            Acerca de
                        </button>

                        <button type="button"
                                data-panel-button="eventos"
                                data-panel-title="Editar eventos"
                                data-panel-subtitle="Gestiona actividades, visitas, talleres y eventos públicos."
                                class="sidebar-link w-full flex items-center gap-3 px-4 py-3 rounded-2xl hover:bg-white/10 text-blue-50 font-bold text-sm transition">
                            <span>📅</span>
                            Eventos
                        </button>

                        <button type="button"
                                data-panel-button="investigacion"
                                data-panel-title="Editar investigación"
                                data-panel-subtitle="Administra proyectos, contenido académico y publicaciones."
                                class="sidebar-link w-full flex items-center gap-3 px-4 py-3 rounded-2xl hover:bg-white/10 text-blue-50 font-bold text-sm transition">
                            <span>🔭</span>
                            Investigación
                        </button>

                        <button type="button"
                                data-panel-button="galeria"
                                data-panel-title="Editar galería"
                                data-panel-subtitle="Sube, ordena y elimina imágenes del observatorio."
                                class="sidebar-link w-full flex items-center gap-3 px-4 py-3 rounded-2xl hover:bg-white/10 text-blue-50 font-bold text-sm transition">
                            <span>🖼️</span>
                            Galería
                        </button>
                    </div>
                </div>

                <div>
                    <p class="text-[10px] text-blue-100/70 font-black uppercase tracking-widest px-3 mb-3">
                        Gestión
                    </p>

                    <div class="space-y-2">
                        <a href="{{ route('reservas.index') }}"
                           class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl hover:bg-white/10 text-blue-50 font-bold text-sm transition">
                            <span>🗓️</span>
                            Reservas
                        </a>

                        <a href="{{ route('profile.edit') }}"
                           class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl hover:bg-white/10 text-blue-50 font-bold text-sm transition">
                            <span>👤</span>
                            Perfil
                        </a>
                    </div>
                </div>
            </nav>

            {{-- Parte inferior --}}
            <div class="p-4 border-t border-white/10 space-y-3">
                <div class="w-full bg-white/10 rounded-2xl p-2 grid grid-cols-2 gap-2">
                    <button id="theme-day"
                          type="button"
                          class="py-2 rounded-xl bg-white text-blue-700 text-[11px] font-black uppercase transition">
                        ☀️ Día
                    </button>
                    <button id="theme-night"
                          type="button"
                          class="py-2 rounded-xl text-blue-100 text-[11px] font-black uppercase transition">
                        🌙 Noche
                    </button>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full bg-rose-500/85 hover:bg-rose-500 text-white rounded-2xl py-3 text-xs font-black uppercase tracking-widest transition">
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </aside>

        {{-- Contenido principal --}}
        <main class="relative z-10 lg:pl-72 min-h-screen">

            {{-- Header móvil --}}
            <header class="lg:hidden sticky top-0 z-30 admin-glass px-4 py-4">
                <div class="flex items-center justify-between">
                    <button onclick="toggleSidebar()"
                            class="w-11 h-11 rounded-2xl bg-blue-600 text-white flex items-center justify-center shadow-lg">
                        ☰
                    </button>

                    <div class="text-right">
                        <h1 class="text-lg font-black">
                            SISOBS Admin
                        </h1>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 font-bold">
                            Panel central
                        </p>
                    </div>
                </div>
            </header>

            <div class="p-4 sm:p-5 xl:p-8 max-w-[1800px] mx-auto">

                {{-- Topbar escritorio --}}
                <section class="hidden lg:flex admin-glass rounded-[28px] px-6 py-5 mb-6 shadow-xl items-center justify-between gap-4">
                    <div>
                        <h2 id="panel-title" class="text-3xl font-black tracking-tight">
                            {{ $activePanelTitle }}
                        </h2>
                        <p id="panel-subtitle" class="text-sm text-slate-500 dark:text-slate-400 mt-1 font-semibold">
                            {{ $activePanelSubtitle }}
                        </p>
                    </div>

                    <div id="dashboard-report-actions" class="{{ $activePanel === 'dashboard' ? 'flex' : 'hidden' }} items-center gap-3">
                        <a href="{{ route('bienvenido') }}" target="_blank"
                           class="bg-white dark:bg-[#17243a] border border-slate-200 dark:border-slate-700 px-5 py-3 rounded-2xl text-xs font-black uppercase tracking-widest hover:scale-[1.02] transition">
                            Ver sitio
                        </a>

                        <button type="button"
                                onclick="setActivePanel('reportes', 'Reportes administrativos', 'Filtra, revisa y descarga reportes institucionales sin salir del panel.')"
                           class="bg-emerald-600 hover:bg-emerald-500 text-white px-5 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition shadow-lg shadow-emerald-600/20">
                            Reporte reservas
                        </button>

                        <button type="button"
                                onclick="setActivePanel('reportes', 'Reportes administrativos', 'Filtra, revisa y descarga reportes institucionales sin salir del panel.')"
                           class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition shadow-lg shadow-blue-600/20">
                            Reporte usuarios
                        </button>
                    </div>
                </section>

                {{-- Alertas --}}
                @if (session('success') || session('status'))
                    <div class="mb-6 admin-card rounded-2xl px-4 py-3 text-sm font-bold text-emerald-600 dark:text-emerald-400">
                        {{ session('success') ?? session('status') }}
                    </div>
                @endif

                {{-- PANEL: DASHBOARD --}}
                <section id="panel-dashboard" class="admin-panel {{ $activePanel === 'dashboard' ? '' : 'hidden' }}">

                    {{-- Stats --}}
                    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
                        @foreach($stats as $s)
                            <div class="admin-card p-5 rounded-[26px]">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-slate-500 dark:text-slate-400 text-[11px] font-black uppercase tracking-widest">
                                            {{ $s['label'] }}
                                        </p>
                                        <h2 class="text-3xl sm:text-4xl font-black mt-3 {{ $s['color'] }}">
                                            {{ $s['value'] }}
                                        </h2>
                                    </div>

                                    <div class="w-11 h-11 rounded-2xl {{ $s['bg'] }} flex items-center justify-center text-xl">
                                        {{ $s['icon'] }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </section>

                    <section class="admin-card rounded-[28px] p-5 mb-6 border border-blue-100 dark:border-blue-500/20">
                        <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4 mb-5">
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-blue-600 dark:text-blue-400">
                                    Reportes y validación
                                </p>
                                <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white mt-1">
                                    Panel de métricas
                                </h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 font-semibold">
                                    Métricas administrativas con pagos validados, reservas confirmadas, eventos publicados e invitados especiales.
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <span class="rounded-2xl bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300 px-4 py-2 text-[10px] font-black uppercase tracking-widest">
                                    Acceso: Admin
                                </span>
                                <span class="rounded-2xl bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-300 px-4 py-2 text-[10px] font-black uppercase tracking-widest">
                                    Secretaria autorizada
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
                            <div class="rounded-2xl bg-slate-50 dark:bg-[#0f172a] border border-slate-100 dark:border-slate-700 p-4">
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Recaudación confirmada</p>
                                <p class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-2">
                                    Bs. {{ number_format((float) ($recaudacionConfirmada ?? 0), 2) }}
                                </p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-bold mt-2">
                                    Solo pagos validados y reservas confirmadas.
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 dark:bg-[#0f172a] border border-slate-100 dark:border-slate-700 p-4">
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Pagos validados</p>
                                <p class="text-2xl sm:text-3xl font-black text-blue-600 dark:text-blue-400 mt-2">
                                    {{ $pagosConfirmados ?? 0 }}
                                </p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-bold mt-2">
                                    Confirmados por secretaría.
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 dark:bg-[#0f172a] border border-slate-100 dark:border-slate-700 p-4">
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Invitados especiales</p>
                                <p class="text-2xl sm:text-3xl font-black text-amber-500 dark:text-amber-400 mt-2">
                                    {{ $invitadosEspecialesCount ?? 0 }}
                                </p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-bold mt-2">
                                    Autoridades, extranjeros o delegaciones.
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 dark:bg-[#0f172a] border border-slate-100 dark:border-slate-700 p-4">
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Eventos publicados</p>
                                <p class="text-2xl sm:text-3xl font-black text-rose-500 dark:text-rose-400 mt-2">
                                    {{ $eventosPublicados ?? 0 }}
                                </p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-bold mt-2">
                                    Actividades visibles en el sitio público.
                                </p>
                            </div>
                        </div>
                    </section>

                    <section class="grid grid-cols-1 xl:grid-cols-12 gap-5">

                        <div class="xl:col-span-4 admin-card p-5 rounded-[28px] flex flex-col">
                            <div class="flex items-center justify-between gap-3 mb-5">
                                <h3 class="text-sm font-black uppercase tracking-widest">
                                    Control de aforo
                                </h3>
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">
                                    Hoy
                                </span>
                            </div>

                            <div class="flex-1 flex flex-col items-center justify-center text-center">
                                <div class="relative inline-flex items-center justify-center mb-6">
                                    <svg class="w-36 h-36 transform -rotate-90">
                                        <circle cx="72" cy="72" r="58" stroke="currentColor" stroke-width="13" fill="transparent"
                                                class="text-slate-100 dark:text-slate-700" />
                                        <circle id="aforoCircle"
                                                cx="72" cy="72" r="58" stroke="currentColor" stroke-width="13" fill="transparent"
                                                stroke-dasharray="364.42" stroke-dashoffset="{{ ($aforoOcupadoHoy ?? 0) > 0 ? '0' : '364.42' }}"
                                                class="text-amber-500 drop-shadow-[0_0_8px_rgba(245,158,11,0.4)]" />
                                    </svg>

                                    <div class="absolute flex flex-col items-center">
                                        <span class="text-4xl font-black text-slate-900 dark:text-white" id="valAforo">{{ $aforoOcupadoHoy ?? 0 }}</span>
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Personas</span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-3 w-full mb-4">
                                    <div class="rounded-2xl bg-slate-50 dark:bg-[#0f172a] border border-slate-200 dark:border-slate-700 p-3">
                                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Ocupado</p>
                                        <p class="text-2xl font-black mt-2">{{ $aforoOcupadoHoy ?? 0 }}</p>
                                    </div>
                                    <div class="rounded-2xl bg-slate-50 dark:bg-[#0f172a] border border-slate-200 dark:border-slate-700 p-3">
                                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Disponibilidad</p>
                                        <p class="text-2xl font-black mt-2">{{ $disponibilidadHoy ?? 'Disponible' }}</p>
                                    </div>
                                </div>

                                <div class="w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                    <div class="h-full bg-amber-500 rounded-full"
                                         style="width: {{ min(max(($aforoOcupadoHoy ?? 0), 0), 100) }}%"></div>
                                </div>
                                <p class="mt-4 text-xs font-bold text-slate-500 dark:text-slate-400">
                                    Ocupación flexible de hoy: {{ $aforoOcupadoHoy ?? 0 }} persona(s). No existe límite fijo de aforo.
                                </p>
                            </div>
                        </div>

                        <div class="xl:col-span-4 admin-card p-5 rounded-[28px]">
                            <div class="flex items-center justify-between gap-3 mb-5">
                                <h3 class="text-sm font-black uppercase tracking-widest">
                                    Actividad semanal
                                </h3>
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Lun - Vie</span>
                            </div>

                            <div class="h-[280px] w-full">
                                <canvas id="mainChart"></canvas>
                            </div>

                            <p class="mt-3 text-xs font-bold text-slate-500 dark:text-slate-400">
                                La actividad semanal considera solo atención de lunes a viernes; sábado y domingo permanecen cerrados.
                            </p>
                        </div>

                        <div class="xl:col-span-4 admin-card p-5 rounded-[28px]">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3 mb-5">
                                <h3 class="text-sm font-black uppercase tracking-widest">
                                    Disponibilidad
                                </h3>

                                <div class="flex flex-wrap gap-3 text-[9px] font-black uppercase">
                                    <span class="flex items-center text-slate-500 dark:text-slate-400">
                                        <span class="w-2.5 h-2.5 bg-green-400 dark:bg-green-500 rounded-full mr-1.5"></span>
                                        Libre
                                    </span>

                                    <span class="flex items-center text-slate-500 dark:text-slate-400">
                                        <span class="w-2.5 h-2.5 bg-rose-400 dark:bg-rose-500 rounded-full mr-1.5"></span>
                                        Reservado
                                    </span>

                                    <span class="flex items-center text-slate-500 dark:text-slate-400">
                                        <span class="w-2.5 h-2.5 bg-slate-400 rounded-full mr-1.5"></span>
                                        Cerrado
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-7 gap-1.5">
                                @foreach(['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'] as $day)
                                    <div class="text-[10px] font-black text-slate-400 py-1 text-center">
                                        {{ $day }}
                                    </div>
                                @endforeach

                                @foreach(($availabilityDays ?? collect()) as $day)
                                    <a href="{{ route('admin.dashboard', ['panel' => 'dashboard', 'fecha' => $day['date']]) }}#selected-date-details"
                                       title="{{ $day['date'] }} | Reservas: {{ $day['reservas'] }} | Visitantes: {{ $day['visitantes'] }}"
                                       class="py-2 rounded-xl text-xs font-black text-center transition-colors
                                        {{ $day['selected'] ? 'ring-2 ring-blue-500 ring-offset-2 ring-offset-white dark:ring-offset-[#101b2d]' : '' }}
                                        {{ $day['blocked']
                                            ? 'bg-slate-200 text-slate-400 dark:bg-slate-800 dark:text-slate-500 cursor-not-allowed'
                                            : ($day['reserved'] || $day['full']
                                                ? 'bg-rose-100 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400 border border-rose-200 dark:border-rose-500/30'
                                                : 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20 hover:bg-blue-50 dark:hover:bg-blue-900/30') }}">
                                        {{ $day['day'] }}
                                    </a>
                                @endforeach
                            </div>

                            <div id="selected-date-details" class="mt-5 rounded-2xl bg-slate-50 dark:bg-[#0f172a] border border-slate-200 dark:border-slate-700 p-4">
                                <div class="flex items-center justify-between gap-3 mb-3">
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Detalle del día</p>
                                        <h4 class="text-sm font-black">{{ ($selectedDate ?? now())->format('d/m/Y') }}</h4>
                                    </div>
                                    <a href="{{ route('admin.reportes.reservas.pdf', ['fecha_inicio' => ($selectedDate ?? now())->toDateString(), 'fecha_fin' => ($selectedDate ?? now())->toDateString()]) }}"
                                       class="bg-blue-600 hover:bg-blue-500 text-white rounded-xl px-3 py-2 text-[10px] font-black uppercase">
                                        PDF día
                                    </a>
                                </div>

                                <div class="grid grid-cols-2 gap-2 text-center mb-3">
                                    <div class="rounded-xl bg-white dark:bg-slate-900 p-2">
                                        <p class="text-lg font-black">{{ $selectedDateStats['total'] ?? 0 }}</p>
                                        <p class="text-[9px] text-slate-400 font-black uppercase">Reservas</p>
                                    </div>
                                    <div class="rounded-xl bg-white dark:bg-slate-900 p-2">
                                        <p class="text-lg font-black">{{ $selectedDateStats['visitantes'] ?? 0 }}</p>
                                        <p class="text-[9px] text-slate-400 font-black uppercase">Visitantes</p>
                                    </div>
                                </div>

                                <div class="max-h-40 overflow-y-auto custom-scrollbar space-y-2">
                                    @forelse(($selectedDateReservas ?? collect()) as $reserva)
                                        <div class="bg-white dark:bg-slate-900 rounded-xl p-3 border border-slate-100 dark:border-slate-800">
                                            <div class="flex justify-between gap-2 text-xs">
                                                <span class="font-bold truncate">{{ $reserva->nombre }}</span>
                                                <span class="shrink-0 font-black">{{ $reserva->cantidad_personas }} pax</span>
                                            </div>
                                            <div class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                                                {{ $reserva->horario?->hora_inicio ?? 'Sin hora' }}
                                                @if($reserva->horario?->hora_fin)
                                                    - {{ $reserva->horario->hora_fin }}
                                                @endif
                                                · {{ $reserva->estado }}
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-xs text-slate-400 font-bold text-center py-3">Sin reservas para este día.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </section>
                </section>

                {{-- PANEL: ASIGNACIÓN DE GUÍA --}}
                <section id="panel-guias" class="admin-panel {{ $activePanel === 'guias' ? '' : 'hidden' }}">
                    <div class="grid grid-cols-1 xl:grid-cols-12 gap-5">
                        <div class="xl:col-span-5 admin-card p-5 rounded-[28px]">
                            <h3 class="text-sm font-black mb-5 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700 pb-3">
                                Nueva asignación
                            </h3>

                            <form method="POST" action="{{ route('admin.guias.store') }}" class="space-y-4">
                                @csrf

                                <div>
                                    <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase block mb-2">
                                        Nombre del guía
                                    </label>
                                    <input type="text"
                                           name="nombre"
                                           required
                                           class="w-full bg-slate-50 dark:bg-[#0f172a] border-slate-200 dark:border-slate-600 rounded-2xl text-sm p-3 dark:text-white focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase block mb-2">
                                            Cargo
                                        </label>
                                        <input type="text"
                                               name="cargo"
                                               placeholder="Ej. Guía astronómico"
                                               class="w-full bg-slate-50 dark:bg-[#0f172a] border-slate-200 dark:border-slate-600 rounded-2xl text-sm p-3 dark:text-white placeholder-slate-400">
                                    </div>

                                    <div>
                                        <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase block mb-2">
                                            Carnet
                                        </label>
                                        <input type="text"
                                               name="ci"
                                               class="w-full bg-slate-50 dark:bg-[#0f172a] border-slate-200 dark:border-slate-600 rounded-2xl text-sm p-3 dark:text-white placeholder-slate-400">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase block mb-2">
                                            WhatsApp
                                        </label>
                                        <input type="text"
                                               name="telefono"
                                               placeholder="Ej. 70012345"
                                               class="w-full bg-slate-50 dark:bg-[#0f172a] border-slate-200 dark:border-slate-600 rounded-2xl text-sm p-3 dark:text-white placeholder-slate-400">
                                    </div>

                                    <div>
                                        <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase block mb-2">
                                            Correo
                                        </label>
                                        <input type="email"
                                               name="email"
                                               placeholder="guia@correo.com"
                                               class="w-full bg-slate-50 dark:bg-[#0f172a] border-slate-200 dark:border-slate-600 rounded-2xl text-sm p-3 dark:text-white placeholder-slate-400">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase block mb-2">
                                            Fecha
                                        </label>
                                        <input type="date"
                                               name="fecha"
                                               required
                                               value="{{ ($selectedDate ?? now())->format('Y-m-d') }}"
                                               class="w-full bg-slate-50 dark:bg-[#0f172a] border-slate-200 dark:border-slate-600 rounded-2xl text-sm p-3 dark:text-white">
                                    </div>

                                    <div>
                                        <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase block mb-2">
                                            Inicio
                                        </label>
                                        <input type="time"
                                               name="hora_inicio"
                                               value="08:00"
                                               class="w-full bg-slate-50 dark:bg-[#0f172a] border-slate-200 dark:border-slate-600 rounded-2xl text-sm p-3 dark:text-white">
                                    </div>

                                    <div>
                                        <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase block mb-2">
                                            Fin
                                        </label>
                                        <input type="time"
                                               name="hora_fin"
                                               value="09:30"
                                               class="w-full bg-slate-50 dark:bg-[#0f172a] border-slate-200 dark:border-slate-600 rounded-2xl text-sm p-3 dark:text-white">
                                    </div>
                                </div>

                                <div>
                                    <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase block mb-2">
                                        Observación
                                    </label>
                                    <textarea name="observacion"
                                              rows="3"
                                              placeholder="Detalle de la sesión o indicaciones internas."
                                              class="w-full bg-slate-50 dark:bg-[#0f172a] border-slate-200 dark:border-slate-600 rounded-2xl text-sm p-3 dark:text-white placeholder-slate-400"></textarea>
                                </div>

                                <button type="submit"
                                        class="w-full bg-blue-600 hover:bg-blue-500 text-white py-3 rounded-2xl font-black text-xs uppercase tracking-widest transition shadow-md shadow-blue-500/20">
                                    Asignar guía
                                </button>
                            </form>
                        </div>

                        <div class="xl:col-span-7 admin-card p-5 rounded-[28px]">
                            <div class="flex items-center justify-between gap-3 mb-5 border-b border-slate-100 dark:border-slate-700 pb-3">
                                <div>
                                    <h3 class="text-sm font-black uppercase tracking-widest">
                                        Guías asignados
                                    </h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-semibold">
                                        El correo se envía automáticamente; WhatsApp se abre con el mensaje listo para confirmar el envío.
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-3 max-h-[620px] overflow-y-auto custom-scrollbar pr-1">
                                @forelse(($guideAssignments ?? collect()) as $assignment)
                                    @php
                                        $fechaGuia = $assignment->fecha?->format('d/m/Y') ?? 'sin fecha';
                                        $horaGuia = trim(($assignment->hora_inicio ? substr($assignment->hora_inicio, 0, 5) : '') . ($assignment->hora_fin ? ' - ' . substr($assignment->hora_fin, 0, 5) : ''));
                                        $mensajeGuia = "Hola {$assignment->nombre}, se le asignó una sesión como guía del Observatorio Max Schreier para el {$fechaGuia}" . ($horaGuia ? " en el horario {$horaGuia}" : '') . ". Por favor confirmar disponibilidad.";
                                        $telefonoGuia = preg_replace('/\D+/', '', $assignment->telefono ?? '');
                                        $telefonoWhatsapp = $telefonoGuia && str_starts_with($telefonoGuia, '591') ? $telefonoGuia : '591' . $telefonoGuia;
                                        $whatsappUrl = $telefonoGuia ? 'https://wa.me/' . $telefonoWhatsapp . '?text=' . rawurlencode($mensajeGuia) : null;
                                        $emailUrl = $assignment->email
                                            ? 'https://mail.google.com/mail/?view=cm&fs=1&to=' . rawurlencode($assignment->email) . '&su=' . rawurlencode('Asignación de guía - Observatorio Max Schreier') . '&body=' . rawurlencode($mensajeGuia)
                                            : null;
                                    @endphp

                                    <div class="p-4 bg-slate-50 dark:bg-[#0f172a] rounded-2xl border border-slate-100 dark:border-slate-700/50">
                                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3">
                                            <div class="min-w-0">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <p class="text-sm font-black text-slate-800 dark:text-slate-200 truncate">
                                                        {{ $assignment->nombre }}
                                                    </p>
                                                    <span class="text-[9px] font-black px-2.5 py-1.5 rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400 uppercase tracking-widest">
                                                        {{ $assignment->cargo ?: 'Guía' }}
                                                    </span>
                                                </div>
                                                <p class="text-[11px] text-slate-500 truncate mt-1">
                                                    {{ $fechaGuia }} · {{ $horaGuia ?: 'Sin horario' }} · CI: {{ $assignment->ci ?: 'N/A' }}
                                                </p>
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-3 text-[11px] font-bold text-slate-500 dark:text-slate-400">
                                                    <span class="truncate rounded-xl bg-white dark:bg-slate-900/70 border border-slate-100 dark:border-slate-700 px-3 py-2">
                                                        WhatsApp: {{ $assignment->telefono ?: 'Sin número' }}
                                                    </span>
                                                    <span class="truncate rounded-xl bg-white dark:bg-slate-900/70 border border-slate-100 dark:border-slate-700 px-3 py-2">
                                                        Gmail: {{ $assignment->email ?: 'Sin correo' }}
                                                    </span>
                                                </div>
                                                <div class="flex flex-wrap gap-2 mt-2">
                                                    @if($assignment->email_sent_at)
                                                        <span class="text-[9px] font-black uppercase tracking-widest rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300 px-2.5 py-1.5">
                                                            Correo automático enviado
                                                        </span>
                                                    @elseif($assignment->email)
                                                        <span class="text-[9px] font-black uppercase tracking-widest rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300 px-2.5 py-1.5">
                                                            Correo pendiente
                                                        </span>
                                                    @else
                                                        <span class="text-[9px] font-black uppercase tracking-widest rounded-xl bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300 px-2.5 py-1.5">
                                                            Sin correo
                                                        </span>
                                                    @endif

                                                    @if($assignment->whatsapp_link_generated_at)
                                                        <span class="text-[9px] font-black uppercase tracking-widest rounded-xl bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300 px-2.5 py-1.5">
                                                            WhatsApp listo
                                                        </span>
                                                    @elseif($assignment->telefono)
                                                        <span class="text-[9px] font-black uppercase tracking-widest rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300 px-2.5 py-1.5">
                                                            WhatsApp pendiente
                                                        </span>
                                                    @else
                                                        <span class="text-[9px] font-black uppercase tracking-widest rounded-xl bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300 px-2.5 py-1.5">
                                                            Sin WhatsApp
                                                        </span>
                                                    @endif
                                                </div>
                                                @if($assignment->observacion)
                                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                                                        {{ $assignment->observacion }}
                                                    </p>
                                                @endif
                                            </div>

                                            <div class="flex flex-col sm:flex-row gap-2 lg:justify-end">
                                                @if($whatsappUrl)
                                                    <a href="{{ $whatsappUrl }}"
                                                       target="_blank"
                                                       class="bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl px-3 py-2 text-[10px] font-black uppercase text-center">
                                                        WhatsApp
                                                    </a>
                                                @endif

                                                @if($emailUrl)
                                                    <a href="{{ $emailUrl }}"
                                                       target="_blank"
                                                       class="bg-blue-600 hover:bg-blue-500 text-white rounded-xl px-3 py-2 text-[10px] font-black uppercase text-center">
                                                        Correo
                                                    </a>
                                                @endif

                                                <form method="POST"
                                                      action="{{ route('admin.guias.destroy', $assignment) }}"
                                                      onsubmit="return confirm('¿Eliminar esta asignación de guía?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="w-full bg-rose-600 hover:bg-rose-500 text-white rounded-xl px-3 py-2 text-[10px] font-black uppercase">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-10 text-slate-400 text-sm bg-slate-50 dark:bg-[#0f172a] rounded-2xl border border-slate-100 dark:border-slate-700">
                                        Las asignaciones de guía aparecerán aquí.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </section>

                {{-- PANEL: INVITADOS ESPECIALES --}}
                <section id="panel-invitados" class="admin-panel {{ $activePanel === 'invitados' ? '' : 'hidden' }}">
                    <div class="grid grid-cols-1 xl:grid-cols-12 gap-5">
                        <div class="xl:col-span-5 admin-card p-5 rounded-[28px]">
                            <div class="mb-5 border-b border-slate-100 dark:border-slate-700 pb-3">
                                <p class="text-[10px] font-black uppercase tracking-widest text-amber-500 mb-1">
                                    Reserva institucional
                                </p>
                                <h3 class="text-sm font-black uppercase tracking-widest">
                                    Nuevo invitado especial
                                </h3>
                            </div>

                            <form method="POST" action="{{ route('admin.invitados.store') }}" class="space-y-4">
                                @csrf

                                <div>
                                    <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase block mb-2">
                                        Nombre completo
                                    </label>
                                    <input type="text"
                                           name="nombre"
                                           required
                                           placeholder="Ej. Embajador, rector, investigador invitado"
                                           class="w-full bg-slate-50 dark:bg-[#0f172a] border-slate-200 dark:border-slate-600 rounded-2xl text-sm p-3 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase block mb-2">
                                            Tipo
                                        </label>
                                        <select name="tipo_visita"
                                                class="w-full bg-slate-50 dark:bg-[#0f172a] border-slate-200 dark:border-slate-600 rounded-2xl text-sm p-3 dark:text-white">
                                            <option value="Autoridad">Autoridad</option>
                                            <option value="Visitante extranjero">Visitante extranjero</option>
                                            <option value="Delegación institucional">Delegación institucional</option>
                                            <option value="Invitado especial">Invitado especial</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase block mb-2">
                                            Cargo
                                        </label>
                                        <input type="text"
                                               name="cargo"
                                               placeholder="Ej. Rector, director, investigador"
                                               class="w-full bg-slate-50 dark:bg-[#0f172a] border-slate-200 dark:border-slate-600 rounded-2xl text-sm p-3 dark:text-white placeholder-slate-400">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase block mb-2">
                                            Institución
                                        </label>
                                        <input type="text"
                                               name="institucion"
                                               placeholder="Universidad, embajada, colegio, entidad"
                                               class="w-full bg-slate-50 dark:bg-[#0f172a] border-slate-200 dark:border-slate-600 rounded-2xl text-sm p-3 dark:text-white placeholder-slate-400">
                                    </div>

                                    <div>
                                        <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase block mb-2">
                                            País
                                        </label>
                                        <input type="text"
                                               name="pais"
                                               placeholder="Ej. Bolivia, Chile, Francia"
                                               class="w-full bg-slate-50 dark:bg-[#0f172a] border-slate-200 dark:border-slate-600 rounded-2xl text-sm p-3 dark:text-white placeholder-slate-400">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase block mb-2">
                                            Correo
                                        </label>
                                        <input type="email"
                                               name="correo"
                                               placeholder="contacto@gmail.com"
                                               class="w-full bg-slate-50 dark:bg-[#0f172a] border-slate-200 dark:border-slate-600 rounded-2xl text-sm p-3 dark:text-white placeholder-slate-400">
                                    </div>

                                    <div>
                                        <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase block mb-2">
                                            WhatsApp
                                        </label>
                                        <input type="text"
                                               name="telefono"
                                               placeholder="Ej. 70012345"
                                               class="w-full bg-slate-50 dark:bg-[#0f172a] border-slate-200 dark:border-slate-600 rounded-2xl text-sm p-3 dark:text-white placeholder-slate-400">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                    <div>
                                        <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase block mb-2">
                                            Fecha
                                        </label>
                                        <input type="date"
                                               name="fecha"
                                               required
                                               value="{{ now()->format('Y-m-d') }}"
                                               class="w-full bg-slate-50 dark:bg-[#0f172a] border-slate-200 dark:border-slate-600 rounded-2xl text-sm p-3 dark:text-white">
                                    </div>

                                    <div>
                                        <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase block mb-2">
                                            Inicio
                                        </label>
                                        <input type="time"
                                               name="hora_inicio"
                                               value="08:30"
                                               class="w-full bg-slate-50 dark:bg-[#0f172a] border-slate-200 dark:border-slate-600 rounded-2xl text-sm p-3 dark:text-white">
                                    </div>

                                    <div>
                                        <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase block mb-2">
                                            Fin
                                        </label>
                                        <input type="time"
                                               name="hora_fin"
                                               value="10:00"
                                               class="w-full bg-slate-50 dark:bg-[#0f172a] border-slate-200 dark:border-slate-600 rounded-2xl text-sm p-3 dark:text-white">
                                    </div>

                                    <div>
                                        <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase block mb-2">
                                            Personas
                                        </label>
                                        <input type="number"
                                               name="cantidad_personas"
                                               min="1"
                                               max="300"
                                               value="1"
                                               required
                                               class="w-full bg-slate-50 dark:bg-[#0f172a] border-slate-200 dark:border-slate-600 rounded-2xl text-sm p-3 dark:text-white">
                                    </div>
                                </div>

                                <div>
                                    <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase block mb-2">
                                        Motivo de la visita
                                    </label>
                                    <textarea name="motivo"
                                              rows="3"
                                              placeholder="Ej. Recorrido protocolar, visita académica, convenio institucional."
                                              class="w-full bg-slate-50 dark:bg-[#0f172a] border-slate-200 dark:border-slate-600 rounded-2xl text-sm p-3 dark:text-white placeholder-slate-400"></textarea>
                                </div>

                                <div>
                                    <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase block mb-2">
                                        Observación interna
                                    </label>
                                    <textarea name="observacion"
                                              rows="2"
                                              placeholder="Indicaciones para administración o secretaría."
                                              class="w-full bg-slate-50 dark:bg-[#0f172a] border-slate-200 dark:border-slate-600 rounded-2xl text-sm p-3 dark:text-white placeholder-slate-400"></textarea>
                                </div>

                                <button type="submit"
                                        class="w-full bg-amber-500 hover:bg-amber-400 text-slate-950 py-3 rounded-2xl font-black text-xs uppercase tracking-widest transition shadow-md shadow-amber-500/20">
                                    Crear reserva especial
                                </button>
                            </form>
                        </div>

                        <div class="xl:col-span-7 admin-card p-5 rounded-[28px]">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5 border-b border-slate-100 dark:border-slate-700 pb-3">
                                <div>
                                    <h3 class="text-sm font-black uppercase tracking-widest">
                                        Invitados registrados
                                    </h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-semibold">
                                        Reservas especiales creadas por administración, separadas del flujo normal de usuarios.
                                    </p>
                                </div>
                                <span class="self-start sm:self-center rounded-2xl bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300 px-4 py-2 text-[10px] font-black uppercase tracking-widest">
                                    {{ ($specialGuestReservations ?? collect())->count() }} registros
                                </span>
                            </div>

                            <div class="space-y-3 max-h-[720px] overflow-y-auto custom-scrollbar pr-1">
                                @forelse(($specialGuestReservations ?? collect()) as $guest)
                                    @php
                                        $guestDate = $guest->fecha?->format('d/m/Y') ?? 'Sin fecha';
                                        $guestHours = trim(($guest->hora_inicio ? substr($guest->hora_inicio, 0, 5) : '') . ($guest->hora_fin ? ' - ' . substr($guest->hora_fin, 0, 5) : ''));
                                    @endphp
                                    <div class="p-4 bg-slate-50 dark:bg-[#0f172a] rounded-2xl border border-slate-100 dark:border-slate-700/50">
                                        <div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-3">
                                            <div class="min-w-0">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <p class="text-sm font-black text-slate-800 dark:text-slate-200 truncate">
                                                        {{ $guest->nombre }}
                                                    </p>
                                                    <span class="text-[9px] font-black px-2.5 py-1.5 rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300 uppercase tracking-widest">
                                                        {{ $guest->tipo_visita }}
                                                    </span>
                                                    <span class="text-[9px] font-black px-2.5 py-1.5 rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300 uppercase tracking-widest">
                                                        {{ $guest->estado }}
                                                    </span>
                                                </div>

                                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">
                                                    {{ $guestDate }} · {{ $guestHours ?: 'Sin horario' }} · {{ $guest->cantidad_personas }} persona(s)
                                                </p>

                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-3 text-[11px] font-bold text-slate-500 dark:text-slate-400">
                                                    <span class="truncate rounded-xl bg-white dark:bg-slate-900/70 border border-slate-100 dark:border-slate-700 px-3 py-2">
                                                        Cargo: {{ $guest->cargo ?: 'No registrado' }}
                                                    </span>
                                                    <span class="truncate rounded-xl bg-white dark:bg-slate-900/70 border border-slate-100 dark:border-slate-700 px-3 py-2">
                                                        Institución: {{ $guest->institucion ?: 'No registrada' }}
                                                    </span>
                                                    <span class="truncate rounded-xl bg-white dark:bg-slate-900/70 border border-slate-100 dark:border-slate-700 px-3 py-2">
                                                        País: {{ $guest->pais ?: 'No registrado' }}
                                                    </span>
                                                    <span class="truncate rounded-xl bg-white dark:bg-slate-900/70 border border-slate-100 dark:border-slate-700 px-3 py-2">
                                                        Contacto: {{ $guest->correo ?: $guest->telefono ?: 'No registrado' }}
                                                    </span>
                                                </div>

                                                @if($guest->motivo || $guest->observacion)
                                                    <div class="mt-3 rounded-2xl bg-white dark:bg-slate-900/70 border border-slate-100 dark:border-slate-700 px-3 py-2 text-xs text-slate-600 dark:text-slate-300 space-y-1">
                                                        @if($guest->motivo)
                                                            <p><strong>Motivo:</strong> {{ $guest->motivo }}</p>
                                                        @endif
                                                        @if($guest->observacion)
                                                            <p><strong>Observación:</strong> {{ $guest->observacion }}</p>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>

                                            <form method="POST"
                                                  action="{{ route('admin.invitados.destroy', $guest) }}"
                                                  onsubmit="return confirm('¿Eliminar esta reserva especial?')"
                                                  class="shrink-0">
                                                @csrf
                                                @method('DELETE')
                                                <button class="w-full bg-rose-600 hover:bg-rose-500 text-white rounded-xl px-3 py-2 text-[10px] font-black uppercase">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-12 text-slate-400 text-sm bg-slate-50 dark:bg-[#0f172a] rounded-2xl border border-slate-100 dark:border-slate-700">
                                        Todavía no hay invitados especiales registrados.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </section>

                {{-- PANEL: GESTIÓN DE USUARIOS --}}
                <section id="panel-usuarios" class="admin-panel {{ $activePanel === 'usuarios' ? '' : 'hidden' }}">
                    <div class="grid grid-cols-1 xl:grid-cols-12 gap-5">
                        <div class="xl:col-span-5 admin-card p-5 rounded-[28px]">
                            <h3 class="text-sm font-black mb-5 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700 pb-3">
                                Nuevo acceso interno
                            </h3>

                            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                                @csrf

                                <div>
                                    <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase block mb-2">
                                        Nombre completo
                                    </label>
                                    <input type="text"
                                           name="name"
                                           required
                                           class="w-full bg-slate-50 dark:bg-[#0f172a] border-slate-200 dark:border-slate-600 rounded-2xl text-sm p-3 dark:text-white focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase block mb-2">
                                        Correo electrónico
                                    </label>
                                    <input type="email"
                                           name="email"
                                           required
                                           class="w-full bg-slate-50 dark:bg-[#0f172a] border-slate-200 dark:border-slate-600 rounded-2xl text-sm p-3 dark:text-white focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase block mb-2">
                                            Rol
                                        </label>
                                        <select name="role"
                                                class="w-full bg-slate-50 dark:bg-[#0f172a] border-slate-200 dark:border-slate-600 rounded-2xl text-sm p-3 dark:text-white">
                                            @forelse(($roles ?? collect()) as $role)
                                                <option value="{{ $role->nombre }}">{{ ucfirst($role->nombre) }}</option>
                                            @empty
                                                <option value="secretaria">Secretaría</option>
                                                <option value="admin">Admin</option>
                                            @endforelse
                                        </select>
                                    </div>

                                    <div>
                                        <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase block mb-2">
                                            Carnet identidad
                                        </label>
                                        <input type="text"
                                               name="carnet_identidad"
                                               class="w-full bg-slate-50 dark:bg-[#0f172a] border-slate-200 dark:border-slate-600 rounded-2xl text-sm p-3 dark:text-white placeholder-slate-400"
                                               placeholder="Ej. 1234567">
                                    </div>
                                </div>

                                <div>
                                    <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase block mb-2">
                                        Teléfono
                                    </label>
                                    <input type="text"
                                           name="telefono"
                                           class="w-full bg-slate-50 dark:bg-[#0f172a] border-slate-200 dark:border-slate-600 rounded-2xl text-sm p-3 dark:text-white placeholder-slate-400"
                                           placeholder="Ej. 70012345">
                                </div>

                                <button type="submit"
                                        class="w-full bg-blue-600 hover:bg-blue-500 text-white py-3 rounded-2xl font-black text-xs uppercase tracking-widest transition shadow-md shadow-blue-500/20">
                                    Registrar usuario
                                </button>
                            </form>
                        </div>

                        <div class="xl:col-span-7 admin-card p-5 rounded-[28px] flex flex-col">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5 border-b border-slate-100 dark:border-slate-700 pb-3">
                                <div>
                                    <h3 class="text-sm font-black uppercase tracking-widest">
                                        Cuentas internas
                                    </h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-semibold">
                                        Cuentas activas del sistema. Puedes bloquearlas, restaurarlas o eliminarlas.
                                    </p>
                                </div>

                                <form method="POST" action="{{ route('admin.roles.store') }}" class="flex gap-2">
                                    @csrf
                                    <input name="nombre" placeholder="Nuevo rol"
                                           class="w-32 bg-slate-50 dark:bg-[#0f172a] border-slate-200 dark:border-slate-600 rounded-xl text-xs p-2 dark:text-white">
                                    <button class="bg-slate-900 dark:bg-white dark:text-slate-900 text-white rounded-xl px-3 text-[10px] font-black uppercase">
                                        Crear
                                    </button>
                                </form>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-5">
                                <div class="rounded-2xl bg-slate-50 dark:bg-[#0f172a] border border-slate-100 dark:border-slate-700 p-4">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Internos</p>
                                    <p class="text-3xl font-black mt-2">{{ ($usuariosInternos ?? collect())->count() }}</p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 dark:bg-[#0f172a] border border-slate-100 dark:border-slate-700 p-4">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Roles</p>
                                    <p class="text-3xl font-black mt-2">{{ ($roles ?? collect())->count() }}</p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 dark:bg-[#0f172a] border border-slate-100 dark:border-slate-700 p-4">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Visitantes</p>
                                    <p class="text-3xl font-black mt-2">{{ $usuariosRegistrados ?? 0 }}</p>
                                </div>
                            </div>

                            <div class="space-y-3 flex-grow max-h-[430px] overflow-y-auto custom-scrollbar pr-1">
                                @forelse($usuariosGestion ?? [] as $usuarioGestion)
                                    <div class="p-4 bg-slate-50 dark:bg-[#0f172a] rounded-2xl border border-slate-100 dark:border-slate-700/50">
                                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3">
                                            <div class="min-w-0">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <p class="text-sm font-black text-slate-800 dark:text-slate-200 truncate">
                                                        {{ $usuarioGestion->name }} {{ $usuarioGestion->apellido }}
                                                    </p>
                                                    <span class="shrink-0 text-[9px] font-black px-2.5 py-1.5 rounded-xl {{ $usuarioGestion->role === 'admin' ? 'bg-rose-100 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400' : ($usuarioGestion->role === 'secretaria' ? 'bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400') }} uppercase tracking-widest">
                                                        {{ $usuarioGestion->role }}
                                                    </span>
                                                </div>
                                                <p class="text-[11px] text-slate-500 truncate mt-1">
                                                    {{ $usuarioGestion->email }} · CI: {{ $usuarioGestion->ci ?? 'N/A' }}
                                                </p>
                                            </div>

                                            @if($usuarioGestion->id !== auth()->id())
                                                <div class="flex flex-col sm:flex-row gap-2 lg:justify-end">
                                                    <form method="POST"
                                                          action="{{ route('admin.users.blacklist', $usuarioGestion) }}"
                                                          class="flex flex-col sm:flex-row gap-2">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input name="blacklist_reason"
                                                               placeholder="Motivo"
                                                               class="w-full sm:w-40 bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-600 rounded-xl text-xs p-2 dark:text-white">
                                                        <button class="bg-amber-500 hover:bg-amber-400 text-white rounded-xl px-3 py-2 text-[10px] font-black uppercase">
                                                            Lista negra
                                                        </button>
                                                    </form>

                                                    <form method="POST"
                                                          action="{{ route('admin.users.destroy', $usuarioGestion) }}"
                                                          onsubmit="return confirm('¿Eliminar esta cuenta definitivamente?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="w-full bg-rose-600 hover:bg-rose-500 text-white rounded-xl px-3 py-2 text-[10px] font-black uppercase">
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">
                                                    Sesión actual
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-10 text-slate-400 text-sm">
                                        Cuentas activas aparecerán aquí.
                                    </div>
                                @endforelse
                            </div>

                            <div class="mt-6 border-t border-slate-100 dark:border-slate-700 pt-5">
                                <h4 class="text-xs font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-3">
                                    Lista negra
                                </h4>

                                <div class="space-y-3 max-h-[260px] overflow-y-auto custom-scrollbar pr-1">
                                    @forelse($usuariosListaNegra ?? [] as $bloqueado)
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 bg-rose-50 dark:bg-rose-500/10 rounded-2xl border border-rose-100 dark:border-rose-500/20">
                                            <div class="min-w-0">
                                                <p class="text-sm font-black text-rose-700 dark:text-rose-300 truncate">
                                                    {{ $bloqueado->name }} {{ $bloqueado->apellido }}
                                                </p>
                                                <p class="text-[11px] text-rose-500 dark:text-rose-300/80 truncate">
                                                    {{ $bloqueado->blacklist_reason ?? 'Sin motivo registrado.' }}
                                                </p>
                                            </div>

                                            <form method="POST" action="{{ route('admin.users.restore', $bloqueado) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button class="bg-white dark:bg-slate-900 text-rose-600 dark:text-rose-300 border border-rose-200 dark:border-rose-500/30 rounded-xl px-3 py-2 text-[10px] font-black uppercase">
                                                    Restaurar
                                                </button>
                                            </form>
                                        </div>
                                    @empty
                                        <div class="text-center py-6 text-slate-400 text-sm bg-slate-50 dark:bg-[#0f172a] rounded-2xl border border-slate-100 dark:border-slate-700">
                                            No hay usuarios en lista negra.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- PANEL: BIENVENIDO --}}
                <section id="panel-bienvenido" class="admin-panel {{ $activePanel === 'bienvenido' ? '' : 'hidden' }}">
                    @include('admin.contenido.bienvenido.edit')
                </section>

                {{-- PANEL: ACERCA --}}
                <section id="panel-acerca" class="admin-panel {{ $activePanel === 'acerca' ? '' : 'hidden' }}">
                    @include('admin.contenido.acerca.edit')
                </section>

                {{-- PANEL: EVENTOS --}}
                <section id="panel-eventos" class="admin-panel {{ $activePanel === 'eventos' ? '' : 'hidden' }}">
                    @include('admin.contenido.eventos.edit')
                </section>

                {{-- PANEL: INVESTIGACIÓN --}}
                <section id="panel-investigacion" class="admin-panel {{ $activePanel === 'investigacion' ? '' : 'hidden' }}">
                    @include('admin.contenido.investigacion.edit')
                </section>

                {{-- PANEL: GALERÍA --}}
                <section id="panel-galeria" class="admin-panel {{ $activePanel === 'galeria' ? '' : 'hidden' }}">
                    @include('admin.contenido.galeria.edit')
                </section>

                {{-- PANEL: BACKUPS Y LOGS --}}
                <section id="panel-mantenimiento" class="admin-panel {{ $activePanel === 'mantenimiento' ? '' : 'hidden' }}">
                    @php
                        $maintenance = $maintenanceInfo ?? [];
                    @endphp

                    <div class="grid grid-cols-1 xl:grid-cols-12 gap-5">
                        <div class="xl:col-span-5 space-y-5">
                            <div class="admin-card rounded-[28px] p-5">
                                <div class="flex items-start justify-between gap-4 mb-5">
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-[0.28em] text-blue-600 dark:text-blue-400">
                                            Respaldo institucional
                                        </p>
                                        <h2 class="text-2xl font-black mt-2">
                                            Backup de base de datos
                                        </h2>
                                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 font-semibold">
                                            Genera un archivo SQL con estructura y datos actuales para restauración o evidencia técnica.
                                        </p>
                                    </div>

                                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl">
                                        💾
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">
                                    <div class="rounded-2xl bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 p-4">
                                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Base de datos</p>
                                        <p class="text-sm font-black mt-2 break-all">{{ $maintenance['database'] ?? 'No detectada' }}</p>
                                    </div>

                                    <div class="rounded-2xl bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 p-4">
                                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Motor</p>
                                        <p class="text-sm font-black mt-2 uppercase">{{ $maintenance['driver'] ?? 'N/A' }}</p>
                                    </div>
                                </div>

                                <a href="{{ route('admin.backups.database') }}"
                                   class="w-full inline-flex items-center justify-center bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl px-5 py-3 text-xs font-black uppercase tracking-widest shadow-lg shadow-emerald-600/20">
                                    Descargar backup SQL
                                </a>
                            </div>

                            <div class="admin-card rounded-[28px] p-5">
                                <div class="flex items-start justify-between gap-4 mb-5">
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-[0.28em] text-amber-600 dark:text-amber-400">
                                            Diagnóstico
                                        </p>
                                        <h2 class="text-2xl font-black mt-2">
                                            Estado de logs
                                        </h2>
                                    </div>

                                    <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl">
                                        🧾
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div class="rounded-2xl bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 p-4">
                                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Tamaño</p>
                                        <p class="text-2xl font-black mt-2">{{ $maintenance['log_size'] ?? '0 B' }}</p>
                                    </div>

                                    <div class="rounded-2xl bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 p-4">
                                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Última modificación</p>
                                        <p class="text-sm font-black mt-2">{{ $maintenance['log_modified'] ?? 'Sin registros' }}</p>
                                    </div>
                                </div>

                                <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <a href="{{ route('admin.logs.download') }}"
                                       class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-500 text-white rounded-2xl px-5 py-3 text-xs font-black uppercase tracking-widest">
                                        Descargar log
                                    </a>

                                    <form method="POST"
                                          action="{{ route('admin.logs.clear') }}"
                                          onsubmit="return confirm('¿Limpiar el log actual del sistema?')">
                                        @csrf
                                        <button class="w-full bg-rose-600 hover:bg-rose-500 text-white rounded-2xl px-5 py-3 text-xs font-black uppercase tracking-widest">
                                            Limpiar log
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="xl:col-span-7 admin-card rounded-[28px] p-5">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-5">
                                <div>
                                    <h2 class="text-xl font-black uppercase tracking-widest">
                                        Últimos eventos del sistema
                                    </h2>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 font-semibold">
                                        Vista rápida del archivo <span class="font-black">storage/logs/laravel.log</span>.
                                    </p>
                                </div>

                                <span class="inline-flex w-fit rounded-2xl px-3 py-2 text-[10px] font-black uppercase tracking-widest {{ ($maintenance['storage_writable'] ?? false) ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400' }}">
                                    {{ ($maintenance['storage_writable'] ?? false) ? 'Storage OK' : 'Storage bloqueado' }}
                                </span>
                            </div>

                            <div class="rounded-3xl bg-slate-950 text-slate-100 border border-slate-800 p-4 overflow-hidden">
                                <div class="flex items-center justify-between gap-3 border-b border-slate-800 pb-3 mb-3">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">
                                        Log reciente
                                    </p>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-500">
                                        {{ $maintenance['generated_at'] ?? now()->format('d/m/Y H:i') }}
                                    </p>
                                </div>

                                <div class="max-h-[520px] overflow-y-auto custom-scrollbar space-y-2 font-mono text-[11px] leading-5">
                                    @forelse(($maintenance['log_lines'] ?? []) as $line)
                                        <div class="rounded-xl bg-white/5 border border-white/5 px-3 py-2 break-words">
                                            {{ $line }}
                                        </div>
                                    @empty
                                        <div class="rounded-xl bg-white/5 border border-white/5 px-3 py-8 text-center text-slate-400 font-sans font-bold">
                                            No hay eventos registrados todavía.
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <div class="mt-5 rounded-2xl border border-blue-100 dark:border-blue-500/20 bg-blue-50 dark:bg-blue-500/10 p-4">
                                <p class="text-[10px] font-black uppercase tracking-widest text-blue-600 dark:text-blue-400">
                                    Recomendación para producción
                                </p>
                                <p class="text-sm text-slate-600 dark:text-slate-300 mt-2 font-semibold">
                                    Genera un backup antes de cambios grandes, conserva el archivo SQL fuera del servidor y revisa logs después de pruebas OWASP o JMeter.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- PANEL: REPORTES --}}
                <section id="panel-reportes" class="admin-panel {{ $activePanel === 'reportes' ? '' : 'hidden' }}">
                    <div class="grid grid-cols-1 2xl:grid-cols-2 gap-6">
                        <div class="admin-card rounded-[28px] p-5">
                            <div class="mb-5">
                                <h2 class="text-xl font-black uppercase tracking-widest">
                                    Reporte de reservas
                                </h2>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                                    Selecciona rango semanal, mensual, anual o personalizado y descarga el PDF.
                                </p>
                            </div>

                            <form method="GET"
                                  action="{{ route('admin.dashboard') }}#panel-reportes"
                                  class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-slate-50/80 dark:bg-[#07111f]/80 p-4 mb-5">
                                <input type="hidden" name="panel" value="reportes">
                                <input type="hidden" name="tipo_usuarios" value="{{ $reportTipoUsuarios ?? 'usuario' }}">

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                    <label class="block">
                                        <span class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2">Periodo</span>
                                        <select name="preset" class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-white dark:bg-[#07111f] text-sm dark:text-white">
                                            <option value="semanal" @selected(($reportPreset ?? 'mensual') === 'semanal')>Semanal</option>
                                            <option value="mensual" @selected(($reportPreset ?? 'mensual') === 'mensual')>Mensual</option>
                                            <option value="anual" @selected(($reportPreset ?? 'mensual') === 'anual')>Anual</option>
                                            <option value="personalizado" @selected(($reportPreset ?? 'mensual') === 'personalizado')>Personalizado</option>
                                        </select>
                                    </label>

                                    <label class="block">
                                        <span class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2">Desde</span>
                                        <input type="date" name="fecha_inicio" value="{{ ($reportFechaInicio ?? now())->format('Y-m-d') }}"
                                               class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-white dark:bg-[#07111f] text-sm dark:text-white">
                                    </label>

                                    <label class="block">
                                        <span class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2">Hasta</span>
                                        <input type="date" name="fecha_fin" value="{{ ($reportFechaFin ?? now())->format('Y-m-d') }}"
                                               class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-white dark:bg-[#07111f] text-sm dark:text-white">
                                    </label>

                                    <div class="flex items-end">
                                        <button class="w-full bg-blue-600 hover:bg-blue-500 text-white rounded-2xl px-5 py-3 text-xs font-black uppercase tracking-widest">
                                            Aplicar filtro
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-5">
                                @foreach ([
                                    ['label' => 'Reservas', 'value' => $reportReservaStats['total'] ?? 0],
                                    ['label' => 'Usuarios', 'value' => $reportReservaStats['usuarios'] ?? 0],
                                    ['label' => 'Visitantes', 'value' => $reportReservaStats['visitantes'] ?? 0],
                                    ['label' => 'Confirmadas', 'value' => $reportReservaStats['confirmadas'] ?? 0],
                                    ['label' => 'Pendientes', 'value' => $reportReservaStats['pendientes'] ?? 0],
                                ] as $stat)
                                    <div class="rounded-2xl bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 p-3 text-center">
                                        <p class="text-2xl font-black">{{ $stat['value'] }}</p>
                                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">{{ $stat['label'] }}</p>
                                    </div>
                                @endforeach
                            </div>

                            <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-700 max-h-[360px]">
                                <table class="w-full text-left text-sm">
                                    <thead class="sticky top-0 bg-slate-100 dark:bg-[#07111f] text-[10px] uppercase tracking-widest text-slate-500">
                                        <tr>
                                            <th class="p-3">Fecha</th>
                                            <th class="p-3">Hora</th>
                                            <th class="p-3">Usuario</th>
                                            <th class="p-3 text-center">Personas</th>
                                            <th class="p-3">Pago</th>
                                            <th class="p-3">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                        @forelse (($reportReservas ?? collect()) as $reserva)
                                            <tr>
                                                <td class="p-3 font-bold">{{ \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y') }}</td>
                                                <td class="p-3">{{ $reserva->horario?->hora_inicio ?? 'N/A' }}</td>
                                                <td class="p-3">
                                                    <div class="font-bold">{{ $reserva->nombre }}</div>
                                                    <div class="text-xs text-slate-500">{{ $reserva->correo }}</div>
                                                </td>
                                                <td class="p-3 text-center font-black">{{ $reserva->cantidad_personas }}</td>
                                                <td class="p-3">
                                                    {{ $reserva->pago ? 'Bs. ' . number_format((float) $reserva->pago->monto, 2) : 'Sin pago' }}
                                                </td>
                                                <td class="p-3">{{ $reserva->estado }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="p-6 text-center text-slate-400 font-bold">
                                                    Sin reservas en este rango.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <a href="{{ route('admin.reportes.reservas.pdf', ['fecha_inicio' => ($reportFechaInicio ?? now())->format('Y-m-d'), 'fecha_fin' => ($reportFechaFin ?? now())->format('Y-m-d'), 'preset' => $reportPreset ?? 'mensual']) }}"
                               class="mt-5 inline-flex bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl px-5 py-3 text-xs font-black uppercase tracking-widest shadow-lg shadow-emerald-600/20">
                                Descargar PDF de reservas
                            </a>
                        </div>

                        <div class="admin-card rounded-[28px] p-5">
                            <div class="mb-5">
                                <h2 class="text-xl font-black uppercase tracking-widest">
                                    Reporte de usuarios
                                </h2>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                                    Por defecto muestra usuarios visitantes. Puedes incluir internos o todos.
                                </p>
                            </div>

                            <form method="GET"
                                  action="{{ route('admin.dashboard') }}#panel-reportes"
                                  class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-slate-50/80 dark:bg-[#07111f]/80 p-4 mb-5">
                                <input type="hidden" name="panel" value="reportes">
                                <input type="hidden" name="fecha_inicio" value="{{ ($reportFechaInicio ?? now())->format('Y-m-d') }}">
                                <input type="hidden" name="fecha_fin" value="{{ ($reportFechaFin ?? now())->format('Y-m-d') }}">
                                <input type="hidden" name="preset" value="{{ $reportPreset ?? 'mensual' }}">

                                <div class="grid grid-cols-1 md:grid-cols-[1fr_auto] gap-3">
                                    <label class="block">
                                        <span class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2">Tipo de usuarios</span>
                                        <select name="tipo_usuarios" class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-white dark:bg-[#07111f] text-sm dark:text-white">
                                            <option value="usuario" @selected(($reportTipoUsuarios ?? 'usuario') === 'usuario')>Solo usuarios visitantes</option>
                                            <option value="internos" @selected(($reportTipoUsuarios ?? 'usuario') === 'internos')>Administradores y secretarias</option>
                                            <option value="todos" @selected(($reportTipoUsuarios ?? 'usuario') === 'todos')>Todos los usuarios</option>
                                        </select>
                                    </label>

                                    <div class="flex items-end">
                                        <button class="w-full md:w-auto bg-blue-600 hover:bg-blue-500 text-white rounded-2xl px-6 py-3 text-xs font-black uppercase tracking-widest">
                                            Aplicar filtro
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <div class="grid grid-cols-3 gap-3 mb-5">
                                <div class="rounded-2xl bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 p-3 text-center">
                                    <p class="text-2xl font-black">{{ $reportUsuarioStats['total'] ?? 0 }}</p>
                                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">Total</p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 p-3 text-center">
                                    <p class="text-2xl font-black">{{ $reportUsuarioStats['visitantes'] ?? 0 }}</p>
                                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">Visitantes</p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 p-3 text-center">
                                    <p class="text-2xl font-black">{{ $reportUsuarioStats['internos'] ?? 0 }}</p>
                                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">Internos</p>
                                </div>
                            </div>

                            <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-700 max-h-[360px]">
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
                                        @forelse (($reportUsuarios ?? collect()) as $usuario)
                                            <tr>
                                                <td class="p-3 font-bold">{{ $usuario->name }} {{ $usuario->apellido }}</td>
                                                <td class="p-3">{{ $usuario->ci }}</td>
                                                <td class="p-3">{{ $usuario->telefono }}</td>
                                                <td class="p-3">{{ $usuario->email }}</td>
                                                <td class="p-3 uppercase text-xs font-black">{{ $usuario->role }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="p-6 text-center text-slate-400 font-bold">
                                                    Sin usuarios para este filtro.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <a href="{{ route('admin.reportes.usuarios.pdf', ['tipo' => $reportTipoUsuarios ?? 'usuario']) }}"
                               class="mt-5 inline-flex bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl px-5 py-3 text-xs font-black uppercase tracking-widest shadow-lg shadow-emerald-600/20">
                                Descargar PDF de usuarios
                            </a>
                        </div>
                    </div>
                </section>

            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');

        function toggleSidebar() {
            sidebar.classList.toggle('-translate-x-full');
            sidebarOverlay.classList.toggle('hidden');
        }

        const wrapper = document.getElementById('dashboard-wrapper');
        const htmlDoc = document.documentElement;
        const themeDay = document.getElementById('theme-day');
        const themeNight = document.getElementById('theme-night');

        const panelTitle = document.getElementById('panel-title');
        const panelSubtitle = document.getElementById('panel-subtitle');
        const dashboardReportActions = document.getElementById('dashboard-report-actions');
        const chartLabels = @json($chartLabels ?? []);
        const chartReservas = @json($chartReservas ?? []);
        const chartVisitantes = @json($chartVisitantes ?? []);

        function setActivePanel(panelName, title = null, subtitle = null) {
            document.querySelectorAll('.admin-panel').forEach(panel => {
                panel.classList.add('hidden');
            });

            const targetPanel = document.getElementById(`panel-${panelName}`);
            if (targetPanel) {
                targetPanel.classList.remove('hidden');
            }

            document.querySelectorAll('[data-panel-button]').forEach(button => {
                button.classList.remove('active');
            });

            const activeButton = document.querySelector(`[data-panel-button="${panelName}"]`);
            if (activeButton) {
                activeButton.classList.add('active');

                title = title || activeButton.dataset.panelTitle;
                subtitle = subtitle || activeButton.dataset.panelSubtitle;
            }

            if (panelTitle && title) {
                panelTitle.innerText = title;
            }

            if (panelSubtitle && subtitle) {
                panelSubtitle.innerText = subtitle;
            }

            if (dashboardReportActions) {
                dashboardReportActions.classList.toggle('hidden', panelName !== 'dashboard');
                dashboardReportActions.classList.toggle('flex', panelName === 'dashboard');
            }

            if (window.innerWidth < 1024 && sidebar && !sidebar.classList.contains('-translate-x-full')) {
                toggleSidebar();
            }

            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        document.querySelectorAll('[data-panel-button]').forEach(button => {
            button.addEventListener('click', () => {
                setActivePanel(
                    button.dataset.panelButton,
                    button.dataset.panelTitle,
                    button.dataset.panelSubtitle
                );
            });
        });

        const slideAforo = document.getElementById('slideAforo');
        const valAforo = document.getElementById('valAforo');
        const aforoCircle = document.getElementById('aforoCircle');

        function updateAforo(value) {
            if (!valAforo || !aforoCircle) return;

            const radius = 58;
            const circumference = 2 * Math.PI * radius;
            const percent = Number(value) > 0 ? 100 : 0;
            const offset = circumference - (percent / 100) * circumference;

            valAforo.innerText = value;
            aforoCircle.style.strokeDasharray = circumference;
            aforoCircle.style.strokeDashoffset = offset;
        }

        if (slideAforo) {
            updateAforo(slideAforo.value);

            slideAforo.addEventListener('input', (e) => {
                updateAforo(e.target.value);
            });
        }

        function updateThemeButton(isDark) {
            if (!themeDay || !themeNight) return;

            if (isDark) {
                themeDay.classList.remove('bg-white', 'text-blue-700');
                themeDay.classList.add('text-blue-100');

                themeNight.classList.add('bg-white', 'text-blue-700');
                themeNight.classList.remove('text-blue-100');
            } else {
                themeDay.classList.add('bg-white', 'text-blue-700');
                themeDay.classList.remove('text-blue-100');

                themeNight.classList.remove('bg-white', 'text-blue-700');
                themeNight.classList.add('text-blue-100');
            }
        }

        function applyTheme(isDark) {
            if (isDark) {
                wrapper.classList.add('dark');
                htmlDoc.classList.add('dark');
                document.body.classList.add('dark');
                document.body.style.colorScheme = 'dark';
                localStorage.setItem('theme', 'dark');
            } else {
                wrapper.classList.remove('dark');
                htmlDoc.classList.remove('dark');
                document.body.classList.remove('dark');
                document.body.style.colorScheme = 'light';
                localStorage.setItem('theme', 'light');
            }

            updateThemeButton(isDark);
            renderChart();
        }

        const savedTheme = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

        applyTheme(savedTheme === 'dark' || (!savedTheme && prefersDark));

        if (themeDay) {
            themeDay.addEventListener('click', function () {
                applyTheme(false);
            });
        }

        if (themeNight) {
            themeNight.addEventListener('click', function () {
                applyTheme(true);
            });
        }

        let chartInstance = null;

        function renderChart() {
            const canvas = document.getElementById('mainChart');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            const isDark = wrapper.classList.contains('dark');

            if (chartInstance) {
                chartInstance.destroy();
            }

            chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartLabels.length ? chartLabels : ['Lun', 'Mar', 'Mié', 'Jue', 'Vie'],
                    datasets: [
                        {
                            label: 'Reservas',
                            data: chartReservas.length ? chartReservas : [0, 0, 0, 0, 0],
                            backgroundColor: '#3b82f6',
                            borderRadius: 10,
                            barThickness: 'flex',
                            maxBarThickness: 28
                        },
                        {
                            label: 'Visitantes confirmados',
                            data: chartVisitantes.length ? chartVisitantes : [0, 0, 0, 0, 0],
                            backgroundColor: '#10b981',
                            borderRadius: 10,
                            barThickness: 'flex',
                            maxBarThickness: 28
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                color: isDark ? '#cbd5e1' : '#334155',
                                boxWidth: 10,
                                font: {
                                    size: 10,
                                    weight: 'bold'
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            grid: {
                                color: isDark ? '#334155' : '#e2e8f0',
                                drawBorder: false
                            },
                            ticks: {
                                color: isDark ? '#94a3b8' : '#64748b',
                                font: {
                                    size: 10
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: isDark ? '#94a3b8' : '#64748b',
                                font: {
                                    size: 11,
                                    weight: 'bold'
                                }
                            }
                        }
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            const urlPanel = urlParams.get('panel');
            localStorage.removeItem('admin-active-panel');

            if (urlPanel && urlPanel !== 'reportes' && window.history?.replaceState) {
                urlParams.delete('panel');
                const cleanQuery = urlParams.toString();
                const cleanUrl = window.location.pathname + (cleanQuery ? `?${cleanQuery}` : '') + window.location.hash;
                window.history.replaceState({}, '', cleanUrl);
            }

            const savedPanel = urlPanel === 'reportes' ? 'reportes' : 'dashboard';
            const button = document.querySelector(`[data-panel-button="${savedPanel}"]`);

            if (button) {
                setActivePanel(
                    savedPanel,
                    button.dataset.panelTitle,
                    button.dataset.panelSubtitle
                );
            } else {
                setActivePanel('dashboard');
            }

            const whatsappUrl = @json(session('whatsapp_url'));
            if (whatsappUrl) {
                window.open(whatsappUrl, '_blank', 'noopener,noreferrer');
            }
        });
    </script>
</x-app-layout>
