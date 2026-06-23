@php
    $aboutSections = $aboutSections ?? collect();

    $defaults = [
        'hero' => [
            'title' => 'El Observatorio',
            'subtitle' => 'Exploración e investigación científica UMSA',
            'body' => 'Un espacio universitario dedicado a observar, estudiar y compartir el cielo desde La Paz.',
            'image' => 'https://images.unsplash.com/photo-1446776811953-b23d57bd21aa?q=80&w=1600',
        ],
        'historia' => [
            'title' => 'Antecedentes institucionales',
            'subtitle' => 'Historia y legado',
            'body' => 'Aquí se detallará la trayectoria del Observatorio Astronómico Max Schreier y su vínculo con la Universidad Mayor de San Andrés.',
            'image' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?q=80&w=1600',
        ],
        'investigacion' => [
            'title' => 'Líneas de investigación',
            'subtitle' => 'Ciencia, monitoreo y formación',
            'body' => 'Investigación aplicada en astrofísica, monitoreo solar, estudios atmosféricos y actividades académicas con docentes y estudiantes.',
            'image' => 'https://images.unsplash.com/photo-1543722530-d2c3201371e7?q=80&w=1600',
        ],
        'ubicacion' => [
            'title' => 'Ubicación',
            'subtitle' => 'Cota Cota, La Paz',
            'body' => 'El observatorio se encuentra en la zona de Cota Cota, Calle 27, como parte del entorno académico de la UMSA.',
            'image' => 'https://images.unsplash.com/photo-1454789548928-9efd52dc4031?q=80&w=1600',
        ],
    ];

    $section = function ($key) use ($aboutSections, $defaults) {
        $item = $aboutSections->get($key);
        $fallback = $defaults[$key];

        return (object) [
            'title' => $item?->title ?: $fallback['title'],
            'subtitle' => $item?->subtitle ?: $fallback['subtitle'],
            'body' => $item?->body ?: $fallback['body'],
            'image' => $item?->image_path ? asset('storage/' . $item->image_path) : $fallback['image'],
        ];
    };

    $hero = $section('hero');
    $history = $section('historia');
    $research = $section('investigacion');
    $location = $section('ubicacion');

    $navItems = [
        'bienvenido' => ['label' => 'Inicio', 'route' => route('bienvenido')],
        'investigacion' => ['label' => 'Investigación', 'route' => route('investigacion')],
        'eventos' => ['label' => 'Eventos', 'route' => route('eventos')],
        'galeria' => ['label' => 'Galería', 'route' => route('galeria')],
        'contacto' => ['label' => 'Contacto', 'route' => route('contacto')],
    ];
@endphp

<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acerca del Observatorio | Max Schreier</title>

    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Plus+Jakarta+Sans:wght@400;600;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        cinzel: ['Cinzel', 'serif'],
                        sans: ['Plus Jakarta Sans', 'sans-serif']
                    }
                }
            }
        }
    </script>

    <style>
        body { transition: background-color .35s ease, color .35s ease; }
        .glass { background: rgba(255,255,255,.76); border: 1px solid rgba(15,23,42,.10); backdrop-filter: blur(22px); }
        .dark .glass { background: rgba(2,6,23,.70); border-color: rgba(255,255,255,.10); }
    </style>
</head>

