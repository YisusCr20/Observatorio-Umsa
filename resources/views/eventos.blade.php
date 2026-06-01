<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Eventos Especiales | Observatorio Max Schreier</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Syncopate:wght@400;700&family=Plus+Jakarta+Sans:wght@200;400;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #000; color: white; font-family: 'Plus Jakarta Sans', sans-serif; overflow-x: hidden; }
        .glass-event { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .bg-nebula { background-image: radial-gradient(circle at 10% 20%, rgba(0, 163, 255, 0.1) 0%, transparent 40%), radial-gradient(circle at 90% 80%, rgba(255, 0, 200, 0.05) 0%, transparent 40%); }
        
        .reveal { opacity: 0; transform: scale(0.95); transition: 1s cubic-bezier(0.17, 0.67, 0.83, 0.67); }
        .reveal.active { opacity: 1; transform: scale(1); }

        /* Estilo para las redes sociales flotantes */
        .social-bar { position: fixed; right: 2rem; top: 50%; transform: translateY(-50%); z-index: 100; }
        .social-icon { width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 50%; margin-bottom: 1rem; transition: 0.3s; color: #fff; }
        .social-icon:hover { background: #22d3ee; color: #000; transform: scale(1.1); box-shadow: 0 0 20px rgba(34, 211, 238, 0.4); }
    </style>
</head>
<body class="bg-nebula">

    <aside class="social-bar hidden lg:flex flex-col">
        <a href="https://facebook.com" target="_blank" class="social-icon" title="Facebook">FB</a>
        <a href="https://twitter.com" target="_blank" class="social-icon" title="Twitter/X">X</a>
        <a href="https://tiktok.com" target="_blank" class="social-icon" title="TikTok">TK</a>
    </aside>

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

    <main class="max-w-7xl mx-auto px-6 pt-32 pb-20">
        
        <header class="text-center mb-24 reveal active">
            <h1 class="font-syncopate text-5xl md:text-8xl font-black mb-4 uppercase leading-none tracking-tighter">Agenda <br> <span class="text-cyan-400 text-shadow-glow">Astronómica</span></h1>
            <p class="text-slate-500 uppercase tracking-[0.6em] text-[10px]">Fenómenos captados desde la UMSA - Gestión 2026</p>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            
            <div class="md:col-span-2 glass-event rounded-[40px] overflow-hidden group reveal">
                <div class="flex flex-col lg:flex-row">
                    <div class="lg:w-3/5 h-96 lg:h-auto overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1543722530-d2c3201371e7?q=80&w=2074" class="w-full h-full object-cover group-hover:scale-105 transition duration-1000">
                    </div>
                    <div class="lg:w-2/5 p-12 flex flex-col justify-center space-y-6">
                        <div class="flex items-center gap-4">
                            <span class="bg-red-500 text-white text-[9px] font-black px-4 py-1 rounded-full uppercase animate-pulse">En Vivo / Próximo</span>
                            <span class="text-slate-500 text-[10px] font-bold uppercase tracking-widest">20 de Marzo, 2026</span>
                        </div>
                        <h2 class="text-4xl font-bold font-syncopate tracking-tight leading-tight">ALINEACIÓN DE <br>LOS GIGANTES</h2>
                        <p class="text-slate-400 leading-relaxed font-light">
                            Un evento único para este 2026. Estudiaremos la conjunción de **Venus, Marte y Plutón**. El observatorio habilitará telescopios de alta potencia para el seguimiento de sus órbitas.
                        </p>
                        <a href="{{ route('reservas.create') }}" class="bg-white text-black text-[10px] font-black py-4 px-8 rounded-full text-center uppercase tracking-widest hover:bg-cyan-400 transition">Agendar para este evento</a>
                    </div>
                </div>
            </div>

            <div class="glass-event rounded-[40px] p-8 group reveal" style="transition-delay: 200ms">
               <div class="h-64 rounded-[30px] overflow-hidden mb-8">
    <img src="https://images.unsplash.com/photo-1614730321146-b6fa6a46bcb4?q=80&w=2070"
         class="w-full h-full object-cover group-hover:scale-110 transition duration-700 opacity-70 group-hover:opacity-100 brightness-110 contrast-125">
</div>
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-2xl font-bold font-syncopate uppercase tracking-tighter">Obervar la Tierra<br>2026</h3>
                    <span class="text-cyan-400 font-bold text-xs uppercase tracking-widest">Abril 12</span>
                </div>
                <p class="text-slate-400 text-sm leading-relaxed mb-6">
                    Captura directa de la Tierra. El administrador posteará aquí las fotos exclusivas captadas por los sensores del Max Schreier.
                </p>
            </div>

            <div class="glass-event rounded-[40px] p-8 group reveal" style="transition-delay: 400ms">
                <div class="h-64 rounded-[30px] overflow-hidden mb-8">
                    <img src="https://images.unsplash.com/photo-1446941611757-91d2c3bd3d45?q=80&w=2004" class="w-full h-full object-cover group-hover:scale-110 transition duration-700 opacity-60 group-hover:opacity-100">
                </div>
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-2xl font-bold font-syncopate uppercase tracking-tighter">Paso del <br>Cometa C-2025</h3>
                    <span class="text-cyan-400 font-bold text-xs uppercase tracking-widest">Mayo 05</span>
                </div>
                <p class="text-slate-400 text-sm leading-relaxed mb-6">
                    Seguimiento de cola y núcleo. Datos técnicos de la trayectoria del cometa cruzando el hemisferio sur.
                </p>
            </div>
        </div>

        <section class="mt-32 p-12 glass-event rounded-[40px] text-center lg:hidden">
            <h4 class="font-syncopate text-xl mb-8">Síguenos en Redes</h4>
            <div class="flex justify-center gap-6">
                <a href="#" class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center font-bold">FB</a>
                <a href="#" class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center font-bold">X</a>
                <a href="#" class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center font-bold">TK</a>
            </div>
        </section>

    </main>

    <footer class="py-12 border-t border-white/5 text-center">
        <p class="text-[9px] font-bold text-slate-700 tracking-[0.5em] uppercase">Gestión de Eventos Astrónomicos • UMSA 2026</p>
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