<x-app-layout>
    <div id="dashboard-wrapper" class="transition-colors duration-500 bg-[#f8fafc] dark:bg-[#0f172a] min-h-screen text-slate-900 dark:text-slate-100 font-sans p-4 md:p-6">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/></svg>
                </div>
                <div>
                    <h1 class="text-xl font-black tracking-tight leading-none">SISOBS <span class="text-blue-600 dark:text-blue-400 font-medium ml-1">Admin</span></h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">Panel de Gestión Central</p>
                </div>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <button class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg text-xs font-bold transition-all shadow-lg shadow-emerald-600/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    GENERAR REPORTE MENSUAL
                </button>

                <button id="theme-toggle" class="relative inline-flex items-center h-8 w-16 rounded-full bg-slate-200 dark:bg-slate-700 transition-colors duration-300 focus:outline-none shadow-inner border border-slate-300 dark:border-slate-600">
                    <span class="absolute left-1.5 flex h-5 w-5 items-center justify-center text-amber-500 transition-opacity duration-300 dark:opacity-0">☀️</span>
                    <span class="absolute right-1.5 flex h-5 w-5 items-center justify-center text-blue-300 opacity-0 transition-opacity duration-300 dark:opacity-100">🌙</span>
                    <span id="theme-toggle-circle" class="inline-block h-6 w-6 transform rounded-full bg-white dark:bg-slate-900 shadow-md transition-transform duration-300 translate-x-1 dark:translate-x-9"></span>
                </button>

                <span class="text-xs font-bold text-slate-500 dark:text-slate-300 bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700 px-3 py-2 rounded-lg uppercase">
                    {{ auth()->user()->name ?? 'Admin' }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            @php
                $stats = [
                    ['label' => 'Total Usuarios', 'val' => $totalUsuarios ?? 0, 'color' => 'text-slate-900 dark:text-white', 'icon' => '👥'],
                    ['label' => 'Reservas Hoy', 'val' => '15', 'color' => 'text-blue-600 dark:text-blue-400', 'icon' => '📅'],
                    ['label' => 'Cupos Libres', 'val' => '12/30', 'color' => 'text-amber-500 dark:text-amber-400', 'icon' => '⭐'],
                    ['label' => 'Por Confirmar', 'val' => '03', 'color' => 'text-rose-500 dark:text-rose-400', 'icon' => '⚠️']
                ];
            @endphp
            @foreach($stats as $s)
            <div class="bg-white dark:bg-[#1e293b] p-4 rounded-2xl border border-slate-200 dark:border-slate-700/50 shadow-sm transition-colors duration-500 flex flex-col justify-between">
                <div class="flex justify-between items-start mb-2">
                    <p class="text-slate-500 dark:text-slate-400 text-[10px] sm:text-xs font-bold uppercase tracking-wide">{{ $s['label'] }}</p>
                    <span class="text-sm">{{ $s['icon'] }}</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black {{ $s['color'] }}">{{ $s['val'] }}</h2>
            </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 mb-6">
            
            <div class="lg:col-span-4 bg-white dark:bg-[#1e293b] p-5 rounded-2xl border border-slate-200 dark:border-slate-700/50 shadow-sm transition-colors duration-500">
                <h3 class="text-sm font-black mb-4 uppercase tracking-wider border-b border-slate-100 dark:border-slate-700 pb-2">Nuevo Acceso</h3>
                <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase block mb-1">Nombre Completo</label>
                        <input type="text" name="name" class="w-full bg-slate-50 dark:bg-[#0f172a] border-slate-200 dark:border-slate-600 rounded-xl text-xs p-2.5 dark:text-white focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase block mb-1">Correo Electrónico</label>
                        <input type="email" name="email" class="w-full bg-slate-50 dark:bg-[#0f172a] border-slate-200 dark:border-slate-600 rounded-xl text-xs p-2.5 dark:text-white focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase block mb-1">Rol</label>
                            <select name="role" class="w-full bg-slate-50 dark:bg-[#0f172a] border-slate-200 dark:border-slate-600 rounded-xl text-xs p-2.5 dark:text-white">
                                <option value="secretaria">Secretaría</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase block mb-1">Carnet Identidad</label>
                            <input type="text" name="carnet_identidad" class="w-full bg-slate-50 dark:bg-[#0f172a] border-slate-200 dark:border-slate-600 rounded-xl text-xs p-2.5 dark:text-white placeholder-slate-400" placeholder="Ej. 1234567">
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white py-2.5 rounded-xl font-bold text-xs uppercase tracking-widest transition shadow-md shadow-blue-500/20 mt-2">Registrar Usuario</button>
                </form>
            </div>

            <div class="lg:col-span-4 bg-white dark:bg-[#1e293b] p-5 rounded-2xl border border-slate-200 dark:border-slate-700/50 shadow-sm transition-colors duration-500 flex flex-col">
                <h3 class="text-sm font-black mb-4 uppercase tracking-wider border-b border-slate-100 dark:border-slate-700 pb-2">Cuentas Internas</h3>
                <div class="space-y-2 flex-grow max-h-[220px] overflow-y-auto custom-scrollbar pr-1">
                    @forelse($usuariosInternos ?? [] as $interno)
                    <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-[#0f172a] rounded-xl border border-slate-100 dark:border-slate-700/50">
                        <div>
                            <p class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ $interno->name }}</p>
                            <p class="text-[10px] text-slate-500">{{ $interno->email }}</p>
                        </div>
                        <span class="text-[9px] font-black px-2 py-1 rounded-md {{ $interno->role === 'admin' ? 'bg-rose-100 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400' : 'bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400' }} uppercase tracking-wider">
                            {{ $interno->role }}
                        </span>
                    </div>
                    @empty
                    <div class="text-center py-6 text-slate-400 text-xs">Cuentas registradas aparecerán aquí.</div>
                    @endforelse
                </div>
            </div>

            <div class="lg:col-span-4 bg-white dark:bg-[#1e293b] p-5 rounded-2xl border border-slate-200 dark:border-slate-700/50 shadow-sm transition-colors duration-500 flex flex-col justify-center items-center text-center">
                <h3 class="text-sm font-black mb-6 uppercase tracking-wider w-full text-left">Control de Aforo</h3>
                <div class="relative inline-flex items-center justify-center mb-8">
                    <svg class="w-32 h-32 transform -rotate-90">
                        <circle cx="64" cy="64" r="54" stroke="currentColor" stroke-width="12" fill="transparent" class="text-slate-100 dark:text-slate-700" />
                        <circle cx="64" cy="64" r="54" stroke="currentColor" stroke-width="12" fill="transparent" stroke-dasharray="339.29" stroke-dashoffset="200" class="text-amber-500 drop-shadow-[0_0_8px_rgba(245,158,11,0.4)]" />
                    </svg>
                    <div class="absolute flex flex-col items-center">
                        <span class="text-3xl font-black text-slate-900 dark:text-white" id="valAforo">40</span>
                        <span class="text-[9px] font-bold text-slate-400 uppercase">Personas</span>
                    </div>
                </div>
                <input type="range" min="0" max="100" value="40" id="slideAforo" class="w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-lg appearance-none cursor-pointer accent-amber-500">
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            <div class="lg:col-span-7 bg-white dark:bg-[#1e293b] p-5 rounded-2xl border border-slate-200 dark:border-slate-700/50 shadow-sm transition-colors duration-500">
                <h3 class="text-sm font-black mb-4 uppercase tracking-wider">Actividad Semanal</h3>
                <div class="h-[200px] w-full"><canvas id="mainChart"></canvas></div>
            </div>

            <div class="lg:col-span-5 bg-white dark:bg-[#1e293b] p-5 rounded-2xl border border-slate-200 dark:border-slate-700/50 shadow-sm transition-colors duration-500">
                <div class="flex justify-between items-end mb-4">
                    <h3 class="text-sm font-black uppercase tracking-wider">Disponibilidad</h3>
                    <div class="flex gap-3 text-[9px] font-bold uppercase">
                        <span class="flex items-center text-slate-500 dark:text-slate-400"><span class="w-2.5 h-2.5 bg-green-400 dark:bg-green-500 rounded-full mr-1.5"></span> Libre</span>
                        <span class="flex items-center text-slate-500 dark:text-slate-400"><span class="w-2.5 h-2.5 bg-rose-400 dark:bg-rose-500 rounded-full mr-1.5"></span> Lleno</span>
                    </div>
                </div>
                <div class="grid grid-cols-7 gap-1.5">
                    @foreach(['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'] as $day)
                        <div class="text-[10px] font-black text-slate-400 py-1 text-center">{{ $day }}</div>
                    @endforeach
                    @for($i = 1; $i <= 31; $i++)
                        <div class="py-2 rounded-lg text-xs font-bold text-center transition-colors 
                            {{ in_array($i, [15, 20]) ? 'bg-rose-100 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400 border border-rose-200 dark:border-rose-500/30' : 'bg-slate-50 text-slate-600 dark:bg-[#0f172a] dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-blue-50 dark:hover:bg-blue-900/30 cursor-pointer' }}">
                            {{ $i }}
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; }
        input[type="range"]::-webkit-slider-thumb { -webkit-appearance: none; width: 16px; height: 16px; background: #f59e0b; border-radius: 50%; cursor: pointer; }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Aforo
        const slide = document.getElementById('slideAforo');
        const val = document.getElementById('valAforo');
        slide.addEventListener('input', (e) => { val.innerText = e.target.value; });

        // SISTEMA DE TEMA CLARO/OSCURO MEJORADO
        const themeToggleBtn = document.getElementById('theme-toggle');
        const wrapper = document.getElementById('dashboard-wrapper');
        const htmlDoc = document.documentElement; // También intentamos cambiarlo a nivel HTML

        // Función para aplicar tema
        function applyTheme(isDark) {
            if (isDark) {
                wrapper.classList.add('dark');
                htmlDoc.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            } else {
                wrapper.classList.remove('dark');
                htmlDoc.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            }
            renderChart();
        }

        // Cargar preferencia al iniciar
        const savedTheme = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        applyTheme(savedTheme === 'dark' || (!savedTheme && prefersDark));

        // Evento de click en el botón
        themeToggleBtn.addEventListener('click', function() {
            const isCurrentlyDark = wrapper.classList.contains('dark');
            applyTheme(!isCurrentlyDark);
        });

        // Gráfica
        let chartInstance = null;
        function renderChart() {
            const ctx = document.getElementById('mainChart').getContext('2d');
            const isDark = wrapper.classList.contains('dark');
            
            if(chartInstance) chartInstance.destroy();

            chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
                    datasets: [{ 
                        data: [12, 19, 13, 25, 32, 40, 27], 
                        backgroundColor: '#3b82f6', 
                        borderRadius: 6,
                        barThickness: 'flex',
                        maxBarThickness: 24
                    }]
                },
                options: { 
                    maintainAspectRatio: false, 
                    plugins: { legend: { display: false } },
                    scales: { 
                        y: { 
                            grid: { color: isDark ? '#334155' : '#e2e8f0', drawBorder: false }, 
                            ticks: { color: isDark ? '#94a3b8' : '#64748b', font: { size: 10 } } 
                        }, 
                        x: { 
                            grid: { display: false }, 
                            ticks: { color: isDark ? '#94a3b8' : '#64748b', font: { size: 11, weight: 'bold' } } 
                        } 
                    }
                }
            });
        }
    </script>
</x-app-layout>