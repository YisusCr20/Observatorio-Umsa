@php
    $eventItems = $eventItems ?? collect();

    $fallbackEvents = [
        [
            'title' => 'Alineación de los gigantes',
            'category' => 'Próximo',
            'date' => '2026-03-20',
            'body' => 'Una jornada de observación dedicada al seguimiento de conjunciones planetarias y explicación guiada para visitantes.',
            'image' => 'https://images.unsplash.com/photo-1543722530-d2c3201371e7?q=80&w=1600',
            'button_label' => 'Agendar visita',
            'button_url' => route('reservas.create'),
        ],
        [
            'title' => 'Observación de la Tierra',
            'category' => 'Divulgación',
            'date' => '2026-04-12',
            'body' => 'Actividad educativa con material visual y explicación de sensores, órbitas y observación desde el entorno universitario.',
            'image' => 'https://images.unsplash.com/photo-1614730321146-b6fa6a46bcb4?q=80&w=1600',
            'button_label' => null,
            'button_url' => null,
        ],
        [
            'title' => 'Paso del cometa C-2025',
            'category' => 'Fenómeno',
            'date' => '2026-05-05',
            'body' => 'Seguimiento de cola, núcleo y trayectoria de un cometa visible desde el hemisferio sur.',
            'image' => 'https://images.unsplash.com/photo-1446941611757-91d2c3bd3d45?q=80&w=1600',
            'button_label' => null,
            'button_url' => null,
        ],
    ];

    $navItems = [
        'bienvenido' => ['Inicio', route('bienvenido')],
        'acerca' => ['Acerca de', route('acerca')],
        'investigacion' => ['Investigación', route('investigacion')],
        'galeria' => ['Galería', route('galeria')],
        'contacto' => ['Contacto', route('contacto')],
    ];
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Eventos | Observatorio Max Schreier</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Plus+Jakarta+Sans:wght@400;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class', theme: { extend: { fontFamily: { cinzel: ['Cinzel'], sans: ['Plus Jakarta Sans'] } } } }
    </script>
</head>
<body class="bg-slate-50 text-slate-950 dark:bg-[#02040a] dark:text-white font-sans min-h-screen overflow-x-hidden">
    <div class="fixed inset-0 pointer-events-none bg-[radial-gradient(circle_at_20%_10%,rgba(14,165,233,.16),transparent_30%),radial-gradient(circle_at_90%_25%,rgba(244,63,94,.12),transparent_28%)] dark:bg-[radial-gradient(circle_at_20%_10%,rgba(34,211,238,.12),transparent_30%),radial-gradient(circle_at_90%_25%,rgba(59,130,246,.18),transparent_28%)]"></div>

    <header class="fixed top-0 left-0 right-0 z-50 px-4 py-4">
        <nav class="max-w-7xl mx-auto rounded-2xl px-5 py-3 bg-white/80 dark:bg-slate-950/70 border border-slate-200 dark:border-white/10 backdrop-blur-xl flex items-center justify-between shadow-xl">
            <a href="{{ route('bienvenido') }}">
                <span class="block font-cinzel font-black text-lg uppercase tracking-widest">Max Schreier</span>
                <span class="block text-[9px] text-cyan-500 font-black uppercase tracking-[0.35em]">Agenda astronómica</span>
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
            <p class="text-[10px] font-black uppercase tracking-[0.55em] text-cyan-500 mb-5">Actividades y fenómenos</p>
            <h1 class="font-cinzel text-5xl md:text-7xl font-black">Eventos astronómicos</h1>
            <p class="mt-5 max-w-2xl text-slate-600 dark:text-white/70 leading-8">
                Publicaciones actualizadas desde administración para mostrar actividades, fechas importantes y convocatorias.
            </p>
        </section>

        <section class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-6">
            @forelse ($eventItems as $item)
                <article class="{{ $loop->first ? 'lg:col-span-2 lg:grid-cols-[1.15fr_.85fr]' : '' }} grid grid-cols-1 rounded-[2rem] overflow-hidden bg-white dark:bg-white/[.04] border border-slate-200 dark:border-white/10 shadow-xl">
                    <div class="aspect-[16/10] {{ $loop->first ? 'lg:aspect-auto' : '' }} overflow-hidden">
                        <img src="{{ $item->image_path ? asset('storage/' . $item->image_path) : 'https://images.unsplash.com/photo-1543722530-d2c3201371e7?q=80&w=1600' }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                    </div>
                    <div class="p-7 md:p-10 flex flex-col justify-center">
                        <div class="flex flex-wrap items-center gap-3 mb-5">
                            @if ($item->category)
                                <span class="bg-cyan-500 text-slate-950 text-[9px] font-black px-3 py-1 rounded-full uppercase tracking-widest">{{ $item->category }}</span>
                            @endif
                            @if ($item->event_date)
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-white/50">{{ $item->event_date->format('d/m/Y') }}</span>
                            @endif
                        </div>
                        <h2 class="font-cinzel text-3xl md:text-5xl font-black leading-tight">{{ $item->title }}</h2>
                        <p class="mt-5 text-slate-600 dark:text-white/70 leading-8">{{ $item->body }}</p>
                        @if ($item->button_label && $item->button_url)
                            <a href="{{ $item->button_url }}" class="mt-7 inline-flex w-fit bg-slate-950 text-white dark:bg-white dark:text-slate-950 rounded-2xl px-6 py-3 text-xs font-black uppercase tracking-widest">
                                {{ $item->button_label }}
                            </a>
                        @endif
                    </div>
                </article>
            @empty
                @foreach ($fallbackEvents as $item)
                    <article class="{{ $loop->first ? 'lg:col-span-2 lg:grid-cols-[1.15fr_.85fr]' : '' }} grid grid-cols-1 rounded-[2rem] overflow-hidden bg-white dark:bg-white/[.04] border border-slate-200 dark:border-white/10 shadow-xl">
                        <div class="aspect-[16/10] overflow-hidden">
                            <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover">
                        </div>
                        <div class="p-7 md:p-10 flex flex-col justify-center">
                            <div class="flex flex-wrap items-center gap-3 mb-5">
                                <span class="bg-cyan-500 text-slate-950 text-[9px] font-black px-3 py-1 rounded-full uppercase tracking-widest">{{ $item['category'] }}</span>
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-white/50">{{ \Carbon\Carbon::parse($item['date'])->format('d/m/Y') }}</span>
                            </div>
                            <h2 class="font-cinzel text-3xl md:text-5xl font-black leading-tight">{{ $item['title'] }}</h2>
                            <p class="mt-5 text-slate-600 dark:text-white/70 leading-8">{{ $item['body'] }}</p>
                            @if ($item['button_label'] && $item['button_url'])
                                <a href="{{ $item['button_url'] }}" class="mt-7 inline-flex w-fit bg-slate-950 text-white dark:bg-white dark:text-slate-950 rounded-2xl px-6 py-3 text-xs font-black uppercase tracking-widest">{{ $item['button_label'] }}</a>
                            @endif
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
