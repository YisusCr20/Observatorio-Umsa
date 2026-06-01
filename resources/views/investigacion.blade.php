<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Investigación | Observatorio Max Schreier</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Syncopate:wght@400;700&family=Plus+Jakarta+Sans:wght@200;400;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        syncopate: ['Syncopate', 'sans-serif'],
                        jakarta: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #010409; color: white; scroll-behavior: smooth; }
        .glass-item { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.08); }
        .bg-stars { background-image: radial-gradient(circle at center, #0a192f 0%, #000 100%); }
        
        /* Efecto de Scanline de radar */
        .radar-line { height: 2px; background: linear-gradient(90deg, transparent, #22d3ee, transparent); width: 100%; position: absolute; top: 0; animation: scan 4s linear infinite; }
        @keyframes scan { from { top: 0; } to { top: 100%; } }

        .reveal { opacity: 0; transform: translateY(40px); transition: all 0.8s ease-out; }
        .reveal.active { opacity: 1; transform: translateY(0); }
    </style>
</head>
<body class="font-jakarta bg-stars overflow-x-hidden">

    <nav class="fixed top-0 w-full z-[100] px-12 py-6 flex justify-between items-center bg-black/60 backdrop-blur-xl border-b border-white/5">
    <div class="flex items-center gap-3">
        <div class="w-8 h-[2px] bg-cyan-400"></div>
        <a href="{{ route('bienvenido') }}" class="font-syncopate text-[10px] tracking-[0.4em] font-bold text-white hover:text-cyan-400 transition">
            MAX SCHREIER
        </a>
    </div>

    <div class="flex gap-10 text-[9px] font-black uppercase tracking-[0.3em]">
        <a href="{{ route('bienvenido') }}" class="{{ request()->routeIs('bienvenido') ? 'text-cyan-400 border-b border-cyan-400' : 'text-white/60 hover:text-white' }} transition pb-1">
            Inicio
        </a>
        <a href="{{ route('acerca') }}" class="{{ request()->routeIs('acerca') ? 'text-cyan-400 border-b border-cyan-400' : 'text-white/60 hover:text-white' }} transition pb-1">
            Acerca de
        </a>
        <a href="{{ route('investigacion') }}" class="{{ request()->routeIs('investigacion') ? 'text-cyan-400 border-b border-cyan-400' : 'text-white/60 hover:text-white' }} transition pb-1">
            Investigación
        </a>
        <a href="{{ route('eventos') }}" class="{{ request()->routeIs('eventos') ? 'text-cyan-400 border-b border-cyan-400' : 'text-white/60 hover:text-white' }} transition pb-1">
            Eventos
        </a>
    </div>
</nav>

    <header class="relative h-[60vh] flex items-center justify-center overflow-hidden">
        <div class="radar-line opacity-20"></div>
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=2072')] bg-cover bg-center opacity-30"></div>
        <div class="text-center z-10 space-y-4 px-6 reveal active">
            <h1 class="font-syncopate text-5xl md:text-8xl font-black tracking-tighter uppercase leading-none">
                CENTRO DE <br> <span class="text-cyan-400">DATOS</span>
            </h1>
            <p class="text-slate-400 font-light tracking-[0.4em] text-xs uppercase">Monitoreo interplanetario y eventos astronómicos</p>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-20">
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <article class="glass-item rounded-[30px] p-8 space-y-6 hover:border-cyan-400/50 transition group reveal">
                <div class="h-48 overflow-hidden rounded-2xl relative">
                    <img src="https://images.unsplash.com/photo-1614728894747-a83421e2b9c9?q=80&w=2000" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    <span class="absolute top-4 left-4 bg-cyan-400 text-black text-[9px] font-black px-3 py-1 rounded-full uppercase">Alerta NEO</span>
                </div>
                <h3 class="text-2xl font-bold font-syncopate tracking-tight">Seguimiento de Asteroides</h3>
                <p class="text-slate-400 text-sm leading-relaxed">
                    Monitoreo constante de Objetos Cercanos a la Tierra (NEO). Registro de trayectorias y análisis espectral de cuerpos menores que cruzan nuestra órbita.
                </p>
                <div class="flex justify-between items-center pt-4 border-t border-white/5">
                    <span class="text-[10px] text-slate-500">UMSA RESEARCH 2026</span>
                    <a href="#" class="text-cyan-400 text-[10px] font-bold uppercase tracking-widest">Ver Informe</a>
                </div>
            </article>

            <article class="glass-item rounded-[30px] p-8 space-y-6 hover:border-orange-500/50 transition group reveal" style="transition-delay: 200ms">
                <div class="h-48 overflow-hidden rounded-2xl relative">
                    <img src="https://images.unsplash.com/photo-1614728468164-aa3ef0371c6a?q=80&w=2000" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    <span class="absolute top-4 left-4 bg-orange-500 text-white text-[9px] font-black px-3 py-1 rounded-full uppercase">Misión Marte</span>
                </div>
                <h3 class="text-2xl font-bold font-syncopate tracking-tight">Exploración Marciana</h3>
                <p class="text-slate-400 text-sm leading-relaxed">
                    Seguimiento de las últimas misiones en el Planeta Rojo. Análisis de datos atmosféricos y colaboración en red con observatorios internacionales.
                </p>
                <div class="flex justify-between items-center pt-4 border-t border-white/5">
                    <span class="text-[10px] text-slate-500">POST-DOC DATA</span>
                    <a href="#" class="text-orange-500 text-[10px] font-bold uppercase tracking-widest">Seguir Misión</a>
                </div>
            </article>

            <article class="glass-item rounded-[30px] p-8 space-y-6 hover:border-indigo-500/50 transition group reveal" style="transition-delay: 400ms">
                <div class="h-48 overflow-hidden rounded-2xl relative">
                    <img src="https://images.unsplash.com/photo-1543722530-d2c3201371e7?q=80&w=2074" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    <span class="absolute top-4 left-4 bg-indigo-500 text-white text-[9px] font-black px-3 py-1 rounded-full uppercase">Eventos</span>
                </div>
                <h3 class="text-2xl font-bold font-syncopate tracking-tight">Eclipses & Fenómenos</h3>
                <p class="text-slate-400 text-sm leading-relaxed">
                    Documentación de eclipses lunares y solares capturados desde el observatorio. Archivo fotográfico y datos de ocultación astronómica.
                </p>
                <div class="flex justify-between items-center pt-4 border-t border-white/5">
                    <span class="text-[10px] text-slate-500">HISTÓRICO</span>
                    <a href="#" class="text-indigo-400 text-[10px] font-bold uppercase tracking-widest">Galería</a>
                </div>
            </article>

        </div>

        <section class="mt-40 space-y-16">
            <div class="text-center reveal">
                <h2 class="text-4xl font-black font-syncopate">CRÓNICAS DEL <span class="text-cyan-400">AVANCE HUMANO</span></h2>
                <div class="h-1 w-20 bg-cyan-500 mx-auto mt-6"></div>
            </div>

            <div class="glass-item rounded-[40px] p-12 reveal">
                <div class="flex flex-col lg:flex-row gap-12 items-center">
                    <div class="lg:w-1/2">
                        <img src="https://images.unsplash.com/photo-1446776811953-b23d57bd21aa?q=80&w=2040" class="rounded-[30px] w-full shadow-2xl">
                    </div>
                    <div class="lg:w-1/2 space-y-8">
                        <h4 class="text-3xl font-bold uppercase font-syncopate leading-tight">Nuevos horizontes <br> en la astrofísica</h4>
                        <p class="text-lg text-slate-400 font-light leading-relaxed">
                            Aquí el administrador podrá redactar artículos extensos sobre cómo la humanidad ha ido descubriendo de a poco los secretos del universo. Un espacio para la divulgación científica profunda.
                        </p>
                        <div class="flex gap-4">
                            <span class="px-4 py-2 bg-white/5 rounded-full text-[10px] font-bold">#Astrobiología</span>
                            <span class="px-4 py-2 bg-white/5 rounded-full text-[10px] font-bold">#COSMOS</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <footer class="py-20 border-t border-white/5 bg-black px-12 mt-20">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center">
            <p class="text-[10px] font-bold tracking-[0.5em] text-slate-600 uppercase italic">"Ad Astra Per Aspera"</p>
            <p class="text-[10px] font-bold tracking-[0.5em] text-slate-600 uppercase">Carrera de Física UMSA 2026</p>
        </div>
    </footer>

    <script>
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) entry.target.classList.add('active');
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
    </script>
</body>
</html>