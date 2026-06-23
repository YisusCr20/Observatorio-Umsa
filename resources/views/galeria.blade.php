@php
    $galleryImages = $galleryImages ?? collect();

    $fallbackImages = [
        [
            'title' => 'Observación astronómica',
            'description' => 'Registro visual de actividades y observaciones realizadas en el observatorio.',
            'image' => 'https://images.unsplash.com/photo-1446776811953-b23d57bd21aa?q=80&w=1200',
        ],
        [
            'title' => 'Equipamiento científico',
            'description' => 'Telescopios, sensores y recursos usados en actividades académicas.',
            'image' => 'https://images.unsplash.com/photo-1543722530-d2c3201371e7?q=80&w=1200',
        ],
        [
            'title' => 'Cielo de La Paz',
            'description' => 'Imágenes relacionadas con el entorno astronómico local.',
            'image' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=1200',
        ],
    ];

    $navItems = [
        'bienvenido' => ['label' => 'Inicio', 'route' => route('bienvenido')],
        'acerca' => ['label' => 'Acerca de', 'route' => route('acerca')],
        'investigacion' => ['label' => 'Investigación', 'route' => route('investigacion')],
        'eventos' => ['label' => 'Eventos', 'route' => route('eventos')],
        'contacto' => ['label' => 'Contacto', 'route' => route('contacto')],
    ];
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Galería | Observatorio Max Schreier</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Plus+Jakarta+Sans:wght@400;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class', theme: { extend: { fontFamily: { cinzel: ['Cinzel'], sans: ['Plus Jakarta Sans'] } } } }
    </script>
</head>
<body class="bg-slate-50 text-slate-950 dark:bg-[#02040a] dark:text-white font-sans min-h-screen">
    <header class="fixed top-0 left-0 right-0 z-50 px-4 py-4">
        <nav class="max-w-7xl mx-auto rounded-2xl px-5 py-3 bg-white/80 dark:bg-slate-950/70 border border-slate-200 dark:border-white/10 backdrop-blur-xl flex items-center justify-between shadow-xl">
            <a href="{{ route('bienvenido') }}">
                <span class="block font-cinzel font-black text-lg uppercase tracking-widest">Max Schreier</span>
                <span class="block text-[9px] text-cyan-500 font-black uppercase tracking-[0.35em]">Galería UMSA</span>
            </a>

            <div class="hidden lg:flex items-center gap-7 text-[10px] font-black uppercase tracking-[0.22em]">
                @foreach ($navItems as $name => $item)
                    @if (! request()->routeIs($name))
                        <a href="{{ $item['route'] }}" class="text-slate-500 hover:text-cyan-500 dark:text-white/60 dark:hover:text-cyan-300 transition">{{ $item['label'] }}</a>
                    @endif
                @endforeach
            </div>

            <button id="theme-toggle" class="w-12 h-7 rounded-full bg-slate-200 dark:bg-white/10 border border-slate-300 dark:border-white/10 flex items-center px-1">
                <span id="theme-dot" class="w-5 h-5 rounded-full bg-cyan-500 transition-transform"></span>
            </button>
        </nav>
    </header>

    <main class="relative px-6 pt-36 pb-20">
        <section class="max-w-7xl mx-auto mb-12">
            <p class="text-[10px] font-black uppercase tracking-[0.55em] text-cyan-500 mb-5">Archivo visual</p>
            <h1 class="font-cinzel text-5xl md:text-7xl font-black">Galería de imágenes</h1>
            <p class="mt-5 max-w-2xl text-slate-600 dark:text-white/70 leading-8">
                Fotografías institucionales, registros de observación y material visual administrado desde el panel.
            </p>
        </section>

        <section class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse ($galleryImages as $image)
                <article class="group rounded-[2rem] overflow-hidden bg-white dark:bg-white/[.03] border border-slate-200 dark:border-white/10 shadow-xl">
                    <div class="aspect-[4/3] overflow-hidden">
                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $image->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
                    </div>
                    <div class="p-6">
                        <h2 class="font-black text-xl">{{ $image->title ?? 'Imagen del observatorio' }}</h2>
                        <p class="text-sm text-slate-500 dark:text-white/60 mt-3 leading-6">{{ $image->description }}</p>
                    </div>
                </article>
            @empty
                @foreach ($fallbackImages as $item)
                    <article class="group rounded-[2rem] overflow-hidden bg-white dark:bg-white/[.03] border border-slate-200 dark:border-white/10 shadow-xl">
                        <div class="aspect-[4/3] overflow-hidden">
                            <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
                        </div>
                        <div class="p-6">
                            <h2 class="font-black text-xl">{{ $item['title'] }}</h2>
                            <p class="text-sm text-slate-500 dark:text-white/60 mt-3 leading-6">{{ $item['description'] }}</p>
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
        document.getElementById('theme-toggle')?.addEventListener('click', () => {
            applyPublicTheme(html.classList.contains('dark') ? 'light' : 'dark');
        });
    </script>
</body>
</html>
