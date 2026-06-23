@php
    $researchItems = $researchItems ?? collect();

    $fallbackResearch = [
        [
            'title' => 'Seguimiento de asteroides',
            'category' => 'Alerta NEO',
            'date' => '2026-01-15',
            'body' => 'Registro de trayectorias y análisis de objetos cercanos a la Tierra observados desde el entorno académico.',
            'image' => 'https://images.unsplash.com/photo-1614728894747-a83421e2b9c9?q=80&w=1600',
        ],
        [
            'title' => 'Exploración marciana',
            'category' => 'Misión Marte',
            'date' => '2026-02-03',
            'body' => 'Seguimiento de datos atmosféricos y material de divulgación sobre misiones actuales al planeta rojo.',
            'image' => 'https://images.unsplash.com/photo-1614728468164-aa3ef0371c6a?q=80&w=1600',
        ],
        [
            'title' => 'Eclipses y fenómenos',
            'category' => 'Histórico',
            'date' => '2026-04-18',
            'body' => 'Documentación fotográfica y técnica de eclipses, ocultaciones y eventos visibles desde Bolivia.',
            'image' => 'https://images.unsplash.com/photo-1543722530-d2c3201371e7?q=80&w=1600',
        ],
    ];

    $navItems = [
        'bienvenido' => ['Inicio', route('bienvenido')],
        'acerca' => ['Acerca de', route('acerca')],
        'eventos' => ['Eventos', route('eventos')],
        'galeria' => ['Galería', route('galeria')],
        'contacto' => ['Contacto', route('contacto')],
    ];
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Investigación | Observatorio Max Schreier</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Plus+Jakarta+Sans:wght@400;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class', theme: { extend: { fontFamily: { cinzel: ['Cinzel'], sans: ['Plus Jakarta Sans'] } } } }
    </script>
</head>
<body class="bg-slate-50 text-slate-950 dark:bg-[#02040a] dark:text-white font-sans min-h-screen overflow-x-hidden">
    <div class="fixed inset-0 pointer-events-none bg-[radial-gradient(circle_at_18%_15%,rgba(14,165,233,.16),transparent_28%),radial-gradient(circle_at_82%_12%,rgba(99,102,241,.12),transparent_30%)] dark:bg-[radial-gradient(circle_at_18%_15%,rgba(34,211,238,.12),transparent_28%),radial-gradient(circle_at_82%_12%,rgba(99,102,241,.18),transparent_30%)]"></div>

    <header class="fixed top-0 left-0 right-0 z-50 px-4 py-4">
        <nav class="max-w-7xl mx-auto rounded-2xl px-5 py-3 bg-white/80 dark:bg-slate-950/70 border border-slate-200 dark:border-white/10 backdrop-blur-xl flex items-center justify-between shadow-xl">
            <a href="{{ route('bienvenido') }}">
                <span class="block font-cinzel font-black text-lg uppercase tracking-widest">Max Schreier</span>
                <span class="block text-[9px] text-cyan-500 font-black uppercase tracking-[0.35em]">Centro de datos</span>
            </a>

            <div class="hidden lg:flex items-center gap-7 text-[10px] font-black uppercase tracking-[0.22em]">
                @foreach ($navItems as $name => [$label, $url])
                    @if (! request()->routeIs($name))
                        <a href="{{ $url }}" class="text-slate-500 hover:text-cyan-500 dark:text-white/60 dark:hover:text-cyan-300 transition">{{ $label }}</a>
                    @endif
                @endforeach
            </div>

            <button id="theme-toggle" class="w-12 h-7 rounded-full bg-slate-200 dark:bg-white/10 border border-slate-300 dark:border-white/10 flex items-center px-1">
                <span id="theme-dot" class="w-5 h-5 rounded-full bg-cyan-500 transition-transform"></span>
            </button>
        </nav>
    </header>

    <main class="relative z-10 px-6 pt-36 pb-20">
        <section class="max-w-7xl mx-auto mb-12">
            <p class="text-[10px] font-black uppercase tracking-[0.55em] text-cyan-500 mb-5">Investigaciones realizadas</p>
            <h1 class="font-cinzel text-5xl md:text-7xl font-black">Centro de investigación</h1>
            <p class="mt-5 max-w-2xl text-slate-600 dark:text-white/70 leading-8">
                Proyectos, informes y registros científicos publicados desde el panel administrativo.
            </p>
        </section>

        <section class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse ($researchItems as $item)
                <article class="rounded-[2rem] overflow-hidden bg-white dark:bg-white/[.04] border border-slate-200 dark:border-white/10 shadow-xl flex flex-col">
                    <div class="aspect-[4/3] overflow-hidden">
                        <img src="{{ $item->image_path ? asset('storage/' . $item->image_path) : 'https://images.unsplash.com/photo-1614728894747-a83421e2b9c9?q=80&w=1600' }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                    </div>
                    <div class="p-7 flex-1 flex flex-col">
                        <div class="flex flex-wrap items-center gap-3 mb-5">
                            @if ($item->category)
                                <span class="bg-cyan-500 text-slate-950 text-[9px] font-black px-3 py-1 rounded-full uppercase tracking-widest">{{ $item->category }}</span>
                            @endif
                            @if ($item->event_date)
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-white/50">{{ $item->event_date->format('d/m/Y') }}</span>
                            @endif
                        </div>
                        <h2 class="font-cinzel text-2xl md:text-3xl font-black leading-tight">{{ $item->title }}</h2>
                        <p class="mt-4 text-sm text-slate-600 dark:text-white/70 leading-7 flex-1">{{ $item->body }}</p>
                        @if ($item->button_label && $item->button_url)
                            <a href="{{ $item->button_url }}" class="mt-6 inline-flex w-fit bg-slate-950 text-white dark:bg-white dark:text-slate-950 rounded-2xl px-5 py-3 text-[10px] font-black uppercase tracking-widest">{{ $item->button_label }}</a>
                        @endif
                    </div>
                </article>
            @empty
                @foreach ($fallbackResearch as $item)
                    <article class="rounded-[2rem] overflow-hidden bg-white dark:bg-white/[.04] border border-slate-200 dark:border-white/10 shadow-xl flex flex-col">
                        <div class="aspect-[4/3] overflow-hidden">
                            <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover">
                        </div>
                        <div class="p-7 flex-1 flex flex-col">
                            <div class="flex flex-wrap items-center gap-3 mb-5">
                                <span class="bg-cyan-500 text-slate-950 text-[9px] font-black px-3 py-1 rounded-full uppercase tracking-widest">{{ $item['category'] }}</span>
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-white/50">{{ \Carbon\Carbon::parse($item['date'])->format('d/m/Y') }}</span>
                            </div>
                            <h2 class="font-cinzel text-2xl md:text-3xl font-black leading-tight">{{ $item['title'] }}</h2>
                            <p class="mt-4 text-sm text-slate-600 dark:text-white/70 leading-7">{{ $item['body'] }}</p>
                        </div>
                    </article>
                @endforeach
            @endforelse
        </section>
    </main>

    <script>
        const html = document.documentElement;
        const dot = document.getElementById('theme-dot');
        function applyPublicTheme(theme) {
            html.classList.toggle('dark', theme === 'dark');
            localStorage.setItem('public-theme', theme);
            if (dot) dot.style.transform = theme === 'dark' ? 'translateX(20px)' : 'translateX(0)';
        }
        applyPublicTheme(localStorage.getItem('public-theme') || 'dark');
        document.getElementById('theme-toggle')?.addEventListener('click', () => applyPublicTheme(html.classList.contains('dark') ? 'light' : 'dark'));
    </script>
</body>
</html>