<body class="bg-slate-50 text-slate-950 dark:bg-[#02040a] dark:text-white font-sans overflow-x-hidden">
    <div class="fixed inset-0 pointer-events-none">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_10%,rgba(14,165,233,.18),transparent_30%),radial-gradient(circle_at_85%_20%,rgba(245,158,11,.14),transparent_28%)] dark:bg-[radial-gradient(circle_at_20%_10%,rgba(34,211,238,.16),transparent_30%),radial-gradient(circle_at_85%_20%,rgba(59,130,246,.18),transparent_30%)]"></div>
    </div>

    <header class="fixed top-0 left-0 right-0 z-50 px-4 py-4">
        <nav class="glass max-w-7xl mx-auto rounded-2xl px-4 md:px-6 py-3 flex items-center justify-between gap-4 shadow-xl">
            <a href="{{ route('bienvenido') }}" class="min-w-0">
                <span class="block font-cinzel font-black text-sm md:text-lg tracking-widest uppercase">Max Schreier</span>
                <span class="block text-[8px] md:text-[9px] font-black uppercase tracking-[0.35em] text-cyan-500">Observatorio UMSA</span>
            </a>

            <div class="hidden lg:flex items-center gap-7 text-[10px] font-black uppercase tracking-[0.22em]">
                @foreach ($navItems as $name => $item)
                    @if (! request()->routeIs($name))
                        <a href="{{ $item['route'] }}" class="text-slate-500 hover:text-cyan-500 dark:text-white/60 dark:hover:text-cyan-300 transition">
                            {{ $item['label'] }}
                        </a>
                    @endif
                @endforeach
            </div>

            <button type="button" id="theme-toggle"
                    class="w-12 h-7 rounded-full bg-slate-200 dark:bg-white/10 border border-slate-300 dark:border-white/10 flex items-center px-1">
                <span id="theme-dot" class="w-5 h-5 rounded-full bg-cyan-500 transition-transform"></span>
            </button>
        </nav>
    </header>

    <main class="relative z-10">
        <section class="min-h-[92vh] flex items-center px-6 pt-28 pb-16">
            <div class="max-w-7xl mx-auto grid lg:grid-cols-[1.05fr_.95fr] gap-10 items-center">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.55em] text-cyan-500 mb-5">
                        {{ $hero->subtitle }}
                    </p>
                    <h1 class="font-cinzel text-5xl md:text-7xl xl:text-8xl font-black leading-none tracking-tight">
                        {{ $hero->title }}
                    </h1>
                    <p class="mt-7 text-base md:text-lg leading-8 text-slate-600 dark:text-white/70 max-w-2xl">
                        {{ $hero->body }}
                    </p>
                </div>

                <div class="relative">
                    <div class="aspect-[4/5] md:aspect-[5/4] rounded-[2rem] overflow-hidden shadow-2xl border border-white/20">
                        <img src="{{ $hero->image }}" alt="{{ $hero->title }}" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>
        </section>

        <section class="px-6 py-12">
            <div class="max-w-7xl mx-auto space-y-10">
                <article class="grid lg:grid-cols-2 gap-8 items-center">
                    <img src="{{ $history->image }}" alt="{{ $history->title }}" class="w-full aspect-[16/10] object-cover rounded-[2rem] shadow-xl">
                    <div class="glass rounded-[2rem] p-7 md:p-10">
                        <p class="text-[10px] font-black uppercase tracking-[0.35em] text-cyan-500 mb-4">{{ $history->subtitle }}</p>
                        <h2 class="font-cinzel text-3xl md:text-5xl font-black mb-5">{{ $history->title }}</h2>
                        <p class="text-slate-600 dark:text-white/70 leading-8">{{ $history->body }}</p>
                    </div>
                </article>

                <article class="grid lg:grid-cols-2 gap-8 items-center">
                    <div class="glass rounded-[2rem] p-7 md:p-10 lg:order-1 order-2">
                        <p class="text-[10px] font-black uppercase tracking-[0.35em] text-cyan-500 mb-4">{{ $research->subtitle }}</p>
                        <h2 class="font-cinzel text-3xl md:text-5xl font-black mb-5">{{ $research->title }}</h2>
                        <p class="text-slate-600 dark:text-white/70 leading-8">{{ $research->body }}</p>
                    </div>
                    <img src="{{ $research->image }}" alt="{{ $research->title }}" class="lg:order-2 order-1 w-full aspect-[16/10] object-cover rounded-[2rem] shadow-xl">
                </article>
            </div>
        </section>

        <section class="px-6 py-16">
            <div class="max-w-7xl mx-auto grid lg:grid-cols-[.8fr_1.2fr] gap-8 items-stretch">
                <div class="glass rounded-[2rem] p-7 md:p-10">
                    <p class="text-[10px] font-black uppercase tracking-[0.35em] text-cyan-500 mb-4">{{ $location->subtitle }}</p>
                    <h2 class="font-cinzel text-3xl md:text-5xl font-black mb-5">{{ $location->title }}</h2>
                    <p class="text-slate-600 dark:text-white/70 leading-8">{{ $location->body }}</p>
                </div>

                <div class="rounded-[2rem] overflow-hidden shadow-xl border border-slate-200 dark:border-white/10 min-h-[360px]">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3824.978502391696!2d-68.0673322239634!3d-16.527181084224795!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x915f212217c1815d%3A0x6d9f82607f230f3f!2sObservatorio%20Astron%C3%B3mico%20Max%20Schreier!5e0!3m2!1ses!2sbo!4v1714675000000!5m2!1ses!2sbo"
                            class="w-full h-full border-0 min-h-[360px]" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </section>
    </main>

    <footer class="relative z-10 py-10 text-center text-[9px] font-black uppercase tracking-[0.45em] text-slate-400">
        Observatorio Max Schreier - UMSA
    </footer>

    <script>
        const html = document.documentElement;
        const dot = document.getElementById('theme-dot');

        function applyPublicTheme(theme) {
            html.classList.toggle('dark', theme === 'dark');
            localStorage.setItem('public-theme', theme);
            if (dot) dot.style.transform = theme === 'dark' ? 'translateX(20px)' : 'translateX(0)';
        }

        applyPublicTheme(localStorage.getItem('public-theme') || 'dark');

        document.getElementById('theme-toggle')?.addEventListener('click', () => {
            applyPublicTheme(html.classList.contains('dark') ? 'light' : 'dark');
        });
    </script>
</body>
</html>
